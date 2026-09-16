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

    public function buscarPorIdYUsuario(int $idReserva, int $idUsuario): ?array {
        $sql = "SELECT r.*, e.codigo_espacio, e.piso_sector, e.id_parqueo, p.nombre_parqueo, p.direccion, p.zona 
                FROM ([RESERVAS] r 
                INNER JOIN [ESPACIOS] e ON r.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo 
                WHERE r.id_reserva = ? AND r.id_usuario = ?";
        return $this->queryOne($sql, [$idReserva, $idUsuario]);
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
}
