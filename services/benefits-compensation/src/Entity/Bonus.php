<?php

namespace HRPayroll\BenefitsCompensation\Entity;

use DateTime;

class Bonus
{
    private string $id;
    private string $tenantId;
    private string $employeeId;
    private string $type; // PERFORMANCE, ANNUAL, SPOT, REFERRAL, RETENTION
    private float $amount;
    private string $currency;
    private string $status; // PENDING, APPROVED, REJECTED, PAID
    private ?DateTime $paymentDate;
    private string $reason;
    private ?string $approvedBy;
    private ?DateTime $approvedAt;
    private array $metadata;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        string $employeeId,
        string $type,
        float $amount,
        string $currency,
        string $reason
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->employeeId = $employeeId;
        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->reason = $reason;
        $this->status = 'PENDING';
        $this->metadata = [];
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function approve(string $approvedBy): void
    {
        $this->status = 'APPROVED';
        $this->approvedBy = $approvedBy;
        $this->approvedAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function reject(): void
    {
        $this->status = 'REJECTED';
        $this->updatedAt = new DateTime();
    }

    public function markAsPaid(DateTime $paymentDate): void
    {
        $this->status = 'PAID';
        $this->paymentDate = $paymentDate;
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getEmployeeId(): string { return $this->employeeId; }
    public function getAmount(): float { return $this->amount; }
    public function getStatus(): string { return $this->status; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'employeeId' => $this->employeeId,
            'type' => $this->type,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'reason' => $this->reason,
            'paymentDate' => $this->paymentDate?->format('Y-m-d'),
            'approvedBy' => $this->approvedBy,
            'approvedAt' => $this->approvedAt?->format('c'),
            'createdAt' => $this->createdAt->format('c'),
            'updatedAt' => $this->updatedAt->format('c')
        ];
    }
}
