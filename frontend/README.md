# HR & Payroll Management System - Frontend

Modern, role-based user interface for the HR & Payroll Management System built with React, TypeScript, and Tailwind CSS.

## Features

### 🎨 Modern UI/UX
- Clean, professional enterprise design
- Responsive layout for all devices
- Intuitive navigation with role-based menus
- Consistent component library

### 🔐 Role-Based Access Control
- **Super Admin**: Full system access
- **HR Admin**: Employee and HR management
- **Payroll Admin**: Payroll processing and reports
- **HR Manager**: Employee oversight and approvals
- **Manager**: Team management
- **Employee**: Self-service portal

### 📊 Admin Features
- **Dashboard**: Real-time analytics and insights
- **Employee Management**: Complete employee lifecycle
- **Payroll Processing**: Multi-currency payroll runs
- **Reporting**: Comprehensive HR analytics
- **Recruitment**: Applicant tracking system
- **Performance**: Goal tracking and reviews

### 👤 Employee Self-Service
- Personal dashboard
- Leave requests and balance tracking
- Payslip viewing and download
- Attendance tracking
- Profile management
- Performance reviews

## Tech Stack

- **Framework**: React 18 with TypeScript
- **Build Tool**: Vite
- **Styling**: Tailwind CSS
- **State Management**: Zustand
- **Routing**: React Router v6
- **HTTP Client**: Axios
- **Charts**: Recharts
- **Form Validation**: React Hook Form + Zod
- **Date Handling**: date-fns

## Getting Started

### Prerequisites
- Node.js 18+ and npm

### Installation

1. Install dependencies:
```bash
npm install
```

2. Create environment file:
```bash
cp .env.example .env
```

3. Start development server:
```bash
npm run dev
```

The application will be available at `http://localhost:3000`

### Build for Production

```bash
npm run build
```

### Preview Production Build

```bash
npm run preview
```

## Docker Deployment

### Build Docker Image

```bash
docker build -t hr-frontend:latest .
```

### Run Container

```bash
docker run -p 3000:80 hr-frontend:latest
```

### Using Docker Compose

The frontend is included in the main docker-compose.yml:

```bash
cd ../infrastructure
docker-compose up frontend
```

## Project Structure

```
frontend/
├── src/
│   ├── components/          # Reusable UI components
│   │   ├── common/          # Generic components (Button, Input, etc.)
│   │   ├── layout/          # Layout components (Sidebar, Header)
│   │   ├── auth/            # Authentication components
│   │   ├── employee/        # Employee-specific components
│   │   ├── payroll/         # Payroll components
│   │   └── dashboard/       # Dashboard widgets
│   ├── pages/               # Page components
│   │   ├── admin/           # Admin pages
│   │   └── employee/        # Employee self-service pages
│   ├── services/            # API services
│   ├── store/               # State management
│   ├── types/               # TypeScript types
│   ├── utils/               # Utility functions
│   ├── App.tsx              # Main app component
│   └── main.tsx             # Entry point
├── public/                  # Static assets
├── Dockerfile               # Docker configuration
├── nginx.conf               # Nginx configuration
└── package.json             # Dependencies
```

## Component Library

### Common Components
- **Button**: Primary, secondary, outline, ghost, danger variants
- **Input**: Text, email, password, number inputs with validation
- **Select**: Dropdown with options
- **Card**: Container with header, title, content sections
- **Table**: Data table with sorting and pagination
- **Modal**: Dialog with customizable size
- **Badge**: Status indicators
- **Avatar**: User profile pictures with initials fallback

### Layout Components
- **Sidebar**: Role-based navigation menu
- **Header**: User profile and notifications
- **MainLayout**: Main application layout wrapper

## API Integration

The frontend communicates with backend microservices through the API Gateway:

- Base URL: `/api/v1`
- Authentication: JWT Bearer tokens
- Auto-retry on network errors
- Automatic token refresh

### API Services
- `authService`: Login, logout, user management
- `employeeService`: Employee CRUD operations
- `payrollService`: Payroll processing
- `leaveService`: Leave management
- `attendanceService`: Time tracking

## Authentication Flow

1. User enters credentials on login page
2. Frontend sends POST to `/api/v1/auth/login`
3. Backend validates and returns JWT token
4. Token stored in localStorage
5. Token included in all subsequent requests
6. Auto-redirect to login on 401 responses

## Role-Based Routing

Routes are protected based on user roles:

```typescript
<ProtectedRoute allowedRoles={[UserRole.HR_ADMIN, UserRole.SUPER_ADMIN]}>
  <EmployeesPage />
</ProtectedRoute>
```

## Styling Guidelines

### Tailwind CSS
- Use utility classes for styling
- Follow mobile-first responsive design
- Consistent spacing scale (4px base)
- Color palette: primary (blue), secondary (gray)

### Component Patterns
- Composition over configuration
- Consistent prop naming
- TypeScript for type safety
- Accessible by default

## Performance Optimization

- Code splitting with React.lazy
- Image optimization
- Gzip compression (nginx)
- Static asset caching
- Bundle size monitoring

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Development

### Code Quality
```bash
npm run lint        # ESLint
npm run type-check  # TypeScript
```

### Environment Variables
- `VITE_API_URL`: Backend API URL
- `VITE_APP_NAME`: Application name

## Deployment

### Production Checklist
- [ ] Update API URL in environment
- [ ] Build optimized bundle
- [ ] Configure nginx for SPA routing
- [ ] Enable HTTPS
- [ ] Set security headers
- [ ] Configure CORS
- [ ] Enable gzip compression

## License

Proprietary - All rights reserved
