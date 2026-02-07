# Integration Guide - HR & Payroll Management System

## Overview

This guide provides instructions for integrating with the HR & Payroll Management System, including authentication, API usage, webhooks, and third-party integrations.

## Getting Started

### 1. Obtain API Credentials

Contact your system administrator to:
1. Create a tenant account
2. Generate API credentials
3. Assign appropriate roles and permissions

### 2. Authentication

#### OAuth 2.0 Flow

**Step 1: Login**
```bash
curl -X POST https://api.hrpayroll.example.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "your-password"
  }'
```

**Response**:
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

**Step 2: Use Access Token**
```bash
curl -X GET https://api.hrpayroll.example.com/api/v1/employees \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." \
  -H "Content-Type: application/json"
```

**Step 3: Refresh Token (when expired)**
```bash
curl -X POST https://api.hrpayroll.example.com/api/v1/auth/refresh \
  -H "Content-Type: application/json" \
  -d '{
    "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }'
```

## Common Integration Scenarios

### Scenario 1: Employee Onboarding

**Workflow**:
1. Create employee record
2. Create user account
3. Assign benefits
4. Set up leave balances
5. Send welcome notification

**Implementation**:

```javascript
// 1. Create employee
const employee = await fetch('https://api.hrpayroll.example.com/api/v1/employees', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${accessToken}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    tenantId: 'tenant-123',
    firstName: 'Jane',
    lastName: 'Smith',
    email: 'jane.smith@company.com',
    dateOfBirth: '1990-05-15',
    hireDate: '2026-02-01',
    departmentId: 'dept-456',
    jobTitle: 'Software Engineer',
    employmentType: 'FULL_TIME',
    location: {
      country: 'US',
      city: 'New York'
    }
  })
}).then(r => r.json());

// 2. Create user account
const user = await fetch('https://api.hrpayroll.example.com/api/v1/users', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${accessToken}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    tenantId: 'tenant-123',
    email: 'jane.smith@company.com',
    firstName: 'Jane',
    lastName: 'Smith',
    roles: ['EMPLOYEE'],
    sendWelcomeEmail: true
  })
}).then(r => r.json());

// 3. Enroll in default benefits
const enrollment = await fetch('https://api.hrpayroll.example.com/api/v1/benefits/enrollments', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${accessToken}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    employeeId: employee.id,
    benefitId: 'benefit-health-001',
    effectiveDate: '2026-02-01'
  })
}).then(r => r.json());

console.log('Employee onboarded successfully:', employee.employeeNumber);
```

### Scenario 2: Monthly Payroll Processing

**Workflow**:
1. Fetch active employees
2. Collect attendance data
3. Initiate payroll run
4. Monitor processing status
5. Approve payroll
6. Disburse payments

**Implementation**:

```python
import requests
import time

BASE_URL = 'https://api.hrpayroll.example.com'
headers = {'Authorization': f'Bearer {access_token}'}

# 1. Initiate payroll run
payroll_run = requests.post(
    f'{BASE_URL}/api/v1/payroll/runs',
    headers=headers,
    json={
        'tenantId': 'tenant-123',
        'payPeriod': {
            'startDate': '2026-02-01',
            'endDate': '2026-02-28'
        },
        'paymentDate': '2026-03-05',
        'country': 'US',
        'currency': 'USD'
    }
).json()

run_id = payroll_run['runId']
print(f'Payroll run initiated: {run_id}')

# 2. Monitor status
while True:
    status = requests.get(
        f'{BASE_URL}/api/v1/payroll/runs/{run_id}',
        headers=headers
    ).json()
    
    print(f'Status: {status["status"]}')
    
    if status['status'] == 'CALCULATED':
        print(f'Summary: {status["summary"]}')
        break
    elif status['status'] == 'FAILED':
        print('Payroll processing failed')
        break
    
    time.sleep(10)

# 3. Approve payroll
approval = requests.post(
    f'{BASE_URL}/api/v1/payroll/runs/{run_id}/approve',
    headers=headers
).json()

print(f'Payroll approved: {approval["approvedAt"]}')

# 4. Disburse payments
disbursement = requests.post(
    f'{BASE_URL}/api/v1/payroll/runs/{run_id}/disburse',
    headers=headers
).json()

print(f'Payments disbursed: {disbursement["paymentReference"]}')
```

### Scenario 3: Time Tracking Integration

**Mobile App Clock-In/Out**:

