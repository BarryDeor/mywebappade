<?php

declare(strict_types=1);

namespace HRPayroll\UserAccessManagement\Controller;

use HRPayroll\Shared\Application\Route;
use HRPayroll\Shared\Security\JWTManager;
use HRPayroll\Shared\Security\RBACManager;
use HRPayroll\UserAccessManagement\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentication Controller - OAuth 2.0 / OpenID Connect
 */
class AuthController
{
    private UserRepository $userRepository;
    private JWTManager $jwtManager;
    private RBACManager $rbacManager;

    public function __construct(
        UserRepository $userRepository,
        JWTManager $jwtManager,
        RBACManager $rbacManager
    ) {
        $this->userRepository = $userRepository;
        $this->jwtManager = $jwtManager;
        $this->rbacManager = $rbacManager;
    }

    /**
     * User login
     */
    #[Route('/api/v1/auth/login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse([
                'error' => 'Email and password are required'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->userRepository->findByEmail($data['email']);

            if (!$user) {
                return new JsonResponse([
                    'error' => 'Invalid credentials'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Verify password
            if (!password_verify($data['password'], $user['password_hash'])) {
                return new JsonResponse([
                    'error' => 'Invalid credentials'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Check if user is active
            if ($user['status'] !== 'ACTIVE') {
                return new JsonResponse([
                    'error' => 'Account is not active'
                ], Response::HTTP_FORBIDDEN);
            }

            // Generate tokens
            $accessToken = $this->jwtManager->generateAccessToken(
                $user['id'],
                $user['tenant_id'],
                $user['roles']
            );

            $refreshToken = $this->jwtManager->generateRefreshToken(
                $user['id'],
                $user['tenant_id']
            );

            // Update last login
            $this->userRepository->updateLastLogin($user['id']);

            return new JsonResponse([
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'firstName' => $user['first_name'],
                    'lastName' => $user['last_name'],
                    'roles' => $user['roles'],
                    'tenantId' => $user['tenant_id']
                ]
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Authentication failed'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Refresh access token
     */
    #[Route('/api/v1/auth/refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['refresh_token'])) {
            return new JsonResponse([
                'error' => 'Refresh token is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $payload = $this->jwtManager->validateToken($data['refresh_token']);

            if ($payload['type'] !== 'refresh') {
                return new JsonResponse([
                    'error' => 'Invalid token type'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Get user to fetch current roles
            $user = $this->userRepository->findById($payload['sub']);

            if (!$user || $user['status'] !== 'ACTIVE') {
                return new JsonResponse([
                    'error' => 'User not found or inactive'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Generate new access token
            $accessToken = $this->jwtManager->generateAccessToken(
                $user['id'],
                $user['tenant_id'],
                $user['roles']
            );

            return new JsonResponse([
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => 3600
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Invalid refresh token'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * User logout
     */
    #[Route('/api/v1/auth/logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        // In a production system, you would:
        // 1. Blacklist the token in Redis
        // 2. Clear any session data
        // 3. Log the logout event

        $authHeader = $request->headers->get('Authorization');

        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);

            try {
                $payload = $this->jwtManager->validateToken($token);
                // Add token to blacklist (implement in production)
                // $this->tokenBlacklist->add($token, $payload['exp']);
            } catch (\Exception $e) {
                // Token already invalid
            }
        }

        return new JsonResponse([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Verify token and get user info
     */
    #[Route('/api/v1/auth/me', methods: ['GET'])]
    public function me(Request $request): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse([
                'error' => 'Authorization header missing'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = substr($authHeader, 7);

        try {
            $payload = $this->jwtManager->validateToken($token);
            $user = $this->userRepository->findById($payload['sub']);

            if (!$user) {
                return new JsonResponse([
                    'error' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'id' => $user['id'],
                'email' => $user['email'],
                'firstName' => $user['first_name'],
                'lastName' => $user['last_name'],
                'roles' => $user['roles'],
                'tenantId' => $user['tenant_id'],
                'permissions' => $this->getUserPermissions($user['roles'])
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Invalid token'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Check permission
     */
    #[Route('/api/v1/auth/check-permission', methods: ['POST'])]
    public function checkPermission(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['permission'])) {
            return new JsonResponse([
                'error' => 'Permission is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse([
                'error' => 'Authorization header missing'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = substr($authHeader, 7);

        try {
            $roles = $this->jwtManager->getRolesFromToken($token);
            $hasPermission = $this->rbacManager->hasPermission($roles, $data['permission']);

            return new JsonResponse([
                'hasPermission' => $hasPermission,
                'permission' => $data['permission'],
                'roles' => $roles
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Invalid token'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Get all permissions for user's roles
     */
    private function getUserPermissions(array $roles): array
    {
        $permissions = [];

        foreach ($roles as $role) {
            $rolePermissions = $this->rbacManager->getRolePermissions($role);
            $permissions = array_merge($permissions, $rolePermissions);
        }

        return array_unique($permissions);
    }
}
