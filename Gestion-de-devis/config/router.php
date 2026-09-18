<?php
/**
 * src/Router.php
 * ---------------
 * Routeur HTTP minimaliste.
 * Mappe chaque combinaison méthode + chemin vers un contrôleur et une action.
 * Supporte les segments dynamiques (ex: /devis/{id}).
 */

namespace Biscaphone;

class Router
{
    /** @var array<array{method: string, pattern: string, controller: string, action: string}> */
    private array $routes = [];

    public function __construct()
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        $register = require __DIR__ . '/routes.php';
        $register($this);
    }

    public function add(string $method, string $pattern, string $controller, string $action): void
    {
        $this->routes[] = compact('method', 'pattern', 'controller', 'action');
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path   = '/' . trim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            $params = $this->match($route['method'], $route['pattern'], $method, $path);
            if ($params !== null) {
                $class = 'Biscaphone\\Controllers\\' . $route['controller'];
                $controller = new $class();
                if (!empty($params)) {
                    $controller->{$route['action']}($params);
                } else {
                    $controller->{$route['action']}();
                }
                return;
            }
        }

        http_response_code(404);
        echo '<h1>404 — Page introuvable</h1>';
    }

    private function match(string $routeMethod, string $pattern, string $method, string $path): ?array
    {
        if ($routeMethod !== $method) {
            return null;
        }

        $regex = preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '@^' . $regex . '$@';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }
}
