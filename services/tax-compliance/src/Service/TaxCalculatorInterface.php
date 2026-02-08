<?php

namespace HRPayroll\TaxCompliance\Service;

interface TaxCalculatorInterface
{
    /**
     * Calculate income tax for an employee
     * 
     * @param float $grossIncome Gross income amount
     * @param string $country Country code (US, UK, IN, etc.)
     * @param array $employeeData Additional employee data (dependents, deductions, etc.)
     * @return array Tax breakdown
     */
    public function calculateIncomeTax(float $grossIncome, string $country, array $employeeData): array;

    /**
     * Calculate social security contributions
     * 
     * @param float $grossIncome Gross income amount
     * @param string $country Country code
     * @return array Social security breakdown
     */
    public function calculateSocialSecurity(float $grossIncome, string $country): array;

    /**
     * Get tax rules for a specific country
     * 
     * @param string $country Country code
     * @return array Tax rules and brackets
     */
    public function getTaxRules(string $country): array;
}