```swift
// iOS Swift Example
import Foundation

class AttendanceService {
    let baseURL = "https://api.hrpayroll.example.com"
    let accessToken: String
    
    init(accessToken: String) {
        self.accessToken = accessToken
    }
    
    func clockIn(employeeId: String, location: CLLocation) async throws {
        let url = URL(string: "\(baseURL)/api/v1/attendance/clock-in")!
        var request = URLRequest(url: url)
        request.httpMethod = "POST"
        request.setValue("Bearer \(accessToken)", forHTTPHeaderField: "Authorization")
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        
        let body: [String: Any] = [
            "employeeId": employeeId,
            "timestamp": ISO8601DateFormatter().string(from: Date()),
            "location": [
                "latitude": location.coordinate.latitude,
                "longitude": location.coordinate.longitude
            ],
            "deviceId": UIDevice.current.identifierForVendor?.uuidString ?? ""
        ]
        
        request.httpBody = try JSONSerialization.data(withJSONObject: body)
        
        let (data, response) = try await URLSession.shared.data(for: request)
        
        guard let httpResponse = response as? HTTPURLResponse,
              httpResponse.statusCode == 200 else {
            throw AttendanceError.clockInFailed
        }
        
        let result = try JSONDecoder().decode(ClockInResponse.self, from: data)
        print("Clocked in successfully: \(result.attendanceId)")
    }
}
```

## Webhook Integration

### Setting Up Webhooks

**1. Register Webhook Endpoint**:
```bash
curl -X POST https://api.hrpayroll.example.com/api/v1/webhooks \
  -H "Authorization: Bearer ${ACCESS_TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://your-app.com/webhooks/hrpayroll",
    "events": [
      "employee.created",
      "employee.terminated",
      "payroll.disbursed",
      "leave.approved"
    ],
    "secret": "your-webhook-secret"
  }'
```

**2. Handle Webhook Events**:

```javascript
// Node.js Express Example
const express = require('express');
const crypto = require('crypto');

const app = express();
app.use(express.json());

const WEBHOOK_SECRET = 'your-webhook-secret';

app.post('/webhooks/hrpayroll', (req, res) => {
  // Verify signature
  const signature = req.headers['x-hrpayroll-signature'];
  const payload = JSON.stringify(req.body);
  const expectedSignature = crypto
    .createHmac('sha256', WEBHOOK_SECRET)
    .update(payload)
    .digest('hex');
  
  if (signature !== expectedSignature) {
    return res.status(401).send('Invalid signature');
  }
  
  // Process event
  const event = req.body;
  
  switch (event.eventType) {
    case 'employee.created':
      handleEmployeeCreated(event.data);
      break;
    case 'payroll.disbursed':
      handlePayrollDisbursed(event.data);
      break;
    case 'leave.approved':
      handleLeaveApproved(event.data);
      break;
    default:
      console.log('Unknown event type:', event.eventType);
  }
  
  res.status(200).send('OK');
});

function handleEmployeeCreated(data) {
  console.log('New employee:', data.employeeId);
  // Send to your internal systems
  // Update your database
  // Trigger onboarding workflow
}

function handlePayrollDisbursed(data) {
  console.log('Payroll disbursed:', data.runId);
  // Update accounting system
  // Send notifications
}

function handleLeaveApproved(data) {
  console.log('Leave approved:', data.leaveId);
  // Update calendar
  // Notify team
}

app.listen(3000);
```

## Third-Party Integrations

### Accounting Systems (QuickBooks, Xero)

**Export Payroll to QuickBooks**:

```python
from quickbooks import QuickBooks
import requests

# Fetch payroll summary
payroll_summary = requests.get(
    'https://api.hrpayroll.example.com/api/v1/analytics/payroll-summary',
    headers={'Authorization': f'Bearer {access_token}'},
    params={'tenantId': 'tenant-123', 'year': 2026, 'month': 2}
).json()

# Connect to QuickBooks
qb = QuickBooks(
    client_id='your-qb-client-id',
    client_secret='your-qb-client-secret',
    access_token='qb-access-token'
)

# Create journal entry
journal_entry = qb.JournalEntry()
journal_entry.TxnDate = '2026-02-28'

# Debit: Salary Expense
journal_entry.Line.append({
    'DetailType': 'JournalEntryLineDetail',
    'Amount': payroll_summary['totalGrossPay'],
    'JournalEntryLineDetail': {
        'PostingType': 'Debit',
        'AccountRef': {'value': '5000'}  # Salary Expense Account
    }
})

# Credit: Cash
journal_entry.Line.append({
    'DetailType': 'JournalEntryLineDetail',
    'Amount': payroll_summary['totalNetPay'],
    'JournalEntryLineDetail': {
        'PostingType': 'Credit',
        'AccountRef': {'value': '1000'}  # Cash Account
    }
})

journal_entry.save()
print('Payroll exported to QuickBooks')
```

### Single Sign-On (SSO) Integration

**SAML 2.0 Configuration**:

```xml
<!-- Service Provider Metadata -->
<EntityDescriptor entityID="https://api.hrpayroll.example.com">
  <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    <AssertionConsumerService 
      Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
      Location="https://api.hrpayroll.example.com/api/v1/auth/saml/acs"
      index="0"/>
  </SPSSODescriptor>
</EntityDescriptor>
```

**Okta Integration**:
1. Add HR Payroll as SAML 2.0 application in Okta
2. Configure ACS URL: `https://api.hrpayroll.example.com/api/v1/auth/saml/acs`
3. Map attributes: email, firstName, lastName, roles
4. Enable provisioning (SCIM)

