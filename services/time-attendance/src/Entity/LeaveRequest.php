<?php

namespace HRPayroll\TimeAttendance\Entity;

use DateTime;

class LeaveRequest
{
    private string $id;
    private string $tenantId;
    private string $employeeId;
    private string $leaveType; // ANNUAL, SICK, MATERNITY, PATERNITY, UNPAID, etc.
    private DateTime $startDate;
    private DateTime $endDate;
    private float $daysRequested;
    private string $reason;
    private string $status; // PENDING, APPROVED, REJECTED, CANCELLED
    private ?string $approvedBy;
    private ?DateTime $approvedAt;
    private ?string $rejectionReason;
    private array $metadata;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        string $employeeId,
        string $leaveType,
        DateTime $startDate,
        DateTime $endDate,
        float $daysRequested,
        string $reason
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->employeeId = $employeeId;
        $this->leaveType = $leaveType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->daysRequested = $daysRequested;
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

    public function reject(string $reason): void
    {
        $this->status = 'REJECTED';
        $this->rejectionReason = $reason;
        $this->updatedAt = new DateTime();
    }

    public function cancel(): void
    {
        $this->status = 'CANCELLED';
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getEmployeeId(): string { return $this->employeeId; }
    public function getStatus(): string { return $this->status; }
    public function getDaysRequested(): float { return $this->daysRequested; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'employeeId' => $this->employeeId,
            'leaveType' => $this->leaveType,
            'startDate' => $this->startDate->format('Y-m-d'),
            'endDate' => $this->endDate->format('Y-m-d'),
            'daysRequested' => $this->daysRequested,
            'reason' => $this->reason,
            'status' => $this->status,
            'approvedBy' => $this->approvedBy,
            'approvedAt' => $this->approvedAt?->format('c'),
            'rejectionReason' => $this->rejectionReason,
            'createdAt' => $this->createdAt->format('c'),
            'updatedAt' => $this->updatedAt->format('c')
        ];
    }
}
