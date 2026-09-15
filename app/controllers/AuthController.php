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
        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        $this->render('auth/login', [
            'titulo' => 'Iniciar Sesión - ParkApp',
            'error' => $error,
            'success' => $success,
            'email' => $_GET['email'] ?? ''
        ], false);
    }

    public function authenticate(): void {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $this->render('auth/login', [
                'titulo' => 'Iniciar Sesión - ParkApp',
                'error' => 'Por favor ingrese su correo y contraseña.',
                'email' => $email
            ], false);
            return;
        }

        $usuario = $this->usuarioModel->findByEmail($email);

        if (!$usuario) {
            $this->render('auth/login', [
                'titulo' => 'Iniciar Sesión - ParkApp',
                'error' => 'Correo o contraseña incorrectos.',
                'email' => $email
            ], false);
            return;
        }

        // Validar estado de la cuenta
        $estado = $usuario['estado'] ?? false;
        if ($estado !== true && $estado !== 1 && $estado !== '1' && $estado !== -1 && $estado !== '-1') {
            $this->render('auth/login', [
                'titulo' => 'Iniciar Sesión - ParkApp',
                'error' => 'Tu cuenta se encuentra inhabilitada. Contacta al administrador.',
                'email' => $email
            ], false);
            return;
        }

        // Verificación de contraseña: hash seguro BCRYPT o fallback para semillas de pruebas
        $passwordValida = false;
        if (password_verify($password, $usuario['password_hash'])) {
            $passwordValida = true;
        } elseif ($password === $usuario['password_hash']) {
            // Contraseña de prueba en texto plano -> se valida y se actualiza a hash seguro
            $passwordValida = true;
            $nuevoHash = password_hash($password, PASSWORD_BCRYPT);
            $this->usuarioModel->actualizarPassword((int)$usuario['id_usuario'], $nuevoHash);
            $usuario['password_hash'] = $nuevoHash;
        }

        if (!$passwordValida) {
            $this->render('auth/login', [
                'titulo' => 'Iniciar Sesión - ParkApp',
                'error' => 'Correo o contraseña incorrectos.',
                'email' => $email
            ], false);
            return;
        }

        // Iniciar sesión
        Auth::login($usuario);

        // Redirección inteligente por rol
        $this->redirectByRole();
    }

    public function registro(): void {
        if (Auth::check()) {
            $this->redirectByRole();
        }
        $this->render('auth/registro', [
            'titulo' => 'Registro de Conductor - ParkApp',
            'error' => null,
            'valores' => []
        ], false);
    }

    public function register(): void {
        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $ciNit = trim($_POST['ci_nit'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = trim($_POST['password'] ?? '');

        $valores = [
            'nombre_completo' => $nombreCompleto,
            'ci_nit' => $ciNit,
            'telefono' => $telefono,
            'email' => $email
        ];

        // Validaciones requeridas
        if (empty($nombreCompleto) || empty($ciNit) || empty($telefono) || empty($email) || empty($password)) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'Todos los campos son obligatorios.',
                'valores' => $valores
            ], false);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'El formato del correo electrónico no es válido.',
                'valores' => $valores
            ], false);
            return;
        }

        if (strlen($password) < 6) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'La contraseña debe tener al menos 6 caracteres.',
                'valores' => $valores
            ], false);
            return;
        }

        if ($this->usuarioModel->emailExiste($email)) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'El correo electrónico ya se encuentra registrado en el sistema.',
                'valores' => $valores
            ], false);
            return;
        }

        // Registro de nuevo usuario (Rol 3 = Conductor)
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $nuevoId = $this->usuarioModel->crearUsuario([
            'id_rol' => 3,
            'nombre_completo' => $nombreCompleto,
            'ci_nit' => $ciNit,
            'telefono' => $telefono,
            'email' => $email,
            'password_hash' => $passwordHash
        ]);

        if (!$nuevoId) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'Ocurrió un problema al registrar la cuenta. Intenta nuevamente.',
                'valores' => $valores
            ], false);
            return;
        }

        // Obtener usuario completo e iniciar sesión automáticamente
        $usuarioCreado = $this->usuarioModel->findById($nuevoId);
        if ($usuarioCreado) {
            Auth::login($usuarioCreado);
        }

        $this->redirect('disponibilidad');
    }

    public function logout(): void {
        Auth::logout();
        $_SESSION['flash_success'] = 'Has cerrado sesión correctamente.';
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
