<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $pattern, $handler): void { $this->add('GET', $pattern, $handler); }
    public function post(string $pattern, $handler): void { $this->add('POST', $pattern, $handler); }

    private function add(string $method, string $pattern, $handler): void
    {
        $pattern = '/' . trim($pattern, '/');
        if ($pattern === '//') $pattern = '/';
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        $this->routes[] = [$method, '#^' . $regex . '$#', $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $base = app_base_path();
        if ($base && str_starts_with($path, $base)) $path = substr($path, strlen($base)) ?: '/';
        $path = '/' . trim($path, '/');
        if ($path === '//') $path = '/';

        foreach ($this->routes as [$routeMethod, $regex, $handler]) {
            if ($method !== $routeMethod || !preg_match($regex, $path, $matches)) continue;
            $params = array_filter($matches, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY);
            if (is_array($handler)) {
                [$class, $action] = $handler;
                $controller = new $class();
                $controller->$action(...array_values($params));
            } else {
                $handler(...array_values($params));
            }
            return;
        }
        http_response_code(404);
        view('errors/404', ['title' => 'Página no encontrada']);
    }
}
