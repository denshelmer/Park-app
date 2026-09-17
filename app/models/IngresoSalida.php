<?php
/**
 * Modelo IngresoSalida
 * Mapeo de tabla: INGRESOS_SALIDAS
 */
require_once APP_PATH . '/core/Model.php';

class IngresoSalida extends Model {
    protected string $table = 'INGRESOS_SALIDAS';

    /**
     * Obtiene los vehículos actualmente dentro del parqueo
     */
    public function getActivosEnParqueo(int $idParqueo): array {
        $sql = "SELECT i.*, e.codigo_espacio, e.piso_sector, e.id_tipo_vehiculo, tv.nombre_tipo 
                FROM ([INGRESOS_SALIDAS] i 
                INNER JOIN [ESPACIOS] e ON i.id_espacio = e.id_espacio)
                INNER JOIN [TIPOS_VEHICULO] tv ON e.id_tipo_vehiculo = tv.id_tipo_vehiculo 
                WHERE e.id_parqueo = ? AND i.estado_estancia = 'En Parqueo' 
                ORDER BY i.fecha_hora_entrada DESC";
        return $this->query($sql, [$idParqueo]);
    }

    /**
     * Busca una estancia activa por número de ticket o placa vehicular
     */
    public function buscarEstanciaActiva(string $termino, ?int $idParqueo = null): ?array {
        $termino = trim(strtoupper($termino));
        $params = [$termino, $termino];

        $sql = "SELECT i.*, e.codigo_espacio, e.piso_sector, e.id_parqueo, e.id_tipo_vehiculo, 
                       p.nombre_parqueo, p.direccion, p.zona, tv.nombre_tipo,
                       u.nombre_completo AS operador_entrada
                FROM ((([INGRESOS_SALIDAS] i 
                INNER JOIN [ESPACIOS] e ON i.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo)
                INNER JOIN [TIPOS_VEHICULO] tv ON e.id_tipo_vehiculo = tv.id_tipo_vehiculo)
                LEFT JOIN [USUARIOS] u ON i.id_operador_entrada = u.id_usuario 
                WHERE (i.numero_ticket = ? OR i.placa = ?) AND i.estado_estancia = 'En Parqueo'";

        if ($idParqueo !== null && $idParqueo > 0) {
            $sql .= " AND e.id_parqueo = ?";
            $params[] = $idParqueo;
        }

        return $this->queryOne($sql, $params);
    }

    /**
     * Registra un ingreso directo (sin reserva previa / walk-in)
     */
    public function crearIngresoDirecto(array $datos): int|bool {
        $id = $this->getNextId('id_ingreso_salida');
        $numTicket = 'TCK-' . date('Y') . '-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO [INGRESOS_SALIDAS] (
                    id_ingreso_salida, numero_ticket, id_espacio, id_operador_entrada, 
                    placa, fecha_hora_entrada, estado_estancia
                ) VALUES (?, ?, ?, ?, ?, Now(), 'En Parqueo')";

        $ok = $this->execute($sql, [
            $id,
            $numTicket,
            (int)$datos['id_espacio'],
            (int)$datos['id_operador_entrada'],
            trim(strtoupper($datos['placa']))
        ]);

        return $ok ? $id : false;
    }

    /**
     * Registra un ingreso validado desde código QR de reserva
     */
    public function crearIngresoDesdeReserva(int $idReserva, int $idEspacio, string $placa, int $idOperador): int|bool {
        $id = $this->getNextId('id_ingreso_salida');
        $numTicket = 'TCK-QR-' . date('Y') . '-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO [INGRESOS_SALIDAS] (
                    id_ingreso_salida, numero_ticket, id_reserva, id_espacio, 
                    id_operador_entrada, placa, fecha_hora_entrada, estado_estancia
                ) VALUES (?, ?, ?, ?, ?, ?, Now(), 'En Parqueo')";

        $ok = $this->execute($sql, [
            $id,
            $numTicket,
            $idReserva,
            $idEspacio,
            $idOperador,
            trim(strtoupper($placa))
        ]);

