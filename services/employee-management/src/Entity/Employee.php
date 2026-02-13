<?php

declare(strict_types=1);

namespace HRPayroll\EmployeeManagement\Entity;

use DateTimeImmutable;

/**
 * Employee Entity
 */
class Employee
{
    private string $id;
    private string $tenantId;
    private string $employeeNumber;
    private string $firstName;
    private string $lastName;
    private string $email;
    private ?string $phone;
    private DateTimeImmutable $dateOfBirth;
    private DateTimeImmutable $hireDate;
    private ?DateTimeImmutable $terminationDate;
    private string $departmentId;
    private string $jobTitle;
    private string $employmentType; // FULL_TIME, PART_TIME, CONTRACT, INTERN
    private string $status; // ACTIVE, INACTIVE, TERMINATED, ON_LEAVE
    private array $location;
    private ?string $managerId;
    private array $metadata;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        string $employeeNumber,
        string $firstName,
        string $lastName,
        string $email,
        DateTimeImmutable $dateOfBirth,
        DateTimeImmutable $hireDate,
        string $departmentId,
        string $jobTitle,
        string $employmentType,
        array $location
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->employeeNumber = $employeeNumber;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->dateOfBirth = $dateOfBirth;
        $this->hireDate = $hireDate;
        $this->departmentId = $departmentId;
        $this->jobTitle = $jobTitle;
        $this->employmentType = $employmentType;
        $this->location = $location;
        $this->status = 'ACTIVE';
        $this->metadata = [];
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getTenantId(): string { return $this->tenantId; }
    public function getEmployeeNumber(): string { return $this->employeeNumber; }
    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getFullName(): string { return $this->firstName . ' ' . $this->lastName; }
    public function getEmail(): string { return $this->email; }
    public function getPhone(): ?string { return $this->phone; }
    public function getDateOfBirth(): DateTimeImmutable { return $this->dateOfBirth; }
    public function getHireDate(): DateTimeImmutable { return $this->hireDate; }
    public function getTerminationDate(): ?DateTimeImmutable { return $this->terminationDate; }
    public function getDepartmentId(): string { return $this->departmentId; }
    public function getJobTitle(): string { return $this->jobTitle; }
    public function getEmploymentType(): string { return $this->employmentType; }
    public function getStatus(): string { return $this->status; }
    public function getLocation(): array { return $this->location; }
    public function getManagerId(): ?string { return $this->managerId; }
    public function getMetadata(): array { return $this->metadata; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    // Setters
    public function setPhone(?string $phone): void { 
        $this->phone = $phone; 
        $this->touch();
    }

    public function setDepartmentId(string $departmentId): void { 
        $this->departmentId = $departmentId; 
        $this->touch();
    }

    public function setJobTitle(string $jobTitle): void { 
        $this->jobTitle = $jobTitle; 
        $this->touch();
    }

    public function setManagerId(?string $managerId): void { 
        $this->managerId = $managerId; 
        $this->touch();
    }

    public function setStatus(string $status): void { 
        $this->status = $status; 
        $this->touch();
    }

    public function terminate(DateTimeImmutable $terminationDate): void {
        $this->terminationDate = $terminationDate;
        $this->status = 'TERMINATED';
        $this->touch();
    }

    public function updateLocation(array $location): void {
        $this->location = $location;
        $this->touch();
    }

    public function addMetadata(string $key, mixed $value): void {
        $this->metadata[$key] = $value;
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
            'employeeNumber' => $this->employeeNumber,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'dateOfBirth' => $this->dateOfBirth->format('Y-m-d'),
            'hireDate' => $this->hireDate->format('Y-m-d'),
            'terminationDate' => $this->terminationDate?->format('Y-m-d'),
            'departmentId' => $this->departmentId,
            'jobTitle' => $this->jobTitle,
            'employmentType' => $this->employmentType,
            'status' => $this->status,
            'location' => $this->location,
            'managerId' => $this->managerId,
            'metadata' => $this->metadata,
            'createdAt' => $this->createdAt->format(DateTimeImmutable::RFC3339),
            'updatedAt' => $this->updatedAt->format(DateTimeImmutable::RFC3339)
        ];
    }
}
