<!-- Encabezado de la Sección -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--park-primary); letter-spacing: -0.5px;">
            <i class="bi bi-geo-alt-fill me-2" style="color: #c89234;"></i>Disponibilidad de Parqueos
        </h3>
        <p class="text-muted small mb-0">Consulte en tiempo real los espacios libres en las diferentes zonas de la ciudad de El Alto.</p>
    </div>
    <div class="mt-2 mt-md-0">
        <span class="badge px-3 py-2 text-white" style="background-color: var(--park-primary); font-size: 0.82rem; font-weight: 500;">
            <i class="bi bi-broadcast me-1 text-success"></i>Monitoreo en Tiempo Real
        </span>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="classic-alert classic-alert-danger mb-4">
        <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
        <div><?= htmlspecialchars($error) ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="classic-alert classic-alert-success mb-4">
        <i class="bi bi-check-circle alert-icon"></i>
        <div><?= htmlspecialchars($success) ?></div>
    </div>
<?php endif; ?>

<!-- Listado de Parqueos -->
<div class="row g-4">
    <?php if (empty($parqueos)): ?>
        <div class="col-12">
            <div class="formal-card p-5 text-center">
                <i class="bi bi-building-slash fs-1 text-muted d-block mb-3"></i>
                <h5 class="fw-bold text-secondary">No se encontraron establecimientos activos</h5>
                <p class="text-muted small">Actualmente no existen parqueos habilitados en el sistema.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($parqueos as $p): ?>
            <?php 
            $libres = (int)($p['espacios_libres'] ?? 0);
            $totales = (int)($p['espacios_totales'] ?? 0);
            $ocupacion = (int)($p['porcentaje_ocupacion'] ?? 0);
            $disponible = ($libres > 0);
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card formal-card h-100 park-card">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0 text-truncate" style="color: var(--park-primary);" title="<?= htmlspecialchars($p['nombre_parqueo']) ?>">
                                <?= htmlspecialchars($p['nombre_parqueo']) ?>
                            </h5>
                            <span class="badge px-2 py-1" style="background-color: #f1f5f9; color: var(--park-primary); border: 1px solid #cbd5e1; font-size: 0.75rem;">
                                <?= htmlspecialchars($p['zona']) ?>
                            </span>
                        </div>

                        <p class="text-muted small mb-3">
                            <i class="bi bi-pin-map text-danger me-1"></i><?= htmlspecialchars($p['direccion']) ?>
                        </p>

                        <!-- Panel de Ocupación en Vivo -->
                        <div class="p-3 rounded mb-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-semibold text-secondary">Disponibilidad:</span>
                                <?php if ($disponible): ?>
                                    <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i><?= $libres ?> Libres</span>
                                <?php else: ?>
                                    <span class="badge bg-danger px-2 py-1"><i class="bi bi-slash-circle me-1"></i>Completo</span>
                                <?php endif; ?>
                            </div>

                            <!-- Barra de Ocupación -->
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar <?= $ocupacion > 85 ? 'bg-danger' : ($ocupacion > 60 ? 'bg-warning' : 'bg-success') ?>" 
                                     role="progressbar" style="width: <?= $ocupacion ?>%;" aria-valuenow="<?= $ocupacion ?>" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between text-muted small mt-2" style="font-size: 0.78rem;">
                                <span>Capacidad: <strong><?= $totales ?> espacios</strong></span>
                                <span>Ocupación: <strong><?= $ocupacion ?>%</strong></span>
                            </div>
                        </div>

                        <!-- Ficha de Horarios -->
                        <div class="small text-muted mb-4 mt-auto">
                            <i class="bi bi-clock me-1 text-primary"></i>Horario de atención: 
                            <strong class="text-dark">
                                <?= !empty($p['hora_apertura']) ? substr($p['hora_apertura'], 11, 5) : '06:00' ?> &mdash; 
                                <?= !empty($p['hora_cierre']) ? substr($p['hora_cierre'], 11, 5) : '23:00' ?>
                            </strong>
                        </div>

                        <!-- Botón de Acción -->
                        <?php if ($disponible): ?>
                            <a href="<?= BASE_URL ?>/reservar?parqueo=<?= $p['id_parqueo'] ?>" class="btn btn-primary-formal w-100 text-center">
                                <i class="bi bi-calendar-plus me-1"></i>Reservar Lugar
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100 text-center disabled" disabled>
                                <i class="bi bi-lock me-1"></i>Sin Lugares Disponibles
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

