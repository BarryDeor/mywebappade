<?php

namespace HRPayroll\TimeAttendance\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use HRPayroll\TimeAttendance\Entity\LeaveRequest;

class LeaveController
{
    /**
     * POST /api/v1/leaves/request
     * Request leave
     */
    public function requestLeave(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tenantId = $request->headers->get('X-Tenant-ID');

        $required = ['employeeId', 'leaveType', 'startDate', 'endDate', 'daysRequested', 'reason'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $leaveRequest = new LeaveRequest(
            'leave-' . uniqid(),
            $tenantId,
            $data['employeeId'],
            $data['leaveType'],
            new \DateTime($data['startDate']),
            new \DateTime($data['endDate']),
            $data['daysRequested'],
            $data['reason']
        );

        $this->publishEvent('leave.requested', $leaveRequest->toArray());

        return new JsonResponse($leaveRequest->toArray(), 201);
    }

    /**
     * PUT /api/v1/leaves/{id}/approve
     * Approve leave request
     */
    public function approveLeave(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $approvedBy = $data['approvedBy'] ?? 'system';

        // In production, fetch leave request and approve
        $this->publishEvent('leave.approved', [
            'leaveId' => $id,
            'approvedBy' => $approvedBy,
            'approvedAt' => (new \DateTime())->format('c')
        ]);

        return new JsonResponse([
            'leaveId' => $id,
            'status' => 'APPROVED',
            'message' => 'Leave request approved'
        ]);
    }

    /**
     * PUT /api/v1/leaves/{id}/reject
     * Reject leave request
     */
    public function rejectLeave(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $reason = $data['reason'] ?? 'Not specified';

        $this->publishEvent('leave.rejected', [
            'leaveId' => $id,
            'reason' => $reason,
            'rejectedAt' => (new \DateTime())->format('c')
        ]);

        return new JsonResponse([
            'leaveId' => $id,
            'status' => 'REJECTED',
            'message' => 'Leave request rejected'
        ]);
    }

    /**
     * GET /api/v1/leaves/{employeeId}
     * Get employee leave requests
     */
    public function getLeaveRequests(string $employeeId, Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        $year = $request->query->get('year', date('Y'));

        // Mock data
        $leaves = [
            [
                'id' => 'leave-001',
                'employeeId' => $employeeId,
                'leaveType' => 'ANNUAL',
                'startDate' => '2026-02-15',
                'endDate' => '2026-02-17',
                'daysRequested' => 3,
                'status' => 'APPROVED',
                'reason' => 'Family vacation'
            ],
            [
                'id' => 'leave-002',
                'employeeId' => $employeeId,
                'leaveType' => 'SICK',
                'startDate' => '2026-01-20',
                'endDate' => '2026-01-21',
                'daysRequested' => 2,
                'status' => 'APPROVED',
                'reason' => 'Medical appointment'
            ]
        ];

        return new JsonResponse([
            'data' => $leaves,
            'total' => count($leaves)
        ]);
    }

    /**
     * GET /api/v1/leaves/{employeeId}/balance
     * Get leave balance
     */
    public function getLeaveBalance(string $employeeId, Request $request): JsonResponse
    {
        // Mock data
        $balance = [
            'employeeId' => $employeeId,
            'year' => date('Y'),
            'balances' => [
                [
                    'leaveType' => 'ANNUAL',
                    'allocated' => 20,
                    'used' => 5,
                    'pending' => 3,
                    'available' => 12
                ],
                [
                    'leaveType' => 'SICK',
                    'allocated' => 10,
                    'used' => 2,
                    'pending' => 0,
                    'available' => 8
                ],
                [
                    'leaveType' => 'PERSONAL',
                    'allocated' => 5,
                    'used' => 0,
                    'pending' => 0,
                    'available' => 5
                ]
            ],
            'totalAllocated' => 35,
            'totalUsed' => 7,
            'totalAvailable' => 25
        ];

        return new JsonResponse($balance);
    }

    private function publishEvent(string $eventType, array $data): void
    {
        // Publish to message broker
    }
}
