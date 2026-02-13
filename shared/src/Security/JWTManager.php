<?php

declare(strict_types=1);

namespace HRPayroll\Shared\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * JWT Token Manager for authentication
 */
class JWTManager
{
    private string $secretKey;
    private string $algorithm;
    private int $accessTokenTTL;
    private int $refreshTokenTTL;

    public function __construct(
        string $secretKey,
        string $algorithm = 'HS256',
        int $accessTokenTTL = 3600,
        int $refreshTokenTTL = 604800
    ) {
        $this->secretKey = $secretKey;
        $this->algorithm = $algorithm;
        $this->accessTokenTTL = $accessTokenTTL;
        $this->refreshTokenTTL = $refreshTokenTTL;
    }

    /**
     * Generate access token
     */
    public function generateAccessToken(string $userId, string $tenantId, array $roles): string
    {
        $issuedAt = time();
        $expiresAt = $issuedAt + $this->accessTokenTTL;

        $payload = [
            'iss' => 'hr-payroll-system',
            'iat' => $issuedAt,
            'exp' => $expiresAt,
            'sub' => $userId,
            'tenant_id' => $tenantId,
            'roles' => $roles,
            'type' => 'access'
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Generate refresh token
     */
    public function generateRefreshToken(string $userId, string $tenantId): string
    {
        $issuedAt = time();
        $expiresAt = $issuedAt + $this->refreshTokenTTL;

        $payload = [
            'iss' => 'hr-payroll-system',
            'iat' => $issuedAt,
            'exp' => $expiresAt,
            'sub' => $userId,
            'tenant_id' => $tenantId,
            'type' => 'refresh'
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Validate and decode token
     */
    public function validateToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decoded;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid token: ' . $e->getMessage());
        }
    }

    /**
     * Check if token is expired
     */
    public function isTokenExpired(string $token): bool
    {
        try {
            $payload = $this->validateToken($token);
            return $payload['exp'] < time();
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Extract user ID from token
     */
    public function getUserIdFromToken(string $token): string
    {
        $payload = $this->validateToken($token);
        return $payload['sub'];
    }

    /**
     * Extract tenant ID from token
     */
    public function getTenantIdFromToken(string $token): string
    {
        $payload = $this->validateToken($token);
        return $payload['tenant_id'];
    }

    /**
     * Extract roles from token
     */
    public function getRolesFromToken(string $token): array
    {
        $payload = $this->validateToken($token);
        return $payload['roles'] ?? [];
    }
}
