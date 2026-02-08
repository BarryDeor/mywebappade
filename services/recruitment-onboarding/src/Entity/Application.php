<?php

namespace HRPayroll\RecruitmentOnboarding\Entity;

use DateTime;

class Application
{
    private string $id;
    private string $tenantId;
    private string $jobPostingId;
    private string $candidateName;
    private string $candidateEmail;
    private string $candidatePhone;
    private ?string $resumeUrl;
    private ?string $coverLetter;
    private string $status; // SUBMITTED, SCREENING, INTERVIEW, OFFER, REJECTED, HIRED
    private array $interviewSchedules;
    private array $feedback;
    private ?string $rejectionReason;
    private DateTime $appliedAt;
    private DateTime $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        string $jobPostingId,
        string $candidateName,
        string $candidateEmail,
        string $candidatePhone
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->jobPostingId = $jobPostingId;
        $this->candidateName = $candidateName;
        $this->candidateEmail = $candidateEmail;
        $this->candidatePhone = $candidatePhone;
        $this->status = 'SUBMITTED';
        $this->interviewSchedules = [];
        $this->feedback = [];
        $this->appliedAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function moveToStage(string $status): void
    {
        $this->status = $status;
        $this->updatedAt = new DateTime();
    }

    public function scheduleInterview(array $interview): void
    {
        $this->interviewSchedules[] = $interview;
        $this->status = 'INTERVIEW';
        $this->updatedAt = new DateTime();
    }

    public function addFeedback(array $feedback): void
    {
        $this->feedback[] = $feedback;
        $this->updatedAt = new DateTime();
    }

    public function reject(string $reason): void
    {
        $this->status = 'REJECTED';
        $this->rejectionReason = $reason;
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getJobPostingId(): string { return $this->jobPostingId; }
    public function getStatus(): string { return $this->status; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'jobPostingId' => $this->jobPostingId,
            'candidate' => [
                'name' => $this->candidateName,
                'email' => $this->candidateEmail,
                'phone' => $this->candidatePhone
            ],
            'resumeUrl' => $this->resumeUrl,
            'coverLetter' => $this->coverLetter,
            'status' => $this->status,
            'interviewSchedules' => $this->interviewSchedules,
            'feedback' => $this->feedback,
            'rejectionReason' => $this->rejectionReason,
            'appliedAt' => $this->appliedAt->format('c'),
            'updatedAt' => $this->updatedAt->format('c')
        ];
    }
}
