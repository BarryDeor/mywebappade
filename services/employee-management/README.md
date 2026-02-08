# Employee Management Service

## Overview
The Employee Management Service is a core microservice responsible for managing employee profiles, organizational structure, and employment lifecycle within the HR & Payroll Management System.

## Features

### Employee Profile Management
- Create, read, update, and delete employee records
- Store comprehensive employee information (personal, contact, employment details)
- Document management (contracts, certifications, ID documents)
- Employment history tracking

### Organizational Structure
- Department management
- Role and position definitions
- Reporting hierarchy
- Team assignments

### Employee Lifecycle
- Onboarding integration
- Status transitions (active, on-leave, terminated)
- Offboarding workflows
- Rehire management

## Technology Stack
- **Language**: PHP 8.2+
- **Framework**: Symfony Components
- **Database**: PostgreSQL
- **ORM**: Doctrine
- **Message Broker**: RabbitMQ (AMQP)
- **Containerization**: Docker

## API Endpoints

### Employee Management
- `POST /api/v1/employees` - Create new employee
- `GET /api/v1/employees` - List all employees (with pagination)
- `GET /api/v1/employees/{id}` - Get employee details
- `PUT /api/v1/employees/{id}` - Update employee
- `DELETE /api/v1/employees/{id}` - Soft delete employee
- `GET /api/v1/employees/{id}/history` - Get employment history

### Department Management
- `POST /api/v1/departments` - Create department
- `GET /api/v1/departments` - List departments
- `GET /api/v1/departments/{id}` - Get department details
- `PUT /api/v1/departments/{id}` - Update department
- `GET /api/v1/departments/{id}/employees` - Get department employees

### Document Management
- `POST /api/v1/employees/{id}/documents` - Upload document
- `GET /api/v1/employees/{id}/documents` - List employee documents
- `GET /api/v1/employees/{id}/documents/{docId}` - Download document
- `DELETE /api/v1/employees/{id}/documents/{docId}` - Delete document

## Events Published

### Employee Events
- `employee.created` - When a new employee is created
- `employee.updated` - When employee details are updated
- `employee.status_changed` - When employee status changes
- `employee.terminated` - When employee is terminated
- `employee.document_uploaded` - When a document is uploaded

### Department Events
- `department.created` - When a new department is created
- `department.updated` - When department is updated
- `department.employee_assigned` - When employee is assigned to department

## Events Consumed
- `onboarding.completed` - Activate employee after onboarding
- `payroll.employee_data_requested` - Provide employee data for payroll
- `leave.status_changed` - Update employee leave status

## Database Schema

### employees
- `id` (UUID, PK)
- `tenant_id` (UUID, FK)
- `employee_number` (VARCHAR, UNIQUE)
- `first_name` (VARCHAR)
- `last_name` (VARCHAR)
- `email` (VARCHAR, UNIQUE)
- `phone` (VARCHAR)
- `date_of_birth` (DATE)
- `hire_date` (DATE)
- `department_id` (UUID, FK)
- `position` (VARCHAR)
- `manager_id` (UUID, FK)
- `employment_type` (ENUM: full_time, part_time, contract)
- `status` (ENUM: active, on_leave, terminated)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### departments
- `id` (UUID, PK)
- `tenant_id` (UUID, FK)
- `name` (VARCHAR)
- `code` (VARCHAR, UNIQUE)
- `parent_id` (UUID, FK, nullable)
- `manager_id` (UUID, FK)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### employee_documents
- `id` (UUID, PK)
- `employee_id` (UUID, FK)
- `document_type` (VARCHAR)
- `file_name` (VARCHAR)
- `file_path` (VARCHAR)
- `uploaded_by` (UUID, FK)
- `uploaded_at` (TIMESTAMP)

## Environment Variables
```env
DATABASE_URL=postgresql://user:password@postgres:5432/employee_db
RABBITMQ_URL=amqp://guest:guest@rabbitmq:5672
REDIS_URL=redis://redis:6379
JWT_SECRET=your-secret-key
TENANT_ID=default-tenant
```

## Docker Build & Run

### Build
```bash
docker build -t hrpayroll/employee-management:latest .
```

### Run
```bash
docker run -d \
  --name employee-management \
  -p 9001:9000 \
  -e DATABASE_URL=postgresql://user:password@postgres:5432/employee_db \
  -e RABBITMQ_URL=amqp://guest:guest@rabbitmq:5672 \
  hrpayroll/employee-management:latest
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

## Security Considerations
- All endpoints require JWT authentication
- Role-based access control (RBAC)
- Sensitive data encryption at rest
- Audit logging for all operations
- Multi-tenant data isolation

## Performance Optimizations
- Database query optimization with indexes
- Caching frequently accessed data (Redis)
- Pagination for large datasets
- Async event processing
- OpCache enabled for PHP

## Monitoring & Health Checks
- Health endpoint: `GET /health`
- Metrics endpoint: `GET /metrics`
- Readiness probe: `GET /ready`
- Liveness probe: Built-in Docker health check

## Support
For issues and questions, please contact the platform team.
