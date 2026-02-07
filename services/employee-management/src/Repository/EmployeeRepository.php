<?php

declare(strict_types=1);

namespace HRPayroll\EmployeeManagement\Repository;

use HRPayroll\EmployeeManagement\Entity\Employee;
use PDO;

/**
 * Employee Repository for database operations
 */
class EmployeeRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Save employee to database
     */
    public function save(Employee $employee): void
    {
        $sql = "INSERT INTO employees (
            id, tenant_id, employee_number, first_name, last_name, email, phone,
            date_of_birth, hire_date, termination_date, department_id, job_title,
            employment_type, status, location, manager_id, metadata, created_at, updated_at
        ) VALUES (
            :id, :tenant_id, :employee_number, :first_name, :last_name, :email, :phone,
            :date_of_birth, :hire_date, :termination_date, :department_id, :job_title,
            :employment_type, :status, :location, :manager_id, :metadata, :created_at, :updated_at
        ) ON CONFLICT (id) DO UPDATE SET
            first_name = EXCLUDED.first_name,
            last_name = EXCLUDED.last_name,
            email = EXCLUDED.email,
            phone = EXCLUDED.phone,
            department_id = EXCLUDED.department_id,
            job_title = EXCLUDED.job_title,
            employment_type = EXCLUDED.employment_type,
            status = EXCLUDED.status,
            location = EXCLUDED.location,
            manager_id = EXCLUDED.manager_id,
            metadata = EXCLUDED.metadata,
            termination_date = EXCLUDED.termination_date,
            updated_at = EXCLUDED.updated_at";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'id' => $employee->getId(),
            'tenant_id' => $employee->getTenantId(),
            'employee_number' => $employee->getEmployeeNumber(),
            'first_name' => $employee->getFirstName(),
            'last_name' => $employee->getLastName(),
            'email' => $employee->getEmail(),
            'phone' => $employee->getPhone(),
            'date_of_birth' => $employee->getDateOfBirth()->format('Y-m-d'),
            'hire_date' => $employee->getHireDate()->format('Y-m-d'),
            'termination_date' => $employee->getTerminationDate()?->format('Y-m-d'),
            'department_id' => $employee->getDepartmentId(),
            'job_title' => $employee->getJobTitle(),
            'employment_type' => $employee->getEmploymentType(),
            'status' => $employee->getStatus(),
            'location' => json_encode($employee->getLocation()),
            'manager_id' => $employee->getManagerId(),
            'metadata' => json_encode($employee->getMetadata()),
            'created_at' => $employee->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $employee->getUpdatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Find employee by ID
     */
    public function findById(string $id): ?Employee
    {
        $sql = "SELECT * FROM employees WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Find employees by tenant ID
     */
    public function findByTenantId(string $tenantId, int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT * FROM employees WHERE tenant_id = :tenant_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('tenant_id', $tenantId);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $employees = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $employees[] = $this->hydrate($row);
        }
        
        return $employees;
    }

    /**
     * Find employees by department
     */
    public function findByDepartment(string $tenantId, string $departmentId): array
    {
        $sql = "SELECT * FROM employees WHERE tenant_id = :tenant_id AND department_id = :department_id AND status = 'ACTIVE'";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'tenant_id' => $tenantId,
            'department_id' => $departmentId
        ]);
        
        $employees = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $employees[] = $this->hydrate($row);
        }
        
        return $employees;
    }

    /**
     * Find active employees
     */
    public function findActiveEmployees(string $tenantId): array
    {
        $sql = "SELECT * FROM employees WHERE tenant_id = :tenant_id AND status = 'ACTIVE' ORDER BY employee_number";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['tenant_id' => $tenantId]);
        
        $employees = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $employees[] = $this->hydrate($row);
        }
        
        return $employees;
    }

    /**
     * Delete employee
     */
    public function delete(string $id): void
    {
        $sql = "DELETE FROM employees WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    /**
     * Hydrate employee from database row
     */
    private function hydrate(array $row): Employee
    {
        $employee = new Employee(
            $row['id'],
            $row['tenant_id'],
            $row['employee_number'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            new \DateTimeImmutable($row['date_of_birth']),
            new \DateTimeImmutable($row['hire_date']),
            $row['department_id'],
            $row['job_title'],
            $row['employment_type'],
            json_decode($row['location'], true)
        );

        if ($row['phone']) {
            $employee->setPhone($row['phone']);
        }

        if ($row['manager_id']) {
            $employee->setManagerId($row['manager_id']);
        }

        $employee->setStatus($row['status']);

        if ($row['termination_date']) {
            $employee->terminate(new \DateTimeImmutable($row['termination_date']));
        }

        $metadata = json_decode($row['metadata'], true);
        foreach ($metadata as $key => $value) {
            $employee->addMetadata($key, $value);
        }

        return $employee;
    }
}
