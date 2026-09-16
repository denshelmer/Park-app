<?php
/**
 * OperadorController
 * SECCIÓN 2: Módulo de Caseta / Control de Ingresos, Salidas y Cobros
 */
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/models/Parqueo.php';
require_once APP_PATH . '/models/Espacio.php';
require_once APP_PATH . '/models/IngresoSalida.php';
require_once APP_PATH . '/models/Tarifa.php';
require_once APP_PATH . '/models/Reserva.php';
require_once APP_PATH . '/models/Pago.php';
require_once APP_PATH . '/models/TipoVehiculo.php';

class OperadorController extends Controller {
    private Parqueo $parqueoModel;
    private Espacio $espacioModel;
    private IngresoSalida $ingresoSalidaModel;
    private Tarifa $tarifaModel;
    private Reserva $reservaModel;
    private Pago $pagoModel;
    private TipoVehiculo $tipoVehiculoModel;

    public function __construct() {
        $this->parqueoModel = new Parqueo();
        $this->espacioModel = new Espacio();
        $this->ingresoSalidaModel = new IngresoSalida();
        $this->tarifaModel = new Tarifa();
        $this->reservaModel = new Reserva();
        $this->pagoModel = new Pago();
        $this->tipoVehiculoModel = new TipoVehiculo();
    }

