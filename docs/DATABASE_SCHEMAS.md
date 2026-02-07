# Database Schemas - HR & Payroll Management System

## Overview

This document outlines the database schemas for all microservices. Each service owns its database following the microservices pattern.

## Employee Management Service Database

**Database**: `employee_db` (PostgreSQL)

### Table: employees

```sql
CREATE TABLE employees (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    employee_number VARCHAR(20) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE NOT NULL,
    hire_date DATE NOT NULL,
    termination_date DATE,
    department_id VARCHAR(36) NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    employment_type VARCHAR(20) NOT NULL CHECK (employment_type IN ('FULL_TIME', 'PART_TIME', 'CONTRACT', 'INTERN')),
    status VARCHAR(20) NOT NULL CHECK (status IN ('ACTIVE', 'INACTIVE', 'TERMINATED', 'ON_LEAVE')),
    location JSONB,
    manager_id VARCHAR(36),
    metadata JSONB,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE(tenant_id, employee_number),
    UNIQUE(tenant_id, email),
    FOREIGN KEY (manager_id) REFERENCES employees(id)
);

CREATE INDEX idx_employees_tenant_id ON employees(tenant_id);
CREATE INDEX idx_employees_department_id ON employees(department_id);
CREATE INDEX idx_employees_status ON employees(status);
CREATE INDEX idx_employees_email ON employees(email);
CREATE INDEX idx_employees_manager_id ON employees(manager_id);
```

### Table: departments

```sql
CREATE TABLE departments (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL,
    parent_department_id VARCHAR(36),
    head_employee_id VARCHAR(36),
    cost_center VARCHAR(50),
    location JSONB,
    metadata JSONB,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE(tenant_id, code),
    FOREIGN KEY (parent_department_id) REFERENCES departments(id),
    FOREIGN KEY (head_employee_id) REFERENCES employees(id)
);

CREATE INDEX idx_departments_tenant_id ON departments(tenant_id);
CREATE INDEX idx_departments_parent_id ON departments(parent_department_id);
```

### Table: employee_documents

```sql
CREATE TABLE employee_documents (
    id VARCHAR(36) PRIMARY KEY,
    employee_id VARCHAR(36) NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT,
    mime_type VARCHAR(100),
    uploaded_by VARCHAR(36),
    uploaded_at TIMESTAMP NOT NULL DEFAULT NOW(),
    expires_at TIMESTAMP,
    metadata JSONB,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

CREATE INDEX idx_employee_documents_employee_id ON employee_documents(employee_id);
CREATE INDEX idx_employee_documents_type ON employee_documents(document_type);
```

## Payroll Processing Service Database

**Database**: `payroll_db` (PostgreSQL)

### Table: payroll_runs

```sql
CREATE TABLE payroll_runs (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    payment_date DATE NOT NULL,
    country VARCHAR(2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    status VARCHAR(20) NOT NULL CHECK (status IN ('DRAFT', 'PROCESSING', 'CALCULATED', 'APPROVED', 'DISBURSED', 'FAILED')),
    employee_filter JSONB,
    summary JSONB,
    created_by VARCHAR(36),
    approved_by VARCHAR(36),
    approved_at TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_payroll_runs_tenant_id ON payroll_runs(tenant_id);
CREATE INDEX idx_payroll_runs_status ON payroll_runs(status);
CREATE INDEX idx_payroll_runs_period ON payroll_runs(period_start, period_end);
```

### Table: payslips

