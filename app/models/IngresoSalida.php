<?php
/**
 * Modelo IngresoSalida
 * Mapeo de tabla: INGRESOS_SALIDAS
 */
require_once APP_PATH . '/core/Model.php';

class IngresoSalida extends Model {
    protected string $table = 'INGRESOS_SALIDAS';

    public function getActivosEnParqueo(int $idParqueo): array {
        $sql = "SELECT i.*, e.codigo_espacio, e.piso_sector 
                FROM [INGRESOS_SALIDAS] i 
                INNER JOIN [ESPACIOS] e ON i.id_espacio = e.id_espacio 
                WHERE e.id_parqueo = ? AND i.estado_estancia = 'En Parqueo' 
                ORDER BY i.fecha_hora_entrada DESC";
        return $this->query($sql, [$idParqueo]);
    }

    public function findByTicket(string $numeroTicket): ?array {
        $sql = "SELECT i.*, e.codigo_espacio, e.id_parqueo, e.id_tipo_vehiculo 
                FROM [INGRESOS_SALIDAS] i 
                INNER JOIN [ESPACIOS] e ON i.id_espacio = e.id_espacio 
                WHERE i.numero_ticket = ? AND i.estado_estancia = 'En Parqueo'";
        return $this->queryOne($sql, [$numeroTicket]);
    }
}
