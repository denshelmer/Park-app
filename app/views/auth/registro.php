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

        <form action="<?= BASE_URL ?>/registro" method="POST">
            <div class="mb-3">
                <label for="nombre_completo" class="form-label fw-semibold">Nombre Completo</label>
                <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" placeholder="Ej. Juan Pérez Ramos" required>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label for="ci_nit" class="form-label fw-semibold">CI / Documento</label>
                    <input type="text" class="form-control" id="ci_nit" name="ci_nit" placeholder="Ej. 6849201 LP" required>
                </div>
                <div class="col-md-6">
                    <label for="telefono" class="form-label fw-semibold">Teléfono / Celular</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej. 71234567" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="conductor@ejemplo.com" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
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
</body>
</html>
