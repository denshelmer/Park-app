<?php
/**
 * Clase Base Controller
 * Proporciona renderizado de vistas, redirección y respuestas JSON.
 */
class Controller {
    /**
     * Renderiza una vista dentro del layout estándar o de forma limpia
     */
    protected function render(string $view, array $data = [], bool $withLayout = true): void {
        extract($data);
        $viewFile = APP_PATH . "/views/{$view}.php";

        if (!file_exists($viewFile)) {
            die("Error: La vista '{$view}' no existe en: {$viewFile}");
        }

        if ($withLayout) {
            require_once APP_PATH . '/views/layouts/header.php';
            require_once APP_PATH . '/views/layouts/navbar.php';
            require_once $viewFile;
            require_once APP_PATH . '/views/layouts/footer.php';
        } else {
            require_once $viewFile;
        }
    }

    /**
     * Redirecciona a una ruta interna relativa
     */
    protected function redirect(string $path): void {
        $cleanPath = ltrim($path, '/');
        header("Location: " . BASE_URL . "/{$cleanPath}");
        exit;
    }

    /**
     * Retorna una respuesta en formato JSON (para peticiones AJAX / API)
     */
    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
