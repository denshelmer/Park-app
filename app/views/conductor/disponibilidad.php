<!-- Encabezado de la Sección Estandarizado -->
<div class="mb-4">
    <h2 class="fw-bold mb-1" style="color: #152b47;">
        Disponibilidad de Parqueos
    </h2>
    <p class="text-muted small mb-0">Consulte en tiempo real los espacios libres en las diferentes zonas de la ciudad de El Alto</p>
</div>

<?php if (!empty($error) || isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error ?? $_SESSION['flash_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($success) || isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success ?? $_SESSION['flash_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Listado de Parqueos -->
<div class="row g-4">
    <?php if (empty($parqueos)): ?>
        <div class="col-12">
            <div class="formal-card p-5 text-center">
                <h5 class="fw-bold text-secondary">No se encontraron establecimientos activos</h5>
                <p class="text-muted small mb-0">Actualmente no existen parqueos habilitados en el sistema.</p>
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
                            <?= htmlspecialchars($p['direccion']) ?>
                        </p>

                        <!-- Panel de Ocupación en Vivo -->
                        <div class="p-3 rounded mb-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-semibold text-secondary">Disponibilidad:</span>
                                <?php if ($disponible): ?>
                                    <span class="badge bg-success px-2 py-1"><?= $libres ?> Libres</span>
                                <?php else: ?>
                                    <span class="badge bg-danger px-2 py-1">Completo</span>
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
                            Horario de atención: 
                            <strong class="text-dark">
                                <?= !empty($p['hora_apertura']) ? substr($p['hora_apertura'], 11, 5) : '06:00' ?> &mdash; 
                                <?= !empty($p['hora_cierre']) ? substr($p['hora_cierre'], 11, 5) : '23:00' ?>
                            </strong>
                        </div>

                        <!-- Botón de Acción -->
                        <?php if ($disponible): ?>
                            <a href="<?= BASE_URL ?>/reservar?parqueo=<?= $p['id_parqueo'] ?>" class="btn btn-primary-formal w-100 text-center">
                                Reservar Lugar
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100 text-center disabled" disabled>
                                Sin Lugares Disponibles
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

