<?php
/**
 * Modelo Tarifa
 * Mapeo de tabla: TARIFAS y TIPOS_VEHICULO
 */
require_once APP_PATH . '/core/Model.php';

class Tarifa extends Model {
    protected string $table = 'TARIFAS';

    public function getPorParqueo(int $idParqueo): array {
        $sql = "SELECT t.*, tv.nombre_tipo 
                FROM [TARIFAS] t 
                INNER JOIN [TIPOS_VEHICULO] tv ON t.id_tipo_vehiculo = tv.id_tipo_vehiculo 
                WHERE t.id_parqueo = ? AND t.vigente = True";
        return $this->query($sql, [$idParqueo]);
    }

    public function getTarifaEspecifica(int $idParqueo, int $idTipoVehiculo): ?array {
        $sql = "SELECT * FROM [TARIFAS] 
                WHERE id_parqueo = ? AND id_tipo_vehiculo = ? AND vigente = True";
        return $this->queryOne($sql, [$idParqueo, $idTipoVehiculo]);
    }

    /**
     * Calcula de forma precisa el monto a cobrar en base al tiempo de estancia y la tarifa oficial
     */
    public function calcularMonto(int $idParqueo, int $idTipoVehiculo, int $minutosEstancia): array {
        $tarifa = $this->getTarifaEspecifica($idParqueo, $idTipoVehiculo);

        $precioHora = $tarifa ? (float)$tarifa['precio_hora'] : 5.00;
        $precioFraccion = $tarifa ? (float)$tarifa['precio_fraccion'] : 2.50;
        $precioDia = $tarifa ? (float)$tarifa['precio_dia'] : 40.00;
        $toleranciaMinutos = $tarifa ? (int)$tarifa['tolerancia_minutos'] : 10;

        // 1. Caso de cortesía / salida inmediata dentro de la tolerancia
        if ($minutosEstancia <= $toleranciaMinutos) {
            return [
                'minutos_totales' => $minutosEstancia,
                'minutos' => $minutosEstancia,
                'tiempo_formateado' => $minutosEstancia . ' min',
                'es_cortesia' => true,
                'tolerancia_aplicada' => true,
                'dias' => 0,
                'horas' => 0,
                'fracciones' => 0,
                'precio_hora' => $precioHora,
                'precio_fraccion' => $precioFraccion,
                'monto_total' => 0.00,
                'total' => 0.00,
                'detalle_texto' => 'Cortesía de tolerancia (' . $toleranciaMinutos . ' min gratis)',
                'desglose' => 'Cortesía de tolerancia (' . $toleranciaMinutos . ' min gratis)'
            ];
        }

        // 2. Cálculo por días completos (1440 min = 24 hrs)
        $dias = (int)floor($minutosEstancia / 1440);
        $minutosRestantes = $minutosEstancia % 1440;

        $horas = (int)floor($minutosRestantes / 60);
        $restoMinutos = $minutosRestantes % 60;

        $fracciones = 0;
        if ($restoMinutos > 0 && $restoMinutos <= 30) {
            $fracciones = 1;
        } elseif ($restoMinutos > 30) {
            $horas += 1;
        }

        // Si no se completó ni una hora ni fracción pero pasó la tolerancia, cobrar mínimo 1 fracción
        if ($dias === 0 && $horas === 0 && $fracciones === 0) {
            $fracciones = 1;
        }

        $montoParcial = ($horas * $precioHora) + ($fracciones * $precioFraccion);
        // Si el monto parcial supera la tarifa diaria, cobrar como máximo un día
        if ($montoParcial > $precioDia) {
            $montoParcial = $precioDia;
        }

        $montoTotal = ($dias * $precioDia) + $montoParcial;

        // Formato legible de tiempo
        $partesTiempo = [];
        if ($dias > 0) $partesTiempo[] = $dias . ' d';
        if ($horas > 0) $partesTiempo[] = $horas . ' h';
        if ($restoMinutos > 0) $partesTiempo[] = $restoMinutos . ' min';
        $tiempoFmt = !empty($partesTiempo) ? implode(' ', $partesTiempo) : $minutosEstancia . ' min';

        // Detalle explicativo formal
        $detalles = [];
        if ($dias > 0) $detalles[] = "{$dias} día(s) x Bs. " . number_format($precioDia, 2);
        if ($horas > 0) $detalles[] = "{$horas} hora(s) x Bs. " . number_format($precioHora, 2);
        if ($fracciones > 0) $detalles[] = "{$fracciones} frac. (30 min) x Bs. " . number_format($precioFraccion, 2);
        $detalleTexto = implode(' + ', $detalles);

        return [
            'minutos_totales' => $minutosEstancia,
            'minutos' => $minutosEstancia,
            'tiempo_formateado' => $tiempoFmt,
            'es_cortesia' => false,
            'tolerancia_aplicada' => false,
            'dias' => $dias,
            'horas' => $horas,
            'fracciones' => $fracciones,
            'precio_hora' => $precioHora,
            'precio_fraccion' => $precioFraccion,
            'precio_dia' => $precioDia,
            'monto_total' => round($montoTotal, 2),
            'total' => round($montoTotal, 2),
            'detalle_texto' => $detalleTexto,
            'desglose' => $detalleTexto
        ];
    }
}
