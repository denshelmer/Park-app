<?php
/**
 * Modelo TipoVehiculo
 * Mapeo de tabla: TIPOS_VEHICULO
 */
require_once APP_PATH . '/core/Model.php';

class TipoVehiculo extends Model {
    protected string $table = 'TIPOS_VEHICULO';

    public function getAllActivos(): array {
        $sql = "SELECT * FROM [TIPOS_VEHICULO] ORDER BY id_tipo_vehiculo";
        return $this->query($sql);
    }

    public function findById(int $idTipo): ?array {
        $sql = "SELECT * FROM [TIPOS_VEHICULO] WHERE id_tipo_vehiculo = ?";
        return $this->queryOne($sql, [$idTipo]);
    }
}
