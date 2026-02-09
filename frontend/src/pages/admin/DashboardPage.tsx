import React from 'react';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/common/Card';
import { Badge } from '@/components/common/Badge';
import { formatCurrency, formatNumber } from '@/utils/format';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, LineChart, Line } from 'recharts';

const statsData = [
  { label: 'Total Employees', value: 1247, change: '+12%', trend: 'up' },
  { label: 'Active Employees', value: 1198, change: '+5%', trend: 'up' },
  { label: 'On Leave', value: 49, change: '-3%', trend: 'down' },
  { label: 'Pending Approvals', value: 23, change: '+8%', trend: 'up' },
];

const departmentData = [
  { name: 'Engineering', employees: 450 },
  { name: 'Sales', employees: 280 },
  { name: 'Marketing', employees: 150 },
  { name: 'HR', employees: 80 },
  { name: 'Finance', employees: 120 },
  { name: 'Operations', employees: 167 },
];

const payrollTrend = [
  { month: 'Jan', amount: 2450000 },
  { month: 'Feb', amount: 2520000 },
  { month: 'Mar', amount: 2480000 },
  { month: 'Apr', amount: 2650000 },
  { month: 'May', amount: 2720000 },
  { month: 'Jun', amount: 2800000 },
];

const recentActivities = [
  { id: 1, type: 'leave', employee: 'John Doe', action: 'requested leave', time: '2 hours ago' },
  { id: 2, type: 'payroll', employee: 'System', action: 'processed payroll for June', time: '5 hours ago' },
  { id: 3, type: 'employee', employee: 'Jane Smith', action: 'joined the company', time: '1 day ago' },
  { id: 4, type: 'performance', employee: 'Mike Johnson', action: 'completed performance review', time: '2 days ago' },
];

export const DashboardPage: React.FC = () => {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-secondary-900">Dashboard</h1>
        <p className="text-secondary-600">Overview of your HR operations</p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {statsData.map((stat) => (
          <Card key={stat.label}>
            <CardContent className="pt-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-secondary-600">{stat.label}</p>
                  <p className="text-3xl font-bold text-secondary-900 mt-1">
                    {formatNumber(stat.value)}
                  </p>
                </div>
                <Badge variant={stat.trend === 'up' ? 'success' : 'warning'}>
                  {stat.change}
                </Badge>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Employees by Department</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={departmentData}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="name" />
                <YAxis />
                <Tooltip />
                <Bar dataKey="employees" fill="#3b82f6" />
              </BarChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Payroll Trend (Last 6 Months)</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <LineChart data={payrollTrend}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="month" />
                <YAxis />
                <Tooltip formatter={(value: number) => formatCurrency(value)} />
                <Line type="monotone" dataKey="amount" stroke="#3b82f6" strokeWidth={2} />
              </LineChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </div>

      {/* Recent Activities */}
      <Card>
        <CardHeader>
          <CardTitle>Recent Activities</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {recentActivities.map((activity) => (
              <div key={activity.id} className="flex items-center gap-4 pb-4 border-b border-secondary-200 last:border-0">
                <div className="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                  <svg className="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div className="flex-1">
                  <p className="text-sm text-secondary-900">
                    <strong>{activity.employee}</strong> {activity.action}
                  </p>
                  <p className="text-xs text-secondary-500">{activity.time}</p>
                </div>
                <Badge variant="info">{activity.type}</Badge>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
};
