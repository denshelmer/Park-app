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

class ReporteController extends Controller {
    private IngresoSalida $ingresoSalidaModel;
    private Pago $pagoModel;

    public function __construct() {
        $this->ingresoSalidaModel = new IngresoSalida();
        $this->pagoModel = new Pago();
    }

    public function ingresos(): void {
        Auth::requireRole(1); // Solo Administrador
        $this->render('reportes/ingresos', ['titulo' => 'Reporte de Recaudación Económica']);
    }

    public function ocupacion(): void {
        Auth::requireRole(1);
        $this->render('reportes/ocupacion', ['titulo' => 'Reporte de Ocupación y Flujo']);
    }
}
