# Performance Management Service

## Overview
Microservice responsible for employee performance tracking, goal management, and performance reviews.

## Features
- Goal setting and OKRs
- KPI tracking
- Performance review cycles
- 360-degree feedback
- Competency frameworks
- Performance improvement plans (PIP)

## API Endpoints

### Goals
- `POST /api/v1/performance/goals` - Create goal
- `PUT /api/v1/performance/goals/{id}/progress` - Update progress
- `GET /api/v1/performance/goals/{employeeId}` - Get employee goals
- `GET /api/v1/performance/goals/{employeeId}/kpis` - Get KPIs

### Reviews
- `POST /api/v1/performance/reviews` - Create review
- `POST /api/v1/performance/reviews/{id}/ratings` - Add ratings
- `POST /api/v1/performance/reviews/{id}/complete` - Complete review
- `POST /api/v1/performance/feedback` - Submit 360 feedback
- `GET /api/v1/performance/reviews/{employeeId}` - Get reviews

## Goal Types
- INDIVIDUAL - Personal goals
- TEAM - Team objectives
- COMPANY - Company-wide goals

## Review Types
- ANNUAL - Annual performance review
- QUARTERLY - Quarterly check-in
- PROBATION - Probation period review
- 360_DEGREE - 360-degree feedback

## Events Published
- `goal.created`
- `goal.progress-updated`
- `review.created`
- `review.completed`
- `feedback.submitted`

## Database Schema
- `goals` - Employee goals and OKRs
- `performance_reviews` - Review records
- `feedback` - 360-degree feedback
- `kpis` - Key performance indicators

## Running Locally
```bash
composer install
php -S localhost:8006
```

## Docker
```bash
docker build -t performance-management-service .
docker run -p 8006:9000 performance-management-service
```
