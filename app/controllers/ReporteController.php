<?php
/**
 * ReporteController
 * SECCIÓN 3: Emisión de Informes y Estadísticas
 * Recaudación económica, ocupación y flujo vehicular.
 */
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/models/IngresoSalida.php';
require_once APP_PATH . '/models/Pago.php';
require_once APP_PATH . '/models/Parqueo.php';

class ReporteController extends Controller {
    private IngresoSalida $ingresoSalidaModel;
    private Pago $pagoModel;
    private Parqueo $parqueoModel;

    public function __construct() {
        $this->ingresoSalidaModel = new IngresoSalida();
        $this->pagoModel = new Pago();
        $this->parqueoModel = new Parqueo();
    }

    /**
     * Reporte Económico y de Recaudación
     */
    public function ingresos(): void {
        Auth::requireRole(1);

        $fechaInicio = !empty($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : date('Y-m-01');
        $fechaFin = !empty($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : date('Y-m-d');
        $idParqueo = isset($_GET['id_parqueo']) && is_numeric($_GET['id_parqueo']) && (int)$_GET['id_parqueo'] > 0 
            ? (int)$_GET['id_parqueo'] 
            : null;
        $metodoPago = !empty($_GET['metodo_pago']) ? trim($_GET['metodo_pago']) : null;

        $parqueos = $this->parqueoModel->getAllActivos();
        $registros = $this->pagoModel->getReporteIngresos($fechaInicio, $fechaFin, $idParqueo, $metodoPago);

        $totales = [
            'total_recaudado' => 0.0,
            'total_efectivo' => 0.0,
            'total_qr' => 0.0,
            'cantidad_transacciones' => count($registros)
        ];

        foreach ($registros as $r) {
            $m = (float)$r['monto'];
            $totales['total_recaudado'] += $m;
            if ($r['metodo_pago'] === 'Efectivo') {
                $totales['total_efectivo'] += $m;
            } else {
                $totales['total_qr'] += $m;
            }
        }

        $this->render('reportes/ingresos', [
            'titulo' => 'Reporte Financiero y Recaudación',
            'parqueos' => $parqueos,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'idParqueo' => $idParqueo,
            'metodoPago' => $metodoPago,
            'registros' => $registros,
            'totales' => $totales
        ]);
    }

    /**
     * Reporte de Ocupación, Rotación y Picos de Demanda
     */
    public function ocupacion(): void {
        Auth::requireRole(1);

        $fechaInicio = !empty($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : date('Y-m-01');
        $fechaFin = !empty($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : date('Y-m-d');
        $idParqueo = isset($_GET['id_parqueo']) && is_numeric($_GET['id_parqueo']) && (int)$_GET['id_parqueo'] > 0 
            ? (int)$_GET['id_parqueo'] 
            : null;

        $parqueos = $this->parqueoModel->getAllActivos();
        $estadisticas = $this->ingresoSalidaModel->getEstadisticasOcupacion($fechaInicio, $fechaFin, $idParqueo);

        $this->render('reportes/ocupacion', [
            'titulo' => 'Reporte de Ocupación y Rotación Vehicular',
            'parqueos' => $parqueos,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'idParqueo' => $idParqueo,
            'estadisticas' => $estadisticas
        ]);
    }
}
