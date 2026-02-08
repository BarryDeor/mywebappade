# User Access Management Service

## Overview
The User Access Management Service is a critical security microservice responsible for authentication, authorization, user management, and access control within the HR & Payroll Management System.

## Features

### Authentication
- OAuth 2.0 / OpenID Connect implementation
- JWT token-based authentication
- Multi-factor authentication (MFA)
- Single Sign-On (SSO) support
- Session management
- Token refresh mechanism
- Password policies and enforcement

### Authorization
- Role-Based Access Control (RBAC)
- Fine-grained permissions
- Resource-level access control
- Multi-tenant isolation
- Dynamic permission evaluation

### User Management
- User registration and provisioning
- User profile management
- Password reset workflows
- Account activation/deactivation
- User audit logs

### Security Features
- Token blacklisting (logout)
- Brute force protection
- Account lockout policies
- Security event logging
- Compliance reporting

## Technology Stack
- **Language**: PHP 8.2+
- **Framework**: Symfony Components
- **Database**: PostgreSQL
- **Cache/Session**: Redis
- **JWT**: firebase/php-jwt
- **Message Broker**: RabbitMQ (AMQP)
- **Containerization**: Docker

## API Endpoints

### Authentication
- `POST /api/v1/auth/login` - User login
- `POST /api/v1/auth/logout` - User logout
- `POST /api/v1/auth/refresh` - Refresh access token
- `GET /api/v1/auth/me` - Get current user info
- `POST /api/v1/auth/check-permission` - Check user permission

### User Management
- `POST /api/v1/users` - Create new user
- `GET /api/v1/users` - List users (admin only)
- `GET /api/v1/users/{id}` - Get user details
- `PUT /api/v1/users/{id}` - Update user
- `DELETE /api/v1/users/{id}` - Deactivate user
- `POST /api/v1/users/{id}/reset-password` - Reset password
- `POST /api/v1/users/{id}/activate` - Activate user
- `POST /api/v1/users/{id}/deactivate` - Deactivate user

### Role Management
- `POST /api/v1/roles` - Create role
- `GET /api/v1/roles` - List roles
- `GET /api/v1/roles/{id}` - Get role details
- `PUT /api/v1/roles/{id}` - Update role
- `DELETE /api/v1/roles/{id}` - Delete role
- `POST /api/v1/roles/{id}/permissions` - Assign permissions
- `GET /api/v1/roles/{id}/permissions` - Get role permissions

### Permission Management
- `GET /api/v1/permissions` - List all permissions
- `POST /api/v1/users/{id}/roles` - Assign role to user
- `DELETE /api/v1/users/{id}/roles/{roleId}` - Remove role from user

## Predefined Roles

### SUPER_ADMIN
- Full system access
- User and role management
- System configuration
- All permissions

### HR_ADMIN
- Employee management
- Recruitment management
- Performance management
- Benefits administration
- Reporting access

### PAYROLL_ADMIN
- Payroll processing
- Salary management
- Tax compliance
- Payroll reports
- Payment processing

### MANAGER
- Team management
- Approve leave requests
- Performance reviews
- View team reports
- Time approval

### EMPLOYEE
- View own profile
- Submit leave requests
- View payslips
- Update personal info
- View benefits

### AUDITOR
- Read-only access
- Audit logs
- Compliance reports
- System reports

## Events Published

### Authentication Events
- `auth.user_logged_in` - User successfully logged in
- `auth.user_logged_out` - User logged out
- `auth.login_failed` - Failed login attempt
- `auth.token_refreshed` - Access token refreshed
- `auth.password_reset_requested` - Password reset requested
- `auth.password_changed` - Password changed

### User Events
- `user.created` - New user created
- `user.updated` - User details updated
- `user.activated` - User account activated
- `user.deactivated` - User account deactivated
- `user.role_assigned` - Role assigned to user
- `user.role_removed` - Role removed from user

### Security Events
- `security.account_locked` - Account locked due to failed attempts
- `security.suspicious_activity` - Suspicious activity detected
- `security.permission_denied` - Permission denied event

## Events Consumed
- `employee.created` - Create user account for new employee
- `employee.terminated` - Deactivate user account
- `employee.updated` - Sync user profile with employee data

## Database Schema

