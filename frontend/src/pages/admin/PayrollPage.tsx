import React, { useState } from 'react';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/common/Card';
import { Button } from '@/components/common/Button';
import { Table } from '@/components/common/Table';
import { Badge } from '@/components/common/Badge';
import { Modal } from '@/components/common/Modal';
import { PayrollRun } from '@/types';
import { formatDate, formatCurrency } from '@/utils/format';

const mockPayrollRuns: PayrollRun[] = [
  {
    id: '1',
    period: 'June 2024',
    startDate: '2024-06-01',
    endDate: '2024-06-30',
    status: 'Completed',
    totalEmployees: 1247,
    totalGrossPay: 8450000,
    totalDeductions: 1250000,
    totalNetPay: 7200000,
    currency: 'USD',
    createdAt: '2024-06-25',
    processedAt: '2024-06-30',
  },
  {
    id: '2',
    period: 'May 2024',
    startDate: '2024-05-01',
    endDate: '2024-05-31',
    status: 'Completed',
    totalEmployees: 1235,
    totalGrossPay: 8320000,
    totalDeductions: 1230000,
    totalNetPay: 7090000,
    currency: 'USD',
    createdAt: '2024-05-25',
    processedAt: '2024-05-31',
  },
  {
    id: '3',
    period: 'April 2024',
    startDate: '2024-04-01',
    endDate: '2024-04-30',
    status: 'Completed',
    totalEmployees: 1220,
    totalGrossPay: 8180000,
    totalDeductions: 1210000,
    totalNetPay: 6970000,
    currency: 'USD',
    createdAt: '2024-04-25',
    processedAt: '2024-04-30',
  },
];

export const PayrollPage: React.FC = () => {
  const [selectedRun, setSelectedRun] = useState<PayrollRun | null>(null);
  const [showNewRunModal, setShowNewRunModal] = useState(false);

  const columns = [
    {
      key: 'period',
      header: 'Period',
      render: (run: PayrollRun) => (
        <div>
          <p className="font-medium">{run.period}</p>
          <p className="text-sm text-secondary-500">
            {formatDate(run.startDate)} - {formatDate(run.endDate)}
          </p>
        </div>
      ),
    },
    {
      key: 'totalEmployees',
      header: 'Employees',
    },
    {
      key: 'totalGrossPay',
      header: 'Gross Pay',
      render: (run: PayrollRun) => formatCurrency(run.totalGrossPay, run.currency),
    },
    {
      key: 'totalDeductions',
      header: 'Deductions',
      render: (run: PayrollRun) => formatCurrency(run.totalDeductions, run.currency),
    },
    {
      key: 'totalNetPay',
      header: 'Net Pay',
      render: (run: PayrollRun) => (
        <span className="font-semibold text-green-600">
          {formatCurrency(run.totalNetPay, run.currency)}
        </span>
      ),
    },
    {
      key: 'status',
      header: 'Status',
      render: (run: PayrollRun) => {
        const variants: Record<string, 'success' | 'warning' | 'danger' | 'info'> = {
          Completed: 'success',
          Processing: 'info',
          Draft: 'warning',
          Failed: 'danger',
        };
        return <Badge variant={variants[run.status]}>{run.status}</Badge>;
      },
    },
    {
      key: 'processedAt',
      header: 'Processed',
      render: (run: PayrollRun) => (run.processedAt ? formatDate(run.processedAt) : '-'),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-secondary-900">Payroll Management</h1>
          <p className="text-secondary-600">Process and manage payroll runs</p>
        </div>
        <Button onClick={() => setShowNewRunModal(true)}>
          <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
          </svg>
          New Payroll Run
        </Button>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card>
          <CardContent className="pt-6">
            <p className="text-sm text-secondary-600">Current Month Payroll</p>
            <p className="text-3xl font-bold text-secondary-900 mt-1">
              {formatCurrency(7200000)}
            </p>
            <p className="text-sm text-green-600 mt-2">1,247 employees</p>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="pt-6">
            <p className="text-sm text-secondary-600">Average Salary</p>
            <p className="text-3xl font-bold text-secondary-900 mt-1">
              {formatCurrency(5773)}
            </p>
            <p className="text-sm text-secondary-500 mt-2">Per employee</p>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="pt-6">
            <p className="text-sm text-secondary-600">Total Deductions</p>
            <p className="text-3xl font-bold text-secondary-900 mt-1">
              {formatCurrency(1250000)}
            </p>
            <p className="text-sm text-secondary-500 mt-2">14.8% of gross pay</p>
          </CardContent>
        </Card>
      </div>

      {/* Payroll Runs Table */}
      <Card>
        <CardHeader>
          <CardTitle>Payroll History</CardTitle>
        </CardHeader>
        <CardContent className="p-0">
          <Table
            data={mockPayrollRuns}
            columns={columns}
            onRowClick={(run) => setSelectedRun(run)}
          />
        </CardContent>
      </Card>

      {/* Payroll Run Details Modal */}
      <Modal
        isOpen={!!selectedRun}
        onClose={() => setSelectedRun(null)}
        title="Payroll Run Details"
        size="lg"
      >
        {selectedRun && (
          <div className="space-y-6">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-sm text-secondary-600">Period</p>
                <p className="font-medium text-lg">{selectedRun.period}</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Status</p>
                <Badge variant="success" className="mt-1">
                  {selectedRun.status}
                </Badge>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Total Employees</p>
                <p className="font-medium">{selectedRun.totalEmployees}</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Processed Date</p>
                <p className="font-medium">{formatDate(selectedRun.processedAt!)}</p>
              </div>
            </div>

            <div className="border-t border-secondary-200 pt-4">
              <h4 className="font-semibold mb-3">Financial Summary</h4>
              <div className="space-y-2">
                <div className="flex justify-between">
                  <span className="text-secondary-600">Gross Pay</span>
                  <span className="font-medium">
                    {formatCurrency(selectedRun.totalGrossPay, selectedRun.currency)}
                  </span>
                </div>
                <div className="flex justify-between">
                  <span className="text-secondary-600">Total Deductions</span>
                  <span className="font-medium text-red-600">
                    -{formatCurrency(selectedRun.totalDeductions, selectedRun.currency)}
                  </span>
                </div>
                <div className="flex justify-between pt-2 border-t border-secondary-200">
                  <span className="font-semibold">Net Pay</span>
                  <span className="font-bold text-green-600 text-lg">
                    {formatCurrency(selectedRun.totalNetPay, selectedRun.currency)}
                  </span>
                </div>
              </div>
            </div>

            <div className="flex gap-3">
              <Button variant="primary">Download Report</Button>
              <Button variant="outline">View Payslips</Button>
            </div>
          </div>
        )}
      </Modal>

      {/* New Payroll Run Modal */}
      <Modal
        isOpen={showNewRunModal}
        onClose={() => setShowNewRunModal(false)}
        title="Create New Payroll Run"
      >
        <div className="space-y-4">
          <p className="text-secondary-600">
            This will create a new payroll run for all active employees for the current period.
          </p>
          <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p className="text-sm text-blue-800">
              <strong>Period:</strong> July 2024
            </p>
            <p className="text-sm text-blue-800">
              <strong>Employees:</strong> 1,247 active employees
            </p>
            <p className="text-sm text-blue-800">
              <strong>Estimated Gross Pay:</strong> {formatCurrency(8500000)}
            </p>
          </div>
          <div className="flex gap-3 justify-end">
            <Button variant="outline" onClick={() => setShowNewRunModal(false)}>
              Cancel
            </Button>
            <Button>Create Payroll Run</Button>
          </div>
        </div>
      </Modal>
    </div>
  );
};