```sql
CREATE TABLE payslips (
    id VARCHAR(36) PRIMARY KEY,
    payroll_run_id VARCHAR(36) NOT NULL,
    tenant_id VARCHAR(36) NOT NULL,
    employee_id VARCHAR(36) NOT NULL,
    employee_number VARCHAR(20) NOT NULL,
    employee_name VARCHAR(200) NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    payment_date DATE NOT NULL,
    currency VARCHAR(3) NOT NULL,
    basic_salary DECIMAL(15,2) NOT NULL,
    allowances JSONB,
    gross_pay DECIMAL(15,2) NOT NULL,
    deductions JSONB,
    total_deductions DECIMAL(15,2) NOT NULL,
    net_pay DECIMAL(15,2) NOT NULL,
    metadata JSONB,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (payroll_run_id) REFERENCES payroll_runs(id)
);

CREATE INDEX idx_payslips_payroll_run_id ON payslips(payroll_run_id);
CREATE INDEX idx_payslips_employee_id ON payslips(employee_id);
CREATE INDEX idx_payslips_tenant_id ON payslips(tenant_id);
CREATE INDEX idx_payslips_period ON payslips(period_start, period_end);
```

### Table: salary_structures

```sql
CREATE TABLE salary_structures (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    employee_id VARCHAR(36) NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE,
    basic_salary DECIMAL(15,2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    allowances JSONB,
    deductions JSONB,
    payment_frequency VARCHAR(20) NOT NULL CHECK (payment_frequency IN ('MONTHLY', 'BI_WEEKLY', 'WEEKLY')),
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_salary_structures_employee_id ON salary_structures(employee_id);
CREATE INDEX idx_salary_structures_effective_from ON salary_structures(effective_from);
```

## Time & Attendance Service Database

**Database**: `attendance_db` (PostgreSQL)

### Table: attendance_records

```sql
CREATE TABLE attendance_records (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    employee_id VARCHAR(36) NOT NULL,
    date DATE NOT NULL,
    clock_in_time TIMESTAMP,
    clock_out_time TIMESTAMP,
    clock_in_location JSONB,
    clock_out_location JSONB,
    total_hours DECIMAL(5,2),
    overtime_hours DECIMAL(5,2),
    status VARCHAR(20) NOT NULL CHECK (status IN ('PRESENT', 'ABSENT', 'HALF_DAY', 'ON_LEAVE', 'HOLIDAY')),
    shift_id VARCHAR(36),
    device_id VARCHAR(100),
    metadata JSONB,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE(tenant_id, employee_id, date)
);

CREATE INDEX idx_attendance_employee_id ON attendance_records(employee_id);
CREATE INDEX idx_attendance_date ON attendance_records(date);
CREATE INDEX idx_attendance_status ON attendance_records(status);
```

### Table: leave_requests

```sql
CREATE TABLE leave_requests (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    employee_id VARCHAR(36) NOT NULL,
    leave_type VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days DECIMAL(5,1) NOT NULL,
    half_day BOOLEAN DEFAULT FALSE,
    reason TEXT,
    status VARCHAR(20) NOT NULL CHECK (status IN ('PENDING', 'APPROVED', 'REJECTED', 'CANCELLED')),
    approved_by VARCHAR(36),
    approved_at TIMESTAMP,
    comments TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_leave_requests_employee_id ON leave_requests(employee_id);
CREATE INDEX idx_leave_requests_status ON leave_requests(status);
CREATE INDEX idx_leave_requests_dates ON leave_requests(start_date, end_date);
```

### Table: leave_balances

```sql
CREATE TABLE leave_balances (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    employee_id VARCHAR(36) NOT NULL,
    leave_type VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    total_allocated DECIMAL(5,1) NOT NULL,
    used DECIMAL(5,1) DEFAULT 0,
    pending DECIMAL(5,1) DEFAULT 0,
    remaining DECIMAL(5,1) NOT NULL,
    carried_forward DECIMAL(5,1) DEFAULT 0,
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE(tenant_id, employee_id, leave_type, year)
);

CREATE INDEX idx_leave_balances_employee_id ON leave_balances(employee_id);
CREATE INDEX idx_leave_balances_year ON leave_balances(year);
```

## User & Access Management Service Database

**Database**: `user_db` (PostgreSQL)

### Table: users

