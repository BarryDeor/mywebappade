<?php

declare(strict_types=1);

namespace HRPayroll\Shared\Application;

/**
 * Route attribute for controller methods.
 * 
 * Usage:
 *   #[Route('/api/v1/auth/login', methods: ['POST'])]
 *   public function login(Request $request): JsonResponse { ... }
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Route
{
    public function __construct(
        public readonly string $path,
        public readonly array $methods = ['GET'],
    ) {
    }
}
