# Notification Service

## Overview
Microservice responsible for multi-channel notifications across the HR & Payroll system.

## Features
- Email notifications
- SMS notifications
- In-app notifications
- Push notifications (mobile)
- Notification templates
- Delivery tracking
- User notification preferences
- Bulk notifications

## API Endpoints

### Notifications
- `POST /api/v1/notifications/send` - Send notification
- `GET /api/v1/notifications/{userId}` - Get user notifications
- `PUT /api/v1/notifications/{id}/read` - Mark as read
- `POST /api/v1/notifications/preferences` - Set preferences
- `POST /api/v1/notifications/bulk` - Send bulk notifications

## Supported Channels
- EMAIL - Email notifications
- SMS - Text messages
- IN_APP - In-application notifications
- PUSH - Mobile push notifications

## Notification Categories
- PAYROLL - Payroll-related notifications
- LEAVE - Leave requests and approvals
- PERFORMANCE - Performance reviews and feedback
- ANNOUNCEMENTS - Company announcements
- SYSTEM - System alerts

## Events Consumed
All events from other services that require notifications:
- `payslip.generated`
- `leave.approved`
- `review.completed`
- `offer.generated`
- etc.

## Integration
- **Email**: SMTP/SendGrid/AWS SES
- **SMS**: Twilio/AWS SNS
- **Push**: Firebase Cloud Messaging (FCM)
- **In-App**: WebSocket/Server-Sent Events

## Database Schema
- `notifications` - Notification records
- `notification_preferences` - User preferences
- `notification_templates` - Message templates
- `delivery_logs` - Delivery tracking

## Running Locally
```bash
composer install
php -S localhost:8009
```

## Docker
```bash
docker build -t notification-service .
docker run -p 8009:9000 notification-service
```

## Environment Variables
```
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=user
SMTP_PASS=password
TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
FCM_SERVER_KEY=your_key
```
