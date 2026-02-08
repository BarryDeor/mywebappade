<?php

namespace HRPayroll\PerformanceManagement\Entity;

use DateTime;

class PerformanceReview
{
    private string $id;
    private string $tenantId;
    private string $employeeId;
    private string $reviewerId;
    private string $reviewCycle; // Q1_2026, ANNUAL_2026, etc.
    private string $type; // ANNUAL, QUARTERLY, PROBATION, 360_DEGREE
    private string $status; // PENDING, IN_PROGRESS, COMPLETED, APPROVED
    private ?DateTime $reviewDate;
    private array $ratings; // Array of competency ratings
    private ?float $overallRating;
    private ?string $summary;
    private ?string $strengths;
    private ?string $areasForImprovement;
    private array $goals; // Goals for next period
    private array $feedback360; // 360-degree feedback
    private ?string $approvedBy;
    private ?DateTime $approvedAt;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        string $employeeId,
        string $reviewerId,
        string $reviewCycle,
        string $type
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->employeeId = $employeeId;
        $this->reviewerId = $reviewerId;
        $this->reviewCycle = $reviewCycle;
        $this->type = $type;
        $this->status = 'PENDING';
        $this->ratings = [];
        $this->goals = [];
        $this->feedback360 = [];
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function addRating(string $competency, float $rating, ?string $comment = null): void
    {
        $this->ratings[] = [
            'competency' => $competency,
            'rating' => $rating,
            'comment' => $comment
        ];
        $this->calculateOverallRating();
        $this->updatedAt = new DateTime();
    }

    public function complete(string $summary, string $strengths, string $areasForImprovement): void
    {
        $this->status = 'COMPLETED';
        $this->reviewDate = new DateTime();
        $this->summary = $summary;
        $this->strengths = $strengths;
        $this->areasForImprovement = $areasForImprovement;
        $this->updatedAt = new DateTime();
    }

    public function approve(string $approvedBy): void
    {
        $this->status = 'APPROVED';
        $this->approvedBy = $approvedBy;
        $this->approvedAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function add360Feedback(string $feedbackFrom, array $feedback): void
    {
        $this->feedback360[] = [
            'from' => $feedbackFrom,
            'feedback' => $feedback,
            'submittedAt' => (new DateTime())->format('c')
        ];
        $this->updatedAt = new DateTime();
    }

    private function calculateOverallRating(): void
    {
        if (empty($this->ratings)) {
            $this->overallRating = null;
            return;
        }

        $sum = array_reduce($this->ratings, fn($carry, $item) => $carry + $item['rating'], 0);
        $this->overallRating = round($sum / count($this->ratings), 2);
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getEmployeeId(): string { return $this->employeeId; }
    public function getStatus(): string { return $this->status; }
    public function getOverallRating(): ?float { return $this->overallRating; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'employeeId' => $this->employeeId,
            'reviewerId' => $this->reviewerId,
            'reviewCycle' => $this->reviewCycle,
            'type' => $this->type,
            'status' => $this->status,
            'reviewDate' => $this->reviewDate?->format('Y-m-d'),
            'ratings' => $this->ratings,
            'overallRating' => $this->overallRating,
            'summary' => $this->summary,
            'strengths' => $this->strengths,
            'areasForImprovement' => $this->areasForImprovement,
            'goals' => $this->goals,
            'feedback360' => $this->feedback360,
            'approvedBy' => $this->approvedBy,
            'approvedAt' => $this->approvedAt?->format('c'),
            'createdAt' => $this->createdAt->format('c'),
            'updatedAt' => $this->updatedAt->format('c')
        ];
    }
}
