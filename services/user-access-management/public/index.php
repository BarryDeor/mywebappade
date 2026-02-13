<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use HRPayroll\Shared\Application\MicroKernel;
use HRPayroll\UserAccessManagement\Controller\AuthController;
use HRPayroll\UserAccessManagement\Repository\UserRepository;
use HRPayroll\Shared\Security\JWTManager;
use HRPayroll\Shared\Security\RBACManager;

// Boot the microservice
$app = new MicroKernel('user-access-management');

// Auto-migrate: create tables if they don't exist
$db = $app->getDatabase();
$db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id VARCHAR(36) PRIMARY KEY,
        tenant_id VARCHAR(36) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        roles JSONB DEFAULT '[]',
        status VARCHAR(20) DEFAULT 'ACTIVE',
        metadata JSONB DEFAULT '{}',
        last_login_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
    CREATE INDEX IF NOT EXISTS idx_users_tenant ON users(tenant_id);
");

// Seed default admin if no users exist
$count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ((int) $count === 0) {
    $stmt = $db->prepare("
        INSERT INTO users (id, tenant_id, email, password_hash, first_name, last_name, roles, status, metadata, created_at, updated_at)
        VALUES (:id, :tenant_id, :email, :password_hash, :first_name, :last_name, :roles, 'ACTIVE', '{}', NOW(), NOW())
    ");
    $stmt->execute([
        'id' => 'admin-' . bin2hex(random_bytes(12)),
        'tenant_id' => 'default-tenant',
        'email' => 'admin@hrpayroll.com',
        'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
        'first_name' => 'Admin',
        'last_name' => 'User',
        'roles' => json_encode(['SUPER_ADMIN']),
    ]);
}

// Wire up dependencies and register controllers
$userRepository = new UserRepository($db);
$jwtManager = new JWTManager(getenv('JWT_SECRET') ?: 'default-jwt-secret-key-for-development');
$rbacManager = new RBACManager();

$app->registerControllerInstance(AuthController::class, new AuthController($userRepository, $jwtManager, $rbacManager));
$app->registerControllers([AuthController::class]);

// Run!
$app->run();