```sql
CREATE TABLE users (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    roles JSONB NOT NULL,
    status VARCHAR(20) NOT NULL CHECK (status IN ('ACTIVE', 'INACTIVE', 'LOCKED', 'PENDING')),
    last_login_at TIMESTAMP,
    failed_login_attempts INT DEFAULT 0,
    metadata JSONB,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE(tenant_id, email)
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_tenant_id ON users(tenant_id);
CREATE INDEX idx_users_status ON users(status);
```

### Table: roles

```sql
CREATE TABLE roles (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36),
    name VARCHAR(100) NOT NULL,
    description TEXT,
    permissions JSONB NOT NULL,
    is_system_role BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE(tenant_id, name)
);

CREATE INDEX idx_roles_tenant_id ON roles(tenant_id);
```

### Table: audit_logs

```sql
CREATE TABLE audit_logs (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    user_id VARCHAR(36),
    action VARCHAR(100) NOT NULL,
    resource_type VARCHAR(50) NOT NULL,
    resource_id VARCHAR(36),
    ip_address VARCHAR(45),
    user_agent TEXT,
    request_data JSONB,
    response_status INT,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_audit_logs_tenant_id ON audit_logs(tenant_id);
CREATE INDEX idx_audit_logs_user_id ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_action ON audit_logs(action);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(created_at);
```

## Benefits & Compensation Service Database

**Database**: `benefits_db` (PostgreSQL)

### Table: benefit_plans

```sql
CREATE TABLE benefit_plans (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    employer_contribution DECIMAL(15,2),
    employee_contribution DECIMAL(15,2),
    currency VARCHAR(3) NOT NULL,
    eligibility_criteria JSONB,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_benefit_plans_tenant_id ON benefit_plans(tenant_id);
CREATE INDEX idx_benefit_plans_category ON benefit_plans(category);
```

### Table: benefit_enrollments

```sql
CREATE TABLE benefit_enrollments (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    employee_id VARCHAR(36) NOT NULL,
    benefit_plan_id VARCHAR(36) NOT NULL,
    status VARCHAR(20) NOT NULL CHECK (status IN ('ACTIVE', 'PENDING', 'CANCELLED', 'EXPIRED')),
    effective_date DATE NOT NULL,
    termination_date DATE,
    monthly_contribution DECIMAL(15,2),
    dependents JSONB,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (benefit_plan_id) REFERENCES benefit_plans(id)
);

CREATE INDEX idx_benefit_enrollments_employee_id ON benefit_enrollments(employee_id);
CREATE INDEX idx_benefit_enrollments_plan_id ON benefit_enrollments(benefit_plan_id);
CREATE INDEX idx_benefit_enrollments_status ON benefit_enrollments(status);
```

## Tax & Compliance Service Database

**Database**: `tax_db` (PostgreSQL)

### Table: tax_rules

```sql
CREATE TABLE tax_rules (
    id VARCHAR(36) PRIMARY KEY,
    country VARCHAR(2) NOT NULL,
    tax_type VARCHAR(50) NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE,
    rules JSONB NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_tax_rules_country ON tax_rules(country);
CREATE INDEX idx_tax_rules_effective_from ON tax_rules(effective_from);
```

### Table: compliance_reports

```sql
CREATE TABLE compliance_reports (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    report_type VARCHAR(50) NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    country VARCHAR(2) NOT NULL,
    status VARCHAR(20) NOT NULL,
    file_path VARCHAR(500),
    generated_by VARCHAR(36),
    generated_at TIMESTAMP,
    submitted_at TIMESTAMP,
    metadata JSONB,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_compliance_reports_tenant_id ON compliance_reports(tenant_id);
CREATE INDEX idx_compliance_reports_type ON compliance_reports(report_type);
CREATE INDEX idx_compliance_reports_period ON compliance_reports(period_start, period_end);
```

## Performance Management Service Database

**Database**: `performance_db` (PostgreSQL)

### Table: performance_goals

