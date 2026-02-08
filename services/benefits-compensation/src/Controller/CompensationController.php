<?php

namespace HRPayroll\BenefitsCompensation\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use HRPayroll\BenefitsCompensation\Entity\Bonus;

class CompensationController
{
    /**
     * POST /api/v1/compensation/bonuses
     * Create a bonus for an employee
     */
    public function createBonus(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tenantId = $request->headers->get('X-Tenant-ID');

        // Validate required fields
        $required = ['employeeId', 'type', 'amount', 'currency', 'reason'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        // Create bonus
        $bonus = new Bonus(
            'bon-' . uniqid(),
            $tenantId,
            $data['employeeId'],
            $data['type'],
            $data['amount'],
            $data['currency'],
            $data['reason']
        );

        // Publish event
        $this->publishEvent('bonus.created', $bonus->toArray());

        return new JsonResponse($bonus->toArray(), 201);
    }

    /**
     * PUT /api/v1/compensation/bonuses/{id}/approve
     * Approve a bonus
     */
    public function approveBonus(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $approvedBy = $data['approvedBy'] ?? 'system';

        // In production, fetch bonus from database
        // $bonus->approve($approvedBy);

        $this->publishEvent('bonus.approved', [
            'bonusId' => $id,
            'approvedBy' => $approvedBy,
            'approvedAt' => (new \DateTime())->format('c')
        ]);

        return new JsonResponse([
            'message' => 'Bonus approved successfully',
            'bonusId' => $id,
            'status' => 'APPROVED'
        ]);
    }

    /**
     * GET /api/v1/compensation/{employeeId}
     * Get total compensation for an employee
     */
    public function getTotalCompensation(string $employeeId, Request $request): JsonResponse
    {
        $tenantId = $request->headers->get('X-Tenant-ID');

        // Mock data - in production, aggregate from multiple sources
        $compensation = [
            'employeeId' => $employeeId,
            'baseSalary' => 80000.00,
            'bonuses' => [
                ['type' => 'ANNUAL', 'amount' => 10000.00, 'status' => 'PAID'],
                ['type' => 'PERFORMANCE', 'amount' => 5000.00, 'status' => 'APPROVED']
            ],
            'benefits' => [
                ['name' => 'Health Insurance', 'employerCost' => 6000.00],
                ['name' => '401(k) Match', 'employerCost' => 4800.00]
            ],
            'totalCash' => 95000.00,
            'totalBenefits' => 10800.00,
            'totalCompensation' => 105800.00,
            'currency' => 'USD'
        ];

        return new JsonResponse($compensation);
    }

    /**
     * GET /api/v1/compensation/bonuses
     * List all bonuses with filters
     */
    public function listBonuses(Request $request): JsonResponse
    {
        $tenantId = $request->headers->get('X-Tenant-ID');
        $status = $request->query->get('status');
        $employeeId = $request->query->get('employeeId');

        // Mock data
        $bonuses = [
            [
                'id' => 'bon-001',
                'employeeId' => 'emp-123',
                'type' => 'PERFORMANCE',
                'amount' => 5000.00,
                'currency' => 'USD',
                'status' => 'APPROVED',
                'reason' => 'Exceptional Q4 performance'
            ]
        ];

        return new JsonResponse([
            'data' => $bonuses,
            'total' => count($bonuses)
        ]);
    }

    private function publishEvent(string $eventType, array $data): void
    {
        // Publish to message broker
    }
}
