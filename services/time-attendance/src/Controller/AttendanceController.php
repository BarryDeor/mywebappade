<?php

namespace HRPayroll\TimeAttendance\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use HRPayroll\TimeAttendance\Entity\AttendanceRecord;

class AttendanceController
{
    /**
     * POST /api/v1/attendance/clock-in
     * Clock in
     */
    public function clockIn(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tenantId = $request->headers->get('X-Tenant-ID');

        $required = ['employeeId', 'timestamp'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $attendance = new AttendanceRecord(
            'att-' . uniqid(),
            $tenantId,
            $data['employeeId'],
            new \DateTime($data['timestamp']),
            $data['location'] ?? null,
            $data['deviceId'] ?? null
        );

        $this->publishEvent('attendance.clocked-in', $attendance->toArray());

        return new JsonResponse($attendance->toArray(), 201);
    }

    /**
     * POST /api/v1/attendance/clock-out
     * Clock out
     */
    public function clockOut(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $required = ['attendanceId', 'timestamp'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        // In production, fetch attendance record and update
        $this->publishEvent('attendance.clocked-out', [
            'attendanceId' => $data['attendanceId'],
            'clockOutTime' => $data['timestamp'],
            'location' => $data['location'] ?? null
        ]);

        return new JsonResponse([
            'attendanceId' => $data['attendanceId'],
            'status' => 'CLOCKED_OUT',
            'message' => 'Clocked out successfully'
        ]);
    }

    /**
     * GET /api/v1/attendance/{employeeId}
     * Get attendance records
     */
    public function getAttendance(string $employeeId, Request $request): JsonResponse
    {
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        // Mock data
        $records = [
            [
                'id' => 'att-001',
                'employeeId' => $employeeId,
                'date' => '2026-02-07',
                'clockInTime' => '2026-02-07T09:00:00Z',
                'clockOutTime' => '2026-02-07T18:00:00Z',
                'hoursWorked' => 8.0,
                'overtimeHours' => 0.0,
                'status' => 'CLOCKED_OUT'
            ],
            [
                'id' => 'att-002',
                'employeeId' => $employeeId,
                'date' => '2026-02-06',
                'clockInTime' => '2026-02-06T08:55:00Z',
                'clockOutTime' => '2026-02-06T19:00:00Z',
                'hoursWorked' => 9.08,
                'overtimeHours' => 1.08,
                'status' => 'CLOCKED_OUT'
            ]
        ];

        return new JsonResponse([
            'data' => $records,
            'total' => count($records),
            'summary' => [
                'totalHours' => 17.08,
                'totalOvertime' => 1.08,
                'daysPresent' => 2
            ]
        ]);
    }

    /**
     * GET /api/v1/shifts
     * Get shift schedules
     */
    public function getShifts(Request $request): JsonResponse
    {
        $employeeId = $request->query->get('employeeId');
        $date = $request->query->get('date', date('Y-m-d'));

        // Mock data
        $shifts = [
            [
                'id' => 'shift-001',
                'employeeId' => $employeeId,
                'date' => $date,
                'shiftType' => 'MORNING',
                'startTime' => '09:00',
                'endTime' => '18:00',
                'breakDuration' => 1.0
            ]
        ];

        return new JsonResponse([
            'data' => $shifts,
            'total' => count($shifts)
        ]);
    }

    /**
     * POST /api/v1/attendance/overtime
     * Record overtime
     */
    public function recordOvertime(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $required = ['employeeId', 'date', 'hours', 'reason'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $overtime = [
            'id' => 'ot-' . uniqid(),
            'employeeId' => $data['employeeId'],
            'date' => $data['date'],
            'hours' => $data['hours'],
            'reason' => $data['reason'],
            'status' => 'PENDING_APPROVAL',
            'createdAt' => (new \DateTime())->format('c')
        ];

        $this->publishEvent('overtime.recorded', $overtime);

        return new JsonResponse($overtime, 201);
    }

    private function publishEvent(string $eventType, array $data): void
    {
        // Publish to message broker
    }
}
