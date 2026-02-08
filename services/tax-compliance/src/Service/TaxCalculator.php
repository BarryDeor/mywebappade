<?php

namespace HRPayroll\TaxCompliance\Service;

class TaxCalculator implements TaxCalculatorInterface
{
    private array $taxRules;

    public function __construct()
    {
        // Load tax rules from database or configuration
        $this->taxRules = $this->loadTaxRules();
    }

    public function calculateIncomeTax(float $grossIncome, string $country, array $employeeData): array
    {
        $rules = $this->getTaxRules($country);
        
        if (!$rules) {
            throw new \Exception("Tax rules not found for country: $country");
        }

        $taxableIncome = $this->calculateTaxableIncome($grossIncome, $employeeData);
        $taxAmount = 0;
        $breakdown = [];

        // Progressive tax calculation
        foreach ($rules['brackets'] as $bracket) {
            if ($taxableIncome <= 0) break;

            $bracketIncome = min(
                $taxableIncome,
                $bracket['upper'] ? ($bracket['upper'] - $bracket['lower']) : $taxableIncome
            );

            $bracketTax = $bracketIncome * ($bracket['rate'] / 100);
            $taxAmount += $bracketTax;

            $breakdown[] = [
                'bracket' => $bracket['lower'] . '-' . ($bracket['upper'] ?? 'above'),
                'rate' => $bracket['rate'],
                'taxableAmount' => $bracketIncome,
                'taxAmount' => $bracketTax
            ];

            $taxableIncome -= $bracketIncome;
        }

        return [
            'grossIncome' => $grossIncome,
            'taxableIncome' => $this->calculateTaxableIncome($grossIncome, $employeeData),
            'totalTax' => round($taxAmount, 2),
            'effectiveRate' => round(($taxAmount / $grossIncome) * 100, 2),
            'breakdown' => $breakdown,
            'country' => $country
        ];
    }

    public function calculateSocialSecurity(float $grossIncome, string $country): array
    {
        $rules = $this->getTaxRules($country);
        
        if (!isset($rules['socialSecurity'])) {
            return ['amount' => 0, 'rate' => 0];
        }

        $ss = $rules['socialSecurity'];
        $cappedIncome = $ss['cap'] ? min($grossIncome, $ss['cap']) : $grossIncome;
        $amount = $cappedIncome * ($ss['rate'] / 100);

        return [
            'grossIncome' => $grossIncome,
            'cappedIncome' => $cappedIncome,
            'rate' => $ss['rate'],
            'amount' => round($amount, 2),
            'cap' => $ss['cap']
        ];
    }

    public function getTaxRules(string $country): array
    {
        return $this->taxRules[$country] ?? [];
    }

    private function calculateTaxableIncome(float $grossIncome, array $employeeData): float
    {
        $deductions = $employeeData['deductions'] ?? 0;
        $exemptions = $employeeData['exemptions'] ?? 0;
        
        return max(0, $grossIncome - $deductions - $exemptions);
    }

    private function loadTaxRules(): array
    {
        // In production, load from database
        return [
            'US' => [
                'brackets' => [
                    ['lower' => 0, 'upper' => 11000, 'rate' => 10],
                    ['lower' => 11000, 'upper' => 44725, 'rate' => 12],
                    ['lower' => 44725, 'upper' => 95375, 'rate' => 22],
                    ['lower' => 95375, 'upper' => 182100, 'rate' => 24],
                    ['lower' => 182100, 'upper' => 231250, 'rate' => 32],
                    ['lower' => 231250, 'upper' => 578125, 'rate' => 35],
                    ['lower' => 578125, 'upper' => null, 'rate' => 37]
                ],
                'socialSecurity' => [
                    'rate' => 6.2,
                    'cap' => 160200
                ],
                'medicare' => [
                    'rate' => 1.45,
                    'cap' => null
                ]
            ],
            'UK' => [
                'brackets' => [
                    ['lower' => 0, 'upper' => 12570, 'rate' => 0],
                    ['lower' => 12570, 'upper' => 50270, 'rate' => 20],
                    ['lower' => 50270, 'upper' => 125140, 'rate' => 40],
                    ['lower' => 125140, 'upper' => null, 'rate' => 45]
                ],
                'socialSecurity' => [
                    'rate' => 12,
                    'cap' => 50270
                ]
            ],
            'IN' => [
                'brackets' => [
                    ['lower' => 0, 'upper' => 250000, 'rate' => 0],
                    ['lower' => 250000, 'upper' => 500000, 'rate' => 5],
                    ['lower' => 500000, 'upper' => 1000000, 'rate' => 20],
                    ['lower' => 1000000, 'upper' => null, 'rate' => 30]
                ],
                'socialSecurity' => [
                    'rate' => 12,
                    'cap' => 1800000
                ]
            ]
        ];
    }
}
