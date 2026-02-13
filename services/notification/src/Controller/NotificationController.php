<?php

namespace HRPayroll\Notification\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use HRPayroll\Notification\Service\NotificationService;

class NotificationController
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * POST /api/v1/notifications/send
     * Send notification
     */
    public function sendNotification(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $required = ['channel', 'recipient', 'subject', 'message'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $result = match($data['channel']) {
            'EMAIL' => $this->notificationService->sendEmail(
                $data['recipient'],
                $data['subject'],
                $data['message'],
                $data['options'] ?? []
            ),
            'SMS' => $this->notificationService->sendSMS(
                $data['recipient'],
                $data['message']
            ),
            'IN_APP' => $this->notificationService->sendInApp(
                $data['recipient'],
                $data['subject'],
                $data['message'],
                $data['data'] ?? []
            ),
            'PUSH' => $this->notificationService->sendPush(
                $data['recipient'],
                $data['subject'],
                $data['message'],
                $data['data'] ?? []
            ),
            default => ['status' => 'FAILED', 'error' => 'Invalid channel']
        };

        return new JsonResponse($result, $result['status'] === 'SENT' ? 200 : 400);
    }

    /**
     * GET /api/v1/notifications/{userId}
     * Get user notifications
     */
    public function getUserNotifications(string $userId, Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        $limit = $request->query->get('limit', 50);

        // Mock data
        $notifications = [
            [
                'id' => 'notif-001',
                'userId' => $userId,
                'title' => 'Payslip Available',
                'message' => 'Your payslip for February 2026 is now available',
                'status' => 'UNREAD',
                'createdAt' => '2026-02-07T10:00:00Z'
            ],
            [
                'id' => 'notif-002',
                'userId' => $userId,
                'title' => 'Leave Request Approved',
                'message' => 'Your leave request for Feb 15-17 has been approved',
                'status' => 'READ',
                'createdAt' => '2026-02-06T14:30:00Z'
            ]
        ];

        return new JsonResponse([
            'data' => $notifications,
            'total' => count($notifications),
            'unreadCount' => 1
        ]);
    }

    /**
     * PUT /api/v1/notifications/{id}/read
     * Mark notification as read
     */
    public function markAsRead(string $id, Request $request): JsonResponse
    {
        // In production, update database
        return new JsonResponse([
            'notificationId' => $id,
            'status' => 'READ',
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * POST /api/v1/notifications/preferences
     * Set notification preferences
     */
    public function setPreferences(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['userId'] ?? null;

        if (!$userId) {
            return new JsonResponse(['error' => 'Missing userId'], 400);
        }

        $preferences = [
            'userId' => $userId,
            'channels' => $data['channels'] ?? ['EMAIL', 'IN_APP'],
            'categories' => [
                'PAYROLL' => $data['payroll'] ?? true,
                'LEAVE' => $data['leave'] ?? true,
                'PERFORMANCE' => $data['performance'] ?? true,
                'ANNOUNCEMENTS' => $data['announcements'] ?? true
            ],
            'quietHours' => $data['quietHours'] ?? null,
            'updatedAt' => (new \DateTime())->format('c')
        ];

        return new JsonResponse($preferences);
    }

    /**
     * POST /api/v1/notifications/bulk
     * Send bulk notifications
     */
    public function sendBulk(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $required = ['recipients', 'subject', 'message'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $channels = $data['channels'] ?? ['EMAIL'];
        $results = $this->notificationService->sendMultiChannel(
            $channels,
            $data['recipients'],
            $data['subject'],
            $data['message'],
            $data['options'] ?? []
        );

        $summary = [
            'total' => count($results),
            'sent' => count(array_filter($results, fn($r) => $r['status'] === 'SENT')),
            'failed' => count(array_filter($results, fn($r) => $r['status'] === 'FAILED')),
            'results' => $results
        ];

        return new JsonResponse($summary);
    }
}
