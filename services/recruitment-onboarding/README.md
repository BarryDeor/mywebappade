# Recruitment & Onboarding Service

## Overview
Microservice responsible for managing the hiring pipeline and new employee onboarding.

## Features
- Job posting management
- Applicant tracking system (ATS)
- Interview scheduling and feedback
- Offer letter generation
- Onboarding workflows and checklists
- Background verification tracking

## API Endpoints

### Recruitment
- `POST /api/v1/jobs` - Create job posting
- `GET /api/v1/jobs` - List job postings
- `POST /api/v1/applications` - Submit application
- `GET /api/v1/applications/{id}` - Get application status
- `POST /api/v1/interviews` - Schedule interview
- `POST /api/v1/offers` - Generate offer

### Onboarding
- `POST /api/v1/onboarding/{employeeId}/tasks` - Assign onboarding tasks
- `PUT /api/v1/onboarding/tasks/{taskId}/complete` - Complete task
- `GET /api/v1/onboarding/{employeeId}` - Get onboarding status

## Application Stages
- SUBMITTED - Application received
- SCREENING - Initial screening
- INTERVIEW - Interview stage
- OFFER - Offer extended
- REJECTED - Application rejected
- HIRED - Candidate hired

## Onboarding Task Categories
- COMPLIANCE - Legal and compliance tasks
- IT - Technology setup
- HR - HR-related tasks
- TRAINING - Training and orientation
- SOCIAL - Team integration

## Events Published
- `job.posted`
- `application.received`
- `interview.scheduled`
- `offer.generated`
- `offer.accepted`
- `onboarding.started`
- `onboarding.completed`

## Database Schema
- `job_postings` - Job listings
- `applications` - Candidate applications
- `interviews` - Interview schedules
- `offers` - Job offers
- `onboarding_tasks` - Onboarding checklists

## Running Locally
```bash
composer install
php -S localhost:8008
```

## Docker
```bash
docker build -t recruitment-onboarding-service .
docker run -p 8008:9000 recruitment-onboarding-service
```
