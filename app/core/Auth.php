<?php
/**
 * Clase Auth
 * Helper para manejo de sesión y control de accesos por roles
 */
class Auth {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool {
        self::start();
        return isset($_SESSION['usuario_id']);
    }

    public static function user(): ?array {
        self::start();
        return $_SESSION['usuario'] ?? null;
    }

    public static function id(): ?int {
        self::start();
        return $_SESSION['usuario_id'] ?? null;
    }

    public static function roleId(): ?int {
        self::start();
        return $_SESSION['rol_id'] ?? null;
    }

    public static function roleName(): ?string {
        self::start();
        return $_SESSION['rol_nombre'] ?? null;
    }

    public static function login(array $usuario): void {
        self::start();
        $_SESSION['usuario_id'] = (int)$usuario['id_usuario'];
        $_SESSION['rol_id'] = (int)$usuario['id_rol'];
        $_SESSION['rol_nombre'] = $usuario['nombre_rol'] ?? '';
        $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario'] = $usuario;
    }

    public static function logout(): void {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Requiere que el usuario esté autenticado con un rol específico
     * @param array|int $allowedRoles IDs de roles permitidos (1=Admin, 2=Operador, 3=Conductor)
     */
    public static function requireRole($allowedRoles): void {
        self::start();
        if (!self::check()) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
        // El Administrador (rol 1) cuenta con acceso universal a todas las áreas del sistema
        if (self::roleId() !== 1 && !in_array(self::roleId(), $roles)) {
            http_response_code(403);
            die("<div style='font-family:sans-serif; text-align:center; padding:50px;'><h2>403 - Acceso Denegado</h2><p>No tienes permisos para acceder a esta sección.</p><a href='" . BASE_URL . "/'>Volver</a></div>");
        }
    }
}
