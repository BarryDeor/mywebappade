<?php

namespace HRPayroll\Notification\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    private MailerInterface $mailer;
    private array $config;

    public function __construct(MailerInterface $mailer, array $config = [])
    {
        $this->mailer = $mailer;
        $this->config = $config;
    }

    /**
     * Send email notification
     */
    public function sendEmail(string $to, string $subject, string $body, array $options = []): array
    {
        try {
            $email = (new Email())
                ->from($options['from'] ?? 'noreply@hrpayroll.com')
                ->to($to)
                ->subject($subject)
                ->html($body);

            if (isset($options['cc'])) {
                $email->cc(...(array)$options['cc']);
            }

            if (isset($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    $email->attachFromPath($attachment);
                }
            }

            $this->mailer->send($email);

            return [
                'status' => 'SENT',
                'channel' => 'EMAIL',
                'recipient' => $to,
                'sentAt' => (new \DateTime())->format('c')
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'FAILED',
                'channel' => 'EMAIL',
                'recipient' => $to,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS notification
     */
    public function sendSMS(string $to, string $message): array
    {
        try {
            // Integration with Twilio or similar SMS provider
            // For now, mock implementation
            
            return [
                'status' => 'SENT',
                'channel' => 'SMS',
                'recipient' => $to,
                'message' => $message,
                'sentAt' => (new \DateTime())->format('c')
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'FAILED',
                'channel' => 'SMS',
                'recipient' => $to,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send in-app notification
     */
    public function sendInApp(string $userId, string $title, string $message, array $data = []): array
    {
        $notification = [
            'id' => 'notif-' . uniqid(),
            'userId' => $userId,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'status' => 'UNREAD',
            'createdAt' => (new \DateTime())->format('c')
        ];

        // Store in database/cache
        // For now, return mock response

        return [
            'status' => 'SENT',
            'channel' => 'IN_APP',
            'notification' => $notification
        ];
    }

    /**
     * Send push notification (mobile)
     */
    public function sendPush(string $deviceToken, string $title, string $body, array $data = []): array
    {
        try {
            // Integration with FCM (Firebase Cloud Messaging) or APNs
            // For now, mock implementation

            return [
                'status' => 'SENT',
                'channel' => 'PUSH',
                'deviceToken' => $deviceToken,
                'title' => $title,
                'sentAt' => (new \DateTime())->format('c')
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'FAILED',
                'channel' => 'PUSH',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send multi-channel notification
     */
    public function sendMultiChannel(array $channels, array $recipients, string $subject, string $message, array $options = []): array
    {
        $results = [];

        foreach ($channels as $channel) {
            switch ($channel) {
                case 'EMAIL':
                    foreach ($recipients['email'] ?? [] as $email) {
                        $results[] = $this->sendEmail($email, $subject, $message, $options);
                    }
                    break;

                case 'SMS':
                    foreach ($recipients['phone'] ?? [] as $phone) {
                        $results[] = $this->sendSMS($phone, $message);
                    }
                    break;

                case 'IN_APP':
                    foreach ($recipients['userId'] ?? [] as $userId) {
                        $results[] = $this->sendInApp($userId, $subject, $message, $options['data'] ?? []);
                    }
                    break;

                case 'PUSH':
                    foreach ($recipients['deviceToken'] ?? [] as $token) {
                        $results[] = $this->sendPush($token, $subject, $message, $options['data'] ?? []);
                    }
                    break;
            }
        }

        return $results;
    }
}
