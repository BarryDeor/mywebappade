import React, { useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuthStore } from './store/authStore';
import { MainLayout } from './components/layout/MainLayout';
import { LoginPage } from './pages/LoginPage';
import { DashboardPage } from './pages/admin/DashboardPage';
import { EmployeesPage } from './pages/admin/EmployeesPage';
import { PayrollPage } from './pages/admin/PayrollPage';
import { EmployeeDashboard } from './pages/employee/EmployeeDashboard';
import { MyLeavesPage } from './pages/employee/MyLeavesPage';
import { UserRole } from './types';

interface ProtectedRouteProps {
  children: React.ReactNode;
  allowedRoles?: UserRole[];
}

const ProtectedRoute: React.FC<ProtectedRouteProps> = ({ children, allowedRoles }) => {
  const { isAuthenticated, user } = useAuthStore();

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  if (allowedRoles && user && !allowedRoles.includes(user.role)) {
    return <Navigate to="/dashboard" replace />;
  }

  return <>{children}</>;
};

function App() {
  const { initialize, isLoading } = useAuthStore();

  useEffect(() => {
    initialize();
  }, [initialize]);

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-secondary-50">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        
        <Route
          path="/"
          element={
            <ProtectedRoute>
              <MainLayout />
            </ProtectedRoute>
          }
        >
          <Route index element={<Navigate to="/dashboard" replace />} />
          
          {/* Common Routes */}
          <Route path="dashboard" element={<DashboardPage />} />
          
          {/* Admin Routes */}
          <Route
            path="employees"
            element={
              <ProtectedRoute allowedRoles={[UserRole.SUPER_ADMIN, UserRole.HR_ADMIN, UserRole.HR_MANAGER]}>
                <EmployeesPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="payroll"
            element={
              <ProtectedRoute allowedRoles={[UserRole.SUPER_ADMIN, UserRole.PAYROLL_ADMIN, UserRole.HR_ADMIN]}>
                <PayrollPage />
              </ProtectedRoute>
            }
          />
          
          {/* Employee Routes */}
          <Route path="leaves" element={<MyLeavesPage />} />
          <Route path="profile" element={<EmployeeDashboard />} />
          
          {/* Placeholder routes */}
          <Route path="attendance" element={<div className="p-6">Attendance Page - Coming Soon</div>} />
          <Route path="performance" element={<div className="p-6">Performance Page - Coming Soon</div>} />
          <Route path="recruitment" element={<div className="p-6">Recruitment Page - Coming Soon</div>} />
          <Route path="reports" element={<div className="p-6">Reports Page - Coming Soon</div>} />
          <Route path="settings" element={<div className="p-6">Settings Page - Coming Soon</div>} />
        </Route>

        <Route path="*" element={<Navigate to="/dashboard" replace />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
