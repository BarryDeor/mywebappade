# Time & Attendance Service

## Overview
Microservice responsible for time tracking, attendance management, and leave management.

## Features
- Clock-in/out with geolocation
- Shift management and scheduling
- Overtime calculation
- Leave types configuration
- Leave request and approval workflow
- Attendance reports
- Leave balance tracking
- Anomaly detection

## API Endpoints

### Attendance
- `POST /api/v1/attendance/clock-in` - Clock in
- `POST /api/v1/attendance/clock-out` - Clock out
- `GET /api/v1/attendance/{employeeId}` - Get attendance records
- `GET /api/v1/shifts` - Get shift schedules
- `POST /api/v1/attendance/overtime` - Record overtime

### Leave Management
- `POST /api/v1/leaves/request` - Request leave
- `PUT /api/v1/leaves/{id}/approve` - Approve leave
- `PUT /api/v1/leaves/{id}/reject` - Reject leave
- `GET /api/v1/leaves/{employeeId}` - Get leave requests
- `GET /api/v1/leaves/{employeeId}/balance` - Get leave balance

## Leave Types
- ANNUAL - Annual/vacation leave
- SICK - Sick leave
- MATERNITY - Maternity leave
- PATERNITY - Paternity leave
- PERSONAL - Personal leave
- UNPAID - Unpaid leave
- BEREAVEMENT - Bereavement leave
- COMPENSATORY - Compensatory time off

## Shift Types
- MORNING - Morning shift (e.g., 9 AM - 6 PM)
- EVENING - Evening shift (e.g., 2 PM - 11 PM)
- NIGHT - Night shift (e.g., 10 PM - 7 AM)
- FLEXIBLE - Flexible hours

## Events Published
- `attendance.clocked-in`
- `attendance.clocked-out`
- `leave.requested`
- `leave.approved`
- `leave.rejected`
- `overtime.recorded`

## Database Schema
- `attendance_records` - Clock-in/out records
- `shifts` - Shift schedules
- `leave_requests` - Leave applications
- `leave_balances` - Employee leave balances
- `overtime_records` - Overtime tracking

## Features
- **Geolocation**: Track clock-in/out locations
- **Real-time**: Redis for real-time attendance tracking
- **Anomalies**: Detect unusual patterns (late arrivals, early departures)
- **Integration**: Integrates with Payroll for overtime pay

## Running Locally
```bash
composer install
php -S localhost:8010
```

## Docker
```bash
docker build -t time-attendance-service .
docker run -p 8010:9000 time-attendance-service
```

## Environment Variables
```
DB_HOST=localhost
DB_PORT=5432
DB_NAME=time_attendance
REDIS_HOST=localhost
REDIS_PORT=6379
```
