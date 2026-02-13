import React, { useState } from 'react';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/common/Card';
import { Button } from '@/components/common/Button';
import { Input } from '@/components/common/Input';
import { Select } from '@/components/common/Select';
import { Table } from '@/components/common/Table';
import { Badge } from '@/components/common/Badge';
import { Modal } from '@/components/common/Modal';
import { LeaveRequest } from '@/types';
import { formatDate } from '@/utils/format';

const mockLeaveRequests: LeaveRequest[] = [
  {
    id: '1',
    employeeId: 'EMP001',
    employeeName: 'John Doe',
    leaveType: 'Annual Leave',
    startDate: '2024-07-15',
    endDate: '2024-07-19',
    days: 5,
    reason: 'Family vacation',
    status: 'Approved',
    approver: 'Jane Smith',
    createdAt: '2024-06-20',
    updatedAt: '2024-06-21',
  },
  {
    id: '2',
    employeeId: 'EMP001',
    employeeName: 'John Doe',
    leaveType: 'Sick Leave',
    startDate: '2024-06-10',
    endDate: '2024-06-11',
    days: 2,
    reason: 'Medical appointment',
    status: 'Approved',
    approver: 'Jane Smith',
    createdAt: '2024-06-09',
    updatedAt: '2024-06-09',
  },
  {
    id: '3',
    employeeId: 'EMP001',
    employeeName: 'John Doe',
    leaveType: 'Personal Leave',
    startDate: '2024-05-20',
    endDate: '2024-05-20',
    days: 1,
    reason: 'Personal matters',
    status: 'Approved',
    approver: 'Jane Smith',
    createdAt: '2024-05-15',
    updatedAt: '2024-05-16',
  },
];

const leaveTypes = [
  { value: '', label: 'Select leave type' },
  { value: 'annual', label: 'Annual Leave' },
  { value: 'sick', label: 'Sick Leave' },
  { value: 'personal', label: 'Personal Leave' },
  { value: 'maternity', label: 'Maternity Leave' },
  { value: 'paternity', label: 'Paternity Leave' },
];

export const MyLeavesPage: React.FC = () => {
  const [showRequestModal, setShowRequestModal] = useState(false);
  const [selectedLeave, setSelectedLeave] = useState<LeaveRequest | null>(null);

  const columns = [
    {
      key: 'leaveType',
      header: 'Leave Type',
    },
    {
      key: 'dates',
      header: 'Dates',
      render: (leave: LeaveRequest) => (
        <div>
          <p>{formatDate(leave.startDate)} - {formatDate(leave.endDate)}</p>
          <p className="text-sm text-secondary-500">{leave.days} day(s)</p>
        </div>
      ),
    },
    {
      key: 'reason',
      header: 'Reason',
      render: (leave: LeaveRequest) => (
        <span className="text-sm">{leave.reason}</span>
      ),
    },
    {
      key: 'status',
      header: 'Status',
      render: (leave: LeaveRequest) => {
        const variants: Record<string, 'success' | 'warning' | 'danger' | 'info'> = {
          Approved: 'success',
          Pending: 'warning',
          Rejected: 'danger',
          Cancelled: 'info',
        };
        return <Badge variant={variants[leave.status]}>{leave.status}</Badge>;
      },
    },
    {
      key: 'approver',
      header: 'Approver',
      render: (leave: LeaveRequest) => leave.approver || '-',
    },
    {
      key: 'createdAt',
      header: 'Requested On',
      render: (leave: LeaveRequest) => formatDate(leave.createdAt),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-secondary-900">My Leaves</h1>
          <p className="text-secondary-600">Manage your leave requests</p>
        </div>
        <Button onClick={() => setShowRequestModal(true)}>
          <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
          </svg>
          Request Leave
        </Button>
      </div>

      {/* Leave Balance Summary */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card>
          <CardContent className="pt-6">
            <p className="text-sm text-secondary-600">Annual Leave</p>
            <p className="text-3xl font-bold text-secondary-900 mt-1">12</p>
            <p className="text-sm text-secondary-500 mt-2">of 20 days remaining</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <p className="text-sm text-secondary-600">Sick Leave</p>
            <p className="text-3xl font-bold text-secondary-900 mt-1">8</p>
            <p className="text-sm text-secondary-500 mt-2">of 10 days remaining</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <p className="text-sm text-secondary-600">Personal Leave</p>
            <p className="text-3xl font-bold text-secondary-900 mt-1">4</p>
            <p className="text-sm text-secondary-500 mt-2">of 5 days remaining</p>
          </CardContent>
        </Card>
      </div>

      {/* Leave Requests Table */}
      <Card>
        <CardHeader>
          <CardTitle>Leave History</CardTitle>
        </CardHeader>
        <CardContent className="p-0">
          <Table
            data={mockLeaveRequests}
            columns={columns}
            onRowClick={(leave) => setSelectedLeave(leave)}
          />
        </CardContent>
      </Card>

      {/* Request Leave Modal */}
      <Modal
        isOpen={showRequestModal}
        onClose={() => setShowRequestModal(false)}
        title="Request Leave"
      >
        <form className="space-y-4">
          <Select label="Leave Type" options={leaveTypes} required />
          <Input label="Start Date" type="date" required />
          <Input label="End Date" type="date" required />
          <div>
            <label className="block text-sm font-medium text-secondary-700 mb-1">
              Reason <span className="text-red-500">*</span>
            </label>
            <textarea
              className="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
              rows={4}
              placeholder="Please provide a reason for your leave request..."
              required
            />
          </div>
          <div className="flex gap-3 justify-end">
            <Button variant="outline" onClick={() => setShowRequestModal(false)}>
              Cancel
            </Button>
            <Button type="submit">Submit Request</Button>
          </div>
        </form>
      </Modal>

      {/* Leave Details Modal */}
      <Modal
        isOpen={!!selectedLeave}
        onClose={() => setSelectedLeave(null)}
        title="Leave Request Details"
      >
        {selectedLeave && (
          <div className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-sm text-secondary-600">Leave Type</p>
                <p className="font-medium">{selectedLeave.leaveType}</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Status</p>
                <Badge variant="success" className="mt-1">{selectedLeave.status}</Badge>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Start Date</p>
                <p className="font-medium">{formatDate(selectedLeave.startDate)}</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">End Date</p>
                <p className="font-medium">{formatDate(selectedLeave.endDate)}</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Duration</p>
                <p className="font-medium">{selectedLeave.days} day(s)</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Approver</p>
                <p className="font-medium">{selectedLeave.approver || 'Pending'}</p>
              </div>
            </div>
            <div>
              <p className="text-sm text-secondary-600">Reason</p>
              <p className="font-medium mt-1">{selectedLeave.reason}</p>
            </div>
            {selectedLeave.status === 'Pending' && (
              <div className="flex gap-3">
                <Button variant="danger" size="sm">Cancel Request</Button>
              </div>
            )}
          </div>
        )}
      </Modal>
    </div>
  );
};