### users
- `id` (UUID, PK)
- `tenant_id` (UUID, FK)
- `employee_id` (UUID, FK, nullable)
- `email` (VARCHAR, UNIQUE)
- `password_hash` (VARCHAR)
- `first_name` (VARCHAR)
- `last_name` (VARCHAR)
- `status` (ENUM: active, inactive, locked, pending)
- `last_login_at` (TIMESTAMP)
- `failed_login_attempts` (INT)
- `locked_until` (TIMESTAMP, nullable)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### roles
- `id` (UUID, PK)
- `name` (VARCHAR, UNIQUE)
- `description` (TEXT)
- `permissions` (JSON)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### user_roles
- `user_id` (UUID, FK)
- `role_id` (UUID, FK)
- `assigned_at` (TIMESTAMP)
- `assigned_by` (UUID, FK)
- PRIMARY KEY (user_id, role_id)

### audit_logs
- `id` (UUID, PK)
- `user_id` (UUID, FK)
- `action` (VARCHAR)
- `resource_type` (VARCHAR)
- `resource_id` (UUID)
- `ip_address` (VARCHAR)
- `user_agent` (TEXT)
- `metadata` (JSON)
- `created_at` (TIMESTAMP)

## JWT Token Structure

### Access Token
```json
{
  "sub": "user-uuid",
  "tenant_id": "tenant-uuid",
  "roles": ["EMPLOYEE", "MANAGER"],
  "type": "access",
  "iat": 1234567890,
  "exp": 1234571490
}
```

### Refresh Token
```json
{
  "sub": "user-uuid",
  "tenant_id": "tenant-uuid",
  "type": "refresh",
  "iat": 1234567890,
  "exp": 1237159890
}
```

## Environment Variables
```env
DATABASE_URL=postgresql://user:password@postgres:5432/auth_db
REDIS_URL=redis://redis:6379
RABBITMQ_URL=amqp://guest:guest@rabbitmq:5672
JWT_SECRET=your-super-secret-key-change-in-production
JWT_ACCESS_TOKEN_TTL=3600
JWT_REFRESH_TOKEN_TTL=2592000
MAX_LOGIN_ATTEMPTS=5
ACCOUNT_LOCKOUT_DURATION=1800
SESSION_LIFETIME=86400
```

## Docker Build & Run

### Build
```bash
docker build -t hrpayroll/user-access-management:latest .
```

### Run
```bash
docker run -d \
  --name user-access-management \
  -p 9003:9000 \
  -e DATABASE_URL=postgresql://user:password@postgres:5432/auth_db \
  -e REDIS_URL=redis://redis:6379 \
  -e JWT_SECRET=your-super-secret-key \
  hrpayroll/user-access-management:latest
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

## Security Best Practices

### Password Policy
- Minimum 8 characters
- Must contain uppercase, lowercase, number, and special character
- Password history (prevent reuse of last 5 passwords)
- Password expiration (90 days)
- Force password change on first login

### Token Security
- Short-lived access tokens (1 hour)
- Long-lived refresh tokens (30 days)
- Token rotation on refresh
- Token blacklisting on logout
- Secure token storage (httpOnly cookies recommended)

### Account Security
- Account lockout after 5 failed attempts
- Lockout duration: 30 minutes
- Email notification on suspicious activity
- IP-based rate limiting
- MFA enforcement for admin roles

## RBAC Permission Model

### Permission Format
`resource:action`

### Examples
- `employees:read` - View employees
- `employees:write` - Create/update employees
- `employees:delete` - Delete employees
- `payroll:process` - Process payroll
- `payroll:approve` - Approve payroll
- `reports:view` - View reports
- `users:manage` - Manage users
- `roles:manage` - Manage roles

## Monitoring & Health Checks
- Health endpoint: `GET /health`
- Metrics endpoint: `GET /metrics`
- Failed login monitoring
- Token generation rate
- Active sessions count
- Security event alerts

## Compliance & Auditing
- All authentication events logged
- User activity tracking
- Access control audit trail
- GDPR compliance (data export, deletion)
- SOC 2 compliance
- ISO 27001 alignment

## Multi-Tenant Isolation
- Tenant-based data segregation
- Tenant-specific user pools
- Cross-tenant access prevention
- Tenant-level role customization

## Support
For issues and questions, please contact the platform team.
