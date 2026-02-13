# Tax & Compliance Service

## Overview
Microservice responsible for tax calculations, compliance reporting, and regulatory management.

## Features
- Country-specific tax calculations
- Progressive tax bracket support
- Social security and statutory deductions
- Tax form generation (W-2, 1099, etc.)
- Compliance reporting
- Audit logging
- Regulatory updates management

## API Endpoints

### Tax Calculation
- `POST /api/v1/tax/calculate` - Calculate taxes
- `GET /api/v1/tax/rules/{country}` - Get tax rules
- `POST /api/v1/tax/forms/{employeeId}` - Generate tax forms
- `GET /api/v1/tax/summary/{employeeId}` - Get tax summary

### Compliance
- `POST /api/v1/compliance/reports` - Generate compliance report
- `GET /api/v1/compliance/audit-logs` - Get audit logs
- `GET /api/v1/compliance/regulations/{country}` - Get regulations
- `POST /api/v1/compliance/validate` - Validate compliance

## Supported Countries
- United States (US)
- United Kingdom (UK)
- India (IN)
- Extensible for additional countries

## Tax Calculation Example
```json
{
  "grossIncome": 80000,
  "country": "US",
  "employeeData": {
    "deductions": 12950,
    "exemptions": 0,
    "dependents": 2
  }
}
```

## Events Published
- `tax.calculated`
- `tax.form-generated`
- `compliance.report-generated`
- `regulation.updated`

## Database Schema
- `tax_rules` - Country-specific tax rules
- `tax_calculations` - Historical calculations
- `compliance_reports` - Generated reports
- `audit_logs` - All system actions

## Running Locally
```bash
composer install
php -S localhost:8005
```

## Docker
```bash
docker build -t tax-compliance-service .
docker run -p 8005:9000 tax-compliance-service
```
