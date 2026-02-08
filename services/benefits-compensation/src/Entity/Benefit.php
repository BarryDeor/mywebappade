<?php

namespace HRPayroll\BenefitsCompensation\Entity;

use DateTime;

class Benefit
{
    private string $id;
    private string $tenantId;
    private string $name;
    private string $description;
    private string $type; // INSURANCE, PENSION, GYM, MEAL_VOUCHER, etc.
    private string $category; // HEALTH, RETIREMENT, WELLNESS, etc.
    private float $employerCost;
    private float $employeeCost;
    private string $currency;
    private array $eligibilityCriteria;
    private bool $isActive;
    private ?DateTime $effectiveFrom;
    private ?DateTime $effectiveTo;
    private array $metadata;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        string $name,
        string $type,
        string $category,
        float $employerCost,
        float $employeeCost,
        string $currency
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->name = $name;
        $this->type = $type;
        $this->category = $category;
        $this->employerCost = $employerCost;
        $this->employeeCost = $employeeCost;
        $this->currency = $currency;
        $this->isActive = true;
        $this->eligibilityCriteria = [];
        $this->metadata = [];
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getTenantId(): string { return $this->tenantId; }
    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }
    public function getCategory(): string { return $this->category; }
    public function getEmployerCost(): float { return $this->employerCost; }
    public function getEmployeeCost(): float { return $this->employeeCost; }
    public function getCurrency(): string { return $this->currency; }
    public function isActive(): bool { return $this->isActive; }

    // Setters
    public function setDescription(string $description): void {
        $this->description = $description;
        $this->updatedAt = new DateTime();
    }

    public function setEligibilityCriteria(array $criteria): void {
        $this->eligibilityCriteria = $criteria;
        $this->updatedAt = new DateTime();
    }

    public function deactivate(): void {
        $this->isActive = false;
        $this->updatedAt = new DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'category' => $this->category,
            'employerCost' => $this->employerCost,
            'employeeCost' => $this->employeeCost,
            'currency' => $this->currency,
            'eligibilityCriteria' => $this->eligibilityCriteria,
            'isActive' => $this->isActive,
            'effectiveFrom' => $this->effectiveFrom?->format('Y-m-d'),
            'effectiveTo' => $this->effectiveTo?->format('Y-m-d'),
            'createdAt' => $this->createdAt->format('c'),
            'updatedAt' => $this->updatedAt->format('c')
        ];
    }
}
