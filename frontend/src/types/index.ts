// User and Authentication Types
export interface User {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  role: UserRole;
  employeeId?: string;
  avatar?: string;
  permissions: string[];
}

export enum UserRole {
  SUPER_ADMIN = 'SUPER_ADMIN',
  HR_ADMIN = 'HR_ADMIN',
  HR_MANAGER = 'HR_MANAGER',
  PAYROLL_ADMIN = 'PAYROLL_ADMIN',
  MANAGER = 'MANAGER',
  EMPLOYEE = 'EMPLOYEE',
}

export interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
}

// Employee Types
export interface Employee {
  id: string;
  employeeId: string;
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  dateOfBirth: string;
  gender: 'Male' | 'Female' | 'Other';
  address: Address;
  department: string;
  position: string;
  manager?: string;
  hireDate: string;
  employmentType: 'Full-Time' | 'Part-Time' | 'Contract';
  status: 'Active' | 'Inactive' | 'On Leave' | 'Terminated';
  salary: number;
  currency: string;
  avatar?: string;
}

export interface Address {
  street: string;
  city: string;
  state: string;
  country: string;
  postalCode: string;
}

// Payroll Types
export interface PayrollRun {
  id: string;
  period: string;
  startDate: string;
  endDate: string;
  status: 'Draft' | 'Processing' | 'Completed' | 'Failed';
  totalEmployees: number;
  totalGrossPay: number;
  totalDeductions: number;
  totalNetPay: number;
  currency: string;
  createdAt: string;
  processedAt?: string;
}

export interface Payslip {
  id: string;
  employeeId: string;
  employeeName: string;
  period: string;
  payDate: string;
  basicSalary: number;
  allowances: PayrollItem[];
  deductions: PayrollItem[];
  grossPay: number;
  netPay: number;
  currency: string;
  status: 'Draft' | 'Approved' | 'Paid';
}

export interface PayrollItem {
  name: string;
  amount: number;
  type: 'allowance' | 'deduction';
}

// Leave Types
export interface LeaveRequest {
  id: string;
  employeeId: string;
  employeeName: string;
  leaveType: string;
  startDate: string;
  endDate: string;
  days: number;
  reason: string;
  status: 'Pending' | 'Approved' | 'Rejected' | 'Cancelled';
  approver?: string;
  createdAt: string;
  updatedAt: string;
}

export interface LeaveBalance {
  leaveType: string;
  total: number;
  used: number;
  remaining: number;
}

// Attendance Types
export interface AttendanceRecord {
  id: string;
  employeeId: string;
  date: string;
  checkIn: string;
  checkOut?: string;
  workHours: number;
  status: 'Present' | 'Absent' | 'Late' | 'Half-Day' | 'On Leave';
  location?: string;
}

// Dashboard Types
export interface DashboardStats {
  totalEmployees: number;
  activeEmployees: number;
  onLeave: number;
  newHires: number;
  pendingLeaves: number;
  pendingApprovals: number;
  totalPayroll: number;
  avgSalary: number;
}

// Notification Types
export interface Notification {
  id: string;
  title: string;
  message: string;
  type: 'info' | 'success' | 'warning' | 'error';
  read: boolean;
  createdAt: string;
  link?: string;
}

// API Response Types
export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
  errors?: Record<string, string[]>;
}

export interface PaginatedResponse<T> {
  data: T[];
  total: number;
  page: number;
  pageSize: number;
  totalPages: number;
}
