<?php

namespace HRPayroll\PerformanceManagement\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use HRPayroll\PerformanceManagement\Entity\Goal;

class GoalsController
{
    /**
     * POST /api/v1/performance/goals
     * Create a new goal
     */
    public function createGoal(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tenantId = $request->headers->get('X-Tenant-ID');

        $required = ['employeeId', 'title', 'type', 'category'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $goal = new Goal(
            'goal-' . uniqid(),
            $tenantId,
            $data['employeeId'],
            $data['title'],
            $data['type'],
            $data['category']
        );

        if (isset($data['startDate']) && isset($data['dueDate'])) {
            $goal->activate(
                new \DateTime($data['startDate']),
                new \DateTime($data['dueDate'])
            );
        }

        if (isset($data['keyResults'])) {
            foreach ($data['keyResults'] as $kr) {
                $goal->addKeyResult($kr);
            }
        }

        $this->publishEvent('goal.created', $goal->toArray());

        return new JsonResponse($goal->toArray(), 201);
    }

    /**
     * PUT /api/v1/performance/goals/{id}/progress
     * Update goal progress
     */
    public function updateProgress(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $progress = $data['progress'] ?? 0;

        // In production, fetch goal from database and update
        $this->publishEvent('goal.progress-updated', [
            'goalId' => $id,
            'progress' => $progress,
            'updatedAt' => (new \DateTime())->format('c')
        ]);

        return new JsonResponse([
            'goalId' => $id,
            'progress' => $progress,
            'message' => 'Progress updated successfully'
        ]);
    }

    /**
     * GET /api/v1/performance/goals/{employeeId}
     * Get employee goals
     */
    public function getEmployeeGoals(string $employeeId, Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        $category = $request->query->get('category');

        // Mock data
        $goals = [
            [
                'id' => 'goal-001',
                'employeeId' => $employeeId,
                'title' => 'Increase sales by 20%',
                'type' => 'INDIVIDUAL',
                'category' => 'KPI',
                'status' => 'ACTIVE',
                'progress' => 65,
                'dueDate' => '2026-12-31'
            ],
            [
                'id' => 'goal-002',
                'employeeId' => $employeeId,
                'title' => 'Complete leadership training',
                'type' => 'INDIVIDUAL',
                'category' => 'DEVELOPMENT',
                'status' => 'ACTIVE',
                'progress' => 40,
                'dueDate' => '2026-06-30'
            ]
        ];

        return new JsonResponse([
            'data' => $goals,
            'total' => count($goals)
        ]);
    }

    /**
     * GET /api/v1/performance/goals/{employeeId}/kpis
     * Get employee KPIs
     */
    public function getKPIs(string $employeeId, Request $request): JsonResponse
    {
        // Mock KPI data
        $kpis = [
            [
                'name' => 'Sales Target Achievement',
                'current' => 850000,
                'target' => 1000000,
                'unit' => 'USD',
                'progress' => 85,
                'trend' => 'UP'
            ],
            [
                'name' => 'Customer Satisfaction Score',
                'current' => 4.5,
                'target' => 4.8,
                'unit' => 'RATING',
                'progress' => 94,
                'trend' => 'STABLE'
            ],
            [
                'name' => 'Project Delivery On-Time',
                'current' => 90,
                'target' => 95,
                'unit' => 'PERCENTAGE',
                'progress' => 95,
                'trend' => 'UP'
            ]
        ];

        return new JsonResponse([
            'employeeId' => $employeeId,
            'kpis' => $kpis,
            'overallPerformance' => 91.3
        ]);
    }

    private function publishEvent(string $eventType, array $data): void
    {
        // Publish to message broker
    }
}
