<?php
/**
 * Modelo Reserva
 * Mapeo de tabla: RESERVAS
 */
require_once APP_PATH . '/core/Model.php';

class Reserva extends Model {
    protected string $table = 'RESERVAS';

    public function findByTokenQR(string $token): ?array {
        $sql = "SELECT r.*, e.codigo_espacio, e.piso_sector, e.id_parqueo, p.nombre_parqueo, p.direccion, p.zona, u.nombre_completo as conductor
                FROM (([RESERVAS] r
                INNER JOIN [ESPACIOS] e ON r.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo)
                INNER JOIN [USUARIOS] u ON r.id_usuario = u.id_usuario
                WHERE r.codigo_qr_token = ?";
        return $this->queryOne($sql, [$token]);
    }

    public function getActivasPorUsuario(int $idUsuario): array {
        $sql = "SELECT r.*, e.codigo_espacio, e.piso_sector, p.nombre_parqueo, p.direccion, p.zona 
                FROM ([RESERVAS] r 
                INNER JOIN [ESPACIOS] e ON r.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo 
                WHERE r.id_usuario = ? 
                ORDER BY r.fecha_hora_reserva DESC";
        return $this->query($sql, [$idUsuario]);
    }

    public function getActivasPorParqueo(int $idParqueo): array {
        $sql = "SELECT r.*, e.codigo_espacio, e.piso_sector, u.nombre_completo as conductor
                FROM (([RESERVAS] r
                INNER JOIN [ESPACIOS] e ON r.id_espacio = e.id_espacio)
                LEFT JOIN [USUARIOS] u ON r.id_usuario = u.id_usuario)
                WHERE e.id_parqueo = ? AND r.estado_reserva = 'Confirmada'
                ORDER BY r.fecha_hora_prevista_llegada ASC";
        return $this->query($sql, [$idParqueo]);
    }

    public function buscarPorIdYUsuario(int $idReserva, int $idUsuario): ?array {
        $sql = "SELECT r.*, e.codigo_espacio, e.piso_sector, e.id_parqueo, p.nombre_parqueo, p.direccion, p.zona 
                FROM ([RESERVAS] r 
                INNER JOIN [ESPACIOS] e ON r.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo 
                WHERE r.id_reserva = ? AND r.id_usuario = ?";
        return $this->queryOne($sql, [$idReserva, $idUsuario]);
    }

    /**
     * Verifica si el usuario ya tiene una reserva activa (Confirmada o En Parqueo) para una placa dada
     */
    public function tieneReservaActivaPlaca(int $idUsuario, string $placa): ?array {
        $sql = "SELECT TOP 1 r.*, e.codigo_espacio, p.nombre_parqueo 
                FROM ([RESERVAS] r 
                INNER JOIN [ESPACIOS] e ON r.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo 
                WHERE r.id_usuario = ? AND r.placa_vehiculo = ? AND (r.estado_reserva = 'Confirmada' OR r.estado_reserva = 'En Parqueo')";
        return $this->queryOne($sql, [$idUsuario, $placa]);
    }

    public function crearReserva(array $datos): int|bool {
        $id = $this->getNextId('id_reserva');
        $sql = "INSERT INTO [RESERVAS] (
                    id_reserva, codigo_qr_token, id_usuario, id_espacio, 
                    placa_vehiculo, fecha_hora_reserva, fecha_hora_prevista_llegada, 
                    minutos_tolerancia, estado_reserva, monto_adelanto, pago_confirmado
                ) VALUES (?, ?, ?, ?, ?, Now(), ?, ?, ?, ?, ?)";

        $ok = $this->execute($sql, [
            $id,
            $datos['codigo_qr_token'],
            (int)$datos['id_usuario'],
            (int)$datos['id_espacio'],
            trim(strtoupper($datos['placa_vehiculo'])),
            $datos['fecha_hora_prevista_llegada'],
            (int)($datos['minutos_tolerancia'] ?? 15),
            'Confirmada',
            (float)($datos['monto_adelanto'] ?? 0.0),
            (bool)($datos['pago_confirmado'] ?? True)
        ]);

        return $ok ? $id : false;
    }

    public function cancelarReserva(int $idReserva, int $idUsuario): bool {
        $reserva = $this->buscarPorIdYUsuario($idReserva, $idUsuario);
        if (!$reserva || $reserva['estado_reserva'] !== 'Confirmada') {
            return false;
        }

        // 1. Cambiar estado de la reserva
        $sql = "UPDATE [RESERVAS] SET estado_reserva = 'Cancelada' WHERE id_reserva = ?";
        $actualizado = $this->execute($sql, [$idReserva]);

        // 2. Liberar el espacio
        if ($actualizado && !empty($reserva['id_espacio'])) {
            $sqlEspacio = "UPDATE [ESPACIOS] SET estado = 'Disponible' WHERE id_espacio = ?";
            $this->execute($sqlEspacio, [(int)$reserva['id_espacio']]);
        }

        return $actualizado;
    }

    /**
     * Cancela una reserva por vencimiento de tolerancia y libera el espacio físico
     */
    public function cancelarPorTolerancia(int $idReserva): bool {
        $sql = "SELECT id_reserva, id_espacio, estado_reserva FROM [RESERVAS] WHERE id_reserva = ?";
        $reserva = $this->queryOne($sql, [$idReserva]);
        if (!$reserva || $reserva['estado_reserva'] !== 'Confirmada') {
            return false;
        }

        // 1. Marcar reserva como Cancelada
        $sqlUpdate = "UPDATE [RESERVAS] SET estado_reserva = 'Cancelada' WHERE id_reserva = ?";
        $actualizado = $this->execute($sqlUpdate, [$idReserva]);

        // 2. Liberar el espacio asignado
        if ($actualizado && !empty($reserva['id_espacio'])) {
            $sqlEspacio = "UPDATE [ESPACIOS] SET estado = 'Disponible' WHERE id_espacio = ?";
            $this->execute($sqlEspacio, [(int)$reserva['id_espacio']]);
        }

        return (bool)$actualizado;
    }

    /**
     * Evalúa y libera automáticamente todas las reservas cuya hora prevista + tolerancia ha expirado
     * @param int|null $idParqueo Opcional, filtra por parqueo
     * @return int Cantidad de reservas canceladas y cajones liberados
     */
    public function liberarReservasVencidas(?int $idParqueo = null): int {
        $sql = "SELECT r.id_reserva, r.id_espacio, r.fecha_hora_prevista_llegada, r.minutos_tolerancia 
                FROM [RESERVAS] r 
                INNER JOIN [ESPACIOS] e ON r.id_espacio = e.id_espacio 
                WHERE r.estado_reserva = 'Confirmada'";
        $params = [];
        if ($idParqueo !== null && $idParqueo > 0) {
            $sql .= " AND e.id_parqueo = ?";
            $params[] = $idParqueo;
        }

        $activas = $this->query($sql, $params);
        if (empty($activas)) {
            return 0;
        }

        $ahora = time();
        $liberadas = 0;

        foreach ($activas as $res) {
            $tolerancia = (int)($res['minutos_tolerancia'] ?? 15);
            $tiempoLlegada = !empty($res['fecha_hora_prevista_llegada']) ? strtotime($res['fecha_hora_prevista_llegada']) : 0;

            // Si la hora de llegada + margen de tolerancia ya pasó, auto-cancelar
            if ($tiempoLlegada > 0 && ($tiempoLlegada + ($tolerancia * 60)) < $ahora) {
                if ($this->cancelarPorTolerancia((int)$res['id_reserva'])) {
                    $liberadas++;
                }
            }
        }

        return $liberadas;
    }
}
