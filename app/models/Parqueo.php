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

    /**
     * Obtiene los parqueos activos con métricas de disponibilidad en tiempo real
     */
    public function getConDisponibilidad(): array {
        $parqueos = $this->getActivos();
        if (empty($parqueos)) {
            return [];
        }

        // Consultar conteo de estados por parqueo
        $sqlEstados = "SELECT id_parqueo, estado, COUNT(*) AS cantidad 
                       FROM [ESPACIOS] 
                       GROUP BY id_parqueo, estado";
        $filasEstados = $this->query($sqlEstados);

        // Agrupar conteos por id_parqueo
        $estadosPorParqueo = [];
        foreach ($filasEstados as $fila) {
            $pId = (int)$fila['id_parqueo'];
            $est = $fila['estado'];
            $cant = (int)$fila['cantidad'];
            if (!isset($estadosPorParqueo[$pId])) {
                $estadosPorParqueo[$pId] = [
                    'Disponible' => 0,
                    'Ocupado' => 0,
                    'Reservado' => 0,
                    'Mantenimiento' => 0,
                    'total' => 0
                ];
            }
            $estadosPorParqueo[$pId][$est] = $cant;
            $estadosPorParqueo[$pId]['total'] += $cant;
        }

        // Unir datos de disponibilidad a cada parqueo
        foreach ($parqueos as &$parqueo) {
            $pId = (int)$parqueo['id_parqueo'];
            $datosEspacio = $estadosPorParqueo[$pId] ?? [
                'Disponible' => 0,
                'Ocupado' => 0,
                'Reservado' => 0,
                'Mantenimiento' => 0,
                'total' => 0
            ];

            $parqueo['espacios_libres'] = $datosEspacio['Disponible'];
            $parqueo['espacios_ocupados'] = $datosEspacio['Ocupado'];
            $parqueo['espacios_reservados'] = $datosEspacio['Reservado'];
            $parqueo['espacios_mantenimiento'] = $datosEspacio['Mantenimiento'];
            $totalFisico = $datosEspacio['total'] > 0 ? $datosEspacio['total'] : (int)$parqueo['capacidad_total'];
            $parqueo['espacios_totales'] = $totalFisico;

            $ocupadosYReservados = $datosEspacio['Ocupado'] + $datosEspacio['Reservado'];
            $parqueo['porcentaje_ocupacion'] = $totalFisico > 0 ? round(($ocupadosYReservados / $totalFisico) * 100) : 0;
            $parqueo['tiene_disponibilidad'] = ($parqueo['espacios_libres'] > 0);
        }
        unset($parqueo);

        return $parqueos;
    }
}
