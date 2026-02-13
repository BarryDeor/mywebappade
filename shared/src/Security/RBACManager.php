<?php

declare(strict_types=1);

namespace HRPayroll\Shared\Security;

/**
 * Role-Based Access Control Manager
 */
class RBACManager
{
    private array $rolePermissions = [];

    public function __construct()
    {
        $this->initializeDefaultRoles();
    }

    /**
     * Initialize default role permissions
     */
    private function initializeDefaultRoles(): void
    {
        $this->rolePermissions = [
            'SUPER_ADMIN' => ['*'],
            'HR_ADMIN' => [
                'employees:*',
                'recruitment:*',
                'performance:*',
                'reports:view',
                'reports:export'
            ],
            'PAYROLL_MANAGER' => [
                'payroll:*',
                'benefits:*',
                'tax:*',
                'employees:read',
                'reports:view',
                'reports:export'
            ],
            'DEPARTMENT_MANAGER' => [
                'employees:read',
                'employees:update',
                'performance:read',
                'performance:write',
                'time-attendance:read',
                'time-attendance:approve',
                'reports:view'
            ],
            'EMPLOYEE' => [
                'employees:read-self',
                'employees:update-self',
                'time-attendance:read-self',
                'time-attendance:write-self',
                'payroll:read-self',
                'benefits:read-self',
                'benefits:enroll',
                'performance:read-self'
            ],
            'AUDITOR' => [
                'employees:read',
                'payroll:read',
                'tax:read',
                'compliance:read',
                'reports:view',
                'reports:export',
                'audit-logs:read'
            ]
        ];
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(array $userRoles, string $permission): bool
    {
        foreach ($userRoles as $role) {
            if (!isset($this->rolePermissions[$role])) {
                continue;
            }

            $permissions = $this->rolePermissions[$role];

            // Check for wildcard permission
            if (in_array('*', $permissions, true)) {
                return true;
            }

            // Check for exact permission match
            if (in_array($permission, $permissions, true)) {
                return true;
            }

            // Check for wildcard resource permission (e.g., employees:*)
            [$resource, $action] = explode(':', $permission);
            if (in_array($resource . ':*', $permissions, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add custom role
     */
    public function addRole(string $role, array $permissions): void
    {
        $this->rolePermissions[$role] = $permissions;
    }

    /**
     * Add permission to role
     */
    public function addPermissionToRole(string $role, string $permission): void
    {
        if (!isset($this->rolePermissions[$role])) {
            $this->rolePermissions[$role] = [];
        }

        if (!in_array($permission, $this->rolePermissions[$role], true)) {
            $this->rolePermissions[$role][] = $permission;
        }
    }

    /**
     * Remove permission from role
     */
    public function removePermissionFromRole(string $role, string $permission): void
    {
        if (!isset($this->rolePermissions[$role])) {
            return;
        }

        $this->rolePermissions[$role] = array_filter(
            $this->rolePermissions[$role],
            fn($p) => $p !== $permission
        );
    }

    /**
     * Get all permissions for a role
     */
    public function getRolePermissions(string $role): array
    {
        return $this->rolePermissions[$role] ?? [];
    }

    /**
     * Get all roles
     */
    public function getAllRoles(): array
    {
        return array_keys($this->rolePermissions);
    }
}
