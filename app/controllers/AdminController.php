<?php
/**
 * AdminController
 * SECCIÓN 3: Panel de Administración
 * Gestión gerencial, parqueos, matriz de espacios, tarifas y usuarios.
 */
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/models/Parqueo.php';
require_once APP_PATH . '/models/Espacio.php';
require_once APP_PATH . '/models/Tarifa.php';
require_once APP_PATH . '/models/Usuario.php';
require_once APP_PATH . '/models/Pago.php';
require_once APP_PATH . '/models/IngresoSalida.php';
require_once APP_PATH . '/models/TipoVehiculo.php';
require_once APP_PATH . '/models/Reserva.php';

class AdminController extends Controller {
    private Parqueo $parqueoModel;
    private Espacio $espacioModel;
    private Tarifa $tarifaModel;
    private Usuario $usuarioModel;
    private Pago $pagoModel;
    private IngresoSalida $ingresoSalidaModel;
    private TipoVehiculo $tipoVehiculoModel;
    private Reserva $reservaModel;

    public function __construct() {
        $this->parqueoModel = new Parqueo();
        $this->espacioModel = new Espacio();
        $this->tarifaModel = new Tarifa();
        $this->usuarioModel = new Usuario();
        $this->pagoModel = new Pago();
        $this->ingresoSalidaModel = new IngresoSalida();
        $this->tipoVehiculoModel = new TipoVehiculo();
        $this->reservaModel = new Reserva();
    }

    /**
     * Dashboard Gerencial: Métricas en tiempo real, KPIs del día y movimientos
     */
    public function dashboard(): void {
        Auth::requireRole(1);

        $idParqueo = isset($_GET['parqueo_id']) && is_numeric($_GET['parqueo_id']) && (int)$_GET['parqueo_id'] > 0 
            ? (int)$_GET['parqueo_id'] 
            : null;

        // Liberar reservas vencidas antes de obtener conteos y métricas
        $this->reservaModel->liberarReservasVencidas($idParqueo);

        $parqueos = $this->parqueoModel->getAllActivos();
        $conteoEspacios = $this->espacioModel->getConteoPorEstado($idParqueo ?? 0);
        $metricasHoy = $this->pagoModel->getMetricasHoy($idParqueo);
        $movimientosHoy = $this->ingresoSalidaModel->getMovimientosHoy($idParqueo);

        $vehiculosActivos = 0;
        foreach ($movimientosHoy as $m) {
            if ($m['estado_estancia'] === 'En Parqueo') {
                $vehiculosActivos++;
            }
        }

        $this->render('admin/dashboard', [
            'titulo' => 'Panel de Control Gerencial',
            'parqueos' => $parqueos,
            'idParqueo' => $idParqueo,
            'conteoEspacios' => $conteoEspacios,
            'metricasHoy' => $metricasHoy,
            'movimientosHoy' => $movimientosHoy,
            'vehiculosActivos' => $vehiculosActivos
        ]);
    }

    /**
     * Gestión de Sedes / Parqueos
     */
    public function parqueos(): void {
        Auth::requireRole(1);
        $parqueos = $this->parqueoModel->getAll();
        $this->render('admin/parqueos', [
            'titulo' => 'Gestión de Parqueos y Sedes',
            'parqueos' => $parqueos
        ]);
    }

