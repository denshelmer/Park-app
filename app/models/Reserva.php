<?php
/**
 * Modelo Reserva
 * Mapeo de tabla: RESERVAS
 */
require_once APP_PATH . '/core/Model.php';

class Reserva extends Model {
    protected string $table = 'RESERVAS';

    public function findByTokenQR(string $token): ?array {
        $sql = "SELECT r.*, e.codigo_espacio, e.id_parqueo, p.nombre_parqueo, u.nombre_completo as conductor
                FROM (([RESERVAS] r
                INNER JOIN [ESPACIOS] e ON r.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo)
                INNER JOIN [USUARIOS] u ON r.id_usuario = u.id_usuario
                WHERE r.codigo_qr_token = ?";
        return $this->queryOne($sql, [$token]);
    }

    public function getActivasPorUsuario(int $idUsuario): array {
        $sql = "SELECT r.*, e.codigo_espacio, p.nombre_parqueo, p.direccion 
                FROM ([RESERVAS] r 
                INNER JOIN [ESPACIOS] e ON r.id_espacio = e.id_espacio)
                INNER JOIN [PARQUEOS] p ON e.id_parqueo = p.id_parqueo 
                WHERE r.id_usuario = ? 
                ORDER BY r.fecha_hora_reserva DESC";
        return $this->query($sql, [$idUsuario]);
    }
}