    /**
     * Panel Principal de Caseta:
     * Cuadrícula en tiempo real organizada por sectores, estados de espacios, y lista de vehículos activos.
     */
    public function caseta(): void {
        Auth::requireRole([1, 2]); // Admin u Operador

        $parqueos = $this->parqueoModel->getActivos();
        if (empty($parqueos)) {
            $this->render('operador/caseta', [
                'titulo' => 'Panel de Caseta - Control en Vivo',
                'parqueos' => [],
                'parqueo' => null,
                'idParqueo' => 0,
                'espaciosPorSector' => [],
                'activosPorEspacio' => [],
                'reservasPorEspacio' => [],
                'activosLista' => [],
                'stats' => ['total' => 0, 'disponibles' => 0, 'ocupados' => 0, 'reservados' => 0, 'mantenimiento' => 0, 'porcentaje_ocupacion' => 0],
                'error' => 'No existen parqueos registrados o activos en el sistema.',
                'success' => null
            ]);
            return;
        }

        // Parqueo seleccionado
        $idParqueo = isset($_GET['id_parqueo']) ? (int)$_GET['id_parqueo'] : ($_SESSION['caseta_id_parqueo'] ?? (int)$parqueos[0]['id_parqueo']);
        $_SESSION['caseta_id_parqueo'] = $idParqueo;

        $parqueo = $this->parqueoModel->getConDetalle($idParqueo);
        $espacios = $this->espacioModel->getPorParqueo($idParqueo);
        $activos = $this->ingresoSalidaModel->getActivosEnParqueo($idParqueo);
        $reservas = $this->reservaModel->getActivasPorParqueo($idParqueo);

        // Mapear vehículos activos por espacio
        $activosPorEspacio = [];
        foreach ($activos as $act) {
            $activosPorEspacio[(int)$act['id_espacio']] = $act;
        }

        // Mapear reservas activas por espacio
        $reservasPorEspacio = [];
        foreach ($reservas as $res) {
            $reservasPorEspacio[(int)$res['id_espacio']] = $res;
        }

        // Agrupar espacios por piso/sector y calcular métricas
        $espaciosPorSector = [];
        $stats = [
            'total' => count($espacios),
            'disponibles' => 0,
            'ocupados' => 0,
            'reservados' => 0,
            'mantenimiento' => 0,
            'porcentaje_ocupacion' => 0
        ];

        foreach ($espacios as $esp) {
            $sector = !empty($esp['piso_sector']) ? $esp['piso_sector'] : 'Sector General';
            $espaciosPorSector[$sector][] = $esp;

            $est = $esp['estado'];
            if ($est === 'Disponible') $stats['disponibles']++;
            elseif ($est === 'Ocupado') $stats['ocupados']++;
            elseif ($est === 'Reservado') $stats['reservados']++;
            elseif ($est === 'Mantenimiento') $stats['mantenimiento']++;
        }

        $ocupadosTotal = $stats['ocupados'] + $stats['reservados'];
        $stats['porcentaje_ocupacion'] = $stats['total'] > 0 ? round(($ocupadosTotal / $stats['total']) * 100) : 0;

        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        $this->render('operador/caseta', [
            'titulo' => 'Panel de Caseta - Control en Vivo',
            'parqueos' => $parqueos,
            'parqueo' => $parqueo,
            'idParqueo' => $idParqueo,
            'espaciosPorSector' => $espaciosPorSector,
            'activosPorEspacio' => $activosPorEspacio,
            'reservasPorEspacio' => $reservasPorEspacio,
            'activosLista' => $activos,
            'stats' => $stats,
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Formulario de Registro de Ingreso (Directo o Validación QR)
     */
    public function ingreso(): void {
        Auth::requireRole([1, 2]);

        $parqueos = $this->parqueoModel->getActivos();
        $idParqueo = isset($_GET['id_parqueo']) ? (int)$_GET['id_parqueo'] : ($_SESSION['caseta_id_parqueo'] ?? (!empty($parqueos) ? (int)$parqueos[0]['id_parqueo'] : 1));
        $_SESSION['caseta_id_parqueo'] = $idParqueo;

        $tiposVehiculo = $this->tipoVehiculoModel->getAllActivos();
        $todosEspacios = $this->espacioModel->getPorParqueo($idParqueo);

        // Filtrar solo los disponibles
        $espaciosDisponibles = array_values(array_filter($todosEspacios, fn($e) => $e['estado'] === 'Disponible'));

        $preEspacioId = isset($_GET['espacio_id']) ? (int)$_GET['espacio_id'] : 0;
        $preToken = isset($_GET['token']) ? trim($_GET['token']) : '';
        $activeTab = (!empty($preToken) || (isset($_GET['tab']) && $_GET['tab'] === 'qr')) ? 'qr' : 'manual';

        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        $this->render('operador/ingreso', [
            'titulo' => 'Registrar Ingreso de Vehículo - ParkApp',
            'parqueos' => $parqueos,
            'idParqueo' => $idParqueo,
            'tiposVehiculo' => $tiposVehiculo,
            'espaciosDisponibles' => $espaciosDisponibles,
            'preEspacioId' => $preEspacioId,
            'preToken' => $preToken,
            'activeTab' => $activeTab,
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Procesa el ingreso del vehículo (Directo con ticket o Validación de QR)
     */
    public function procesarIngreso(): void {
        Auth::requireRole([1, 2]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('caseta/ingreso');
        }

        $tipoIngreso = $_POST['tipo_ingreso'] ?? 'directo';
        $idOperador = Auth::id() ?? 1;

        if ($tipoIngreso === 'directo') {
            // Ingreso Directo (Walk-in)
            $placa = strtoupper(trim($_POST['placa'] ?? ''));
            $placa = preg_replace('/\s+/', '', $placa);
            $idEspacio = (int)($_POST['id_espacio'] ?? 0);

            if (empty($placa) || strlen($placa) < 5) {
                $_SESSION['flash_error'] = 'Debe ingresar una placa boliviana válida (Ej. 2049-ZXY).';
                $this->redirect('caseta/ingreso');
            }

            // Formatear placa si no tiene guión (e.g. 2049ZXY -> 2049-ZXY)
            if (preg_match('/^([0-9]{3,4})([A-Z]{3})$/', $placa, $matches)) {
                $placa = $matches[1] . '-' . $matches[2];
            }

            // Verificar si el vehículo ya está dentro
            $activa = $this->ingresoSalidaModel->buscarEstanciaActiva($placa);
            if ($activa) {
                $_SESSION['flash_error'] = "El vehículo con placa {$placa} ya tiene una estancia activa dentro del parqueo (Ticket: {$activa['numero_ticket']}, Espacio: {$activa['codigo_espacio']}). Registre su salida antes de un nuevo ingreso.";
                $this->redirect('caseta/ingreso');
            }

            // Verificar disponibilidad del espacio
            $espacio = $this->espacioModel->findById($idEspacio);
            if (!$espacio || $espacio['estado'] !== 'Disponible') {
                $_SESSION['flash_error'] = 'El espacio seleccionado ya no se encuentra disponible. Por favor elija otro espacio libre.';
                $this->redirect('caseta/ingreso');
            }

            // Crear registro de ingreso
            $idIngreso = $this->ingresoSalidaModel->crearIngresoDirecto([
                'id_espacio' => $idEspacio,
                'id_operador_entrada' => $idOperador,
                'placa' => $placa
            ]);

            if (!$idIngreso) {
                $_SESSION['flash_error'] = 'Error al registrar el ingreso en la base de datos.';
                $this->redirect('caseta/ingreso');
            }

            // Marcar espacio como ocupado
            $this->espacioModel->actualizarEstado($idEspacio, 'Ocupado');

            $_SESSION['flash_success'] = "Ingreso registrado correctamente para la placa {$placa}.";
            $this->redirect('caseta/ticket?id=' . $idIngreso);

        } elseif ($tipoIngreso === 'qr') {
            // Ingreso por Validación QR
            $token = trim($_POST['codigo_qr_token'] ?? '');
            if (empty($token)) {
                $_SESSION['flash_error'] = 'Debe ingresar o escanear el código QR o token de reserva.';
                $this->redirect('caseta/ingreso?tab=qr');
            }

            $reserva = $this->reservaModel->findByTokenQR($token);
            if (!$reserva) {
                $_SESSION['flash_error'] = "No se encontró ninguna reserva asociada al código QR o token: {$token}.";
                $this->redirect('caseta/ingreso?tab=qr');
            }

            // Validar estado de la reserva
            if ($reserva['estado_reserva'] === 'En Parqueo') {
                $_SESSION['flash_error'] = "Esta reserva ya fue ingresada al parqueo previamente y el vehículo se encuentra en estadía activa.";
                $this->redirect('caseta/ingreso?tab=qr');
            }
            if ($reserva['estado_reserva'] === 'Cancelada') {
                $_SESSION['flash_error'] = "Esta reserva fue cancelada previamente y ha quedado sin efecto.";
                $this->redirect('caseta/ingreso?tab=qr');
            }
            if ($reserva['estado_reserva'] === 'Finalizada') {
                $_SESSION['flash_error'] = "Esta reserva ya concluyó su estadía anteriormente.";
                $this->redirect('caseta/ingreso?tab=qr');
            }
            if ($reserva['estado_reserva'] !== 'Confirmada') {
                $_SESSION['flash_error'] = "La reserva se encuentra en estado '{$reserva['estado_reserva']}' y no puede ser ingresada.";
                $this->redirect('caseta/ingreso?tab=qr');
            }

            // Validar espacio
            $idEspacio = (int)$reserva['id_espacio'];
            $idReserva = (int)$reserva['id_reserva'];
            $placa = $reserva['placa_vehiculo'];

            // Crear estancia desde reserva
            $idIngreso = $this->ingresoSalidaModel->crearIngresoDesdeReserva($idReserva, $idEspacio, $placa, $idOperador);
            if (!$idIngreso) {
                $_SESSION['flash_error'] = 'Error al registrar el ingreso vinculado a la reserva.';
                $this->redirect('caseta/ingreso?tab=qr');
            }

            // Actualizar estado de la reserva a 'En Parqueo'
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE [RESERVAS] SET estado_reserva = 'En Parqueo' WHERE id_reserva = ?");
            $stmt->execute([$idReserva]);

            // Actualizar espacio a 'Ocupado'
            $this->espacioModel->actualizarEstado($idEspacio, 'Ocupado');

            $_SESSION['flash_success'] = "¡Reserva validada con éxito! Ingreso concedido a {$reserva['conductor']} (Placa: {$placa}) en Espacio {$reserva['codigo_espacio']}.";
            $this->redirect('caseta/ticket?id=' . $idIngreso);
        } else {
            $this->redirect('caseta/ingreso');
        }
    }

    /**
     * Pantalla de Cobro y Liquidación de Salida
     */
    public function salidaCobro(): void {
        Auth::requireRole([1, 2]);

        $parqueos = $this->parqueoModel->getActivos();
        $idParqueo = isset($_GET['id_parqueo']) ? (int)$_GET['id_parqueo'] : ($_SESSION['caseta_id_parqueo'] ?? (!empty($parqueos) ? (int)$parqueos[0]['id_parqueo'] : 1));
        $_SESSION['caseta_id_parqueo'] = $idParqueo;

        $activos = $this->ingresoSalidaModel->getActivosEnParqueo($idParqueo);

        $buscar = trim($_GET['buscar'] ?? '');
        $idDirecto = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $estancia = null;
        $calculoTarifa = null;
        $minutosEstancia = 0;
        $tiempoFormateado = '';
        $montoAdelanto = 0.0;
        $totalFinal = 0.0;

        if ($idDirecto > 0) {
            $encontrado = $this->ingresoSalidaModel->getDetalleEstancia($idDirecto);
            if ($encontrado && $encontrado['estado_estancia'] === 'En Parqueo') {
                $estancia = $encontrado;
                $buscar = $estancia['numero_ticket'];
            }
        } elseif (!empty($buscar)) {
            $estancia = $this->ingresoSalidaModel->buscarEstanciaActiva($buscar);
        }

        if (!empty($buscar) && !$estancia && $idDirecto === 0) {
            $_SESSION['flash_error'] = "No se encontró ningún vehículo activo en parqueo con el ticket o placa: " . htmlspecialchars($buscar);
        }

        if ($estancia) {
            $tsEntrada = strtotime($estancia['fecha_hora_entrada']);
            $tsSalida = time();
            $diffSegundos = max(60, $tsSalida - $tsEntrada);
            $minutosEstancia = (int)ceil($diffSegundos / 60);

            $horas = floor($minutosEstancia / 60);
            $minRestantes = $minutosEstancia % 60;
            $dias = floor($horas / 24);
            $horasDelDia = $horas % 24;

            if ($dias > 0) {
                $tiempoFormateado = "{$dias}d {$horasDelDia}h {$minRestantes}m";
            } elseif ($horas > 0) {
                $tiempoFormateado = "{$horas}h {$minRestantes}m";
            } else {
                $tiempoFormateado = "{$minRestantes} min";
            }

            // Cálculo de tarifa
            $calculoTarifa = $this->tarifaModel->calcularMonto((int)$estancia['id_parqueo'], (int)$estancia['id_tipo_vehiculo'], $minutosEstancia);
            $totalCalculado = $calculoTarifa['total'];

            // Verificar si tuvo adelanto de reserva
            if (!empty($estancia['id_reserva'])) {
                $db = Database::getConnection();
                $stmtR = $db->prepare("SELECT monto_adelanto, pago_confirmado FROM [RESERVAS] WHERE id_reserva = ?");
                $stmtR->execute([(int)$estancia['id_reserva']]);
                $resData = $stmtR->fetch(PDO::FETCH_ASSOC);
                if ($resData && !empty($resData['pago_confirmado'])) {
                    $montoAdelanto = (float)$resData['monto_adelanto'];
                }
            }

            $totalFinal = max(0.0, round($totalCalculado - $montoAdelanto, 2));
        }

        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        $this->render('operador/salida_cobro', [
            'titulo' => 'Cobro y Salida de Vehículo - ParkApp',
            'parqueos' => $parqueos,
            'idParqueo' => $idParqueo,
            'activos' => $activos,
            'buscar' => $buscar,
            'estancia' => $estancia,
            'minutosEstancia' => $minutosEstancia,
            'tiempoFormateado' => $tiempoFormateado,
            'calculoTarifa' => $calculoTarifa,
            'montoAdelanto' => $montoAdelanto,
            'totalFinal' => $totalFinal,
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Procesa la liquidación del cobro y registra la salida del vehículo
     */
    public function procesarSalidaCobro(): void {
        Auth::requireRole([1, 2]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('caseta/salida');
        }

        $idIngreso = (int)($_POST['id_ingreso_salida'] ?? 0);
        $metodoPago = trim($_POST['metodo_pago'] ?? 'Efectivo');
        $referencia = trim($_POST['referencia_transaccion'] ?? '');
        $montoRecibido = (float)($_POST['monto_recibido'] ?? 0);
        $idOperador = Auth::id() ?? 1;

        $estancia = $this->ingresoSalidaModel->getDetalleEstancia($idIngreso);
        if (!$estancia || $estancia['estado_estancia'] !== 'En Parqueo') {
            $_SESSION['flash_error'] = 'La estancia seleccionada ya no se encuentra activa o no existe.';
            $this->redirect('caseta');
        }

        // Recalcular minutos y total dinámicamente en servidor
        $tsEntrada = strtotime($estancia['fecha_hora_entrada']);
        $tsSalida = time();
        $minutosTotales = (int)ceil(max(60, $tsSalida - $tsEntrada) / 60);

        $calc = $this->tarifaModel->calcularMonto((int)$estancia['id_parqueo'], (int)$estancia['id_tipo_vehiculo'], $minutosTotales);
        $totalCalculado = $calc['total'];

        $montoAdelanto = 0.0;
        if (!empty($estancia['id_reserva'])) {
            $db = Database::getConnection();
            $stmtR = $db->prepare("SELECT monto_adelanto, pago_confirmado FROM [RESERVAS] WHERE id_reserva = ?");
            $stmtR->execute([(int)$estancia['id_reserva']]);
            $resData = $stmtR->fetch(PDO::FETCH_ASSOC);
            if ($resData && !empty($resData['pago_confirmado'])) {
                $montoAdelanto = (float)$resData['monto_adelanto'];
            }
        }

        $totalAPagar = max(0.0, round($totalCalculado - $montoAdelanto, 2));

        // Validaciones según el método de pago
        if ($metodoPago === 'Efectivo') {
            if ($totalAPagar > 0 && $montoRecibido < $totalAPagar) {
                $_SESSION['flash_error'] = "El monto en efectivo recibido (Bs. " . number_format($montoRecibido, 2) . ") no cubre el total adeudado (Bs. " . number_format($totalAPagar, 2) . ").";
                $this->redirect('caseta/salida?buscar=' . urlencode($estancia['numero_ticket']));
            }
            if (empty($referencia)) {
                $referencia = 'EFE-' . date('Ymd-His');
            }
        } elseif ($metodoPago === 'QR Estático') {
            if (empty($referencia)) {
                $_SESSION['flash_error'] = 'Para pagos con QR Institucional debe ingresar el número de referencia o comprobante bancario exhibido por el conductor.';
                $this->redirect('caseta/salida?buscar=' . urlencode($estancia['numero_ticket']));
            }
        }

        // Registrar pago
        $idPago = $this->pagoModel->crearPago([
            'id_ingreso_salida' => $idIngreso,
            'id_reserva' => !empty($estancia['id_reserva']) ? (int)$estancia['id_reserva'] : null,
            'monto' => $totalAPagar,
            'metodo_pago' => $metodoPago,
            'referencia_transaccion' => $referencia,
            'id_operador_cobro' => $idOperador
        ]);

        if (!$idPago) {
            $_SESSION['flash_error'] = 'Error al registrar el comprobante de pago en el sistema.';
            $this->redirect('caseta/salida?buscar=' . urlencode($estancia['numero_ticket']));
        }

        // Finalizar estancia
        $this->ingresoSalidaModel->finalizarEstanciaYCobro($idIngreso, $idOperador, $minutosTotales, $totalAPagar);

        // Liberar espacio en parqueo
        $this->espacioModel->actualizarEstado((int)$estancia['id_espacio'], 'Disponible');

        // Si fue reserva, finalizarla
        if (!empty($estancia['id_reserva'])) {
            $db = Database::getConnection();
            $stmtRes = $db->prepare("UPDATE [RESERVAS] SET estado_reserva = 'Finalizada' WHERE id_reserva = ?");
            $stmtRes->execute([(int)$estancia['id_reserva']]);
        }

        $_SESSION['flash_success'] = "Salida y cobro procesados correctamente para la placa {$estancia['placa']}. Espacio {$estancia['codigo_espacio']} liberado.";
        $this->redirect('caseta/recibo?id=' . $idIngreso);
    }

    /**
     * Impresión de Ticket Térmico de Ingreso
     */
    public function ticketPrint(): void {
        Auth::requireRole([1, 2]);

        $id = (int)($_GET['id'] ?? 0);
        $estancia = $this->ingresoSalidaModel->getDetalleEstancia($id);

        if (!$estancia) {
            $_SESSION['flash_error'] = 'Ticket no encontrado.';
            $this->redirect('caseta');
        }

        $this->render('operador/ticket_print', [
            'titulo' => 'Ticket de Ingreso - ' . $estancia['numero_ticket'],
            'estancia' => $estancia
        ], false);
    }

    /**
     * Impresión de Recibo Térmico de Cobro / Salida
     */
    public function reciboPrint(): void {
        Auth::requireRole([1, 2]);

        $id = (int)($_GET['id'] ?? 0);
        $estancia = $this->ingresoSalidaModel->getDetalleEstancia($id);

        if (!$estancia) {
            $_SESSION['flash_error'] = 'Comprobante de estancia no encontrado.';
            $this->redirect('caseta');
        }

        $pago = $this->pagoModel->findByIngresoSalida($id);

        $this->render('operador/recibo_print', [
            'titulo' => 'Recibo de Pago - ' . $estancia['numero_ticket'],
            'estancia' => $estancia,
            'pago' => $pago
        ], false);
    }
}

