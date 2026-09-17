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

    /**
     * Registra un nuevo cajón/espacio en el parqueo
     */
    public function crearEspacio(array $datos): int|bool {
        $id = $this->getNextId('id_espacio');
        $sql = "INSERT INTO [ESPACIOS] (
                    id_espacio, id_parqueo, id_tipo_vehiculo, 
                    codigo_espacio, piso_sector, estado
                ) VALUES (?, ?, ?, ?, ?, ?)";

        $ok = $this->execute($sql, [
            $id,
            (int)$datos['id_parqueo'],
            (int)$datos['id_tipo_vehiculo'],
            trim(strtoupper($datos['codigo_espacio'])),
            trim($datos['piso_sector']),
            $datos['estado'] ?? 'Disponible'
        ]);

        return $ok ? $id : false;
    }

    /**
     * Verifica si ya existe un código de espacio registrado en el mismo parqueo
     */
    public function codigoExisteEnParqueo(int $idParqueo, string $codigo, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) AS total FROM [ESPACIOS] 
                WHERE id_parqueo = ? AND codigo_espacio = ? AND id_espacio <> ?";
        $res = $this->queryOne($sql, [$idParqueo, trim(strtoupper($codigo)), $excludeId]);
        return ($res && (int)$res['total'] > 0);
    }

    /**
     * Actualiza la información de un espacio
     */
    public function actualizarEspacio(int $id, array $datos): bool {
        $sql = "UPDATE [ESPACIOS] 
                SET id_tipo_vehiculo = ?, codigo_espacio = ?, piso_sector = ?, estado = ? 
                WHERE id_espacio = ?";
        return $this->execute($sql, [
            (int)$datos['id_tipo_vehiculo'],
            trim(strtoupper($datos['codigo_espacio'])),
            trim($datos['piso_sector']),
            $datos['estado'],
            $id
        ]);
    }

    /**
     * Obtiene el conteo de espacios agrupados por estado para un parqueo
     */
    public function getConteoPorEstado(int $idParqueo = 0): array {
        $espacios = $idParqueo > 0 ? $this->getPorParqueo($idParqueo) : $this->query("SELECT * FROM [ESPACIOS]");
        $conteos = [
            'total' => count($espacios),
            'disponibles' => 0,
            'ocupados' => 0,
            'reservados' => 0,
            'mantenimiento' => 0
        ];
        foreach ($espacios as $e) {
            $estado = strtolower(trim($e['estado']));
            if ($estado === 'disponible') $conteos['disponibles']++;
            elseif ($estado === 'ocupado') $conteos['ocupados']++;
            elseif ($estado === 'reservado') $conteos['reservados']++;
            elseif ($estado === 'mantenimiento') $conteos['mantenimiento']++;
        }
        return $conteos;
    }
}
