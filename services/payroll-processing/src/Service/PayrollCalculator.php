<?php

declare(strict_types=1);

namespace HRPayroll\PayrollProcessing\Service;

use HRPayroll\PayrollProcessing\Entity\Payslip;
use Ramsey\Uuid\Uuid;

/**
 * Payroll Calculator Service
 * Handles payroll calculations with country-specific rules
 */
class PayrollCalculator
{
    private TaxCalculatorInterface $taxCalculator;

    public function __construct(TaxCalculatorInterface $taxCalculator)
    {
        $this->taxCalculator = $taxCalculator;
    }

    /**
     * Calculate payslip for an employee
     */
    public function calculatePayslip(
        string $payrollRunId,
        string $tenantId,
        array $employeeData,
        array $salaryStructure,
        array $attendanceData,
        array $benefitsData,
        \DateTimeImmutable $periodStart,
        \DateTimeImmutable $periodEnd,
        \DateTimeImmutable $paymentDate,
        string $country,
        string $currency
    ): Payslip {
        // Calculate basic salary (pro-rated if needed)
        $basicSalary = $this->calculateBasicSalary(
            $salaryStructure['basicSalary'],
            $attendanceData,
            $periodStart,
            $periodEnd
        );

        // Calculate allowances
        $allowances = $this->calculateAllowances(
            $salaryStructure['allowances'] ?? [],
            $attendanceData
        );

        // Calculate gross pay
        $grossPay = $basicSalary + array_sum($allowances);

        // Calculate tax deductions
        $taxDeductions = $this->taxCalculator->calculateTax(
            $grossPay,
            $country,
            $employeeData
        );

        // Calculate other deductions
        $otherDeductions = $this->calculateDeductions(
            $salaryStructure['deductions'] ?? [],
            $benefitsData,
            $grossPay
        );

        // Merge all deductions
        $deductions = array_merge($taxDeductions, $otherDeductions);

        // Create payslip
        $payslip = new Payslip(
            Uuid::uuid4()->toString(),
            $payrollRunId,
            $tenantId,
            $employeeData['id'],
            $employeeData['employeeNumber'],
            $employeeData['firstName'] . ' ' . $employeeData['lastName'],
            $periodStart,
            $periodEnd,
            $paymentDate,
            $currency,
            $basicSalary,
            $allowances,
            $deductions
        );

        // Add metadata
        $payslip->addMetadata('workingDays', $attendanceData['workingDays'] ?? 0);
        $payslip->addMetadata('presentDays', $attendanceData['presentDays'] ?? 0);
        $payslip->addMetadata('overtimeHours', $attendanceData['overtimeHours'] ?? 0);

        return $payslip;
    }

    /**
     * Calculate basic salary with pro-ration
     */
    private function calculateBasicSalary(
        float $monthlySalary,
        array $attendanceData,
        \DateTimeImmutable $periodStart,
        \DateTimeImmutable $periodEnd
    ): float {
        $workingDays = $attendanceData['workingDays'] ?? 0;
        $presentDays = $attendanceData['presentDays'] ?? 0;
        $unpaidLeaveDays = $attendanceData['unpaidLeaveDays'] ?? 0;

        if ($workingDays === 0) {
            return 0.0;
        }

        // Pro-rate based on present days
        $effectiveDays = $presentDays - $unpaidLeaveDays;
        $dailyRate = $monthlySalary / $workingDays;
        
        return round($dailyRate * $effectiveDays, 2);
    }

    /**
     * Calculate allowances
     */
    private function calculateAllowances(array $allowanceConfig, array $attendanceData): array
    {
        $allowances = [];

        foreach ($allowanceConfig as $name => $config) {
            if ($config['type'] === 'FIXED') {
                $allowances[$name] = $config['amount'];
            } elseif ($config['type'] === 'PERCENTAGE') {
                // Percentage of basic salary
                $allowances[$name] = round($config['amount'], 2);
            } elseif ($config['type'] === 'OVERTIME') {
                $overtimeHours = $attendanceData['overtimeHours'] ?? 0;
                $hourlyRate = $config['hourlyRate'] ?? 0;
                $allowances[$name] = round($overtimeHours * $hourlyRate, 2);
            }
        }

        return $allowances;
    }

    /**
     * Calculate deductions
     */
    private function calculateDeductions(
        array $deductionConfig,
        array $benefitsData,
        float $grossPay
    ): array {
        $deductions = [];

        // Standard deductions
        foreach ($deductionConfig as $name => $config) {
            if ($config['type'] === 'FIXED') {
                $deductions[$name] = $config['amount'];
            } elseif ($config['type'] === 'PERCENTAGE') {
                $deductions[$name] = round($grossPay * ($config['percentage'] / 100), 2);
            }
        }

        // Benefits deductions
        foreach ($benefitsData as $benefit) {
            if (isset($benefit['employeeContribution'])) {
                $deductions[$benefit['name']] = $benefit['employeeContribution'];
            }
        }

        return $deductions;
    }

    /**
     * Calculate payroll summary for a run
     */
    public function calculateSummary(array $payslips): array
    {
        $totalEmployees = count($payslips);
        $totalGrossPay = 0;
        $totalDeductions = 0;
        $totalNetPay = 0;

        foreach ($payslips as $payslip) {
            $totalGrossPay += $payslip->getGrossPay();
            $totalDeductions += $payslip->getTotalDeductions();
            $totalNetPay += $payslip->getNetPay();
        }

        return [
            'totalEmployees' => $totalEmployees,
            'totalGrossPay' => round($totalGrossPay, 2),
            'totalDeductions' => round($totalDeductions, 2),
            'totalNetPay' => round($totalNetPay, 2)
        ];
    }
}
