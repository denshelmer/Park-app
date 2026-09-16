<?php
$token = $reserva['codigo_qr_token'] ?? '';
$fechaPrevista = !empty($reserva['fecha_hora_prevista_llegada']) ? date('d/m/Y H:i', strtotime($reserva['fecha_hora_prevista_llegada'])) : 'N/A';
$estado = $reserva['estado_reserva'] ?? 'Confirmada';
?>
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <?php if (!empty($success)): ?>
            <div class="classic-alert classic-alert-success mb-4 no-print">
                <i class="bi bi-check-circle alert-icon"></i>
                <div><?= htmlspecialchars($success) ?></div>
            </div>
        <?php endif; ?>

        <!-- Tarjeta Formal de Comprobante QR -->
        <div class="card formal-card p-4 p-md-5 text-center shadow-sm">
            <div class="mb-3">
                <span class="badge px-3 py-2 fs-6 <?= $estado === 'Confirmada' ? 'bg-success' : ($estado === 'Cancelada' ? 'bg-secondary' : 'bg-primary') ?>">
                    <i class="bi bi-check2-circle me-1"></i>Reserva <?= htmlspecialchars($estado) ?>
                </span>
            </div>

            <h4 class="fw-bold mb-1" style="color: var(--park-primary);">Comprobante Oficial de Ingreso</h4>
            <p class="text-muted small mb-4">Presente este código QR digital o impreso al operador en la caseta del parqueo</p>

            <!-- Generación de Código QR Real -->
            <div class="p-3 bg-white border rounded d-inline-block shadow-sm mb-3">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=210x210&data=<?= urlencode($token) ?>" 
                     alt="Código QR Reserva <?= htmlspecialchars($token) ?>" class="img-fluid rounded" style="width: 210px; height: 210px;">
            </div>

            <div class="mb-4">
                <h5 class="fw-bold font-monospace mb-1" style="color: var(--park-primary); letter-spacing: 1px;"><?= htmlspecialchars($token) ?></h5>
                <small class="text-muted"><i class="bi bi-shield-check me-1 text-success"></i>Código de validación en tiempo real</small>
            </div>

            <!-- Ficha Técnica de la Reserva -->
            <div class="list-group list-group-flush text-start small mb-4 border rounded">
                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                    <span class="text-muted">Establecimiento:</span>
                    <strong class="text-dark"><?= htmlspecialchars($reserva['nombre_parqueo'] ?? 'Parqueo') ?></strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                    <span class="text-muted">Zona / Dirección:</span>
                    <span class="text-end text-dark"><?= htmlspecialchars($reserva['direccion'] ?? '') ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 bg-light">
                    <span class="text-muted fw-semibold">Espacio Asignado:</span>
                    <span class="badge px-3 py-1 fs-6" style="background-color: var(--park-primary); color: #fff;">
                        <?= htmlspecialchars($reserva['codigo_espacio'] ?? 'Cajón') ?> (<?= htmlspecialchars($reserva['piso_sector'] ?? 'Sector') ?>)
                    </span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                    <span class="text-muted">Vehículo (Placa):</span>
                    <strong class="font-monospace fs-6 text-dark"><?= htmlspecialchars($reserva['placa_vehiculo'] ?? '') ?></strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                    <span class="text-muted">Hora Estimada de Llegada:</span>
                    <strong class="text-dark"><?= $fechaPrevista ?></strong>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                    <span class="text-muted">Margen de Tolerancia:</span>
                    <span class="text-danger fw-semibold"><i class="bi bi-stopwatch me-1"></i><?= (int)($reserva['minutos_tolerancia'] ?? 15) ?> minutos</span>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="d-flex flex-column flex-sm-row gap-2 no-print">
                <button type="button" onclick="window.print()" class="btn btn-outline-dark w-100 fw-semibold">
                    <i class="bi bi-printer me-1"></i>Imprimir Pase
                </button>
                <a href="<?= BASE_URL ?>/mis-reservas" class="btn btn-primary-formal w-100 fw-semibold">
                    <i class="bi bi-clock-history me-1"></i>Mis Reservas
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar, footer, .no-print {
        display: none !important;
    }
    body {
        background: #ffffff !important;
    }
    .formal-card {
        box-shadow: none !important;
        border: 1px dashed #000 !important;
    }
}
</style>

