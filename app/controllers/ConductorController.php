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
require_once APP_PATH . '/models/TipoVehiculo.php';

class ConductorController extends Controller {
    private Parqueo $parqueoModel;
    private Espacio $espacioModel;
    private Reserva $reservaModel;
    private TipoVehiculo $tipoVehiculoModel;

    public function __construct() {
        $this->parqueoModel = new Parqueo();
        $this->espacioModel = new Espacio();
        $this->reservaModel = new Reserva();
        $this->tipoVehiculoModel = new TipoVehiculo();
    }

    public function disponibilidad(): void {
        $parqueos = $this->parqueoModel->getConDisponibilidad();
        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        $this->render('conductor/disponibilidad', [
            'titulo' => 'Disponibilidad de Parqueos en El Alto',
            'parqueos' => $parqueos,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function reservar(): void {
        Auth::requireRole(3); // Solo conductores
        
        $parqueos = $this->parqueoModel->getActivos();
        $tiposVehiculo = $this->tipoVehiculoModel->getAllActivos();
        $parqueoSeleccionado = isset($_GET['parqueo']) ? (int)$_GET['parqueo'] : 0;

        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        $this->render('conductor/reservar', [
            'titulo' => 'Nueva Reserva de Espacio - ParkApp',
            'parqueos' => $parqueos,
            'tiposVehiculo' => $tiposVehiculo,
            'parqueoSeleccionado' => $parqueoSeleccionado,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function procesarReserva(): void {
        Auth::requireRole(3);

        $idParqueo = (int)($_POST['id_parqueo'] ?? 0);
        $idTipoVehiculo = (int)($_POST['id_tipo_vehiculo'] ?? 0);
        $placa = trim(strtoupper($_POST['placa'] ?? ''));
        $fechaHoraLlegada = trim($_POST['fecha_hora_prevista_llegada'] ?? '');

        // 1. Validar campos requeridos
        if ($idParqueo <= 0 || $idTipoVehiculo <= 0 || empty($placa) || empty($fechaHoraLlegada)) {
            $_SESSION['flash_error'] = 'Por favor complete todos los campos del formulario de reserva.';
            $this->redirect('reservar' . ($idParqueo > 0 ? "?parqueo={$idParqueo}" : ''));
            return;
        }

        // 2. Normalizar y validar placa vehicular boliviana (ej. 4829-ABC o 123-XYZ)
        $placaLimpia = preg_replace('/[^0-9A-Z]/', '', $placa);
        if (preg_match('/^([0-9]{3,4})([A-Z]{3})$/', $placaLimpia, $m)) {
            $placa = $m[1] . '-' . $m[2];
        } else {
            $_SESSION['flash_error'] = 'El formato de placa no es válido. Debe contener 3 o 4 dígitos seguidos de 3 letras (ej. 4829-ABC).';
            $this->redirect("reservar?parqueo={$idParqueo}");
            return;
        }

        // 3. Validar fecha y hora (permitir el mismo día a partir del minuto actual, máximo 48 horas)
        $ahora = time();
        $timestampLlegada = strtotime($fechaHoraLlegada);

        if (!$timestampLlegada) {
            $_SESSION['flash_error'] = 'Fecha y hora de llegada no válidas.';
            $this->redirect("reservar?parqueo={$idParqueo}");
            return;
        }

        // Margen de gracia de 5 minutos en el pasado para contemplar el llenado del formulario
        if ($timestampLlegada < ($ahora - 300)) {
            $_SESSION['flash_error'] = 'No es posible reservar para una fecha u hora anterior a la actual. Por favor elija la hora presente o posterior.';
            $this->redirect("reservar?parqueo={$idParqueo}");
            return;
        }

        // Límite de reserva: máximo 48 horas hacia adelante
        if ($timestampLlegada > ($ahora + (86400 * 2))) {
            $_SESSION['flash_error'] = 'Las reservas solo pueden realizarse para el día de hoy o con un máximo de 48 horas de anticipación.';
            $this->redirect("reservar?parqueo={$idParqueo}");
            return;
        }

        // 4. Validar horario de atención del establecimiento
        $parqueoInfo = $this->parqueoModel->getConDetalle($idParqueo);
        if ($parqueoInfo) {
            $horaLlegadaStr = date('H:i:s', $timestampLlegada);
            $horaAperturaStr = date('H:i:s', strtotime($parqueoInfo['hora_apertura']));
            $horaCierreStr = date('H:i:s', strtotime($parqueoInfo['hora_cierre']));

            if ($horaLlegadaStr < $horaAperturaStr || $horaLlegadaStr > $horaCierreStr) {
                $aperturaFmt = date('H:i', strtotime($parqueoInfo['hora_apertura']));
                $cierreFmt = date('H:i', strtotime($parqueoInfo['hora_cierre']));
                $_SESSION['flash_error'] = "El establecimiento {$parqueoInfo['nombre_parqueo']} atiende de {$aperturaFmt} a {$cierreFmt}. Su hora prevista de llegada debe encontrarse dentro de ese rango.";
                $this->redirect("reservar?parqueo={$idParqueo}");
                return;
            }
        }

        // 5. Evitar reservas duplicadas activas para el mismo vehículo
        $reservaActiva = $this->reservaModel->tieneReservaActivaPlaca((int)Auth::id(), $placa);
        if ($reservaActiva) {
            $_SESSION['flash_error'] = "Ya cuenta con una reserva activa para el vehículo con placa {$placa} en {$reservaActiva['nombre_parqueo']} (Espacio {$reservaActiva['codigo_espacio']}). Puede gestionarla en 'Mis Reservas'.";
            $this->redirect("reservar?parqueo={$idParqueo}");
            return;
        }

        // 6. Determinar espacio: selección manual en el mapa o asignación automática
        $idEspacioSeleccionado = (int)($_POST['id_espacio'] ?? 0);
        $espacio = null;

        if ($idEspacioSeleccionado > 0) {
            $espacioCandidato = $this->espacioModel->findById($idEspacioSeleccionado);
            if (
                $espacioCandidato && 
                (int)$espacioCandidato['id_parqueo'] === $idParqueo && 
                (int)$espacioCandidato['id_tipo_vehiculo'] === $idTipoVehiculo && 
                $espacioCandidato['estado'] === 'Disponible'
            ) {
                $espacio = $espacioCandidato;
            } else {
                $_SESSION['flash_error'] = 'El espacio seleccionado ya no está disponible o no coincide con el tipo de vehículo. Por favor seleccione otro espacio del mapa.';
                $this->redirect("reservar?parqueo={$idParqueo}");
                return;
            }
        } else {
            $espacio = $this->espacioModel->buscarPrimerDisponible($idParqueo, $idTipoVehiculo);
        }

        if (!$espacio) {
            $_SESSION['flash_error'] = 'Lo sentimos, no hay espacios libres disponibles para este tipo de vehículo en el parqueo seleccionado.';
            $this->redirect("reservar?parqueo={$idParqueo}");
            return;
        }

        // 5. Generar token único para el código QR (ej: QR-ALTO-2026-X9A2F)
        $tokenQR = 'QR-ALTO-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));

        // 6. Registrar la reserva en la base de datos
        $fechaFormateada = date('Y-m-d H:i:s', $timestampLlegada);
        $idReserva = $this->reservaModel->crearReserva([
            'codigo_qr_token' => $tokenQR,
            'id_usuario' => Auth::id(),
            'id_espacio' => (int)$espacio['id_espacio'],
            'placa_vehiculo' => $placa,
            'fecha_hora_prevista_llegada' => $fechaFormateada,
            'minutos_tolerancia' => 15,
            'monto_adelanto' => 0.00,
            'pago_confirmado' => True
        ]);

        if (!$idReserva) {
            $_SESSION['flash_error'] = 'Ocurrió un error al procesar la reserva. Por favor intente nuevamente.';
            $this->redirect("reservar?parqueo={$idParqueo}");
            return;
        }

        // 7. Cambiar estado del espacio a 'Reservado'
        $this->espacioModel->actualizarEstado((int)$espacio['id_espacio'], 'Reservado');

        $_SESSION['flash_success'] = "¡Reserva confirmada con éxito! Su espacio asignado es el {$espacio['codigo_espacio']}.";
        $this->redirect("reserva/qr?token={$tokenQR}");
    }

    public function misReservas(): void {
        Auth::requireRole(3);

        $reservas = $this->reservaModel->getActivasPorUsuario((int)Auth::id());
        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        $this->render('conductor/mis_reservas', [
            'titulo' => 'Mis Reservas - ParkApp',
            'reservas' => $reservas,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function cancelarReserva(): void {
        Auth::requireRole(3);

        $idReserva = (int)($_POST['id_reserva'] ?? 0);
        if ($idReserva <= 0) {
            $_SESSION['flash_error'] = 'Identificador de reserva no válido.';
            $this->redirect('mis-reservas');
            return;
        }

        $cancelado = $this->reservaModel->cancelarReserva($idReserva, (int)Auth::id());
        if ($cancelado) {
            $_SESSION['flash_success'] = 'La reserva fue cancelada exitosamente y el espacio ha sido liberado.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo cancelar la reserva (posiblemente ya fue utilizada o cancelada previamente).';
        }

        $this->redirect('mis-reservas');
    }

    public function verQR(): void {
        Auth::requireRole(3);

        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            $this->redirect('mis-reservas');
            return;
        }

        $reserva = $this->reservaModel->findByTokenQR($token);

        if (!$reserva) {
            $_SESSION['flash_error'] = 'No se encontró la reserva solicitada.';
            $this->redirect('mis-reservas');
            return;
        }

        // Verificar que la reserva pertenezca al usuario actual (o rol admin)
        if ((int)$reserva['id_usuario'] !== (int)Auth::id() && Auth::roleId() !== 1) {
            $_SESSION['flash_error'] = 'No tiene permisos para ver este comprobante de reserva.';
            $this->redirect('mis-reservas');
            return;
        }

        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        $this->render('conductor/ver_qr', [
            'titulo' => 'Comprobante QR de Reserva - ParkApp',
            'reserva' => $reserva,
            'success' => $success
        ]);
    }

    /**
     * Endpoint API JSON para consultar los espacios de un parqueo (agrupados por sector)
     */
    public function apiEspacios(): void {
        header('Content-Type: application/json; charset=utf-8');

        $idParqueo = (int)($_GET['parqueo'] ?? 0);
        $idTipoVehiculo = isset($_GET['tipo']) && (int)$_GET['tipo'] > 0 ? (int)$_GET['tipo'] : null;

        if ($idParqueo <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de parqueo inválido.']);
            exit;
        }

        $espacios = $this->espacioModel->getPorParqueoYTipo($idParqueo, $idTipoVehiculo);

        // Agrupar por sector / piso
        $sectores = [];
        foreach ($espacios as $esp) {
            $sector = !empty($esp['piso_sector']) ? $esp['piso_sector'] : 'Sector Principal';
            if (!isset($sectores[$sector])) {
                $sectores[$sector] = [];
            }
            $sectores[$sector][] = [
                'id_espacio' => (int)$esp['id_espacio'],
                'codigo_espacio' => $esp['codigo_espacio'],
                'id_tipo_vehiculo' => (int)$esp['id_tipo_vehiculo'],
                'nombre_tipo' => $esp['nombre_tipo'] ?? '',
                'estado' => $esp['estado']
            ];
        }

        echo json_encode([
            'success' => true,
            'parqueo_id' => $idParqueo,
            'total_espacios' => count($espacios),
            'sectores' => $sectores
        ]);
        exit;
    }
}

