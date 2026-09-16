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
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $this->render('auth/login', [
                'titulo' => 'Iniciar Sesión - ParkApp',
                'error' => 'Por favor ingrese su correo y contraseña.',
                'email' => $email
            ], false);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/login', [
                'titulo' => 'Iniciar Sesión - ParkApp',
                'error' => 'El formato del correo electrónico no es válido.',
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
        $ciNumero = trim($_POST['ci_numero'] ?? '');
        $ciExtension = trim($_POST['ci_extension'] ?? 'LP');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = trim($_POST['password'] ?? '');
        $passwordConfirm = trim($_POST['password_confirm'] ?? '');

        // Formateo de CI con extensión de Bolivia
        $extensionesValidas = ['LP', 'CB', 'SC', 'OR', 'PT', 'TJ', 'CH', 'BN', 'PA', 'S/E'];
        if (!in_array($ciExtension, $extensionesValidas)) {
            $ciExtension = 'LP';
        }
        $ciNit = ($ciExtension !== 'S/E' && !empty($ciExtension)) ? "{$ciNumero} {$ciExtension}" : $ciNumero;

        $valores = [
            'nombre_completo' => $nombreCompleto,
            'ci_numero' => $ciNumero,
            'ci_extension' => $ciExtension,
            'ci_nit' => $ciNit,
            'telefono' => $telefono,
            'email' => $email
        ];

        // 1. Validar campos obligatorios
        if (empty($nombreCompleto) || empty($ciNumero) || empty($telefono) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'Por favor complete todos los campos del formulario.',
                'valores' => $valores
            ], false);
            return;
        }

        // 2. Validar Nombre Completo (solo letras, tildes y espacios, 3-80 caracteres)
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,80}$/u', $nombreCompleto)) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'El nombre completo solo debe contener letras y espacios (entre 3 y 80 caracteres).',
                'valores' => $valores
            ], false);
            return;
        }

        // 3. Validar Cédula de Identidad (número numérico entre 4 y 10 dígitos)
        if (!preg_match('/^[0-9]{4,10}$/', $ciNumero)) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'El número de CI debe contener únicamente dígitos numéricos (entre 4 y 10 dígitos).',
                'valores' => $valores
            ], false);
            return;
        }

        // 4. Validar Teléfono (solo números, 7-10 dígitos)
        if (!preg_match('/^[0-9]{7,10}$/', $telefono)) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'El teléfono debe contener únicamente números (entre 7 y 10 dígitos).',
                'valores' => $valores
            ], false);
            return;
        }

        // 5. Validar formato de Correo Electrónico
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'El formato del correo electrónico ingresado no es válido.',
                'valores' => $valores
            ], false);
            return;
        }

        // 6. Validar longitud de Contraseña (mínimo 8 caracteres)
        if (strlen($password) < 8) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'La contraseña debe tener al menos 8 caracteres.',
                'valores' => $valores
            ], false);
            return;
        }

        // 7. Validar coincidencia de Contraseñas
        if ($password !== $passwordConfirm) {
            $this->render('auth/registro', [
                'titulo' => 'Registro de Conductor - ParkApp',
                'error' => 'Las contraseñas ingresadas no coinciden. Por favor verifíquelas.',
                'valores' => $valores
            ], false);
            return;
        }

        // 8. Validar correo no duplicado en el sistema
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
