<?php
/**
 * ConductorController
 * SECCIÓN 1: Portal del Conductor
 * Consulta de disponibilidad en El Alto, reservas anticipadas y comprobante QR.
 */
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/models/Parqueo.php';
require_once APP_PATH . '/models/Espacio.php';
require_once APP_PATH . '/models/Reserva.php';

class ConductorController extends Controller {
    private Parqueo $parqueoModel;
    private Espacio $espacioModel;
    private Reserva $reservaModel;

    public function __construct() {
        $this->parqueoModel = new Parqueo();
        $this->espacioModel = new Espacio();
        $this->reservaModel = new Reserva();
    }

    public function disponibilidad(): void {
        $parqueos = $this->parqueoModel->getActivos();
        $this->render('conductor/disponibilidad', [
            'titulo' => 'Disponibilidad de Parqueos en El Alto',
            'parqueos' => $parqueos
        ]);
    }

    public function reservar(): void {
        Auth::requireRole(3); // Solo conductores
        $this->render('conductor/reservar', ['titulo' => 'Nueva Reserva']);
    }

    public function misReservas(): void {
        Auth::requireRole(3);
        $this->render('conductor/mis_reservas', ['titulo' => 'Mis Reservas']);
    }

    public function verQR(): void {
        Auth::requireRole(3);
        $this->render('conductor/ver_qr', ['titulo' => 'Comprobante QR de Reserva']);
    }
}
