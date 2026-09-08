<?php
/**
 * Modelo Parqueo
 * Mapeo de tabla: PARQUEOS
 */
require_once APP_PATH . '/core/Model.php';

class Parqueo extends Model {
    protected string $table = 'PARQUEOS';

    public function getActivos(): array {
        $sql = "SELECT * FROM [PARQUEOS] WHERE estado = True ORDER BY nombre_parqueo";
        return $this->query($sql);
    }

    public function getConDetalle(int $idParqueo): ?array {
        $sql = "SELECT * FROM [PARQUEOS] WHERE id_parqueo = ?";
        return $this->queryOne($sql, [$idParqueo]);
    }
}
