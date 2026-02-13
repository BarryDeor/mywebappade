<?php

declare(strict_types=1);

namespace HRPayroll\PayrollProcessing\Entity;

use DateTimeImmutable;

/**
 * Payroll Run Entity
 */
class PayrollRun
{
    private string $id;
    private string $tenantId;
    private DateTimeImmutable $periodStart;
    private DateTimeImmutable $periodEnd;
    private DateTimeImmutable $paymentDate;
    private string $country;
    private string $currency;
    private string $status; // DRAFT, PROCESSING, CALCULATED, APPROVED, DISBURSED, FAILED
    private array $employeeFilter;
    private array $summary;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        DateTimeImmutable $periodStart,
        DateTimeImmutable $periodEnd,
        DateTimeImmutable $paymentDate,
        string $country,
        string $currency,
        array $employeeFilter = []
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
        $this->paymentDate = $paymentDate;
        $this->country = $country;
        $this->currency = $currency;
        $this->employeeFilter = $employeeFilter;
        $this->status = 'DRAFT';
        $this->summary = [];
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getTenantId(): string { return $this->tenantId; }
    public function getPeriodStart(): DateTimeImmutable { return $this->periodStart; }
    public function getPeriodEnd(): DateTimeImmutable { return $this->periodEnd; }
    public function getPaymentDate(): DateTimeImmutable { return $this->paymentDate; }
    public function getCountry(): string { return $this->country; }
    public function getCurrency(): string { return $this->currency; }
    public function getStatus(): string { return $this->status; }
    public function getEmployeeFilter(): array { return $this->employeeFilter; }
    public function getSummary(): array { return $this->summary; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    // Status transitions
    public function startProcessing(): void {
        $this->status = 'PROCESSING';
        $this->touch();
    }

    public function markCalculated(array $summary): void {
        $this->status = 'CALCULATED';
        $this->summary = $summary;
        $this->touch();
    }

    public function approve(): void {
        if ($this->status !== 'CALCULATED') {
            throw new \DomainException('Can only approve calculated payroll runs');
        }
        $this->status = 'APPROVED';
        $this->touch();
    }

    public function markDisbursed(): void {
        if ($this->status !== 'APPROVED') {
            throw new \DomainException('Can only disburse approved payroll runs');
        }
        $this->status = 'DISBURSED';
        $this->touch();
    }

    public function markFailed(): void {
        $this->status = 'FAILED';
        $this->touch();
    }

    private function touch(): void {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'periodStart' => $this->periodStart->format('Y-m-d'),
            'periodEnd' => $this->periodEnd->format('Y-m-d'),
            'paymentDate' => $this->paymentDate->format('Y-m-d'),
            'country' => $this->country,
            'currency' => $this->currency,
            'status' => $this->status,
            'employeeFilter' => $this->employeeFilter,
            'summary' => $this->summary,
            'createdAt' => $this->createdAt->format(DateTimeImmutable::RFC3339),
            'updatedAt' => $this->updatedAt->format(DateTimeImmutable::RFC3339)
        ];
    }
}
