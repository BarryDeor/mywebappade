# Payroll Processing Service

## Overview
The Payroll Processing Service is a critical microservice responsible for calculating salaries, managing payroll runs, generating payslips, and handling multi-country payroll compliance within the HR & Payroll Management System.

## Features

### Payroll Calculation
- Salary structure management (base, allowances, deductions)
- Multi-currency support
- Country-specific tax calculations
- Statutory deductions (social security, pension, insurance)
- Overtime and bonus calculations
- Proration for partial months

### Payroll Runs
- Scheduled payroll processing
- Ad-hoc payroll runs
- Payroll approval workflows
- Payroll corrections and adjustments
- Historical payroll data

### Payslip Generation
- Automated payslip generation
- PDF export
- Email distribution
- Employee self-service access
- Multi-language support

### Compliance
- Tax compliance reporting
- Statutory filing support
- Audit trails
- Year-end processing (W2, 1099, etc.)

## Technology Stack
- **Language**: PHP 8.2+
- **Framework**: Symfony Components
- **Database**: PostgreSQL
- **ORM**: Doctrine
- **Message Broker**: RabbitMQ (AMQP)
- **Money Handling**: brick/money
- **Containerization**: Docker

## API Endpoints

### Payroll Runs
- `POST /api/v1/payroll/runs` - Create new payroll run
- `GET /api/v1/payroll/runs` - List payroll runs
- `GET /api/v1/payroll/runs/{id}` - Get payroll run details
- `POST /api/v1/payroll/runs/{id}/process` - Process payroll run
- `POST /api/v1/payroll/runs/{id}/approve` - Approve payroll run
- `POST /api/v1/payroll/runs/{id}/finalize` - Finalize payroll run
- `DELETE /api/v1/payroll/runs/{id}` - Cancel payroll run

### Payslips
- `GET /api/v1/payroll/payslips` - List payslips
- `GET /api/v1/payroll/payslips/{id}` - Get payslip details
- `GET /api/v1/payroll/payslips/{id}/pdf` - Download payslip PDF
- `POST /api/v1/payroll/payslips/{id}/send` - Send payslip via email
- `GET /api/v1/employees/{employeeId}/payslips` - Get employee payslips

### Salary Structures
- `POST /api/v1/payroll/salary-structures` - Create salary structure
- `GET /api/v1/payroll/salary-structures` - List salary structures
- `GET /api/v1/payroll/salary-structures/{id}` - Get salary structure
- `PUT /api/v1/payroll/salary-structures/{id}` - Update salary structure

### Tax Calculations
- `POST /api/v1/payroll/calculate-tax` - Calculate tax for employee
- `GET /api/v1/payroll/tax-brackets/{country}` - Get tax brackets
- `POST /api/v1/payroll/tax-reports` - Generate tax report

## Events Published

### Payroll Events
- `payroll.run_created` - When payroll run is created
- `payroll.run_processed` - When payroll is processed
- `payroll.run_approved` - When payroll is approved
- `payroll.run_finalized` - When payroll is finalized
- `payroll.payslip_generated` - When payslip is generated
- `payroll.payment_initiated` - When payment is initiated

### Compliance Events
- `payroll.tax_calculated` - When tax is calculated
- `payroll.compliance_report_generated` - When compliance report is ready

## Events Consumed
- `employee.created` - Set up salary structure for new employee
- `employee.updated` - Update payroll data when employee changes
- `employee.terminated` - Process final payroll
- `attendance.overtime_approved` - Include overtime in payroll
- `benefits.enrollment_changed` - Update benefit deductions
- `tax.rate_updated` - Recalculate tax with new rates

## Database Schema

### payroll_runs
- `id` (UUID, PK)
- `tenant_id` (UUID, FK)
- `period_start` (DATE)
- `period_end` (DATE)
- `pay_date` (DATE)
- `status` (ENUM: draft, processing, processed, approved, finalized, cancelled)
- `total_gross` (DECIMAL)
- `total_deductions` (DECIMAL)
- `total_net` (DECIMAL)
- `currency` (VARCHAR)
- `created_by` (UUID, FK)
- `approved_by` (UUID, FK)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### payslips
- `id` (UUID, PK)
- `payroll_run_id` (UUID, FK)
- `employee_id` (UUID, FK)
- `employee_number` (VARCHAR)
- `employee_name` (VARCHAR)
- `period_start` (DATE)
- `period_end` (DATE)
- `pay_date` (DATE)
- `base_salary` (DECIMAL)
- `allowances` (JSON)
- `gross_salary` (DECIMAL)
- `deductions` (JSON)
- `tax_amount` (DECIMAL)
- `net_salary` (DECIMAL)
- `currency` (VARCHAR)
- `status` (ENUM: draft, generated, sent, paid)
- `pdf_path` (VARCHAR)
- `created_at` (TIMESTAMP)

### salary_structures
- `id` (UUID, PK)
- `employee_id` (UUID, FK)
- `base_salary` (DECIMAL)
- `currency` (VARCHAR)
- `effective_from` (DATE)
- `effective_to` (DATE, nullable)
- `allowances` (JSON)
- `deductions` (JSON)
- `payment_frequency` (ENUM: monthly, bi_weekly, weekly)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

## Environment Variables
```env
DATABASE_URL=postgresql://user:password@postgres:5432/payroll_db
RABBITMQ_URL=amqp://guest:guest@rabbitmq:5672
REDIS_URL=redis://redis:6379
JWT_SECRET=your-secret-key
TENANT_ID=default-tenant
DEFAULT_CURRENCY=USD
TAX_SERVICE_URL=http://tax-compliance:9000
EMPLOYEE_SERVICE_URL=http://employee-management:9000
```

## Docker Build & Run

### Build
```bash
docker build -t hrpayroll/payroll-processing:latest .
```

### Run
```bash
docker run -d \
  --name payroll-processing \
  -p 9002:9000 \
  -e DATABASE_URL=postgresql://user:password@postgres:5432/payroll_db \
  -e RABBITMQ_URL=amqp://guest:guest@rabbitmq:5672 \
  -e DEFAULT_CURRENCY=USD \
  hrpayroll/payroll-processing:latest
```

## Development

### Install Dependencies
```bash
composer install
```

### Run Tests
```bash
composer test
```

### Code Quality
```bash
composer phpstan
composer cs-fix
```

## Multi-Country Support

### Supported Countries
- United States (US)
- United Kingdom (UK)
- India (IN)
- Canada (CA)
- Australia (AU)
- Germany (DE)
- France (FR)

### Tax Calculation
Each country has its own tax calculator implementing the `TaxCalculatorInterface`:
- Progressive tax brackets
- Social security contributions
- Pension deductions
- Health insurance
- Local/state taxes

## Security Considerations
- All endpoints require JWT authentication
- Role-based access control (RBAC)
- Payroll data encryption at rest
- Audit logging for all payroll operations
- Multi-tenant data isolation
- PCI compliance for payment data
- SOC 2 compliance

## Performance Optimizations
- Batch processing for large payroll runs
- Async payslip generation
- Database query optimization
- Caching tax brackets and rates
- OpCache enabled
- Memory limit: 512MB for large payrolls
- Max execution time: 300s

## Monitoring & Health Checks
- Health endpoint: `GET /health`
- Metrics endpoint: `GET /metrics`
- Payroll run status monitoring
- Payment processing alerts
- Error rate monitoring

## Compliance & Auditing
- All payroll operations are logged
- Immutable payroll history
- Audit trail for approvals
- Compliance reports generation
- Data retention policies

## Support
For issues and questions, please contact the platform team.
