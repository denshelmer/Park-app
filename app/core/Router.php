<?php
/**
 * Clase Router
 * Maneja el registro y despacho de rutas HTTP
 */
class Router {
    private array $routes = [];

    public function get(string $path, string $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void {
        $cleanPath = trim($path, '/');
        $this->routes[$method][$cleanPath] = $handler;
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_GET['url'] ?? '';
        $uri = trim($uri, '/');

        // Soporte alternativo por parámetros c (controller) y a (action)
        if (empty($uri) && isset($_GET['c'])) {
            $controllerName = ucfirst($_GET['c']) . 'Controller';
            $action = $_GET['a'] ?? 'index';
            $this->executeHandler("{$controllerName}@{$action}");
            return;
        }

        if (isset($this->routes[$method][$uri])) {
            $this->executeHandler($this->routes[$method][$uri]);
            return;
        }

        // Si la ruta no existe (404)
        http_response_code(404);
        echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>";
        echo "<h2>404 - Página no encontrada</h2>";
        echo "<p>La ruta solicitada <code>/{$uri}</code> no existe en el sistema.</p>";
        echo "<a href='" . BASE_URL . "/'>Volver al inicio</a>";
        echo "</div>";
    }

    private function executeHandler(string $handler): void {
        [$controllerName, $action] = explode('@', $handler);
        $controllerFile = APP_PATH . "/controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            die("Error: El controlador '{$controllerName}' no fue encontrado en: {$controllerFile}");
        }

        require_once $controllerFile;
        $controller = new $controllerName();

        if (!method_exists($controller, $action)) {
            die("Error: El método '{$action}' no existe en el controlador '{$controllerName}'.");
        }

        $controller->$action();
    }
}
