<?php
/**
 * Modelo Espacio
 * Mapeo de tabla: ESPACIOS y TIPOS_VEHICULO
 */
require_once APP_PATH . '/core/Model.php';

class Espacio extends Model {
    protected string $table = 'ESPACIOS';

    public function getPorParqueo(int $idParqueo): array {
        $sql = "SELECT e.*, tv.nombre_tipo 
                FROM [ESPACIOS] e 
                INNER JOIN [TIPOS_VEHICULO] tv ON e.id_tipo_vehiculo = tv.id_tipo_vehiculo 
                WHERE e.id_parqueo = ? 
                ORDER BY e.piso_sector, e.codigo_espacio";
        return $this->query($sql, [$idParqueo]);
    }

    public function getPorParqueoYTipo(int $idParqueo, ?int $idTipoVehiculo = null): array {
        if ($idTipoVehiculo !== null && $idTipoVehiculo > 0) {
            $sql = "SELECT e.*, tv.nombre_tipo 
                    FROM [ESPACIOS] e 
                    INNER JOIN [TIPOS_VEHICULO] tv ON e.id_tipo_vehiculo = tv.id_tipo_vehiculo 
                    WHERE e.id_parqueo = ? AND e.id_tipo_vehiculo = ? 
                    ORDER BY e.piso_sector, e.codigo_espacio";
            return $this->query($sql, [$idParqueo, $idTipoVehiculo]);
        }
        return $this->getPorParqueo($idParqueo);
    }

    public function getDisponiblesPorTipo(int $idParqueo, int $idTipoVehiculo): array {
        $sql = "SELECT * FROM [ESPACIOS] 
                WHERE id_parqueo = ? AND id_tipo_vehiculo = ? AND estado = 'Disponible'
                ORDER BY codigo_espacio";
        return $this->query($sql, [$idParqueo, $idTipoVehiculo]);
    }

    public function buscarPrimerDisponible(int $idParqueo, int $idTipoVehiculo): ?array {
        $sql = "SELECT TOP 1 * FROM [ESPACIOS] 
                WHERE id_parqueo = ? AND id_tipo_vehiculo = ? AND estado = 'Disponible' 
                ORDER BY codigo_espacio";
        return $this->queryOne($sql, [$idParqueo, $idTipoVehiculo]);
    }

    public function findById(int $idEspacio): ?array {
        $sql = "SELECT e.*, tv.nombre_tipo, p.nombre_parqueo, p.direccion 
                FROM ([ESPACIOS] e 
                INNER JOIN [TIPOS_VEHICULO] tv ON e.id_tipo_vehiculo = tv.id_tipo_vehiculo)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo 
                WHERE e.id_espacio = ?";
        return $this->queryOne($sql, [$idEspacio]);
    }

    public function actualizarEstado(int $idEspacio, string $nuevoEstado): bool {
        $sql = "UPDATE [ESPACIOS] SET estado = ? WHERE id_espacio = ?";
        return $this->execute($sql, [$nuevoEstado, $idEspacio]);
    }
}