    /**
     * Guarda o actualiza una sede de parqueo
     */
    public function guardarParqueo(): void {
        Auth::requireRole(1);

        $idParqueo = (int)($_POST['id_parqueo'] ?? 0);
        $nombre = trim($_POST['nombre_parqueo'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $zona = trim($_POST['zona'] ?? '');
        $capacidad = (int)($_POST['capacidad_total'] ?? 0);
        $horaApertura = trim($_POST['hora_apertura'] ?? '06:00');
        $horaCierre = trim($_POST['hora_cierre'] ?? '23:00');

        if (empty($nombre) || empty($direccion) || empty($zona) || $capacidad <= 0) {
            $_SESSION['flash_error'] = 'Por favor complete todos los campos requeridos con datos válidos.';
            $this->redirect('admin/parqueos');
            return;
        }

        $datos = [
            'nombre_parqueo' => $nombre,
            'direccion' => $direccion,
            'zona' => $zona,
            'capacidad_total' => $capacidad,
            'hora_apertura' => $horaApertura,
            'hora_cierre' => $horaCierre
        ];

        if ($idParqueo > 0) {
            $this->parqueoModel->actualizarParqueo($idParqueo, $datos);
            $_SESSION['flash_success'] = "Parqueo '{$nombre}' actualizado correctamente.";
        } else {
            $this->parqueoModel->crearParqueo($datos);
            $_SESSION['flash_success'] = "Nuevo parqueo '{$nombre}' registrado exitosamente.";
        }

        $this->redirect('admin/parqueos');
    }

    /**
     * Habilita o deshabilita un parqueo
     */
    public function cambiarEstadoParqueo(): void {
        Auth::requireRole(1);

        $idParqueo = (int)($_POST['id_parqueo'] ?? 0);
        $estado = !empty($_POST['estado']);

        if ($idParqueo > 0) {
            $this->parqueoModel->cambiarEstado($idParqueo, $estado);
            $_SESSION['flash_success'] = 'El estado del parqueo ha sido actualizado correctamente.';
        }

        $this->redirect('admin/parqueos');
    }

    /**
     * Matriz de Espacios y Control de Mantenimiento
     */
    public function espacios(): void {
        Auth::requireRole(1);

        $parqueos = $this->parqueoModel->getAllActivos();
        $idParqueo = isset($_GET['parqueo_id']) && (int)$_GET['parqueo_id'] > 0 
            ? (int)$_GET['parqueo_id'] 
            : ($parqueos[0]['id_parqueo'] ?? 1);

        // Liberar reservas vencidas para refrescar estados de cajones en tiempo real
        $this->reservaModel->liberarReservasVencidas($idParqueo);

        $tiposVehiculo = $this->tipoVehiculoModel->getAllActivos();
        $espacios = $this->espacioModel->getPorParqueo($idParqueo);
        $conteo = $this->espacioModel->getConteoPorEstado($idParqueo);

        $this->render('admin/espacios', [
            'titulo' => 'Matriz de Espacios y Mantenimiento',
            'parqueos' => $parqueos,
            'idParqueo' => $idParqueo,
            'tiposVehiculo' => $tiposVehiculo,
            'espacios' => $espacios,
            'conteo' => $conteo
        ]);
    }

    /**
     * Guarda o actualiza un espacio de parqueo
     */
    public function guardarEspacio(): void {
        Auth::requireRole(1);

        $idEspacio = (int)($_POST['id_espacio'] ?? 0);
        $idParqueo = (int)($_POST['id_parqueo'] ?? 0);
        $idTipoVehiculo = (int)($_POST['id_tipo_vehiculo'] ?? 1);
        $codigo = trim(strtoupper($_POST['codigo_espacio'] ?? ''));
        $pisoSector = trim($_POST['piso_sector'] ?? 'Piso 1');
        $estado = trim($_POST['estado'] ?? 'Disponible');

        if (empty($codigo) || $idParqueo <= 0) {
            $_SESSION['flash_error'] = 'Debe especificar el código de espacio y el parqueo correspondiente.';
            $this->redirect('admin/espacios?parqueo_id=' . $idParqueo);
            return;
        }

        if ($this->espacioModel->codigoExisteEnParqueo($idParqueo, $codigo, $idEspacio)) {
            $_SESSION['flash_error'] = "El código '{$codigo}' ya existe registrado en este parqueo.";
            $this->redirect('admin/espacios?parqueo_id=' . $idParqueo);
            return;
        }

        $datos = [
            'id_parqueo' => $idParqueo,
            'id_tipo_vehiculo' => $idTipoVehiculo,
            'codigo_espacio' => $codigo,
            'piso_sector' => $pisoSector,
            'estado' => $estado
        ];

        if ($idEspacio > 0) {
            $this->espacioModel->actualizarEspacio($idEspacio, $datos);
            $_SESSION['flash_success'] = "Espacio '{$codigo}' actualizado correctamente.";
        } else {
            $this->espacioModel->crearEspacio($datos);
            $_SESSION['flash_success'] = "Nuevo espacio '{$codigo}' creado exitosamente.";
        }

        $this->redirect('admin/espacios?parqueo_id=' . $idParqueo);
    }

    /**
     * Cambia el estado de un espacio (p. ej. Disponible <-> Mantenimiento)
     */
    public function cambiarEstadoEspacio(): void {
        Auth::requireRole(1);

        $idEspacio = (int)($_POST['id_espacio'] ?? 0);
        $idParqueo = (int)($_POST['id_parqueo'] ?? 0);
        $nuevoEstado = trim($_POST['nuevo_estado'] ?? 'Disponible');

        $estadosValidos = ['Disponible', 'Mantenimiento', 'Ocupado', 'Reservado'];
        if (in_array($nuevoEstado, $estadosValidos) && $idEspacio > 0) {
            $this->espacioModel->actualizarEstado($idEspacio, $nuevoEstado);
            $_SESSION['flash_success'] = "El espacio fue actualizado a estado '{$nuevoEstado}'.";
        }

        $this->redirect('admin/espacios?parqueo_id=' . $idParqueo);
    }

    /**
     * Configuración de Tarifas y Tolerancias
     */
    public function tarifas(): void {
        Auth::requireRole(1);

        $tarifas = $this->tarifaModel->getAllConDetalle();
        $parqueos = $this->parqueoModel->getAllActivos();
        $tiposVehiculo = $this->tipoVehiculoModel->getAllActivos();

        $this->render('admin/tarifas', [
            'titulo' => 'Políticas Tarifarias y Tolerancias',
            'tarifas' => $tarifas,
            'parqueos' => $parqueos,
            'tiposVehiculo' => $tiposVehiculo
        ]);
    }

    /**
     * Guarda o actualiza una regla tarifaria
     */
    public function guardarTarifa(): void {
        Auth::requireRole(1);

        $idTarifa = (int)($_POST['id_tarifa'] ?? 0);
        $idParqueo = (int)($_POST['id_parqueo'] ?? 0);
        $idTipoVehiculo = (int)($_POST['id_tipo_vehiculo'] ?? 0);
        $precioHora = (float)($_POST['precio_hora'] ?? 0);
        $precioFraccion = (float)($_POST['precio_fraccion'] ?? 0);
        $precioDia = (float)($_POST['precio_dia'] ?? 0);
        $tolerancia = (int)($_POST['tolerancia_minutos'] ?? 10);
        $vigente = !empty($_POST['vigente']);

        if ($idParqueo <= 0 || $idTipoVehiculo <= 0 || $precioHora <= 0) {
            $_SESSION['flash_error'] = 'Debe indicar parqueo, tipo de vehículo y una tarifa por hora válida.';
            $this->redirect('admin/tarifas');
            return;
        }

        if ($precioFraccion <= 0) {
            $precioFraccion = round($precioHora / 2, 2);
        }
        if ($precioDia <= 0) {
            $precioDia = round($precioHora * 8, 2);
        }

        $datos = [
            'id_parqueo' => $idParqueo,
            'id_tipo_vehiculo' => $idTipoVehiculo,
            'precio_hora' => $precioHora,
            'precio_fraccion' => $precioFraccion,
            'precio_dia' => $precioDia,
            'tolerancia_minutos' => $tolerancia,
            'vigente' => $vigente
        ];

        if ($idTarifa > 0) {
            $this->tarifaModel->actualizarTarifa($idTarifa, $datos);
            $_SESSION['flash_success'] = 'Tarifa actualizada correctamente.';
        } else {
            $this->tarifaModel->crearTarifa($datos);
            $_SESSION['flash_success'] = 'Nueva tarifa registrada y activada exitosamente.';
        }

        $this->redirect('admin/tarifas');
    }

    /**
     * Cambia la vigencia de una tarifa
     */
    public function cambiarVigenciaTarifa(): void {
        Auth::requireRole(1);

        $idTarifa = (int)($_POST['id_tarifa'] ?? 0);
        $vigente = !empty($_POST['vigente']);

        if ($idTarifa > 0) {
            $this->tarifaModel->cambiarVigencia($idTarifa, $vigente);
            $_SESSION['flash_success'] = 'Vigencia de tarifa modificada correctamente.';
        }

        $this->redirect('admin/tarifas');
    }

    /**
     * Administración de Usuarios y Personal Operativo
     */
    public function usuarios(): void {
        Auth::requireRole(1);

        $usuarios = $this->usuarioModel->getAllConRoles();
        $roles = $this->usuarioModel->getRoles();

        $this->render('admin/usuarios', [
            'titulo' => 'Gestión de Personal y Usuarios',
            'usuarios' => $usuarios,
            'roles' => $roles
        ]);
    }

    /**
     * Registra o edita un usuario
     */
    public function guardarUsuario(): void {
        Auth::requireRole(1);

        $idUsuario = (int)($_POST['id_usuario'] ?? 0);
        $nombre = trim($_POST['nombre_completo'] ?? '');
        $ciNumero = trim($_POST['ci_numero'] ?? '');
        $ciDepto = trim($_POST['ci_depto'] ?? '');
        $ciNit = trim($ciNumero . ($ciDepto !== '' ? ' ' . $ciDepto : ''));
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $idRol = (int)($_POST['id_rol'] ?? 3);
        $password = $_POST['password'] ?? '';

        if (empty($nombre) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Por favor ingrese un nombre y correo electrónico válidos.';
            $this->redirect('admin/usuarios');
            return;
        }

        if ($this->usuarioModel->emailExiste($email, $idUsuario)) {
            $_SESSION['flash_error'] = "El correo electrónico '{$email}' ya está registrado por otro usuario.";
            $this->redirect('admin/usuarios');
            return;
        }

        $datos = [
            'nombre_completo' => $nombre,
            'ci_nit' => $ciNit,
            'telefono' => $telefono,
            'email' => $email,
            'id_rol' => $idRol
        ];

        if ($idUsuario > 0) {
            $this->usuarioModel->actualizarUsuario($idUsuario, $datos);
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $_SESSION['flash_error'] = 'La contraseña debe contener al menos 6 caracteres.';
                    $this->redirect('admin/usuarios');
                    return;
                }
                $this->usuarioModel->actualizarPassword($idUsuario, password_hash($password, PASSWORD_BCRYPT));
            }
            $_SESSION['flash_success'] = "Usuario '{$nombre}' actualizado correctamente.";
        } else {
            if (empty($password) || strlen($password) < 6) {
                $_SESSION['flash_error'] = 'Para usuarios nuevos la contraseña es obligatoria y debe tener al menos 6 caracteres.';
                $this->redirect('admin/usuarios');
                return;
            }
            $datos['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
            $this->usuarioModel->crearUsuario($datos);
            $_SESSION['flash_success'] = "Usuario '{$nombre}' creado exitosamente.";
        }

        $this->redirect('admin/usuarios');
    }

    /**
     * Habilita o inhabilita la cuenta de un usuario
     */
    public function cambiarEstadoUsuario(): void {
        Auth::requireRole(1);

        $idUsuario = (int)($_POST['id_usuario'] ?? 0);
        $estado = !empty($_POST['estado']);

        if ($idUsuario === (int)Auth::user()['id_usuario'] && !$estado) {
            $_SESSION['flash_error'] = 'No puede deshabilitar su propia cuenta de administrador.';
            $this->redirect('admin/usuarios');
            return;
        }

        if ($idUsuario > 0) {
            $this->usuarioModel->cambiarEstado($idUsuario, $estado);
            $_SESSION['flash_success'] = 'Estado de cuenta actualizado correctamente.';
        }

        $this->redirect('admin/usuarios');
    }

    /**
     * Reestablece la contraseña de un usuario
     */
    public function cambiarPasswordUsuario(): void {
        Auth::requireRole(1);

        $idUsuario = (int)($_POST['id_usuario'] ?? 0);
        $nuevoPassword = $_POST['nuevo_password'] ?? '';

        if ($idUsuario <= 0 || strlen($nuevoPassword) < 6) {
            $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
            $this->redirect('admin/usuarios');
            return;
        }

        $hash = password_hash($nuevoPassword, PASSWORD_BCRYPT);
        $this->usuarioModel->actualizarPassword($idUsuario, $hash);
        $_SESSION['flash_success'] = 'Contraseña actualizada exitosamente.';

        $this->redirect('admin/usuarios');
    }
}