        return $ok ? $id : false;
    }

    /**
     * Finaliza la estancia de un vehículo registrando su salida y monto cobrado
     */
    public function finalizarEstanciaYCobro(int $idIngresoSalida, int $idOperadorSalida, int $minutosTotales, float $totalAPagar): bool {
        $sql = "UPDATE [INGRESOS_SALIDAS] 
                SET fecha_hora_salida = Now(), 
                    id_operador_salida = ?, 
                    minutos_totales = ?, 
                    total_a_pagar = ?, 
                    estado_estancia = 'Finalizado' 
                WHERE id_ingreso_salida = ?";
        return $this->execute($sql, [$idOperadorSalida, $minutosTotales, $totalAPagar, $idIngresoSalida]);
    }

    /**
     * Obtiene el detalle completo de una estancia (para comprobante o recibo)
     */
    public function getDetalleEstancia(int $idIngresoSalida): ?array {
        $sql = "SELECT i.*, e.codigo_espacio, e.piso_sector, e.id_parqueo, e.id_tipo_vehiculo, 
                       p.nombre_parqueo, p.direccion, p.zona, tv.nombre_tipo,
                       ue.nombre_completo AS operador_entrada,
                       us.nombre_completo AS operador_salida
                FROM (((([INGRESOS_SALIDAS] i 
                INNER JOIN [ESPACIOS] e ON i.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo)
                INNER JOIN [TIPOS_VEHICULO] tv ON e.id_tipo_vehiculo = tv.id_tipo_vehiculo)
                LEFT JOIN [USUARIOS] ue ON i.id_operador_entrada = ue.id_usuario)
                LEFT JOIN [USUARIOS] us ON i.id_operador_salida = us.id_usuario 
                WHERE i.id_ingreso_salida = ?";
        return $this->queryOne($sql, [$idIngresoSalida]);
    }

    /**
     * Obtiene los movimientos (ingresos y salidas) de hoy
     */
    public function getMovimientosHoy(?int $idParqueo = null): array {
        $inicioHoy = date('Y-m-d 00:00:00');
        $finHoy = date('Y-m-d 23:59:59');
        $params = [$inicioHoy, $finHoy];

        $sql = "SELECT i.*, e.codigo_espacio, e.piso_sector, p.nombre_parqueo, tv.nombre_tipo,
                       ue.nombre_completo AS operador_entrada,
                       us.nombre_completo AS operador_salida
                FROM (((([INGRESOS_SALIDAS] i 
                INNER JOIN [ESPACIOS] e ON i.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo)
                INNER JOIN [TIPOS_VEHICULO] tv ON e.id_tipo_vehiculo = tv.id_tipo_vehiculo)
                LEFT JOIN [USUARIOS] ue ON i.id_operador_entrada = ue.id_usuario)
                LEFT JOIN [USUARIOS] us ON i.id_operador_salida = us.id_usuario 
                WHERE (i.fecha_hora_entrada >= ? AND i.fecha_hora_entrada <= ?)";

        if ($idParqueo !== null && $idParqueo > 0) {
            $sql .= " AND e.id_parqueo = ?";
            $params[] = $idParqueo;
        }

        $sql .= " ORDER BY i.fecha_hora_entrada DESC";
        return $this->query($sql, $params);
    }

    /**
     * Obtiene estadísticas de ocupación y rotación en un rango de fechas
     */
    public function getEstadisticasOcupacion(?string $fechaInicio = null, ?string $fechaFin = null, ?int $idParqueo = null): array {
        $fechaInicio = $fechaInicio ? date('Y-m-d 00:00:00', strtotime($fechaInicio)) : date('Y-m-01 00:00:00');
        $fechaFin = $fechaFin ? date('Y-m-d 23:59:59', strtotime($fechaFin)) : date('Y-m-d 23:59:59');
        
        $params = [$fechaInicio, $fechaFin];
        $filtroParqueo = "";
        if ($idParqueo !== null && $idParqueo > 0) {
            $filtroParqueo = " AND e.id_parqueo = ?";
            $params[] = $idParqueo;
        }

        $sql = "SELECT i.*, e.codigo_espacio, e.piso_sector, e.id_parqueo, p.nombre_parqueo, tv.nombre_tipo
                FROM (([INGRESOS_SALIDAS] i 
                INNER JOIN [ESPACIOS] e ON i.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo)
                INNER JOIN [TIPOS_VEHICULO] tv ON e.id_tipo_vehiculo = tv.id_tipo_vehiculo
                WHERE i.fecha_hora_entrada >= ? AND i.fecha_hora_entrada <= ?" . $filtroParqueo . "
                ORDER BY i.fecha_hora_entrada DESC";

        $movimientos = $this->query($sql, $params);

        // Agregaciones en PHP para máxima compatibilidad con Access SQL
        $totalIngresos = count($movimientos);
        $totalFinalizados = 0;
        $totalMinutos = 0;
        $porTipoVehiculo = [];
        $porHora = array_fill(0, 24, 0);

        foreach ($movimientos as $m) {
            $tipo = $m['nombre_tipo'] ?? 'Desconocido';
            if (!isset($porTipoVehiculo[$tipo])) {
                $porTipoVehiculo[$tipo] = 0;
            }
            $porTipoVehiculo[$tipo]++;

            if (!empty($m['fecha_hora_entrada'])) {
                $hora = (int)date('H', strtotime($m['fecha_hora_entrada']));
                if ($hora >= 0 && $hora < 24) {
                    $porHora[$hora]++;
                }
            }

            if ($m['estado_estancia'] === 'Finalizado') {
                $totalFinalizados++;
                $totalMinutos += (int)($m['minutos_totales'] ?? 0);
            }
        }

        $promedioMinutos = $totalFinalizados > 0 ? round($totalMinutos / $totalFinalizados) : 0;

        return [
            'total_ingresos' => $totalIngresos,
            'total_finalizados' => $totalFinalizados,
            'en_parqueo' => $totalIngresos - $totalFinalizados,
            'promedio_minutos' => $promedioMinutos,
            'promedio_horas' => round($promedioMinutos / 60, 1),
            'por_tipo_vehiculo' => $porTipoVehiculo,
            'por_hora' => $porHora,
            'movimientos' => $movimientos
        ];
    }
}