```sql
CREATE TABLE performance_goals (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    employee_id VARCHAR(36) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    target_value DECIMAL(15,2),
    current_value DECIMAL(15,2),
    unit VARCHAR(50),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL CHECK (status IN ('DRAFT', 'ACTIVE', 'COMPLETED', 'CANCELLED')),
    created_by VARCHAR(36),
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_performance_goals_employee_id ON performance_goals(employee_id);
CREATE INDEX idx_performance_goals_status ON performance_goals(status);
```

### Table: performance_reviews

```sql
CREATE TABLE performance_reviews (
    id VARCHAR(36) PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    employee_id VARCHAR(36) NOT NULL,
    reviewer_id VARCHAR(36) NOT NULL,
    review_period_start DATE NOT NULL,
    review_period_end DATE NOT NULL,
    overall_rating DECIMAL(3,2),
    strengths TEXT,
    areas_for_improvement TEXT,
    comments TEXT,
    status VARCHAR(20) NOT NULL CHECK (status IN ('DRAFT', 'SUBMITTED', 'COMPLETED')),
    submitted_at TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_performance_reviews_employee_id ON performance_reviews(employee_id);
CREATE INDEX idx_performance_reviews_reviewer_id ON performance_reviews(reviewer_id);
CREATE INDEX idx_performance_reviews_period ON performance_reviews(review_period_start, review_period_end);
```

## Notification Service Database

**Database**: `notifications` (MongoDB)

### Collection: notifications

```javascript
{
  _id: ObjectId,
  tenantId: String,
  userId: String,
  type: String, // EMAIL, SMS, IN_APP, PUSH
  channel: String,
  recipient: String,
  subject: String,
  body: String,
  templateId: String,
  templateData: Object,
  status: String, // PENDING, SENT, FAILED, DELIVERED
  priority: String, // LOW, NORMAL, HIGH, URGENT
  scheduledAt: Date,
  sentAt: Date,
  deliveredAt: Date,
  failureReason: String,
  metadata: Object,
  createdAt: Date,
  updatedAt: Date
}

// Indexes
db.notifications.createIndex({ tenantId: 1, userId: 1 });
db.notifications.createIndex({ status: 1 });
db.notifications.createIndex({ scheduledAt: 1 });
db.notifications.createIndex({ createdAt: -1 });
```

### Collection: notification_templates

```javascript
{
  _id: ObjectId,
  tenantId: String,
  name: String,
  type: String,
  subject: String,
  bodyTemplate: String,
  variables: Array,
  isActive: Boolean,
  createdAt: Date,
  updatedAt: Date
}

// Indexes
db.notification_templates.createIndex({ tenantId: 1, name: 1 }, { unique: true });
```

## Data Relationships

### Cross-Service References

Services communicate via events and APIs, not direct database access:

```
Employee Management → Payroll Processing
  - Employee ID referenced in payslips
  - Employee data fetched via API

Employee Management → Time & Attendance
  - Employee ID referenced in attendance records
  - Employee data fetched via API

Time & Attendance → Payroll Processing
  - Attendance data provided via events
  - Overtime calculations shared

Payroll Processing → Notification
  - Payslip generation triggers notification event
  - Employee email fetched from Employee Management

User Management → All Services
  - Authentication tokens validated
  - Permissions checked via API
```

## Data Retention Policies

| Data Type | Retention Period | Archive Strategy |
|-----------|------------------|------------------|
| Employee Records | 7 years after termination | Cold storage |
| Payroll Data | 7 years | Cold storage |
| Attendance Records | 3 years | Cold storage |
| Audit Logs | 7 years | Cold storage |
| Notifications | 1 year | Delete |
| Performance Reviews | 5 years | Cold storage |

## Backup Strategy

- **Frequency**: Daily automated backups
- **Retention**: 30 days for daily, 12 months for monthly
- **Type**: Full backup + incremental
- **Storage**: Cross-region replication
- **Testing**: Monthly restore tests

## Conclusion

This schema design ensures:
- **Data isolation** per microservice
- **Scalability** through proper indexing
- **Data integrity** via constraints
- **Audit trail** for compliance
- **Performance** optimization
