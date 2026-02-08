<?php

namespace HRPayroll\RecruitmentOnboarding\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use HRPayroll\RecruitmentOnboarding\Entity\JobPosting;
use HRPayroll\RecruitmentOnboarding\Entity\Application;

class RecruitmentController
{
    /**
     * POST /api/v1/jobs
     * Create a job posting
     */
    public function createJob(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tenantId = $request->headers->get('X-Tenant-ID');

        $required = ['title', 'departmentId', 'location', 'employmentType'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $job = new JobPosting(
            'job-' . uniqid(),
            $tenantId,
            $data['title'],
            $data['departmentId'],
            $data['location'],
            $data['employmentType']
        );

        if (isset($data['publish']) && $data['publish']) {
            $job->publish();
        }

        $this->publishEvent('job.posted', $job->toArray());

        return new JsonResponse($job->toArray(), 201);
    }

    /**
     * POST /api/v1/applications
     * Submit a job application
     */
    public function submitApplication(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tenantId = $request->headers->get('X-Tenant-ID');

        $required = ['jobPostingId', 'candidateName', 'candidateEmail', 'candidatePhone'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $application = new Application(
            'app-' . uniqid(),
            $tenantId,
            $data['jobPostingId'],
            $data['candidateName'],
            $data['candidateEmail'],
            $data['candidatePhone']
        );

        $this->publishEvent('application.received', $application->toArray());

        return new JsonResponse($application->toArray(), 201);
    }

    /**
     * GET /api/v1/applications/{id}
     * Get application status
     */
    public function getApplication(string $id, Request $request): JsonResponse
    {
        // Mock data
        $application = [
            'id' => $id,
            'jobPostingId' => 'job-123',
            'jobTitle' => 'Senior Software Engineer',
            'candidate' => [
                'name' => 'John Doe',
                'email' => 'john@example.com'
            ],
            'status' => 'INTERVIEW',
            'currentStage' => 'Technical Interview',
            'appliedAt' => '2026-02-01T10:00:00Z'
        ];

        return new JsonResponse($application);
    }

    /**
     * POST /api/v1/interviews
     * Schedule an interview
     */
    public function scheduleInterview(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $required = ['applicationId', 'interviewType', 'scheduledAt', 'interviewers'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $interview = [
            'id' => 'int-' . uniqid(),
            'applicationId' => $data['applicationId'],
            'type' => $data['interviewType'],
            'scheduledAt' => $data['scheduledAt'],
            'interviewers' => $data['interviewers'],
            'location' => $data['location'] ?? 'Virtual',
            'status' => 'SCHEDULED'
        ];

        $this->publishEvent('interview.scheduled', $interview);

        return new JsonResponse($interview, 201);
    }

    /**
     * POST /api/v1/offers
     * Generate job offer
     */
    public function generateOffer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tenantId = $request->headers->get('X-Tenant-ID');

        $required = ['applicationId', 'salary', 'startDate'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $offer = [
            'id' => 'off-' . uniqid(),
            'tenantId' => $tenantId,
            'applicationId' => $data['applicationId'],
            'salary' => $data['salary'],
            'currency' => $data['currency'] ?? 'USD',
            'startDate' => $data['startDate'],
            'benefits' => $data['benefits'] ?? [],
            'status' => 'PENDING',
            'expiresAt' => (new \DateTime('+7 days'))->format('Y-m-d'),
            'generatedAt' => (new \DateTime())->format('c')
        ];

        $this->publishEvent('offer.generated', $offer);

        return new JsonResponse($offer, 201);
    }

    /**
     * GET /api/v1/jobs
     * List job postings
     */
    public function listJobs(Request $request): JsonResponse
    {
        $status = $request->query->get('status', 'PUBLISHED');

        // Mock data
        $jobs = [
            [
                'id' => 'job-001',
                'title' => 'Senior Software Engineer',
                'department' => 'Engineering',
                'location' => 'San Francisco, CA',
                'employmentType' => 'FULL_TIME',
                'status' => 'PUBLISHED',
                'applicationsCount' => 45
            ],
            [
                'id' => 'job-002',
                'title' => 'Product Manager',
                'department' => 'Product',
                'location' => 'Remote',
                'employmentType' => 'FULL_TIME',
                'status' => 'PUBLISHED',
                'applicationsCount' => 32
            ]
        ];

        return new JsonResponse([
            'data' => $jobs,
            'total' => count($jobs)
        ]);
    }

    private function publishEvent(string $eventType, array $data): void
    {
        // Publish to message broker
    }
}
