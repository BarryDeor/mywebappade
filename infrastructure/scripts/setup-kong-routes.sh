#!/bin/bash
set -e

KONG_ADMIN_URL="http://localhost:8001"

echo "⏳ Waiting for Kong Admin API to be ready..."
until curl -sf "$KONG_ADMIN_URL/status" > /dev/null 2>&1; do
  sleep 2
done
echo "✅ Kong Admin API is ready!"

echo ""
echo "========================================="
echo "  Setting up Kong Services & Routes"
echo "========================================="
echo ""

# ─────────────────────────────────────────────
# 1. User & Access Management Service
# ─────────────────────────────────────────────
echo "📦 Registering: user-service"
curl -sf -X POST "$KONG_ADMIN_URL/services" \
  --data name=user-service \
  --data url=http://user-service:8080 > /dev/null

# Routes: /api/v1/auth/*
curl -sf -X POST "$KONG_ADMIN_URL/services/user-service/routes" \
  --data name=auth-routes \
  --data 'paths[]=/api/v1/auth' \
  --data strip_path=false > /dev/null
echo "  ✅ Route: /api/v1/auth/* → user-service:8080"

# ─────────────────────────────────────────────
# 2. Employee Management Service
# ─────────────────────────────────────────────
echo "📦 Registering: employee-service"
curl -sf -X POST "$KONG_ADMIN_URL/services" \
  --data name=employee-service \
  --data url=http://employee-service:8080 > /dev/null

# Routes: /api/v1/employees/*
curl -sf -X POST "$KONG_ADMIN_URL/services/employee-service/routes" \
  --data name=employee-routes \
  --data 'paths[]=/api/v1/employees' \
  --data strip_path=false > /dev/null
echo "  ✅ Route: /api/v1/employees/* → employee-service:8080"

# ─────────────────────────────────────────────
# 3. Payroll Processing Service
# ─────────────────────────────────────────────
echo "📦 Registering: payroll-service"
curl -sf -X POST "$KONG_ADMIN_URL/services" \
  --data name=payroll-service \
  --data url=http://payroll-service:8080 > /dev/null

# Routes: /api/v1/payroll/*
curl -sf -X POST "$KONG_ADMIN_URL/services/payroll-service/routes" \
  --data name=payroll-routes \
  --data 'paths[]=/api/v1/payroll' \
  --data strip_path=false > /dev/null
echo "  ✅ Route: /api/v1/payroll/* → payroll-service:8080"

# ─────────────────────────────────────────────
# 4. Time & Attendance Service
# ─────────────────────────────────────────────
echo "📦 Registering: time-attendance-service"
curl -sf -X POST "$KONG_ADMIN_URL/services" \
  --data name=time-attendance-service \
  --data url=http://time-attendance-service:8080 > /dev/null

# Routes: /api/v1/attendance/*, /api/v1/shifts/*, /api/v1/leaves/*
curl -sf -X POST "$KONG_ADMIN_URL/services/time-attendance-service/routes" \
  --data name=attendance-routes \
  --data 'paths[]=/api/v1/attendance' \
  --data 'paths[]=/api/v1/shifts' \
  --data 'paths[]=/api/v1/leaves' \
  --data strip_path=false > /dev/null
echo "  ✅ Route: /api/v1/attendance/* → time-attendance-service:8080"
echo "  ✅ Route: /api/v1/shifts/* → time-attendance-service:8080"
echo "  ✅ Route: /api/v1/leaves/* → time-attendance-service:8080"

# ─────────────────────────────────────────────
# 5. Notification Service
# ─────────────────────────────────────────────
echo "📦 Registering: notification-service"
curl -sf -X POST "$KONG_ADMIN_URL/services" \
  --data name=notification-service \
  --data url=http://notification-service:8080 > /dev/null

# Routes: /api/v1/notifications/*
curl -sf -X POST "$KONG_ADMIN_URL/services/notification-service/routes" \
  --data name=notification-routes \
  --data 'paths[]=/api/v1/notifications' \
  --data strip_path=false > /dev/null
echo "  ✅ Route: /api/v1/notifications/* → notification-service:8080"

# ─────────────────────────────────────────────
# 6. Benefits & Compensation Service
#    (not in docker-compose yet, but routes ready)
# ─────────────────────────────────────────────
# echo "📦 Registering: benefits-service"
# curl -sf -X POST "$KONG_ADMIN_URL/services" \
#   --data name=benefits-service \
#   --data url=http://benefits-service:8080 > /dev/null
# curl -sf -X POST "$KONG_ADMIN_URL/services/benefits-service/routes" \
#   --data name=benefits-routes \
#   --data 'paths[]=/api/v1/benefits' \
#   --data 'paths[]=/api/v1/compensation' \
#   --data strip_path=false > /dev/null

