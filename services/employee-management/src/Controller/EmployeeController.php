<?php

declare(strict_types=1);

namespace HRPayroll\EmployeeManagement\Controller;

use HRPayroll\EmployeeManagement\Entity\Employee;
use HRPayroll\EmployeeManagement\Repository\EmployeeRepository;
use HRPayroll\Shared\Event\EventPublisher;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Employee Controller - REST API endpoints
 */
class EmployeeController
{
    private EmployeeRepository $repository;
    private EventPublisher $eventPublisher;

    public function __construct(EmployeeRepository $repository, EventPublisher $eventPublisher)
    {
        $this->repository = $repository;
        $this->eventPublisher = $eventPublisher;
    }

    /**
     * Create new employee
     * POST /api/v1/employees
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate required fields
        $requiredFields = ['tenantId', 'firstName', 'lastName', 'email', 'dateOfBirth', 'hireDate', 'departmentId', 'jobTitle', 'employmentType', 'location'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            $employeeId = Uuid::uuid4()->toString();
            $employeeNumber = $this->generateEmployeeNumber($data['tenantId']);

            $employee = new Employee(
                $employeeId,
                $data['tenantId'],
                $employeeNumber,
                $data['firstName'],
                $data['lastName'],
                $data['email'],
                new \DateTimeImmutable($data['dateOfBirth']),
                new \DateTimeImmutable($data['hireDate']),
                $data['departmentId'],
                $data['jobTitle'],
                $data['employmentType'],
                $data['location']
            );

            if (isset($data['phone'])) {
                $employee->setPhone($data['phone']);
            }

            if (isset($data['managerId'])) {
                $employee->setManagerId($data['managerId']);
            }

            $this->repository->save($employee);

            // Publish employee.created event
            $this->eventPublisher->publish('employee.created', [
                'tenantId' => $employee->getTenantId(),
                'employeeId' => $employee->getId(),
                'employeeNumber' => $employee->getEmployeeNumber(),
                'firstName' => $employee->getFirstName(),
                'lastName' => $employee->getLastName(),
                'email' => $employee->getEmail(),
                'departmentId' => $employee->getDepartmentId(),
                'hireDate' => $employee->getHireDate()->format('Y-m-d')
            ]);

            return new JsonResponse([
                'id' => $employee->getId(),
                'employeeNumber' => $employee->getEmployeeNumber(),
                'status' => $employee->getStatus(),
                'createdAt' => $employee->getCreatedAt()->format(\DateTimeInterface::RFC3339)
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get employee by ID
     * GET /api/v1/employees/{id}
     */
    public function getById(string $id): JsonResponse
    {
        $employee = $this->repository->findById($id);

        if (!$employee) {
            return new JsonResponse(['error' => 'Employee not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($employee->toArray());
    }

    /**
     * List employees
     * GET /api/v1/employees?tenantId=xxx&limit=100&offset=0
     */
    public function list(Request $request): JsonResponse
    {
        $tenantId = $request->query->get('tenantId');
        $limit = (int) $request->query->get('limit', 100);
        $offset = (int) $request->query->get('offset', 0);

        if (!$tenantId) {
            return new JsonResponse(['error' => 'tenantId is required'], Response::HTTP_BAD_REQUEST);
        }

        $employees = $this->repository->findByTenantId($tenantId, $limit, $offset);

        return new JsonResponse([
            'data' => array_map(fn($emp) => $emp->toArray(), $employees),
            'meta' => [
                'limit' => $limit,
                'offset' => $offset,
                'count' => count($employees)
            ]
        ]);
    }

    /**
     * Update employee
     * PUT /api/v1/employees/{id}
     */
    public function update(string $id, Request $request): JsonResponse
    {
        $employee = $this->repository->findById($id);

        if (!$employee) {
            return new JsonResponse(['error' => 'Employee not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        try {
            if (isset($data['phone'])) {
                $employee->setPhone($data['phone']);
            }

            if (isset($data['departmentId'])) {
                $employee->setDepartmentId($data['departmentId']);
            }

            if (isset($data['jobTitle'])) {
                $employee->setJobTitle($data['jobTitle']);
            }

            if (isset($data['managerId'])) {
                $employee->setManagerId($data['managerId']);
            }

            if (isset($data['location'])) {
                $employee->updateLocation($data['location']);
            }

            $this->repository->save($employee);

            // Publish employee.updated event
            $this->eventPublisher->publish('employee.updated', [
                'tenantId' => $employee->getTenantId(),
                'employeeId' => $employee->getId(),
                'changes' => $data
            ]);

            return new JsonResponse($employee->toArray());

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Terminate employee
     * POST /api/v1/employees/{id}/terminate
     */
    public function terminate(string $id, Request $request): JsonResponse
    {
        $employee = $this->repository->findById($id);

        if (!$employee) {
            return new JsonResponse(['error' => 'Employee not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $terminationDate = new \DateTimeImmutable($data['terminationDate'] ?? 'now');

        try {
            $employee->terminate($terminationDate);
            $this->repository->save($employee);

            // Publish employee.terminated event
            $this->eventPublisher->publish('employee.terminated', [
                'tenantId' => $employee->getTenantId(),
                'employeeId' => $employee->getId(),
                'terminationDate' => $terminationDate->format('Y-m-d')
            ]);

            return new JsonResponse($employee->toArray());

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate unique employee number
     */
    private function generateEmployeeNumber(string $tenantId): string
    {
        // Simple implementation - in production, use a more sophisticated approach
        return 'EMP' . strtoupper(substr($tenantId, 0, 3)) . str_pad((string)rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}
