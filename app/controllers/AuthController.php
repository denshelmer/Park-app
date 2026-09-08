<?php
/**
 * AuthController
 * Gestiona el inicio de sesión, registro de conductores y cierre de sesión.
 */
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/models/Usuario.php';

class AuthController extends Controller {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function login(): void {
        if (Auth::check()) {
            $this->redirectByRole();
        }
        $this->render('auth/login', ['titulo' => 'Iniciar Sesión - ParkApp'], false);
    }

    public function authenticate(): void {
        // En los próximos pasos se integrará la validación completa
        $this->redirect('login');
    }

    public function registro(): void {
        $this->render('auth/registro', ['titulo' => 'Registro de Conductor - ParkApp'], false);
    }

    public function logout(): void {
        Auth::logout();
        $this->redirect('login');
    }

    private function redirectByRole(): void {
        $rol = Auth::roleId();
        if ($rol === 1) {
            $this->redirect('admin/dashboard');
        } elseif ($rol === 2) {
            $this->redirect('caseta');
        } else {
            $this->redirect('disponibilidad');
        }
    }
}
