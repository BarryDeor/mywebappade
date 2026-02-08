# Reporting & Analytics Service

## Overview
Microservice responsible for business intelligence, reporting, and analytics across all HR and payroll data.

## Features
- Pre-built HR dashboards
- Payroll analytics and summaries
- Headcount and attrition reports
- Cost analysis
- Custom report builder
- Data export capabilities
- Predictive analytics (attrition, payroll anomalies)

## API Endpoints

### Dashboards
- `GET /api/v1/reports/dashboards` - Get dashboard data
- `GET /api/v1/analytics/headcount` - Headcount analytics
- `GET /api/v1/analytics/payroll-summary` - Payroll summary
- `GET /api/v1/analytics/attrition` - Attrition analytics
- `POST /api/v1/reports/custom` - Generate custom report

## Dashboard Types
- HR_OVERVIEW - Overall HR metrics
- PAYROLL - Payroll analytics
- RECRUITMENT - Hiring pipeline
- PERFORMANCE - Performance metrics

## Analytics Features
- Real-time metrics
- Historical trends
- Predictive analytics
- Comparative analysis
- Drill-down capabilities

## Events Consumed
All events from other services for comprehensive analytics

## Database
- Read replicas for performance
- Data warehouse for historical data
- Redis for caching

## Running Locally
```bash
composer install
php -S localhost:8007
```

## Docker
```bash
docker build -t reporting-analytics-service .
docker run -p 8007:9000 reporting-analytics-service
```
