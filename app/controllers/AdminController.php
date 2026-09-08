<?php
/**
 * AdminController
 * SECCIÓN 3: Panel de Administración
 * Gestión de parqueos, matriz de espacios, tarifas y usuarios.
 */
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/models/Parqueo.php';
require_once APP_PATH . '/models/Espacio.php';
require_once APP_PATH . '/models/Tarifa.php';
require_once APP_PATH . '/models/Usuario.php';

class AdminController extends Controller {
    private Parqueo $parqueoModel;
    private Espacio $espacioModel;
    private Tarifa $tarifaModel;
    private Usuario $usuarioModel;

    public function __construct() {
        $this->parqueoModel = new Parqueo();
        $this->espacioModel = new Espacio();
        $this->tarifaModel = new Tarifa();
        $this->usuarioModel = new Usuario();
    }

    public function dashboard(): void {
        Auth::requireRole(1); // Solo Administrador
        $this->render('admin/dashboard', ['titulo' => 'Panel de Administración']);
    }

    public function parqueos(): void {
        Auth::requireRole(1);
        $this->render('admin/parqueos', ['titulo' => 'Gestión de Parqueos']);
    }

    public function espacios(): void {
        Auth::requireRole(1);
        $this->render('admin/espacios', ['titulo' => 'Gestión de Espacios']);
    }

    public function tarifas(): void {
        Auth::requireRole(1);
        $this->render('admin/tarifas', ['titulo' => 'Gestión de Tarifas']);
    }

    public function usuarios(): void {
        Auth::requireRole(1);
        $this->render('admin/usuarios', ['titulo' => 'Gestión de Personal y Usuarios']);
    }
}
