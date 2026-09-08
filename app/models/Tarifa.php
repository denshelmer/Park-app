<?php
/**
 * Modelo Tarifa
 * Mapeo de tabla: TARIFAS y TIPOS_VEHICULO
 */
require_once APP_PATH . '/core/Model.php';

class Tarifa extends Model {
    protected string $table = 'TARIFAS';

    public function getPorParqueo(int $idParqueo): array {
        $sql = "SELECT t.*, tv.nombre_tipo 
                FROM [TARIFAS] t 
                INNER JOIN [TIPOS_VEHICULO] tv ON t.id_tipo_vehiculo = tv.id_tipo_vehiculo 
                WHERE t.id_parqueo = ? AND t.vigente = True";
        return $this->query($sql, [$idParqueo]);
    }

    public function getTarifaEspecifica(int $idParqueo, int $idTipoVehiculo): ?array {
        $sql = "SELECT * FROM [TARIFAS] 
                WHERE id_parqueo = ? AND id_tipo_vehiculo = ? AND vigente = True";
        return $this->queryOne($sql, [$idParqueo, $idTipoVehiculo]);
    }
}
