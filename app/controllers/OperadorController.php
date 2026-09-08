<?php
/**
 * OperadorController
 * SECCIÓN 2: Módulo de Caseta / Control de Ingresos, Salidas y Cobros
 */
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/models/Parqueo.php';
require_once APP_PATH . '/models/Espacio.php';
require_once APP_PATH . '/models/IngresoSalida.php';
require_once APP_PATH . '/models/Tarifa.php';

class OperadorController extends Controller {
    private Parqueo $parqueoModel;
    private Espacio $espacioModel;
    private IngresoSalida $ingresoSalidaModel;
    private Tarifa $tarifaModel;

    public function __construct() {
        $this->parqueoModel = new Parqueo();
        $this->espacioModel = new Espacio();
        $this->ingresoSalidaModel = new IngresoSalida();
        $this->tarifaModel = new Tarifa();
    }

    public function caseta(): void {
        Auth::requireRole([1, 2]); // Admin u Operador
        $this->render('operador/caseta', ['titulo' => 'Panel de Caseta - Control en Vivo']);
    }

    public function ingreso(): void {
        Auth::requireRole([1, 2]);
        $this->render('operador/ingreso', ['titulo' => 'Registrar Ingreso de Vehículo']);
    }

    public function salidaCobro(): void {
        Auth::requireRole([1, 2]);
        $this->render('operador/salida_cobro', ['titulo' => 'Cobro y Salida de Vehículo']);
    }

    public function ticketPrint(): void {
        Auth::requireRole([1, 2]);
        $this->render('operador/ticket_print', ['titulo' => 'Ticket de Estacionamiento'], false);
    }
}
