<?php

declare(strict_types=1);

namespace HRPayroll\UserAccessManagement\Repository;

use PDO;

/**
 * User Repository
 */
class UserRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $user['roles'] = json_decode($user['roles'], true);
            $user['metadata'] = json_decode($user['metadata'], true);
        }
        
        return $user ?: null;
    }

    /**
     * Find user by ID
     */
    public function findById(string $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $user['roles'] = json_decode($user['roles'], true);
            $user['metadata'] = json_decode($user['metadata'], true);
        }
        
        return $user ?: null;
    }

    /**
     * Create new user
     */
    public function create(array $userData): void
    {
        $sql = "INSERT INTO users (
            id, tenant_id, email, password_hash, first_name, last_name,
            roles, status, metadata, created_at, updated_at
        ) VALUES (
            :id, :tenant_id, :email, :password_hash, :first_name, :last_name,
            :roles, :status, :metadata, :created_at, :updated_at
        )";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'id' => $userData['id'],
            'tenant_id' => $userData['tenant_id'],
            'email' => $userData['email'],
            'password_hash' => $userData['password_hash'],
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'roles' => json_encode($userData['roles']),
            'status' => $userData['status'] ?? 'ACTIVE',
            'metadata' => json_encode($userData['metadata'] ?? []),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Update user
     */
    public function update(string $id, array $updates): void
    {
        $allowedFields = ['first_name', 'last_name', 'roles', 'status', 'metadata'];
        $setClause = [];
        $params = ['id' => $id];

        foreach ($updates as $field => $value) {
            if (in_array($field, $allowedFields, true)) {
                $setClause[] = "$field = :$field";
                $params[$field] = is_array($value) ? json_encode($value) : $value;
            }
        }

        if (empty($setClause)) {
            return;
        }

        $setClause[] = "updated_at = :updated_at";
        $params['updated_at'] = date('Y-m-d H:i:s');

        $sql = "UPDATE users SET " . implode(', ', $setClause) . " WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(string $id): void
    {
        $sql = "UPDATE users SET last_login_at = :last_login_at WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'last_login_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Assign role to user
     */
    public function assignRole(string $userId, string $role): void
    {
        $user = $this->findById($userId);
        
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        $roles = $user['roles'];
        
        if (!in_array($role, $roles, true)) {
            $roles[] = $role;
            $this->update($userId, ['roles' => $roles]);
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole(string $userId, string $role): void
    {
        $user = $this->findById($userId);
        
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        $roles = array_filter($user['roles'], fn($r) => $r !== $role);
        $this->update($userId, ['roles' => array_values($roles)]);
    }

    /**
     * Delete user
     */
    public function delete(string $id): void
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