### Email Service Integration (SendGrid)

```javascript
// Configure notification service to use SendGrid
const sgMail = require('@sendgrid/mail');
sgMail.setApiKey(process.env.SENDGRID_API_KEY);

// Listen to notification events
messageQueue.subscribe('notification.email', async (message) => {
  const { recipient, subject, body, templateId, templateData } = message;
  
  const msg = {
    to: recipient,
    from: 'noreply@hrpayroll.example.com',
    subject: subject,
    templateId: templateId,
    dynamicTemplateData: templateData
  };
  
  try {
    await sgMail.send(msg);
    console.log('Email sent to:', recipient);
  } catch (error) {
    console.error('Email send failed:', error);
  }
});
```

## SDK Usage Examples

### JavaScript/TypeScript SDK

**Installation**:
```bash
npm install @hrpayroll/sdk
```

**Usage**:
```typescript
import { HRPayrollClient } from '@hrpayroll/sdk';

const client = new HRPayrollClient({
  baseUrl: 'https://api.hrpayroll.example.com',
  credentials: {
    email: 'user@example.com',
    password: 'password'
  }
});

// Auto-handles authentication and token refresh
await client.authenticate();

// Create employee
const employee = await client.employees.create({
  tenantId: 'tenant-123',
  firstName: 'John',
  lastName: 'Doe',
  email: 'john.doe@company.com',
  // ... other fields
});

// Get payslips
const payslips = await client.payroll.getPayslips('emp-001', {
  year: 2026,
  month: 2
});

// Request leave
const leave = await client.attendance.requestLeave({
  employeeId: 'emp-001',
  leaveType: 'ANNUAL',
  startDate: '2026-03-10',
  endDate: '2026-03-14',
  reason: 'Vacation'
});
```

### Python SDK

**Installation**:
```bash
pip install hrpayroll-sdk
```

**Usage**:
```python
from hrpayroll import HRPayrollClient

client = HRPayrollClient(
    base_url='https://api.hrpayroll.example.com',
    email='user@example.com',
    password='password'
)

# Auto-handles authentication
client.authenticate()

# Create employee
employee = client.employees.create(
    tenant_id='tenant-123',
    first_name='John',
    last_name='Doe',
    email='john.doe@company.com'
)

# Get payslips
payslips = client.payroll.get_payslips(
    employee_id='emp-001',
    year=2026,
    month=2
)

# Request leave
leave = client.attendance.request_leave(
    employee_id='emp-001',
    leave_type='ANNUAL',
    start_date='2026-03-10',
    end_date='2026-03-14',
    reason='Vacation'
)
```

## Error Handling Best Practices

### Retry Logic

```javascript
async function apiCallWithRetry(url, options, maxRetries = 3) {
  for (let i = 0; i < maxRetries; i++) {
    try {
      const response = await fetch(url, options);
      
      if (response.ok) {
        return await response.json();
      }
      
      // Don't retry client errors (4xx)
      if (response.status >= 400 && response.status < 500) {
        throw new Error(`Client error: ${response.status}`);
      }
      
      // Retry server errors (5xx)
      if (i < maxRetries - 1) {
        const delay = Math.pow(2, i) * 1000; // Exponential backoff
        await new Promise(resolve => setTimeout(resolve, delay));
        continue;
      }
      
      throw new Error(`Server error: ${response.status}`);
    } catch (error) {
      if (i === maxRetries - 1) throw error;
    }
  }
}
```

### Rate Limit Handling

```python
import time

def handle_rate_limit(response):
    if response.status_code == 429:
        retry_after = int(response.headers.get('Retry-After', 60))
        print(f'Rate limited. Waiting {retry_after} seconds...')
        time.sleep(retry_after)
        return True
    return False

def api_call_with_rate_limit(url, headers):
    while True:
        response = requests.get(url, headers=headers)
        
        if handle_rate_limit(response):
            continue
        
        return response.json()
```

## Testing

### Sandbox Environment

Use the sandbox environment for testing:

**Base URL**: `https://sandbox-api.hrpayroll.example.com`

**Test Credentials**:
- Email: `test@example.com`
- Password: `TestPassword123!`

**Test Data**:
- Tenant ID: `test-tenant-001`
- Employee ID: `test-emp-001`

### Postman Collection

Import our Postman collection for easy API testing:

```bash
curl -o hrpayroll-postman.json \
  https://api.hrpayroll.example.com/docs/postman-collection.json
```

## Support

### Resources
- **API Documentation**: https://docs.hrpayroll.example.com
- **Developer Portal**: https://developers.hrpayroll.example.com
- **Status Page**: https://status.hrpayroll.example.com
- **Changelog**: https://changelog.hrpayroll.example.com

### Contact
- **Email**: api-support@hrpayroll.example.com
- **Slack**: #api-support
- **GitHub**: https://github.com/hrpayroll/sdk

### SLA
- **Uptime**: 99.9%
- **Support Response**: < 4 hours (business hours)
- **Critical Issues**: < 1 hour
