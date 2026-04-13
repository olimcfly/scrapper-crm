<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: callable}> */
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $this->convertPattern($pattern),
            'handler' => $handler,
        ];
    }

    private function convertPattern(string $pattern): string
    {
        // Transforme /user/{id} → regex
        $pattern = preg_replace(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            '(?P<$1>[^/]+)',
            $pattern
        );

        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request): void
    {
        $path = $this->normalizePath($request->path());
        $method = strtoupper($request->method());

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            $params = $this->extractParams($matches);

            try {
                ($route['handler'])($request, $params);
            } catch (\Throwable $e) {
    http_response_code(500);
    echo '<pre>';
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n\n";
    echo $e->getTraceAsString();
    echo '</pre>';
    exit;
}

            return;
        }

        $this->notFound();
    }

    private function normalizePath(string $path): string
    {
        // Supprime trailing slash sauf racine
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }

    private function extractParams(array $matches): array
    {
        return array_filter(
            $matches,
            static fn ($key) => !is_int($key),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '404 - Page non trouvée';
    }
}