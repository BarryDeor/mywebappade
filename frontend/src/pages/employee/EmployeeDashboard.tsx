import React from 'react';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/common/Card';
import { Badge } from '@/components/common/Badge';
import { Button } from '@/components/common/Button';
import { formatCurrency, formatDate } from '@/utils/format';
import { useAuthStore } from '@/store/authStore';

const leaveBalance = [
  { type: 'Annual Leave', total: 20, used: 8, remaining: 12 },
  { type: 'Sick Leave', total: 10, used: 2, remaining: 8 },
  { type: 'Personal Leave', total: 5, used: 1, remaining: 4 },
];

const upcomingLeaves = [
  { id: 1, type: 'Annual Leave', startDate: '2024-07-15', endDate: '2024-07-19', days: 5, status: 'Approved' },
  { id: 2, type: 'Sick Leave', startDate: '2024-08-05', endDate: '2024-08-05', days: 1, status: 'Pending' },
];

const recentPayslips = [
  { id: 1, period: 'June 2024', payDate: '2024-06-30', netPay: 5800, status: 'Paid' },
  { id: 2, period: 'May 2024', payDate: '2024-05-31', netPay: 5800, status: 'Paid' },
  { id: 3, period: 'April 2024', payDate: '2024-04-30', netPay: 5800, status: 'Paid' },
];

export const EmployeeDashboard: React.FC = () => {
  const { user } = useAuthStore();

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-secondary-900">My Dashboard</h1>
        <p className="text-secondary-600">Welcome back, {user?.firstName}!</p>
      </div>

      {/* Quick Actions */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Button variant="outline" className="h-auto py-4 flex-col">
          <svg className="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          Request Leave
        </Button>
        <Button variant="outline" className="h-auto py-4 flex-col">
          <svg className="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Clock In/Out
        </Button>
        <Button variant="outline" className="h-auto py-4 flex-col">
          <svg className="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          View Payslips
        </Button>
        <Button variant="outline" className="h-auto py-4 flex-col">
          <svg className="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          Update Profile
        </Button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Leave Balance */}
        <Card>
          <CardHeader>
            <CardTitle>Leave Balance</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {leaveBalance.map((leave) => (
                <div key={leave.type}>
                  <div className="flex justify-between mb-2">
                    <span className="text-sm font-medium text-secondary-700">{leave.type}</span>
                    <span className="text-sm text-secondary-600">
                      {leave.remaining} / {leave.total} days
                    </span>
                  </div>
                  <div className="w-full bg-secondary-200 rounded-full h-2">
                    <div
                      className="bg-primary-600 h-2 rounded-full"
                      style={{ width: `${(leave.remaining / leave.total) * 100}%` }}
                    />
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Upcoming Leaves */}
        <Card>
          <CardHeader>
            <CardTitle>Upcoming Leaves</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {upcomingLeaves.map((leave) => (
                <div key={leave.id} className="flex items-center justify-between p-3 bg-secondary-50 rounded-lg">
                  <div>
                    <p className="font-medium text-secondary-900">{leave.type}</p>
                    <p className="text-sm text-secondary-600">
                      {formatDate(leave.startDate)} - {formatDate(leave.endDate)} ({leave.days} days)
                    </p>
                  </div>
                  <Badge variant={leave.status === 'Approved' ? 'success' : 'warning'}>
                    {leave.status}
                  </Badge>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Recent Payslips */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle>Recent Payslips</CardTitle>
            <Button variant="ghost" size="sm">View All</Button>
          </div>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {recentPayslips.map((payslip) => (
              <div key={payslip.id} className="flex items-center justify-between p-4 border border-secondary-200 rounded-lg hover:bg-secondary-50 transition-colors cursor-pointer">
                <div className="flex items-center gap-4">
                  <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg className="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <div>
                    <p className="font-medium text-secondary-900">{payslip.period}</p>
                    <p className="text-sm text-secondary-600">Paid on {formatDate(payslip.payDate)}</p>
                  </div>
                </div>
                <div className="text-right">
                  <p className="font-semibold text-green-600">{formatCurrency(payslip.netPay)}</p>
                  <Badge variant="success" className="mt-1">{payslip.status}</Badge>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
};
