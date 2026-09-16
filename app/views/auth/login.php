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
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-sm p-4" style="max-width: 420px; width: 100%;">
        <div class="text-center mb-4">
            <i class="bi bi-p-square-fill text-warning fs-1"></i>
            <h4 class="fw-bold mt-2">ParkApp El Alto</h4>
            <p class="text-muted small">Ingreso al Sistema de Parqueos</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i><?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/login" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" placeholder="ejemplo@parkapp.bo" maxlength="100" autocomplete="email" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" maxlength="50" autocomplete="current-password" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)" title="Mostrar / Ocultar contraseña">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar
            </button>
        </form>

        <hr class="my-4">
        <div class="text-center">
            <span class="text-muted small">¿Eres conductor y no tienes cuenta?</span><br>
            <a href="<?= BASE_URL ?>/registro" class="text-decoration-none fw-semibold">Crear cuenta nueva</a>
        </div>
        <div class="text-center mt-2">
            <a href="<?= BASE_URL ?>/disponibilidad" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Ver disponibilidad sin iniciar sesión
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/js/app.js"></script>
</body>
</html>
