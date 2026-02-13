<?php

namespace HRPayroll\RecruitmentOnboarding\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class OnboardingController
{
    /**
     * POST /api/v1/onboarding/{employeeId}/tasks
     * Assign onboarding tasks
     */
    public function assignTasks(string $employeeId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tenantId = $request->headers->get('X-Tenant-ID');

        $defaultTasks = [
            ['name' => 'Complete I-9 Form', 'category' => 'COMPLIANCE', 'dueInDays' => 3],
            ['name' => 'Setup Workstation', 'category' => 'IT', 'dueInDays' => 1],
            ['name' => 'Benefits Enrollment', 'category' => 'HR', 'dueInDays' => 7],
            ['name' => 'Security Training', 'category' => 'TRAINING', 'dueInDays' => 5],
            ['name' => 'Meet Team Members', 'category' => 'SOCIAL', 'dueInDays' => 7]
        ];

        $tasks = $data['tasks'] ?? $defaultTasks;
        $onboarding = [
            'id' => 'onb-' . uniqid(),
            'employeeId' => $employeeId,
            'status' => 'IN_PROGRESS',
            'tasks' => array_map(function($task) {
                return [
                    'id' => 'task-' . uniqid(),
                    'name' => $task['name'],
                    'category' => $task['category'],
                    'status' => 'PENDING',
                    'dueDate' => (new \DateTime("+{$task['dueInDays']} days"))->format('Y-m-d')
                ];
            }, $tasks),
            'startedAt' => (new \DateTime())->format('c')
        ];

        $this->publishEvent('onboarding.started', $onboarding);

        return new JsonResponse($onboarding, 201);
    }

    /**
     * PUT /api/v1/onboarding/tasks/{taskId}/complete
     * Mark task as complete
     */
    public function completeTask(string $taskId, Request $request): JsonResponse
    {
        $this->publishEvent('onboarding.task-completed', [
            'taskId' => $taskId,
            'completedAt' => (new \DateTime())->format('c')
        ]);

        return new JsonResponse([
            'taskId' => $taskId,
            'status' => 'COMPLETED',
            'message' => 'Task completed successfully'
        ]);
    }

    /**
     * GET /api/v1/onboarding/{employeeId}
     * Get onboarding status
     */
    public function getOnboardingStatus(string $employeeId, Request $request): JsonResponse
    {
        // Mock data
        $onboarding = [
            'employeeId' => $employeeId,
            'status' => 'IN_PROGRESS',
            'progress' => 60,
            'tasksCompleted' => 3,
            'tasksTotal' => 5,
            'tasks' => [
                ['name' => 'Complete I-9 Form', 'status' => 'COMPLETED'],
                ['name' => 'Setup Workstation', 'status' => 'COMPLETED'],
                ['name' => 'Benefits Enrollment', 'status' => 'COMPLETED'],
                ['name' => 'Security Training', 'status' => 'IN_PROGRESS'],
                ['name' => 'Meet Team Members', 'status' => 'PENDING']
            ]
        ];

        return new JsonResponse($onboarding);
    }

    private function publishEvent(string $eventType, array $data): void
    {
        // Publish to message broker
    }
}