# ─────────────────────────────────────────────
# 7. Performance Management Service
#    (not in docker-compose yet, but routes ready)
# ─────────────────────────────────────────────
# echo "📦 Registering: performance-service"
# curl -sf -X POST "$KONG_ADMIN_URL/services" \
#   --data name=performance-service \
#   --data url=http://performance-service:8080 > /dev/null
# curl -sf -X POST "$KONG_ADMIN_URL/services/performance-service/routes" \
#   --data name=performance-routes \
#   --data 'paths[]=/api/v1/performance' \
#   --data strip_path=false > /dev/null

# ─────────────────────────────────────────────
# 8. Recruitment & Onboarding Service
#    (not in docker-compose yet, but routes ready)
# ─────────────────────────────────────────────
# echo "📦 Registering: recruitment-service"
# curl -sf -X POST "$KONG_ADMIN_URL/services" \
#   --data name=recruitment-service \
#   --data url=http://recruitment-service:8080 > /dev/null
# curl -sf -X POST "$KONG_ADMIN_URL/services/recruitment-service/routes" \
#   --data name=recruitment-routes \
#   --data 'paths[]=/api/v1/jobs' \
#   --data 'paths[]=/api/v1/applications' \
#   --data 'paths[]=/api/v1/interviews' \
#   --data 'paths[]=/api/v1/offers' \
#   --data 'paths[]=/api/v1/onboarding' \
#   --data strip_path=false > /dev/null

# ─────────────────────────────────────────────
# 9. Tax & Compliance Service
#    (not in docker-compose yet, but routes ready)
# ─────────────────────────────────────────────
# echo "📦 Registering: tax-service"
# curl -sf -X POST "$KONG_ADMIN_URL/services" \
#   --data name=tax-service \
#   --data url=http://tax-service:8080 > /dev/null
# curl -sf -X POST "$KONG_ADMIN_URL/services/tax-service/routes" \
#   --data name=tax-routes \
#   --data 'paths[]=/api/v1/tax' \
#   --data 'paths[]=/api/v1/compliance' \
#   --data strip_path=false > /dev/null

# ─────────────────────────────────────────────
# 10. Reporting & Analytics Service
#     (not in docker-compose yet, but routes ready)
# ─────────────────────────────────────────────
# echo "📦 Registering: reporting-service"
# curl -sf -X POST "$KONG_ADMIN_URL/services" \
#   --data name=reporting-service \
#   --data url=http://reporting-service:8080 > /dev/null
# curl -sf -X POST "$KONG_ADMIN_URL/services/reporting-service/routes" \
#   --data name=reporting-routes \
#   --data 'paths[]=/api/v1/reports' \
#   --data 'paths[]=/api/v1/analytics' \
#   --data strip_path=false > /dev/null

echo ""
echo "========================================="
echo "  Enabling Kong Plugins"
echo "========================================="
echo ""

# Enable CORS globally
curl -sf -X POST "$KONG_ADMIN_URL/plugins" \
  --data name=cors \
  --data "config.origins[]=*" \
  --data "config.methods[]=GET" \
  --data "config.methods[]=POST" \
  --data "config.methods[]=PUT" \
  --data "config.methods[]=PATCH" \
  --data "config.methods[]=DELETE" \
  --data "config.methods[]=OPTIONS" \
  --data "config.headers[]=Accept" \
  --data "config.headers[]=Content-Type" \
  --data "config.headers[]=Authorization" \
  --data "config.credentials=true" \
  --data "config.max_age=3600" > /dev/null
echo "  ✅ CORS plugin enabled (global)"

# Enable rate limiting globally
curl -sf -X POST "$KONG_ADMIN_URL/plugins" \
  --data name=rate-limiting \
  --data "config.minute=100" \
  --data "config.policy=local" > /dev/null
echo "  ✅ Rate Limiting plugin enabled (100 req/min)"

echo ""
echo "========================================="
echo "  ✅ Kong Setup Complete!"
echo "========================================="
echo ""
echo "Active routes:"
curl -sf "$KONG_ADMIN_URL/routes" | python3 -c "
import sys, json
data = json.load(sys.stdin)
for route in data.get('data', []):
    name = route.get('name', 'unnamed')
    paths = ', '.join(route.get('paths', []))
    service_id = route.get('service', {}).get('id', 'N/A')
    print(f'  • {name}: {paths}')
" 2>/dev/null || echo "  (install python3 to see route summary)"
echo ""
echo "Test with: curl http://localhost:8000/api/v1/auth/login"
