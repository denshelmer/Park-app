<?php
/**
 * Modelo Pago
 * Mapeo de tabla: PAGOS
 */
require_once APP_PATH . '/core/Model.php';

class Pago extends Model {
    protected string $table = 'PAGOS';

    public function registrarPago(int $idIngresoSalida, float $monto, string $metodo, string $referencia, int $idOperador): bool {
        $sql = "INSERT INTO [PAGOS] (id_ingreso_salida, monto, metodo_pago, referencia_transaccion, fecha_hora_pago, id_operador_cobro) 
                VALUES (?, ?, ?, ?, Now(), ?)";
        return $this->execute($sql, [$idIngresoSalida, $monto, $metodo, $referencia, $idOperador]);
    }
}
