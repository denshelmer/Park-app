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
}
