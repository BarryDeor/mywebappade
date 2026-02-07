<?php

declare(strict_types=1);

namespace HRPayroll\PayrollProcessing\Entity;

use DateTimeImmutable;

/**
 * Payslip Entity
 */
class Payslip
{
    private string $id;
    private string $payrollRunId;
    private string $tenantId;
    private string $employeeId;
    private string $employeeNumber;
    private string $employeeName;
    private DateTimeImmutable $periodStart;
    private DateTimeImmutable $periodEnd;
    private DateTimeImmutable $paymentDate;
    private string $currency;
    private float $basicSalary;
    private array $allowances;
    private float $grossPay;
    private array $deductions;
    private float $totalDeductions;
    private float $netPay;
    private array $metadata;
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $payrollRunId,
        string $tenantId,
        string $employeeId,
        string $employeeNumber,
        string $employeeName,
        DateTimeImmutable $periodStart,
        DateTimeImmutable $periodEnd,
        DateTimeImmutable $paymentDate,
        string $currency,
        float $basicSalary,
        array $allowances,
        array $deductions
    ) {
        $this->id = $id;
        $this->payrollRunId = $payrollRunId;
        $this->tenantId = $tenantId;
        $this->employeeId = $employeeId;
        $this->employeeNumber = $employeeNumber;
        $this->employeeName = $employeeName;
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
        $this->paymentDate = $paymentDate;
        $this->currency = $currency;
        $this->basicSalary = $basicSalary;
        $this->allowances = $allowances;
        $this->deductions = $deductions;
        $this->metadata = [];
        $this->createdAt = new DateTimeImmutable();

        $this->calculateTotals();
    }

    private function calculateTotals(): void
    {
        $this->grossPay = $this->basicSalary + array_sum($this->allowances);
        $this->totalDeductions = array_sum($this->deductions);
        $this->netPay = $this->grossPay - $this->totalDeductions;
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getPayrollRunId(): string { return $this->payrollRunId; }
    public function getTenantId(): string { return $this->tenantId; }
    public function getEmployeeId(): string { return $this->employeeId; }
    public function getEmployeeNumber(): string { return $this->employeeNumber; }
    public function getEmployeeName(): string { return $this->employeeName; }
    public function getPeriodStart(): DateTimeImmutable { return $this->periodStart; }
    public function getPeriodEnd(): DateTimeImmutable { return $this->periodEnd; }
    public function getPaymentDate(): DateTimeImmutable { return $this->paymentDate; }
    public function getCurrency(): string { return $this->currency; }
    public function getBasicSalary(): float { return $this->basicSalary; }
    public function getAllowances(): array { return $this->allowances; }
    public function getGrossPay(): float { return $this->grossPay; }
    public function getDeductions(): array { return $this->deductions; }
    public function getTotalDeductions(): float { return $this->totalDeductions; }
    public function getNetPay(): float { return $this->netPay; }
    public function getMetadata(): array { return $this->metadata; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }

    public function addMetadata(string $key, mixed $value): void {
        $this->metadata[$key] = $value;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'payrollRunId' => $this->payrollRunId,
            'tenantId' => $this->tenantId,
            'employeeId' => $this->employeeId,
            'employeeNumber' => $this->employeeNumber,
            'employeeName' => $this->employeeName,
            'periodStart' => $this->periodStart->format('Y-m-d'),
            'periodEnd' => $this->periodEnd->format('Y-m-d'),
            'paymentDate' => $this->paymentDate->format('Y-m-d'),
            'currency' => $this->currency,
            'basicSalary' => $this->basicSalary,
            'allowances' => $this->allowances,
            'grossPay' => $this->grossPay,
            'deductions' => $this->deductions,
            'totalDeductions' => $this->totalDeductions,
            'netPay' => $this->netPay,
            'metadata' => $this->metadata,
            'createdAt' => $this->createdAt->format(DateTimeImmutable::RFC3339)
        ];
    }
}
