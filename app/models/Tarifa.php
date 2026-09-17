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

    /**
     * Obtiene todas las tarifas con nombres de parqueo y tipo de vehículo
     */
    public function getAllConDetalle(): array {
        $sql = "SELECT t.*, p.nombre_parqueo, p.zona, tv.nombre_tipo 
                FROM ([TARIFAS] t 
                INNER JOIN [PARQUEOS] p ON t.id_parqueo = p.id_parqueo)
                INNER JOIN [TIPOS_VEHICULO] tv ON t.id_tipo_vehiculo = tv.id_tipo_vehiculo 
                ORDER BY p.nombre_parqueo, tv.id_tipo_vehiculo, t.id_tarifa DESC";
        return $this->query($sql);
    }

    /**
     * Registra una nueva tarifa
     */
    public function crearTarifa(array $datos): int|bool {
        $id = $this->getNextId('id_tarifa');
        $idParqueo = (int)$datos['id_parqueo'];
        $idTipoVehiculo = (int)$datos['id_tipo_vehiculo'];
        $vigente = !empty($datos['vigente']) ? true : false;

        if ($vigente) {
            $this->execute("UPDATE [TARIFAS] SET vigente = False WHERE id_parqueo = ? AND id_tipo_vehiculo = ?", [$idParqueo, $idTipoVehiculo]);
        }

        $vigenteLiteral = $vigente ? 'True' : 'False';
        $sql = "INSERT INTO [TARIFAS] (
                    id_tarifa, id_parqueo, id_tipo_vehiculo, 
                    precio_hora, precio_fraccion, precio_dia, 
                    tolerancia_minutos, vigente
                ) VALUES (?, ?, ?, ?, ?, ?, ?, {$vigenteLiteral})";

        $ok = $this->execute($sql, [
            $id,
            $idParqueo,
            $idTipoVehiculo,
            (float)$datos['precio_hora'],
            (float)$datos['precio_fraccion'],
            (float)$datos['precio_dia'],
            (int)($datos['tolerancia_minutos'] ?? 10)
        ]);

        return $ok ? $id : false;
    }

    /**
     * Actualiza los valores de una tarifa existente
     */
    public function actualizarTarifa(int $id, array $datos): bool {
        $sql = "UPDATE [TARIFAS] 
                SET precio_hora = ?, precio_fraccion = ?, precio_dia = ?, tolerancia_minutos = ? 
                WHERE id_tarifa = ?";
        return $this->execute($sql, [
            (float)$datos['precio_hora'],
            (float)$datos['precio_fraccion'],
            (float)$datos['precio_dia'],
            (int)$datos['tolerancia_minutos'],
            $id
        ]);
    }

    /**
     * Busca una tarifa por su ID
     */
    public function findById(int $idTarifa): ?array {
        $sql = "SELECT * FROM [TARIFAS] WHERE id_tarifa = ?";
        return $this->queryOne($sql, [$idTarifa]);
    }

    /**
     * Cambia la vigencia de una tarifa garantizando solo una tarifa activa por parqueo y tipo
     */
    public function cambiarVigencia(int $id, bool $vigente, ?int $idParqueo = null, ?int $idTipoVehiculo = null): bool {
        if ($vigente) {
            if ($idParqueo === null || $idTipoVehiculo === null) {
                $actual = $this->findById($id);
                if ($actual) {
                    $idParqueo = (int)$actual['id_parqueo'];
                    $idTipoVehiculo = (int)$actual['id_tipo_vehiculo'];
                }
            }
            if ($idParqueo && $idTipoVehiculo) {
                $this->execute("UPDATE [TARIFAS] SET vigente = False WHERE id_parqueo = ? AND id_tipo_vehiculo = ?", [$idParqueo, $idTipoVehiculo]);
            }
        }
        $vigenteLiteral = $vigente ? 'True' : 'False';
        $sql = "UPDATE [TARIFAS] SET vigente = {$vigenteLiteral} WHERE id_tarifa = ?";
        return $this->execute($sql, [$id]);
    }
}
