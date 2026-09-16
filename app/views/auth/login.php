<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - ParkApp El Alto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/app.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4" style="background: linear-gradient(135deg, #101c2e 0%, #1e3a5f 100%);">
    <div class="card formal-card p-4 p-md-5 my-3" style="max-width: 440px; width: 100%;">
        <div class="text-center formal-header mb-4">
            <div class="brand-badge mb-3">
                <i class="bi bi-p-square"></i>
            </div>
            <h3 class="fw-bold mb-1" style="color: var(--park-primary); letter-spacing: -0.5px;">PARKAPP</h3>
            <p class="text-muted small mb-0">Sistema de Gestión y Reserva de Parqueos</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="classic-alert classic-alert-danger mb-4" role="alert">
                <i class="bi bi-shield-exclamation alert-icon"></i>
                <div>
                    <strong>Acceso denegado:</strong><br>
                    <?= htmlspecialchars($error) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="classic-alert classic-alert-success mb-4" role="alert">
                <i class="bi bi-check-circle alert-icon"></i>
                <div>
                    <strong>Operación exitosa:</strong><br>
                    <?= htmlspecialchars($success) ?>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/login" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" placeholder="usuario@parkapp.bo" maxlength="100" autocomplete="email" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="password" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Contraseña</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" maxlength="50" autocomplete="current-password" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)" title="Mostrar / Ocultar contraseña">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-formal w-100 py-2 mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>
        </form>

        <hr class="my-4 text-muted">
        <div class="text-center">
            <span class="text-muted small">¿Es conductor nuevo en el sistema?</span><br>
            <a href="<?= BASE_URL ?>/registro" class="fw-semibold text-decoration-none" style="color: var(--park-primary);">Registrar nueva cuenta</a>
        </div>
        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/disponibilidad" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Ver parqueos disponibles sin ingresar
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/js/app.js"></script>
</body>
</html>
