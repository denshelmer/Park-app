<?php
/**
 * Modelo Vehiculo
 * Mapeo de tabla: VEHICULOS y TIPOS_VEHICULO
 */
require_once APP_PATH . '/core/Model.php';

class Vehiculo extends Model {
    protected string $table = 'VEHICULOS';

    public function getPorUsuario(int $idUsuario): array {
        $sql = "SELECT v.*, tv.nombre_tipo 
                FROM [VEHICULOS] v 
                INNER JOIN [TIPOS_VEHICULO] tv ON v.id_tipo_vehiculo = tv.id_tipo_vehiculo 
                WHERE v.id_usuario = ? 
                ORDER BY v.placa";
        return $this->query($sql, [$idUsuario]);
    }

    public function findByPlaca(string $placa): ?array {
        $sql = "SELECT * FROM [VEHICULOS] WHERE placa = ?";
        return $this->queryOne($sql, [$placa]);
    }
}
