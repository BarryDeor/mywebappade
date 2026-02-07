<?php

declare(strict_types=1);

namespace HRPayroll\PayrollProcessing\Service;

/**
 * Interface for country-specific tax calculators
 */
interface TaxCalculatorInterface
{
    /**
     * Calculate tax deductions for an employee
     *
     * @param float $grossPay
     * @param string $country
     * @param array $employeeData
     * @return array Array of tax deductions ['incomeTax' => amount, 'socialSecurity' => amount, ...]
     */
    public function calculateTax(float $grossPay, string $country, array $employeeData): array;

    /**
     * Get tax rules for a country
     *
     * @param string $country
     * @return array
     */
    public function getTaxRules(string $country): array;
}
