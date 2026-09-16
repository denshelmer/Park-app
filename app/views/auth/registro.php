<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Conductor - ParkApp El Alto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/app.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="card shadow-sm p-4" style="max-width: 480px; width: 100%;">
        <div class="text-center mb-4">
            <i class="bi bi-person-badge text-warning fs-1"></i>
            <h4 class="fw-bold mt-2">Crear Cuenta de Conductor</h4>
            <p class="text-muted small">Reserva espacios en tiempo real en El Alto</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/registro" method="POST">
            <div class="mb-3">
                <label for="nombre_completo" class="form-label fw-semibold">Nombre Completo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                           value="<?= htmlspecialchars($valores['nombre_completo'] ?? '') ?>" 
                           pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,80}" minlength="3" maxlength="80"
                           title="Solo se permiten letras y espacios (entre 3 y 80 caracteres)"
                           placeholder="Ej. Juan Pérez Ramos" required autofocus>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label for="ci_nit" class="form-label fw-semibold">CI / Documento</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                        <input type="text" class="form-control" id="ci_nit" name="ci_nit" 
                               value="<?= htmlspecialchars($valores['ci_nit'] ?? '') ?>" 
                               pattern="[0-9a-zA-Z\s\-]{4,20}" minlength="4" maxlength="20"
                               title="Entre 4 y 20 caracteres alfanuméricos"
                               placeholder="Ej. 6849201 LP" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="telefono" class="form-label fw-semibold">Teléfono / Celular</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="tel" class="form-control" id="telefono" name="telefono" 
                               value="<?= htmlspecialchars($valores['telefono'] ?? '') ?>" 
                               pattern="[0-9]{7,10}" inputmode="numeric" minlength="7" maxlength="10"
                               title="Debe ingresar entre 7 y 10 dígitos numéricos"
                               placeholder="Ej. 71234567" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($valores['email'] ?? '') ?>" 
                           maxlength="100" autocomplete="email"
                           placeholder="conductor@ejemplo.com" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" 
                           minlength="8" maxlength="50" autocomplete="new-password"
                           placeholder="Mínimo 8 caracteres" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)" title="Mostrar / Ocultar contraseña">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="form-text text-muted small"><i class="bi bi-shield-check me-1"></i>Debe contener al menos 8 caracteres.</div>
            </div>

            <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">
                <i class="bi bi-check-circle me-1"></i>Completar Registro
            </button>
        </form>

        <hr class="my-4">
        <div class="text-center">
            <span class="text-muted small">¿Ya tienes una cuenta registrada?</span><br>
            <a href="<?= BASE_URL ?>/login" class="text-decoration-none fw-semibold">Iniciar Sesión</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/js/app.js"></script>
</body>
</html>
