<?php

namespace HRPayroll\PerformanceManagement\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use HRPayroll\PerformanceManagement\Entity\PerformanceReview;

class ReviewsController
{
    /**
     * POST /api/v1/performance/reviews
     * Create a performance review
     */
    public function createReview(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tenantId = $request->headers->get('X-Tenant-ID');

        $required = ['employeeId', 'reviewerId', 'reviewCycle', 'type'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $review = new PerformanceReview(
            'rev-' . uniqid(),
            $tenantId,
            $data['employeeId'],
            $data['reviewerId'],
            $data['reviewCycle'],
            $data['type']
        );

        $this->publishEvent('review.created', $review->toArray());

        return new JsonResponse($review->toArray(), 201);
    }

    /**
     * POST /api/v1/performance/reviews/{id}/ratings
     * Add ratings to a review
     */
    public function addRatings(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // In production, fetch review and add ratings
        $this->publishEvent('review.ratings-added', [
            'reviewId' => $id,
            'ratings' => $data['ratings'] ?? []
        ]);

        return new JsonResponse([
            'reviewId' => $id,
            'message' => 'Ratings added successfully'
        ]);
    }

    /**
     * POST /api/v1/performance/reviews/{id}/complete
     * Complete a review
     */
    public function completeReview(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $required = ['summary', 'strengths', 'areasForImprovement'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $this->publishEvent('review.completed', [
            'reviewId' => $id,
            'completedAt' => (new \DateTime())->format('c')
        ]);

        return new JsonResponse([
            'reviewId' => $id,
            'status' => 'COMPLETED',
            'message' => 'Review completed successfully'
        ]);
    }

    /**
     * POST /api/v1/performance/feedback
     * Submit 360-degree feedback
     */
    public function submitFeedback(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $required = ['reviewId', 'feedbackFrom', 'feedback'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $this->publishEvent('feedback.submitted', [
            'reviewId' => $data['reviewId'],
            'feedbackFrom' => $data['feedbackFrom'],
            'submittedAt' => (new \DateTime())->format('c')
        ]);

        return new JsonResponse([
            'message' => 'Feedback submitted successfully',
            'reviewId' => $data['reviewId']
        ], 201);
    }

    /**
     * GET /api/v1/performance/reviews/{employeeId}
     * Get employee reviews
     */
    public function getEmployeeReviews(string $employeeId, Request $request): JsonResponse
    {
        // Mock data
        $reviews = [
            [
                'id' => 'rev-001',
                'employeeId' => $employeeId,
                'reviewCycle' => 'ANNUAL_2025',
                'type' => 'ANNUAL',
                'status' => 'APPROVED',
                'overallRating' => 4.2,
                'reviewDate' => '2025-12-15',
                'reviewer' => 'John Manager'
            ],
            [
                'id' => 'rev-002',
                'employeeId' => $employeeId,
                'reviewCycle' => 'Q1_2026',
                'type' => 'QUARTERLY',
                'status' => 'IN_PROGRESS',
                'overallRating' => null,
                'reviewDate' => null,
                'reviewer' => 'John Manager'
            ]
        ];

        return new JsonResponse([
            'data' => $reviews,
            'total' => count($reviews)
        ]);
    }

    private function publishEvent(string $eventType, array $data): void
    {
        // Publish to message broker
    }
}
