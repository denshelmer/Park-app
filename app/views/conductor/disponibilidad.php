<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="fw-bold"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Parqueos Disponibles en El Alto</h2>
        <p class="text-muted mb-0">Consulta en tiempo real los espacios libres en las diferentes zonas comerciales y neurálgicas.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <span class="badge bg-success p-2 fs-6"><i class="bi bi-broadcast me-1"></i>Actualización en Vivo</span>
    </div>
</div>

<div class="row g-4">
    <?php if (empty($parqueos)): ?>
        <div class="col-12">
            <div class="alert alert-info text-center py-4">
                <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                No se encontraron parqueos activos en este momento.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($parqueos as $p): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 park-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold mb-0 text-dark"><?= htmlspecialchars($p['nombre_parqueo']) ?></h5>
                            <span class="badge bg-primary"><?= htmlspecialchars($p['zona']) ?></span>
                        </div>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-pin-map me-1"></i><?= htmlspecialchars($p['direccion']) ?>
                        </p>
                        <div class="p-2 bg-light rounded mb-3 small">
                            <div><i class="bi bi-clock me-2"></i>Horario: <strong><?= substr($p['hora_apertura'], 11, 5) ?> - <?= substr($p['hora_cierre'], 11, 5) ?></strong></div>
                            <div><i class="bi bi-car-front me-2"></i>Capacidad Total: <strong><?= $p['capacidad_total'] ?> espacios</strong></div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0 pb-3">
                        <a href="<?= BASE_URL ?>/reservar?parqueo=<?= $p['id_parqueo'] ?>" class="btn btn-warning w-100 fw-semibold">
                            <i class="bi bi-calendar-plus me-1"></i>Reservar Espacio
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
