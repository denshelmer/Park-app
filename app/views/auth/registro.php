<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Conductor - ParkApp El Alto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/app.css?v=<?= time() ?>">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4 px-3 auth-screen-wrapper" style="background: linear-gradient(135deg, #101c2e 0%, #1e3a5f 100%);">
    
    <!-- 1. Botón Explícito de Flecha Atrás (Esquina Superior Izquierda hacia Login) -->
    <a href="<?= BASE_URL ?>/login" class="auth-back-btn" title="Volver al inicio de sesión">
        <i class="bi bi-arrow-left"></i>
        <span>Iniciar Sesión</span>
    </a>

    <div class="card formal-card p-4 p-md-5 my-4 shadow-lg" style="max-width: 540px; width: 100%;">
        
        <!-- 2. Logo / Cabecera como enlace al index -->
        <div class="text-center formal-header mb-4">
            <a href="<?= BASE_URL ?>/" class="d-inline-block text-decoration-none auth-brand-link" title="Ir a la página principal de ParkApp">
                <div class="brand-badge mb-3 mx-auto">
                    <i class="bi bi-p-square-fill" style="color: #c89234;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="color: var(--park-primary); letter-spacing: -0.5px;">Registro de Conductor</h3>
            </a>
            <p class="text-muted small mb-0">Cree su cuenta para reservar espacios de parqueo en tiempo real</p>
        </div>

        <!-- 3. Alertas con Alta Visibilidad y Contraste -->
        <?php if (!empty($error)): ?>
            <div class="auth-alert auth-alert-danger" role="alert">
                <div class="auth-alert-title">Revise los datos ingresados</div>
                <div class="auth-alert-body"><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/registro" method="POST">
            <!-- Nombre Completo -->
            <div class="mb-3">
                <label for="nombre_completo" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Nombre Completo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                           value="<?= htmlspecialchars($valores['nombre_completo'] ?? '') ?>" 
                           pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,80}" minlength="3" maxlength="80"
                           title="Solo se permiten letras y espacios (entre 3 y 80 caracteres)"
                           placeholder="Ej. Juan Pérez Ramos" required autofocus>
                </div>
            </div>

            <!-- CI con selector de Departamento y Teléfono -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-7">
                    <label for="ci_numero" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Cédula de Identidad (CI)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                        <input type="text" class="form-control numeric-only" id="ci_numero" name="ci_numero" 
                               value="<?= htmlspecialchars($valores['ci_numero'] ?? '') ?>" 
                               pattern="[0-9]{4,10}" inputmode="numeric" minlength="4" maxlength="10"
                               placeholder="Ej. 6849201" title="Solo números (4 a 10 dígitos)" required>
                        <select class="form-select" id="ci_extension" name="ci_extension" style="max-width: 82px;" required title="Extensión de departamento">
                            <?php 
                            $extActual = $valores['ci_extension'] ?? 'LP';
                            $departamentos = ['LP', 'CB', 'SC', 'OR', 'PT', 'TJ', 'CH', 'BN', 'PA', 'S/E'];
                            foreach ($departamentos as $dep):
                            ?>
                                <option value="<?= $dep ?>" <?= $extActual === $dep ? 'selected' : '' ?>><?= $dep ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <label for="telefono" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Teléfono / Celular</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="tel" class="form-control numeric-only" id="telefono" name="telefono" 
                               value="<?= htmlspecialchars($valores['telefono'] ?? '') ?>" 
                               pattern="[0-9]{7,10}" inputmode="numeric" minlength="7" maxlength="10"
                               title="Entre 7 y 10 dígitos numéricos"
                               placeholder="Ej. 71234567" required>
                    </div>
                </div>
            </div>

            <!-- Correo Electrónico -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($valores['email'] ?? '') ?>" 
                           maxlength="100" autocomplete="email"
                           placeholder="conductor@ejemplo.com" required>
                </div>
            </div>

            <!-- Contraseña y Confirmación -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label for="password" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               minlength="8" maxlength="50" autocomplete="new-password"
                               placeholder="Mínimo 8 caracteres" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)" title="Mostrar / Ocultar">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="password_confirm" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Confirmar Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                               minlength="8" maxlength="50" autocomplete="new-password"
                               placeholder="Repita la clave" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirm', this)" title="Mostrar / Ocultar">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <small class="text-muted">La contraseña debe contener al menos 8 caracteres.</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-formal w-100 py-2 mb-3">
                Registrar Cuenta
            </button>
        </form>

        <hr class="my-4 text-muted">
        <div class="text-center">
            <span class="text-muted small">¿Ya dispone de una cuenta registrada?</span><br>
            <a href="<?= BASE_URL ?>/login" class="fw-semibold text-decoration-none" style="color: var(--park-primary);">Iniciar Sesión</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/js/app.js"></script>
</body>
</html>
