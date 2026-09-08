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
}
