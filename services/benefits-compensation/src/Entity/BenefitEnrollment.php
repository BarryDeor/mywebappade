<?php

namespace HRPayroll\BenefitsCompensation\Entity;

use DateTime;

class BenefitEnrollment
{
    private string $id;
    private string $tenantId;
    private string $employeeId;
    private string $benefitId;
    private string $status; // PENDING, ACTIVE, CANCELLED, EXPIRED
    private DateTime $enrollmentDate;
    private ?DateTime $effectiveDate;
    private ?DateTime $terminationDate;
    private array $dependents;
    private float $employeeContribution;
    private float $employerContribution;
    private string $currency;
    private array $metadata;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        string $employeeId,
        string $benefitId,
        float $employeeContribution,
        float $employerContribution,
        string $currency
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->employeeId = $employeeId;
        $this->benefitId = $benefitId;
        $this->employeeContribution = $employeeContribution;
        $this->employerContribution = $employerContribution;
        $this->currency = $currency;
        $this->status = 'PENDING';
        $this->enrollmentDate = new DateTime();
        $this->dependents = [];
        $this->metadata = [];
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function activate(DateTime $effectiveDate): void
    {
        $this->status = 'ACTIVE';
        $this->effectiveDate = $effectiveDate;
        $this->updatedAt = new DateTime();
    }

    public function cancel(DateTime $terminationDate): void
    {
        $this->status = 'CANCELLED';
        $this->terminationDate = $terminationDate;
        $this->updatedAt = new DateTime();
    }

    public function addDependent(array $dependent): void
    {
        $this->dependents[] = $dependent;
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getEmployeeId(): string { return $this->employeeId; }
    public function getBenefitId(): string { return $this->benefitId; }
    public function getStatus(): string { return $this->status; }
    public function getEmployeeContribution(): float { return $this->employeeContribution; }
    public function getEmployerContribution(): float { return $this->employerContribution; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'employeeId' => $this->employeeId,
            'benefitId' => $this->benefitId,
            'status' => $this->status,
            'enrollmentDate' => $this->enrollmentDate->format('Y-m-d'),
            'effectiveDate' => $this->effectiveDate?->format('Y-m-d'),
            'terminationDate' => $this->terminationDate?->format('Y-m-d'),
            'dependents' => $this->dependents,
            'employeeContribution' => $this->employeeContribution,
            'employerContribution' => $this->employerContribution,
            'currency' => $this->currency,
            'createdAt' => $this->createdAt->format('c'),
            'updatedAt' => $this->updatedAt->format('c')
        ];
    }
}
