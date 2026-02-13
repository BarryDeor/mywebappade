import React, { useState } from 'react';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/common/Card';
import { Button } from '@/components/common/Button';
import { Input } from '@/components/common/Input';
import { Table } from '@/components/common/Table';
import { Badge } from '@/components/common/Badge';
import { Avatar } from '@/components/common/Avatar';
import { Modal } from '@/components/common/Modal';
import { Employee } from '@/types';
import { formatDate } from '@/utils/format';

const mockEmployees: Employee[] = [
  {
    id: '1',
    employeeId: 'EMP001',
    firstName: 'John',
    lastName: 'Doe',
    email: 'john.doe@company.com',
    phone: '+1 234 567 8900',
    dateOfBirth: '1990-05-15',
    gender: 'Male',
    address: { street: '123 Main St', city: 'New York', state: 'NY', country: 'USA', postalCode: '10001' },
    department: 'Engineering',
    position: 'Senior Software Engineer',
    hireDate: '2020-01-15',
    employmentType: 'Full-Time',
    status: 'Active',
    salary: 120000,
    currency: 'USD',
  },
  {
    id: '2',
    employeeId: 'EMP002',
    firstName: 'Jane',
    lastName: 'Smith',
    email: 'jane.smith@company.com',
    phone: '+1 234 567 8901',
    dateOfBirth: '1988-08-22',
    gender: 'Female',
    address: { street: '456 Oak Ave', city: 'San Francisco', state: 'CA', country: 'USA', postalCode: '94102' },
    department: 'Marketing',
    position: 'Marketing Manager',
    hireDate: '2019-03-20',
    employmentType: 'Full-Time',
    status: 'Active',
    salary: 95000,
    currency: 'USD',
  },
  {
    id: '3',
    employeeId: 'EMP003',
    firstName: 'Mike',
    lastName: 'Johnson',
    email: 'mike.johnson@company.com',
    phone: '+1 234 567 8902',
    dateOfBirth: '1992-11-10',
    gender: 'Male',
    address: { street: '789 Pine Rd', city: 'Austin', state: 'TX', country: 'USA', postalCode: '73301' },
    department: 'Sales',
    position: 'Sales Representative',
    hireDate: '2021-06-01',
    employmentType: 'Full-Time',
    status: 'Active',
    salary: 75000,
    currency: 'USD',
  },
];

export const EmployeesPage: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedEmployee, setSelectedEmployee] = useState<Employee | null>(null);
  const [showAddModal, setShowAddModal] = useState(false);

  const filteredEmployees = mockEmployees.filter(
    (emp) =>
      emp.firstName.toLowerCase().includes(searchTerm.toLowerCase()) ||
      emp.lastName.toLowerCase().includes(searchTerm.toLowerCase()) ||
      emp.employeeId.toLowerCase().includes(searchTerm.toLowerCase()) ||
      emp.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const columns = [
    {
      key: 'employee',
      header: 'Employee',
      render: (emp: Employee) => (
        <div className="flex items-center gap-3">
          <Avatar firstName={emp.firstName} lastName={emp.lastName} />
          <div>
            <p className="font-medium text-secondary-900">
              {emp.firstName} {emp.lastName}
            </p>
            <p className="text-sm text-secondary-500">{emp.employeeId}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'email',
      header: 'Email',
    },
    {
      key: 'department',
      header: 'Department',
    },
    {
      key: 'position',
      header: 'Position',
    },
    {
      key: 'status',
      header: 'Status',
      render: (emp: Employee) => (
        <Badge variant={emp.status === 'Active' ? 'success' : 'warning'}>
          {emp.status}
        </Badge>
      ),
    },
    {
      key: 'hireDate',
      header: 'Hire Date',
      render: (emp: Employee) => formatDate(emp.hireDate),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-secondary-900">Employees</h1>
          <p className="text-secondary-600">Manage your workforce</p>
        </div>
        <Button onClick={() => setShowAddModal(true)}>
          <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
          </svg>
          Add Employee
        </Button>
      </div>

      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle>All Employees ({filteredEmployees.length})</CardTitle>
            <div className="w-64">
              <Input
                type="search"
                placeholder="Search employees..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>
          </div>
        </CardHeader>
        <CardContent className="p-0">
          <Table
            data={filteredEmployees}
            columns={columns}
            onRowClick={(emp) => setSelectedEmployee(emp)}
          />
        </CardContent>
      </Card>

      {/* Employee Details Modal */}
      <Modal
        isOpen={!!selectedEmployee}
        onClose={() => setSelectedEmployee(null)}
        title="Employee Details"
        size="lg"
      >
        {selectedEmployee && (
          <div className="space-y-6">
            <div className="flex items-center gap-4">
              <Avatar
                firstName={selectedEmployee.firstName}
                lastName={selectedEmployee.lastName}
                size="xl"
              />
              <div>
                <h3 className="text-xl font-semibold">
                  {selectedEmployee.firstName} {selectedEmployee.lastName}
                </h3>
                <p className="text-secondary-600">{selectedEmployee.position}</p>
                <Badge variant="success" className="mt-2">
                  {selectedEmployee.status}
                </Badge>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-sm text-secondary-600">Employee ID</p>
                <p className="font-medium">{selectedEmployee.employeeId}</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Department</p>
                <p className="font-medium">{selectedEmployee.department}</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Email</p>
                <p className="font-medium">{selectedEmployee.email}</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Phone</p>
                <p className="font-medium">{selectedEmployee.phone}</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Hire Date</p>
                <p className="font-medium">{formatDate(selectedEmployee.hireDate)}</p>
              </div>
              <div>
                <p className="text-sm text-secondary-600">Employment Type</p>
                <p className="font-medium">{selectedEmployee.employmentType}</p>
              </div>
            </div>

            <div className="flex gap-3">
              <Button variant="primary">Edit Employee</Button>
              <Button variant="outline">View Full Profile</Button>
            </div>
          </div>
        )}
      </Modal>

      {/* Add Employee Modal */}
      <Modal
        isOpen={showAddModal}
        onClose={() => setShowAddModal(false)}
        title="Add New Employee"
        size="lg"
      >
        <form className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <Input label="First Name" required />
            <Input label="Last Name" required />
            <Input label="Email" type="email" required />
            <Input label="Phone" type="tel" required />
            <Input label="Department" required />
            <Input label="Position" required />
            <Input label="Hire Date" type="date" required />
            <Input label="Salary" type="number" required />
          </div>
          <div className="flex gap-3 justify-end">
            <Button variant="outline" onClick={() => setShowAddModal(false)}>
              Cancel
            </Button>
            <Button type="submit">Add Employee</Button>
          </div>
        </form>
      </Modal>
    </div>
  );
};
