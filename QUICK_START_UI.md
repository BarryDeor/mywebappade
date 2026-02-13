# Quick Start Guide - HR & Payroll UI

## 🚀 Get Started in 5 Minutes

### Prerequisites
- Node.js 18+ installed
- npm or yarn package manager
- Docker (optional, for containerized deployment)

## Option 1: Local Development

### Step 1: Navigate to Frontend Directory
```bash
cd frontend
```

### Step 2: Install Dependencies
```bash
npm install
```

### Step 3: Start Development Server
```bash
npm run dev
```

### Step 4: Open Browser
Navigate to: `http://localhost:3000`

### Step 5: Login
Use demo credentials:
- **Admin**: admin@company.com / admin123
- **Employee**: employee@company.com / emp123

## Option 2: Docker Deployment

### Step 1: Build Frontend Image
```bash
cd frontend
docker build -t hr-frontend:latest .
```

### Step 2: Run Container
```bash
docker run -p 3001:80 hr-frontend:latest
```

### Step 3: Access Application
Navigate to: `http://localhost:3001`

## Option 3: Full Stack with Docker Compose

### Step 1: Navigate to Infrastructure
```bash
cd infrastructure
```

### Step 2: Create Environment File
```bash
cp .env.example .env
# Edit .env with your configuration
```

### Step 3: Start All Services
```bash
docker-compose up -d
```

### Step 4: Access Services
- **Frontend**: http://localhost:3001
- **API Gateway**: http://localhost:8000
- **RabbitMQ Management**: http://localhost:15672
- **Grafana**: http://localhost:3000

## 🎨 UI Features Overview

### Admin Dashboard
- View employee statistics
- Monitor payroll trends
- Track recent activities
- Access quick actions

### Employee Management
- Search and filter employees
- Add new employees
- View employee details
- Update employee information

### Payroll Processing
- Create payroll runs
- View payroll history
- Download reports
- Track financial summaries

### Employee Self-Service
- Request leave
- View payslips
- Track attendance
- Update profile

## 🔑 User Roles

### Super Admin
- Full system access
- All features enabled

### HR Admin
- Employee management
- Payroll processing
- Reporting

### Payroll Admin
- Payroll processing
- Financial reports

### HR Manager
- Employee oversight
- Approvals

### Manager
- Team management
- Approvals

### Employee
- Self-service portal
- Personal data only

## 📱 Responsive Design

The UI works seamlessly on:
- 📱 Mobile phones (320px+)
- 📱 Tablets (768px+)
- 💻 Laptops (1024px+)
- 🖥️ Desktops (1280px+)

## 🎯 Key Pages

### `/login`
Authentication page with demo credentials

### `/dashboard`
Main dashboard with analytics (all roles)

### `/employees`
Employee management (admin only)

### `/payroll`
Payroll processing (admin only)

### `/leaves`
Leave management (all roles)

### `/profile`
User profile (all roles)

## 🛠️ Development Commands

```bash
# Install dependencies
npm install

# Start dev server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview

# Run linter
npm run lint

# Type check
npx tsc --noEmit
```

## 🐳 Docker Commands

```bash
# Build image
docker build -t hr-frontend .

# Run container
docker run -p 3001:80 hr-frontend

# Stop container
docker stop <container-id>

# View logs
docker logs <container-id>
```

## 🔧 Configuration

### Environment Variables

Create `.env` file in frontend directory:

```env
VITE_API_URL=http://localhost:8000/api/v1
VITE_APP_NAME=HR & Payroll Management System
```

### API Proxy

Development server proxies API requests:
- `/api/*` → `http://api-gateway:8000`

## 📊 Component Usage Examples

### Button
```tsx
import { Button } from '@/components/common/Button';

<Button variant="primary" onClick={handleClick}>
  Click Me
</Button>
```

### Input
```tsx
import { Input } from '@/components/common/Input';

<Input
  label="Email"
  type="email"
  required
  error={errors.email}
/>
```

### Card
```tsx
import { Card, CardHeader, CardTitle, CardContent } from '@/components/common/Card';

<Card>
  <CardHeader>
    <CardTitle>Dashboard</CardTitle>
  </CardHeader>
  <CardContent>
    Content here
  </CardContent>
</Card>
```

### Table
```tsx
import { Table } from '@/components/common/Table';

<Table
  data={employees}
  columns={columns}
  onRowClick={handleRowClick}
/>
```

## 🎨 Styling

### Tailwind Classes
```tsx
<div className="bg-white p-6 rounded-lg shadow-sm">
  <h1 className="text-2xl font-bold text-secondary-900">
    Title
  </h1>
</div>
```

### Custom Utilities
```tsx
import { cn } from '@/utils/cn';

<div className={cn(
  'base-class',
  isActive && 'active-class',
  className
)}>
```

## 🔐 Authentication

### Login Flow
1. User enters credentials
2. Frontend calls `/api/v1/auth/login`
3. Backend returns JWT token
4. Token stored in localStorage
5. Token included in all requests

### Protected Routes
```tsx
<ProtectedRoute allowedRoles={[UserRole.HR_ADMIN]}>
  <EmployeesPage />
</ProtectedRoute>
```

## 📱 Mobile Testing

### Chrome DevTools
1. Open DevTools (F12)
2. Click device toolbar icon
3. Select device (iPhone, iPad, etc.)
4. Test responsive behavior

### Real Device Testing
1. Find your local IP: `ipconfig` or `ifconfig`
2. Access: `http://<your-ip>:3000`
3. Test on mobile device

## 🐛 Troubleshooting

### Port Already in Use
```bash
# Kill process on port 3000
npx kill-port 3000

# Or use different port
npm run dev -- --port 3001
```

### Dependencies Issues
```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install
```

### Build Errors
```bash
# Clear build cache
rm -rf dist
npm run build
```

### Docker Issues
```bash
# Remove old containers
docker-compose down

# Rebuild images
docker-compose build --no-cache

# Start fresh
docker-compose up -d
```

## 📚 Additional Resources

- **UI Documentation**: `/docs/UI_DOCUMENTATION.md`
- **API Documentation**: `/docs/API_DOCUMENTATION.md`
- **Architecture**: `/ARCHITECTURE.md`
- **Deployment**: `/DEPLOYMENT_STRATEGY.md`

## 🎉 Success!

You should now see the HR & Payroll Management System UI running with:
- ✅ Modern, clean interface
- ✅ Role-based navigation
- ✅ Responsive design
- ✅ Interactive dashboards
- ✅ Secure authentication

## 💡 Tips

1. **Use demo credentials** for testing
2. **Check browser console** for errors
3. **Enable React DevTools** for debugging
4. **Use network tab** to monitor API calls
5. **Test on multiple devices** for responsiveness

## 🆘 Need Help?

- Check documentation in `/docs`
- Review component examples
- Inspect browser console
- Check Docker logs
- Verify API connectivity

---

**Happy Coding! 🚀**
