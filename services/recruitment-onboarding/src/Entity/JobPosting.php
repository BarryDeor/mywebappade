<?php

namespace HRPayroll\RecruitmentOnboarding\Entity;

use DateTime;

class JobPosting
{
    private string $id;
    private string $tenantId;
    private string $title;
    private string $description;
    private string $departmentId;
    private string $location;
    private string $employmentType; // FULL_TIME, PART_TIME, CONTRACT
    private string $experienceLevel; // ENTRY, MID, SENIOR, EXECUTIVE
    private array $requirements;
    private array $responsibilities;
    private ?float $salaryMin;
    private ?float $salaryMax;
    private string $currency;
    private string $status; // DRAFT, PUBLISHED, CLOSED, FILLED
    private ?DateTime $publishedAt;
    private ?DateTime $closedAt;
    private int $applicationsCount;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        string $title,
        string $departmentId,
        string $location,
        string $employmentType
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->title = $title;
        $this->departmentId = $departmentId;
        $this->location = $location;
        $this->employmentType = $employmentType;
        $this->status = 'DRAFT';
        $this->requirements = [];
        $this->responsibilities = [];
        $this->applicationsCount = 0;
        $this->currency = 'USD';
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function publish(): void
    {
        $this->status = 'PUBLISHED';
        $this->publishedAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function close(): void
    {
        $this->status = 'CLOSED';
        $this->closedAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function incrementApplications(): void
    {
        $this->applicationsCount++;
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getStatus(): string { return $this->status; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'title' => $this->title,
            'description' => $this->description,
            'departmentId' => $this->departmentId,
            'location' => $this->location,
            'employmentType' => $this->employmentType,
            'experienceLevel' => $this->experienceLevel,
            'requirements' => $this->requirements,
            'responsibilities' => $this->responsibilities,
            'salaryRange' => [
                'min' => $this->salaryMin,
                'max' => $this->salaryMax,
                'currency' => $this->currency
            ],
            'status' => $this->status,
            'publishedAt' => $this->publishedAt?->format('c'),
            'closedAt' => $this->closedAt?->format('c'),
            'applicationsCount' => $this->applicationsCount,
            'createdAt' => $this->createdAt->format('c'),
            'updatedAt' => $this->updatedAt->format('c')
        ];
    }
}
