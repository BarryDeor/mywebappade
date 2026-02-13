# Benefits & Compensation Service

## Overview
Microservice responsible for managing employee benefits, bonuses, and total compensation.

## Features
- Benefits catalog management
- Employee benefit enrollments
- Bonus and incentive management
- Total compensation calculation
- Flexible benefits configuration

## API Endpoints

### Benefits
- `GET /api/v1/benefits/catalog` - List available benefits
- `POST /api/v1/benefits/enrollments` - Enroll in benefit
- `GET /api/v1/benefits/enrollments/{employeeId}` - Get employee enrollments
- `POST /api/v1/benefits/enrollments/{id}/cancel` - Cancel enrollment

### Compensation
- `POST /api/v1/compensation/bonuses` - Create bonus
- `PUT /api/v1/compensation/bonuses/{id}/approve` - Approve bonus
- `GET /api/v1/compensation/{employeeId}` - Get total compensation
- `GET /api/v1/compensation/bonuses` - List bonuses

## Events Published
- `benefit.enrolled`
- `benefit.cancelled`
- `bonus.created`
- `bonus.approved`
- `compensation.updated`

## Database Schema
- `benefits` - Benefit definitions
- `benefit_enrollments` - Employee enrollments
- `bonuses` - Bonus records
- `equity_grants` - Stock options/RSUs

## Running Locally
```bash
composer install
php -S localhost:8004
```

## Docker
```bash
docker build -t benefits-compensation-service .
docker run -p 8004:9000 benefits-compensation-service
```
