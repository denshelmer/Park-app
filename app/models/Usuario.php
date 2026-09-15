<?php
/**
 * Modelo Usuario
 * Mapeo de tablas: USUARIOS y ROLES
 */
require_once APP_PATH . '/core/Model.php';

class Usuario extends Model {
    protected string $table = 'USUARIOS';

    public function findByEmail(string $email): ?array {
        $sql = "SELECT u.*, r.nombre_rol 
                FROM [USUARIOS] u 
                INNER JOIN [ROLES] r ON u.id_rol = r.id_rol 
                WHERE u.email = ?";
        return $this->queryOne($sql, [$email]);
    }

    public function findById(int $id): ?array {
        $sql = "SELECT u.*, r.nombre_rol 
                FROM [USUARIOS] u 
                INNER JOIN [ROLES] r ON u.id_rol = r.id_rol 
                WHERE u.id_usuario = ?";
        return $this->queryOne($sql, [$id]);
    }

    public function getByRole(int $idRol): array {
        $sql = "SELECT * FROM [USUARIOS] WHERE id_rol = ? ORDER BY nombre_completo";
        return $this->query($sql, [$idRol]);
    }

    public function emailExiste(string $email, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) AS total FROM [USUARIOS] WHERE email = ? AND id_usuario <> ?";
        $res = $this->queryOne($sql, [trim($email), $excludeId]);
        return ($res && (int)$res['total'] > 0);
    }

    public function crearUsuario(array $datos): int|bool {
        $id = $this->getNextId('id_usuario');
        $sql = "INSERT INTO [USUARIOS] (id_usuario, id_rol, nombre_completo, ci_nit, telefono, email, password_hash, estado, fecha_registro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, True, Now())";

        $ok = $this->execute($sql, [
            $id,
            (int)($datos['id_rol'] ?? 3),
            trim($datos['nombre_completo'] ?? ''),
            trim($datos['ci_nit'] ?? ''),
            trim($datos['telefono'] ?? ''),
            trim(strtolower($datos['email'] ?? '')),
            $datos['password_hash'] ?? ''
        ]);

        return $ok ? $id : false;
    }

    public function actualizarPassword(int $idUsuario, string $nuevoHash): bool {
        $sql = "UPDATE [USUARIOS] SET password_hash = ? WHERE id_usuario = ?";
        return $this->execute($sql, [$nuevoHash, $idUsuario]);
    }
}
