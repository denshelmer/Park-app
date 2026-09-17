<?php
/**
 * Modelo Pago
 * Mapeo de tabla: PAGOS
 */
require_once APP_PATH . '/core/Model.php';

class Pago extends Model {
    protected string $table = 'PAGOS';

    /**
     * Registra un pago de salida en la base de datos Access
     */
    public function crearPago(array $datos): int|bool {
        $id = $this->getNextId('id_pago');
        $referencia = !empty($datos['referencia_transaccion']) 
            ? trim($datos['referencia_transaccion']) 
            : 'PAG-' . date('Ymd') . '-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO [PAGOS] (
                    id_pago, id_ingreso_salida, id_reserva, monto, 
                    metodo_pago, referencia_transaccion, fecha_hora_pago, id_operador_cobro
                ) VALUES (?, ?, ?, ?, ?, ?, Now(), ?)";

        $ok = $this->execute($sql, [
            $id,
            (int)$datos['id_ingreso_salida'],
            !empty($datos['id_reserva']) ? (int)$datos['id_reserva'] : null,
            (float)$datos['monto'],
            trim($datos['metodo_pago']),
            $referencia,
            (int)$datos['id_operador_cobro']
        ]);

        return $ok ? $id : false;
    }

    /**
     * Busca el registro de pago asociado a una estancia de ingreso/salida
     */
    public function findByIngresoSalida(int $idIngresoSalida): ?array {
        $sql = "SELECT p.*, u.nombre_completo AS operador_cobro 
                FROM [PAGOS] p 
                LEFT JOIN [USUARIOS] u ON p.id_operador_cobro = u.id_usuario 
                WHERE p.id_ingreso_salida = ?";
        return $this->queryOne($sql, [$idIngresoSalida]);
    }

    /**
     * Obtiene las métricas de recaudación del día actual
     */
    public function getMetricasHoy(?int $idParqueo = null): array {
        $inicioHoy = date('Y-m-d 00:00:00');
        $finHoy = date('Y-m-d 23:59:59');

        if ($idParqueo !== null && $idParqueo > 0) {
            $sql = "SELECT p.monto, p.metodo_pago 
                    FROM (([PAGOS] p
                    INNER JOIN [INGRESOS_SALIDAS] i ON p.id_ingreso_salida = i.id_ingreso_salida)
                    INNER JOIN [ESPACIOS] e ON i.id_espacio = e.id_espacio)
                    WHERE p.fecha_hora_pago >= ? AND p.fecha_hora_pago <= ? AND e.id_parqueo = ?";
            $pagos = $this->query($sql, [$inicioHoy, $finHoy, $idParqueo]);
        } else {
            $sql = "SELECT monto, metodo_pago FROM [PAGOS] 
                    WHERE fecha_hora_pago >= ? AND fecha_hora_pago <= ?";
            $pagos = $this->query($sql, [$inicioHoy, $finHoy]);
        }

        $metricas = [
            'total_hoy' => 0.0,
            'total_efectivo' => 0.0,
            'total_qr' => 0.0,
            'cantidad_transacciones' => count($pagos)
        ];

        foreach ($pagos as $p) {
            $m = (float)$p['monto'];
            $metricas['total_hoy'] += $m;
            if ($p['metodo_pago'] === 'Efectivo') {
                $metricas['total_efectivo'] += $m;
            } else {
                $metricas['total_qr'] += $m;
            }
        }

        return $metricas;
    }

    /**
     * Consulta detallada de ingresos para reporte financiero
     */
    public function getReporteIngresos(?string $fechaInicio = null, ?string $fechaFin = null, ?int $idParqueo = null, ?string $metodoPago = null): array {
        $fInicio = !empty($fechaInicio) ? $fechaInicio . ' 00:00:00' : date('Y-m-01 00:00:00');
        $fFin = !empty($fechaFin) ? $fechaFin . ' 23:59:59' : date('Y-m-d 23:59:59');

        $sql = "SELECT p.*, i.numero_ticket, i.placa, i.fecha_hora_entrada, i.fecha_hora_salida, i.minutos_totales,
                       e.codigo_espacio, par.nombre_parqueo, par.zona,
                       u.nombre_completo AS operador_cobro
                FROM ((([PAGOS] p
                LEFT JOIN [INGRESOS_SALIDAS] i ON p.id_ingreso_salida = i.id_ingreso_salida)
                LEFT JOIN [ESPACIOS] e ON i.id_espacio = e.id_espacio)
                LEFT JOIN [PARQUEOS] par ON e.id_parqueo = par.id_parqueo)
                LEFT JOIN [USUARIOS] u ON p.id_operador_cobro = u.id_usuario
                WHERE p.fecha_hora_pago >= ? AND p.fecha_hora_pago <= ?";

        $params = [$fInicio, $fFin];

        if ($idParqueo !== null && $idParqueo > 0) {
            $sql .= " AND e.id_parqueo = ?";
            $params[] = $idParqueo;
        }

        if (!empty($metodoPago)) {
            $sql .= " AND p.metodo_pago = ?";
            $params[] = $metodoPago;
        }

        $sql .= " ORDER BY p.fecha_hora_pago DESC";

        return $this->query($sql, $params);
    }
}
