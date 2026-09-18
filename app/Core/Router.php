<?php

namespace App\Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->routes['GET'][$path] = ['handler' => $handler, 'middleware' => $middleware];
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->routes['POST'][$path] = ['handler' => $handler, 'middleware' => $middleware];
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $basePath = self::basePath();
        if ($basePath !== '' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . trim($uri, '/');
        $method = strtoupper($method);

        foreach ($this->routes[$method] ?? [] as $path => $route) {
            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $path);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                foreach ($route['middleware'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $middleware->handle();
                }

                [$controllerClass, $action] = $route['handler'];
                $controller = new $controllerClass();
                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }

        http_response_code(404);
        View::render('errors/404');
    }

    public static function basePath(): string
    {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        return $scriptDir === '/' ? '' : $scriptDir;
    }

    public static function url(string $path = ''): string
    {
        return BASE_URL . '/' . ltrim($path, '/');
    }
}
