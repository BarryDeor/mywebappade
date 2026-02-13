<?php

declare(strict_types=1);

namespace HRPayroll\Shared\Application;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lightweight Microservice Application Kernel
 * 
 * Provides automatic controller discovery and route matching
 * using PHP 8 #[Route] attributes. Each microservice only needs
 * a minimal public/index.php to bootstrap this.
 * 
 * Usage:
 *   $app = new MicroKernel('service-name', [
 *       'db' => ['host' => 'postgres', 'port' => 5432, ...],
 *   ]);
 *   $app->registerControllers([MyController::class]);
 *   $app->run();
 */
class MicroKernel
{
    private string $serviceName;
    private array $config;
    private array $routes = [];
    private array $controllerInstances = [];
    private ?\PDO $pdo = null;

    public function __construct(string $serviceName, array $config = [])
    {
        $this->serviceName = $serviceName;
        $this->config = $config;
    }

    /**
     * Get the database connection (lazy-initialized)
     */
    public function getDatabase(): \PDO
    {
        if ($this->pdo === null) {
            $db = $this->config['db'] ?? [];
            $host = $db['host'] ?? (getenv('DB_HOST') ?: 'postgres');
            $port = $db['port'] ?? (getenv('DB_PORT') ?: '5432');
            $name = $db['name'] ?? (getenv('DB_NAME') ?: 'postgres');
            $user = $db['user'] ?? (getenv('DB_USER') ?: 'postgres');
            $pass = $db['password'] ?? (getenv('DB_PASSWORD') ?: 'postgres');

            $dsn = "pgsql:host=$host;port=$port;dbname=$name";
            $this->pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
        }

        return $this->pdo;
    }

    /**
     * Get a config value
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Register controller classes - routes are auto-discovered from #[Route] attributes
     */
    public function registerControllers(array $controllers): void
    {
        foreach ($controllers as $controllerClass) {
            $reflectionClass = new \ReflectionClass($controllerClass);

            foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $attributes = $method->getAttributes(Route::class);

                foreach ($attributes as $attribute) {
                    $route = $attribute->newInstance();
                    $this->routes[] = [
                        'path' => $route->path,
                        'methods' => $route->methods,
                        'controller' => $controllerClass,
                        'action' => $method->getName(),
                        'pattern' => $this->buildPattern($route->path),
                    ];
                }
            }
        }
    }

    /**
     * Register a pre-built controller instance (for dependency injection)
     */
    public function registerControllerInstance(string $className, object $instance): void
    {
        $this->controllerInstances[$className] = $instance;
    }

    /**
     * Run the application - match request to route and dispatch
     */
    public function run(): void
    {
        // CORS headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $request = Request::createFromGlobals();

        try {
            $response = $this->handleRequest($request);
        } catch (\Throwable $e) {
            $response = new JsonResponse([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response->send();
    }

    /**
     * Handle the incoming request
     */
    private function handleRequest(Request $request): Response
    {
        $path = $request->getPathInfo();
        $method = $request->getMethod();

        // Health check (built-in for every service)
        if ($path === '/health') {
            return new JsonResponse([
                'status' => 'healthy',
                'service' => $this->serviceName,
                'timestamp' => date('c'),
            ]);
        }

        // Match route
        foreach ($this->routes as $route) {
            if (!in_array($method, $route['methods'])) {
                continue;
            }

            $params = [];
            if (preg_match($route['pattern'], $path, $params)) {
                // Filter out numeric keys from regex matches
                $params = array_filter($params, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);

                return $this->dispatch($route, $request, $params);
            }
        }

        return new JsonResponse([
            'error' => 'Route not found',
            'path' => $path,
            'method' => $method,
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Dispatch to the controller action
     */
    private function dispatch(array $route, Request $request, array $params): Response
    {
        $controllerClass = $route['controller'];
        $action = $route['action'];

        // Get or create controller instance
        $controller = $this->controllerInstances[$controllerClass] ?? null;

        if ($controller === null) {
            throw new \RuntimeException("Controller $controllerClass not registered. Use registerControllerInstance().");
        }

        // Build arguments: pass Request first, then route params
        $args = [$request];
        $reflection = new \ReflectionMethod($controller, $action);

        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            if ($param->getType()?->getName() === Request::class) {
                continue; // Already handled
            }
            if (isset($params[$name])) {
                $args[] = $params[$name];
            }
        }

        // Call with just Request if method only takes Request, otherwise include route params
        if (count($reflection->getParameters()) === 1) {
            return $controller->$action($request);
        }

        // For methods like getById(string $id, Request $request) or terminate(string $id, Request $request)
        $methodParams = $reflection->getParameters();
        $callArgs = [];
        foreach ($methodParams as $param) {
            $name = $param->getName();
            $type = $param->getType()?->getName();

            if ($type === Request::class) {
                $callArgs[] = $request;
            } elseif (isset($params[$name])) {
                $callArgs[] = $params[$name];
            }
        }

        return $controller->$action(...$callArgs);
    }

    /**
     * Convert a route path like /api/v1/employees/{id} to a regex pattern
     */
    private function buildPattern(string $path): string
    {
        $pattern = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
