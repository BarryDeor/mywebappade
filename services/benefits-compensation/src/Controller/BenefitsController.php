<?php

namespace HRPayroll\BenefitsCompensation\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use HRPayroll\BenefitsCompensation\Entity\Benefit;
use HRPayroll\BenefitsCompensation\Entity\BenefitEnrollment;

class BenefitsController
{
    /**
     * GET /api/v1/benefits/catalog
     * List all available benefits
     */
    public function getCatalog(Request $request): JsonResponse
    {
        $tenantId = $request->headers->get('X-Tenant-ID');
        
        // Mock data - in production, fetch from database
        $benefits = [
            [
                'id' => 'ben-001',
                'name' => 'Health Insurance - Premium',
                'type' => 'INSURANCE',
                'category' => 'HEALTH',
                'employerCost' => 500.00,
                'employeeCost' => 100.00,
                'currency' => 'USD',
                'description' => 'Comprehensive health coverage with dental and vision'
            ],
            [
                'id' => 'ben-002',
                'name' => '401(k) Retirement Plan',
                'type' => 'PENSION',
                'category' => 'RETIREMENT',
                'employerCost' => 0.00,
                'employeeCost' => 0.00,
                'currency' => 'USD',
                'description' => 'Employer matches up to 6% of salary'
            ]
        ];

        return new JsonResponse([
            'data' => $benefits,
            'total' => count($benefits)
        ]);
    }

    /**
     * POST /api/v1/benefits/enrollments
     * Enroll employee in a benefit
     */
    public function enrollBenefit(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tenantId = $request->headers->get('X-Tenant-ID');

        // Validate required fields
        $required = ['employeeId', 'benefitId', 'employeeContribution', 'employerContribution', 'currency'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        // Create enrollment
        $enrollment = new BenefitEnrollment(
            'enr-' . uniqid(),
            $tenantId,
            $data['employeeId'],
            $data['benefitId'],
            $data['employeeContribution'],
            $data['employerContribution'],
            $data['currency']
        );

        // Auto-activate for immediate benefits
        $enrollment->activate(new \DateTime());

        // Publish event
        $this->publishEvent('benefit.enrolled', $enrollment->toArray());

        return new JsonResponse($enrollment->toArray(), 201);
    }

    /**
     * GET /api/v1/benefits/enrollments/{employeeId}
     * Get employee's benefit enrollments
     */
    public function getEmployeeEnrollments(string $employeeId, Request $request): JsonResponse
    {
        $tenantId = $request->headers->get('X-Tenant-ID');

        // Mock data
        $enrollments = [
            [
                'id' => 'enr-001',
                'employeeId' => $employeeId,
                'benefitId' => 'ben-001',
                'benefitName' => 'Health Insurance - Premium',
                'status' => 'ACTIVE',
                'employeeContribution' => 100.00,
                'employerContribution' => 500.00,
                'currency' => 'USD'
            ]
        ];

        return new JsonResponse([
            'data' => $enrollments,
            'total' => count($enrollments)
        ]);
    }

    /**
     * POST /api/v1/benefits/enrollments/{id}/cancel
     * Cancel benefit enrollment
     */
    public function cancelEnrollment(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $terminationDate = new \DateTime($data['terminationDate'] ?? 'now');

        // In production, fetch enrollment from database
        // $enrollment->cancel($terminationDate);

        $this->publishEvent('benefit.cancelled', [
            'enrollmentId' => $id,
            'terminationDate' => $terminationDate->format('Y-m-d')
        ]);

        return new JsonResponse([
            'message' => 'Enrollment cancelled successfully',
            'terminationDate' => $terminationDate->format('Y-m-d')
        ]);
    }

    private function publishEvent(string $eventType, array $data): void
    {
        // Publish to message broker (RabbitMQ/Kafka)
        // Implementation depends on shared EventPublisher
    }
}
