<?php

namespace HRPayroll\TaxCompliance\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use HRPayroll\TaxCompliance\Service\TaxCalculator;

class TaxController
{
    private TaxCalculator $taxCalculator;

    public function __construct()
    {
        $this->taxCalculator = new TaxCalculator();
    }

    /**
     * POST /api/v1/tax/calculate
     * Calculate taxes for an employee
     */
    public function calculateTax(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate required fields
        $required = ['grossIncome', 'country'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        try {
            $incomeTax = $this->taxCalculator->calculateIncomeTax(
                $data['grossIncome'],
                $data['country'],
                $data['employeeData'] ?? []
            );

            $socialSecurity = $this->taxCalculator->calculateSocialSecurity(
                $data['grossIncome'],
                $data['country']
            );

            $result = [
                'incomeTax' => $incomeTax,
                'socialSecurity' => $socialSecurity,
                'totalDeductions' => $incomeTax['totalTax'] + $socialSecurity['amount'],
                'netIncome' => $data['grossIncome'] - ($incomeTax['totalTax'] + $socialSecurity['amount'])
            ];

            // Publish event
            $this->publishEvent('tax.calculated', $result);

            return new JsonResponse($result);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * GET /api/v1/tax/rules/{country}
     * Get tax rules for a country
     */
    public function getTaxRules(string $country, Request $request): JsonResponse
    {
        $rules = $this->taxCalculator->getTaxRules($country);

        if (empty($rules)) {
            return new JsonResponse(['error' => "Tax rules not found for country: $country"], 404);
        }

        return new JsonResponse($rules);
    }

    /**
     * POST /api/v1/tax/forms/{employeeId}
     * Generate tax forms for an employee
     */
    public function generateTaxForm(string $employeeId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $formType = $data['formType'] ?? 'W2'; // W2, 1099, etc.
        $taxYear = $data['taxYear'] ?? date('Y');

        // Mock data - in production, fetch from database and generate PDF
        $form = [
            'employeeId' => $employeeId,
            'formType' => $formType,
            'taxYear' => $taxYear,
            'generatedAt' => (new \DateTime())->format('c'),
            'downloadUrl' => "/api/v1/tax/forms/{$employeeId}/download/{$formType}-{$taxYear}.pdf",
            'status' => 'GENERATED'
        ];

        $this->publishEvent('tax.form-generated', $form);

        return new JsonResponse($form, 201);
    }

    /**
     * GET /api/v1/tax/summary/{employeeId}
     * Get tax summary for an employee
     */
    public function getTaxSummary(string $employeeId, Request $request): JsonResponse
    {
        $year = $request->query->get('year', date('Y'));

        // Mock data
        $summary = [
            'employeeId' => $employeeId,
            'year' => $year,
            'totalGrossIncome' => 80000.00,
            'totalIncomeTax' => 12500.00,
            'totalSocialSecurity' => 4960.00,
            'totalMedicare' => 1160.00,
            'totalDeductions' => 18620.00,
            'effectiveTaxRate' => 15.63,
            'monthlyBreakdown' => [
                ['month' => 'January', 'grossIncome' => 6666.67, 'totalTax' => 1551.67],
                ['month' => 'February', 'grossIncome' => 6666.67, 'totalTax' => 1551.67]
            ]
        ];

        return new JsonResponse($summary);
    }

    private function publishEvent(string $eventType, array $data): void
    {
        // Publish to message broker
    }
}
