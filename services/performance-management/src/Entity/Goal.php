<?php

namespace HRPayroll\PerformanceManagement\Entity;

use DateTime;

class Goal
{
    private string $id;
    private string $tenantId;
    private string $employeeId;
    private string $title;
    private string $description;
    private string $type; // INDIVIDUAL, TEAM, COMPANY
    private string $category; // OKR, KPI, PROJECT, DEVELOPMENT
    private string $status; // DRAFT, ACTIVE, COMPLETED, CANCELLED
    private int $progress; // 0-100
    private ?DateTime $startDate;
    private ?DateTime $dueDate;
    private ?DateTime $completedDate;
    private array $keyResults;
    private ?string $parentGoalId;
    private array $metadata;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        string $employeeId,
        string $title,
        string $type,
        string $category
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->employeeId = $employeeId;
        $this->title = $title;
        $this->type = $type;
        $this->category = $category;
        $this->status = 'DRAFT';
        $this->progress = 0;
        $this->keyResults = [];
        $this->metadata = [];
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function activate(DateTime $startDate, DateTime $dueDate): void
    {
        $this->status = 'ACTIVE';
        $this->startDate = $startDate;
        $this->dueDate = $dueDate;
        $this->updatedAt = new DateTime();
    }

    public function updateProgress(int $progress): void
    {
        $this->progress = max(0, min(100, $progress));
        
        if ($this->progress === 100) {
            $this->status = 'COMPLETED';
            $this->completedDate = new DateTime();
        }
        
        $this->updatedAt = new DateTime();
    }

    public function addKeyResult(array $keyResult): void
    {
        $this->keyResults[] = $keyResult;
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getEmployeeId(): string { return $this->employeeId; }
    public function getTitle(): string { return $this->title; }
    public function getStatus(): string { return $this->status; }
    public function getProgress(): int { return $this->progress; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'employeeId' => $this->employeeId,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'category' => $this->category,
            'status' => $this->status,
            'progress' => $this->progress,
            'startDate' => $this->startDate?->format('Y-m-d'),
            'dueDate' => $this->dueDate?->format('Y-m-d'),
            'completedDate' => $this->completedDate?->format('Y-m-d'),
            'keyResults' => $this->keyResults,
            'parentGoalId' => $this->parentGoalId,
            'createdAt' => $this->createdAt->format('c'),
            'updatedAt' => $this->updatedAt->format('c')
        ];
    }
}
