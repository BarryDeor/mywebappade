# HR & Payroll Management System - API Documentation

## Overview

This document provides comprehensive API documentation for all microservices in the HR & Payroll Management System.

**Base URL**: `https://api.hrpayroll.example.com`

**API Version**: v1

**Authentication**: OAuth 2.0 / JWT Bearer Token

## Authentication

### Login
Authenticate user and receive access token.

**Endpoint**: `POST /api/v1/auth/login`

**Request**:
```json
{
  "email": "user@example.com",
  "password": "securePassword123"
}
```

**Response**: `200 OK`
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "Bearer",
  "expires_in": 3600,
  "user": {
    "id": "user-123",
    "email": "user@example.com",
    "firstName": "John",
    "lastName": "Doe",
    "roles": ["EMPLOYEE"],
    "tenantId": "tenant-456"
  }
}
```

### Refresh Token
Obtain new access token using refresh token.

**Endpoint**: `POST /api/v1/auth/refresh`

**Request**:
```json
{
  "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Response**: `200 OK`
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

### Logout
Invalidate current session.

**Endpoint**: `POST /api/v1/auth/logout`

**Headers**: `Authorization: Bearer {access_token}`

**Response**: `200 OK`
```json
{
  "message": "Logged out successfully"
}
```

## Employee Management Service

### Create Employee
Create a new employee record.

**Endpoint**: `POST /api/v1/employees`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `employees:write`

**Request**:
```json
{
  "tenantId": "tenant-123",
  "firstName": "Jane",
  "lastName": "Smith",
  "email": "jane.smith@company.com",
  "phone": "+1-555-0123",
  "dateOfBirth": "1990-05-15",
  "hireDate": "2026-02-01",
  "departmentId": "dept-456",
  "jobTitle": "Software Engineer",
  "employmentType": "FULL_TIME",
  "location": {
    "country": "US",
    "city": "New York",
    "address": "123 Main St",
    "postalCode": "10001"
  },
  "managerId": "emp-789"
}
```

**Response**: `201 Created`
```json
{
  "id": "emp-001",
  "employeeNumber": "EMP001234",
  "status": "ACTIVE",
  "createdAt": "2026-02-07T10:00:00Z"
}
```

### Get Employee
Retrieve employee details by ID.

**Endpoint**: `GET /api/v1/employees/{id}`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `employees:read`

**Response**: `200 OK`
```json
{
  "id": "emp-001",
  "tenantId": "tenant-123",
  "employeeNumber": "EMP001234",
  "firstName": "Jane",
  "lastName": "Smith",
  "email": "jane.smith@company.com",
  "phone": "+1-555-0123",
  "dateOfBirth": "1990-05-15",
  "hireDate": "2026-02-01",
  "terminationDate": null,
  "departmentId": "dept-456",
  "jobTitle": "Software Engineer",
  "employmentType": "FULL_TIME",
  "status": "ACTIVE",
  "location": {
    "country": "US",
    "city": "New York",
    "address": "123 Main St",
    "postalCode": "10001"
  },
  "managerId": "emp-789",
  "metadata": {},
  "createdAt": "2026-02-07T10:00:00Z",
  "updatedAt": "2026-02-07T10:00:00Z"
}
```

### List Employees
Retrieve list of employees with pagination.

**Endpoint**: `GET /api/v1/employees`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `employees:read`

**Query Parameters**:
- `tenantId` (required): Tenant identifier
- `limit` (optional, default: 100): Number of records per page
- `offset` (optional, default: 0): Pagination offset
- `departmentId` (optional): Filter by department
- `status` (optional): Filter by status (ACTIVE, INACTIVE, TERMINATED)

**Example**: `GET /api/v1/employees?tenantId=tenant-123&limit=50&offset=0&status=ACTIVE`

**Response**: `200 OK`
```json
{
  "data": [
    {
      "id": "emp-001",
      "employeeNumber": "EMP001234",
      "firstName": "Jane",
      "lastName": "Smith",
      "email": "jane.smith@company.com",
      "jobTitle": "Software Engineer",
      "departmentId": "dept-456",
      "status": "ACTIVE"
    }
  ],
  "meta": {
    "limit": 50,
    "offset": 0,
    "count": 1
  }
}
```

### Update Employee
Update employee information.

**Endpoint**: `PUT /api/v1/employees/{id}`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `employees:update`

**Request**:
```json
{
  "phone": "+1-555-9999",
  "jobTitle": "Senior Software Engineer",
  "departmentId": "dept-789"
}
```

**Response**: `200 OK`
```json
{
  "id": "emp-001",
  "employeeNumber": "EMP001234",
  "firstName": "Jane",
  "lastName": "Smith",
  "jobTitle": "Senior Software Engineer",
  "updatedAt": "2026-02-07T11:00:00Z"
}
```

### Terminate Employee
Terminate an employee.

**Endpoint**: `POST /api/v1/employees/{id}/terminate`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `employees:terminate`

**Request**:
```json
{
  "terminationDate": "2026-03-01"
}
```

**Response**: `200 OK`
```json
{
  "id": "emp-001",
  "status": "TERMINATED",
  "terminationDate": "2026-03-01"
}
```

## Payroll Processing Service

### Initiate Payroll Run
Start a new payroll processing run.

**Endpoint**: `POST /api/v1/payroll/runs`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `payroll:process`

**Request**:
```json
{
  "tenantId": "tenant-123",
  "payPeriod": {
    "startDate": "2026-02-01",
    "endDate": "2026-02-28"
  },
  "paymentDate": "2026-03-05",
  "country": "US",
  "currency": "USD",
  "employeeFilter": {
    "departments": ["dept-456"],
    "employmentTypes": ["FULL_TIME", "PART_TIME"]
  }
}
```

**Response**: `202 Accepted`
```json
{
  "runId": "payroll-run-999",
  "status": "PROCESSING",
  "estimatedCompletion": "2026-02-07T11:00:00Z"
}
```

### Get Payroll Run Status
Check status of a payroll run.

**Endpoint**: `GET /api/v1/payroll/runs/{runId}`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `payroll:read`

**Response**: `200 OK`
```json
{
  "id": "payroll-run-999",
  "tenantId": "tenant-123",
  "periodStart": "2026-02-01",
  "periodEnd": "2026-02-28",
  "paymentDate": "2026-03-05",
  "country": "US",
  "currency": "USD",
  "status": "CALCULATED",
  "summary": {
    "totalEmployees": 150,
    "totalGrossPay": 750000.00,
    "totalDeductions": 225000.00,
    "totalNetPay": 525000.00
  },
  "createdAt": "2026-02-07T10:00:00Z",
  "updatedAt": "2026-02-07T10:30:00Z"
}
```

### Approve Payroll Run
Approve a calculated payroll run.

**Endpoint**: `POST /api/v1/payroll/runs/{runId}/approve`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `payroll:approve`

**Response**: `200 OK`
```json
{
  "runId": "payroll-run-999",
  "status": "APPROVED",
  "approvedBy": "user-123",
  "approvedAt": "2026-02-07T12:00:00Z"
}
```

### Disburse Payroll
Process payment disbursement.

**Endpoint**: `POST /api/v1/payroll/runs/{runId}/disburse`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `payroll:disburse`

**Response**: `200 OK`
```json
{
  "runId": "payroll-run-999",
  "status": "DISBURSED",
  "disbursedAt": "2026-02-07T13:00:00Z",
  "paymentReference": "BATCH-20260207-001"
}
```

### Get Employee Payslips
Retrieve payslips for an employee.

**Endpoint**: `GET /api/v1/payroll/payslips/{employeeId}`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `payroll:read` or `payroll:read-self`

**Query Parameters**:
- `year` (optional): Filter by year
- `month` (optional): Filter by month

**Response**: `200 OK`
```json
{
  "data": [
    {
      "id": "payslip-001",
      "payrollRunId": "payroll-run-999",
      "employeeId": "emp-001",
      "employeeNumber": "EMP001234",
      "employeeName": "Jane Smith",
      "periodStart": "2026-02-01",
      "periodEnd": "2026-02-28",
      "paymentDate": "2026-03-05",
      "currency": "USD",
      "basicSalary": 5000.00,
      "allowances": {
        "housing": 1000.00,
        "transport": 500.00,
        "overtime": 200.00
      },
      "grossPay": 6700.00,
      "deductions": {
        "incomeTax": 1340.00,
        "socialSecurity": 335.00,
        "healthInsurance": 200.00
      },
      "totalDeductions": 1875.00,
      "netPay": 4825.00,
      "metadata": {
        "workingDays": 20,
        "presentDays": 20,
        "overtimeHours": 10
      },
      "createdAt": "2026-02-07T10:30:00Z"
    }
  ]
}
```

## Time & Attendance Service

### Clock In
Record employee clock-in.

**Endpoint**: `POST /api/v1/attendance/clock-in`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `time-attendance:write-self`

**Request**:
```json
{
  "employeeId": "emp-001",
  "timestamp": "2026-02-07T09:00:00Z",
  "location": {
    "latitude": 40.7128,
    "longitude": -74.0060
  },
  "deviceId": "mobile-device-123"
}
```

**Response**: `200 OK`
```json
{
  "attendanceId": "att-555",
  "status": "CLOCKED_IN",
  "shift": "MORNING_SHIFT",
  "clockInTime": "2026-02-07T09:00:00Z"
}
```

### Clock Out
Record employee clock-out.

**Endpoint**: `POST /api/v1/attendance/clock-out`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `time-attendance:write-self`

**Request**:
```json
{
  "employeeId": "emp-001",
  "timestamp": "2026-02-07T17:30:00Z",
  "location": {
    "latitude": 40.7128,
    "longitude": -74.0060
  }
}
```

**Response**: `200 OK`
```json
{
  "attendanceId": "att-555",
  "status": "CLOCKED_OUT",
  "clockInTime": "2026-02-07T09:00:00Z",
  "clockOutTime": "2026-02-07T17:30:00Z",
  "totalHours": 8.5,
  "overtimeHours": 0.5
}
```

### Request Leave
Submit a leave request.

**Endpoint**: `POST /api/v1/leaves/request`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `time-attendance:write-self`

**Request**:
```json
{
  "employeeId": "emp-001",
  "leaveType": "ANNUAL",
  "startDate": "2026-03-10",
  "endDate": "2026-03-14",
  "reason": "Family vacation",
  "halfDay": false
}
```

**Response**: `201 Created`
```json
{
  "leaveId": "leave-777",
  "status": "PENDING",
  "totalDays": 5,
  "remainingBalance": 10,
  "createdAt": "2026-02-07T10:00:00Z"
}
```

### Approve/Reject Leave
Approve or reject a leave request.

**Endpoint**: `PUT /api/v1/leaves/{leaveId}/approve`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `time-attendance:approve`

**Request**:
```json
{
  "action": "APPROVE",
  "comments": "Approved for vacation"
}
```

**Response**: `200 OK`
```json
{
  "leaveId": "leave-777",
  "status": "APPROVED",
  "approvedBy": "manager-123",
  "approvedAt": "2026-02-07T11:00:00Z",
  "comments": "Approved for vacation"
}
```

### Get Attendance Records
Retrieve attendance records for an employee.

**Endpoint**: `GET /api/v1/attendance/{employeeId}`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `time-attendance:read` or `time-attendance:read-self`

**Query Parameters**:
- `startDate` (required): Start date (YYYY-MM-DD)
- `endDate` (required): End date (YYYY-MM-DD)

**Response**: `200 OK`
```json
{
  "data": [
    {
      "date": "2026-02-07",
      "clockIn": "2026-02-07T09:00:00Z",
      "clockOut": "2026-02-07T17:30:00Z",
      "totalHours": 8.5,
      "overtimeHours": 0.5,
      "status": "PRESENT"
    }
  ],
  "summary": {
    "totalDays": 20,
    "presentDays": 18,
    "absentDays": 2,
    "totalHours": 153.0,
    "overtimeHours": 10.0
  }
}
```

## Benefits & Compensation Service

### Get Benefits Catalog
Retrieve available benefits.

**Endpoint**: `GET /api/v1/benefits/catalog`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `benefits:read`

**Response**: `200 OK`
```json
{
  "data": [
    {
      "id": "benefit-001",
      "name": "Health Insurance",
      "category": "INSURANCE",
      "description": "Comprehensive health coverage",
      "employerContribution": 500.00,
      "employeeContribution": 200.00,
      "currency": "USD",
      "eligibility": {
        "employmentTypes": ["FULL_TIME"],
        "minimumTenure": 90
      }
    }
  ]
}
```

### Enroll in Benefit
Enroll employee in a benefit plan.

**Endpoint**: `POST /api/v1/benefits/enrollments`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `benefits:enroll`

**Request**:
```json
{
  "employeeId": "emp-001",
  "benefitId": "benefit-001",
  "effectiveDate": "2026-03-01",
  "dependents": [
    {
      "name": "John Smith Jr.",
      "relationship": "CHILD",
      "dateOfBirth": "2015-06-20"
    }
  ]
}
```

**Response**: `201 Created`
```json
{
  "enrollmentId": "enrollment-888",
  "status": "ACTIVE",
  "effectiveDate": "2026-03-01",
  "monthlyContribution": 200.00
}
```

## Reporting & Analytics Service

### Get HR Dashboard
Retrieve HR dashboard data.

**Endpoint**: `GET /api/v1/reports/dashboards/hr`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `reports:view`

**Query Parameters**:
- `tenantId` (required): Tenant identifier
- `period` (optional): Time period (MONTH, QUARTER, YEAR)

**Response**: `200 OK`
```json
{
  "headcount": {
    "total": 500,
    "active": 480,
    "onLeave": 15,
    "terminated": 5
  },
  "demographics": {
    "byDepartment": {
      "Engineering": 200,
      "Sales": 150,
      "Operations": 100,
      "HR": 50
    },
    "byEmploymentType": {
      "FULL_TIME": 450,
      "PART_TIME": 30,
      "CONTRACT": 20
    }
  },
  "attrition": {
    "rate": 8.5,
    "voluntary": 6.2,
    "involuntary": 2.3
  },
  "hiring": {
    "newHires": 25,
    "openPositions": 15
  }
}
```

### Get Payroll Summary
Retrieve payroll summary report.

**Endpoint**: `GET /api/v1/analytics/payroll-summary`

**Headers**: `Authorization: Bearer {access_token}`

**Required Permission**: `reports:view`

**Query Parameters**:
- `tenantId` (required): Tenant identifier
- `year` (required): Year
- `month` (optional): Month (1-12)

**Response**: `200 OK`
```json
{
  "period": "2026-02",
  "totalEmployees": 480,
  "totalGrossPay": 2400000.00,
  "totalDeductions": 720000.00,
  "totalNetPay": 1680000.00,
  "currency": "USD",
  "breakdown": {
    "basicSalary": 2000000.00,
    "allowances": 400000.00,
    "overtime": 100000.00,
    "bonuses": 50000.00
  },
  "deductionBreakdown": {
    "incomeTax": 480000.00,
    "socialSecurity": 120000.00,
    "healthInsurance": 96000.00,
    "other": 24000.00
  }
}
```

## Error Responses

All endpoints may return the following error responses:

### 400 Bad Request
```json
{
  "error": "Invalid request parameters",
  "details": {
    "field": "email",
    "message": "Invalid email format"
  }
}
```

### 401 Unauthorized
```json
{
  "error": "Authentication required",
  "message": "Missing or invalid access token"
}
```

### 403 Forbidden
```json
{
  "error": "Insufficient permissions",
  "message": "User does not have required permission: employees:write"
}
```

### 404 Not Found
```json
{
  "error": "Resource not found",
  "message": "Employee with ID emp-999 not found"
}
```

### 500 Internal Server Error
```json
{
  "error": "Internal server error",
  "message": "An unexpected error occurred",
  "requestId": "req-12345"
}
```

## Rate Limiting

API requests are rate-limited per user/tenant:

- **Standard endpoints**: 100 requests/minute
- **Payroll endpoints**: 50 requests/minute
- **Attendance endpoints**: 200 requests/minute

Rate limit headers:
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1675776000
```

## Pagination

List endpoints support pagination:

**Query Parameters**:
- `limit`: Number of records per page (default: 100, max: 1000)
- `offset`: Number of records to skip (default: 0)

**Response**:
```json
{
  "data": [...],
  "meta": {
    "limit": 100,
    "offset": 0,
    "count": 50,
    "total": 500
  }
}
```

## Webhooks

Subscribe to events via webhooks:

**Event Types**:
- `employee.created`
- `employee.updated`
- `employee.terminated`
- `payroll.calculated`
- `payroll.disbursed`
- `leave.approved`
- `attendance.anomaly`

**Webhook Payload**:
```json
{
  "eventId": "evt-12345",
  "eventType": "employee.created",
  "timestamp": "2026-02-07T10:00:00Z",
  "tenantId": "tenant-123",
  "data": {
    "employeeId": "emp-001",
    "employeeNumber": "EMP001234"
  }
}
```

## SDK Examples

### JavaScript/TypeScript
```typescript
import { HRPayrollClient } from '@hrpayroll/sdk';

const client = new HRPayrollClient({
  baseUrl: 'https://api.hrpayroll.example.com',
  accessToken: 'your-access-token'
});

// Create employee
const employee = await client.employees.create({
  tenantId: 'tenant-123',
  firstName: 'Jane',
  lastName: 'Smith',
  email: 'jane.smith@company.com',
  // ... other fields
});

// Get payslips
const payslips = await client.payroll.getPayslips('emp-001', {
  year: 2026,
  month: 2
});
```

### Python
```python
from hrpayroll import HRPayrollClient

client = HRPayrollClient(
    base_url='https://api.hrpayroll.example.com',
    access_token='your-access-token'
)

# Create employee
employee = client.employees.create(
    tenant_id='tenant-123',
    first_name='Jane',
    last_name='Smith',
    email='jane.smith@company.com'
)

# Get payslips
payslips = client.payroll.get_payslips(
    employee_id='emp-001',
    year=2026,
    month=2
)
```

## Support

For API support:
- **Documentation**: https://docs.hrpayroll.example.com
- **Email**: api-support@hrpayroll.example.com
- **Status Page**: https://status.hrpayroll.example.com
