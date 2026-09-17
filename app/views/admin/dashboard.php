<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #152b47;">
            <i class="bi bi-speedometer2 text-warning me-2"></i>Dashboard Gerencial
        </h2>
        <p class="text-muted small mb-0">Métricas operativas en tiempo real y flujo financiero del día</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <form method="GET" action="<?= BASE_URL ?>/admin/dashboard" class="d-flex align-items-center gap-2">
            <select name="parqueo_id" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()" style="min-width: 220px;">
                <option value="">-- Todas las Sedes --</option>
                <?php foreach ($parqueos as $p): ?>
                    <option value="<?= $p['id_parqueo'] ?>" <?= (isset($idParqueo) && (int)$idParqueo === (int)$p['id_parqueo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre_parqueo']) ?> (<?= htmlspecialchars($p['zona']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <span class="badge p-2 text-white shadow-sm" style="background-color: #152b47; font-size: 0.85rem;">
            <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i') ?>
        </span>
    </div>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<!-- Tarjetas Métricas KPI -->
<?php
$totalCap = max(1, $conteoEspacios['total']);
$totalComprometidos = $conteoEspacios['ocupados'] + $conteoEspacios['reservados'];
$porcentajeOcupacion = round(($totalComprometidos / $totalCap) * 100);
?>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 h-100 border-start border-4" style="border-left-color: #152b47 !important;">
            <div class="text-muted small fw-semibold text-uppercase">Vehículos Estacionados</div>
            <div class="d-flex align-items-baseline justify-content-between mt-2">
                <h2 class="fw-bold my-0" style="color: #152b47;"><?= (int)$vehiculosActivos ?></h2>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Activos</span>
            </div>
            <small class="text-muted mt-2 d-block">
                <i class="bi bi-geo-alt me-1"></i><?= $conteoEspacios['ocupados'] ?> cajones ocupados
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 h-100 border-start border-4" style="border-left-color: #198754 !important;">
            <div class="text-muted small fw-semibold text-uppercase">Recaudación de Hoy</div>
            <div class="d-flex align-items-baseline justify-content-between mt-2">
                <h2 class="fw-bold my-0 text-success">Bs. <?= number_format($metricasHoy['total_hoy'], 2) ?></h2>
                <span class="badge bg-success-subtle text-success border border-success-subtle"><?= $metricasHoy['cantidad_transacciones'] ?> cobros</span>
            </div>
            <div class="d-flex justify-content-between text-muted small mt-2">
                <span><i class="bi bi-cash me-1"></i>Ef: Bs. <?= number_format($metricasHoy['total_efectivo'], 2) ?></span>
                <span><i class="bi bi-qr-code me-1"></i>QR: Bs. <?= number_format($metricasHoy['total_qr'], 2) ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 h-100 border-start border-4" style="border-left-color: #c89234 !important;">
            <div class="text-muted small fw-semibold text-uppercase">Espacios Disponibles</div>
            <div class="d-flex align-items-baseline justify-content-between mt-2">
                <h2 class="fw-bold my-0" style="color: #c89234;"><?= (int)$conteoEspacios['disponibles'] ?></h2>
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle">Libres</span>
            </div>
            <small class="text-muted mt-2 d-block">
                De un total de <?= $conteoEspacios['total'] ?> cajones registrados
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 h-100 border-start border-4" style="border-left-color: #0dcaf0 !important;">
            <div class="text-muted small fw-semibold text-uppercase">Nivel de Ocupación</div>
            <div class="d-flex align-items-baseline justify-content-between mt-2">
                <h2 class="fw-bold my-0 text-info"><?= $porcentajeOcupacion ?>%</h2>
                <span class="badge bg-info-subtle text-info border border-info-subtle">
                    <?= $conteoEspacios['reservados'] ?> reservado(s)
                </span>
            </div>
            <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-info" role="progressbar" style="width: <?= min(100, $porcentajeOcupacion) ?>%"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Tabla de Movimientos de Hoy -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold" style="color: #152b47;">
                    <i class="bi bi-clock-history text-primary me-2"></i>Movimientos del Día (<?= count($movimientosHoy) ?>)
                </span>
                <a href="<?= BASE_URL ?>/reportes/ingresos" class="btn btn-sm btn-outline-primary py-1">
                    Ver Reporte Completo <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Ticket</th>
                            <th>Placa</th>
                            <th>Espacio / Sede</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($movimientosHoy)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    No se registran movimientos para el día de hoy con el filtro seleccionado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (array_slice($movimientosHoy, 0, 15) as $mov): ?>
                                <tr>
                                    <td><code class="fw-bold text-dark"><?= htmlspecialchars($mov['numero_ticket']) ?></code></td>
                                    <td>
                                        <span class="badge bg-dark px-2 py-1" style="letter-spacing: 1px;">
                                            <?= htmlspecialchars($mov['placa']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($mov['codigo_espacio']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($mov['nombre_parqueo']) ?></small>
                                    </td>
                                    <td>
                                        <?= !empty($mov['fecha_hora_entrada']) ? date('H:i', strtotime($mov['fecha_hora_entrada'])) : '-' ?>
                                    </td>
                                    <td>
                                        <?= !empty($mov['fecha_hora_salida']) ? date('H:i', strtotime($mov['fecha_hora_salida'])) : '<span class="text-muted fst-italic">En curso</span>' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($mov['total_a_pagar'])): ?>
                                            <strong class="text-success">Bs. <?= number_format((float)$mov['total_a_pagar'], 2) ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($mov['estado_estancia'] === 'En Parqueo'): ?>
                                            <span class="badge bg-primary"><i class="bi bi-clock me-1"></i>En Parqueo</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>Finalizado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($movimientosHoy) > 15): ?>
                <div class="card-footer bg-white text-center py-2 text-muted small">
                    Mostrando los últimos 15 movimientos de <?= count($movimientosHoy) ?> registrados hoy.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Acciones Rápidas y Resumen de Estado -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="color: #152b47;">
                <i class="bi bi-lightning-charge text-warning me-2"></i>Acciones Rápidas
            </div>
            <div class="card-body d-grid gap-2">
                <a href="<?= BASE_URL ?>/admin/parqueos" class="btn btn-outline-primary text-start d-flex justify-content-between align-items-center py-2">
                    <span><i class="bi bi-building me-2"></i>Gestionar Sedes y Parqueos</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin/espacios" class="btn btn-outline-secondary text-start d-flex justify-content-between align-items-center py-2">
                    <span><i class="bi bi-grid-3x3-gap me-2"></i>Matriz de Espacios y Bloqueos</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin/tarifas" class="btn btn-outline-success text-start d-flex justify-content-between align-items-center py-2">
                    <span><i class="bi bi-cash-coin me-2"></i>Ajustar Tarifas y Fracciones</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-outline-info text-start d-flex justify-content-between align-items-center py-2">
                    <span><i class="bi bi-people me-2"></i>Personal y Cuentas de Acceso</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <hr class="my-1">
                <a href="<?= BASE_URL ?>/reportes/ingresos" class="btn btn-outline-dark text-start d-flex justify-content-between align-items-center py-2">
                    <span><i class="bi bi-file-earmark-bar-graph me-2"></i>Reporte Económico Oficial</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="<?= BASE_URL ?>/reportes/ocupacion" class="btn btn-outline-dark text-start d-flex justify-content-between align-items-center py-2">
                    <span><i class="bi bi-pie-chart me-2"></i>Estadística de Ocupación y Rotación</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>

        <!-- Estado de la Infraestructura -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 fw-bold" style="color: #152b47;">
                <i class="bi bi-pie-chart text-info me-2"></i>Distribución de Espacios
            </div>
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                    <span class="small"><i class="bi bi-circle-fill text-success me-2"></i>Disponibles</span>
                    <span class="fw-bold"><?= $conteoEspacios['disponibles'] ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                    <span class="small"><i class="bi bi-circle-fill text-danger me-2"></i>Ocupados</span>
                    <span class="fw-bold"><?= $conteoEspacios['ocupados'] ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                    <span class="small"><i class="bi bi-circle-fill text-warning me-2"></i>Reservados</span>
                    <span class="fw-bold"><?= $conteoEspacios['reservados'] ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span class="small"><i class="bi bi-circle-fill text-secondary me-2"></i>En Mantenimiento</span>
                    <span class="fw-bold"><?= $conteoEspacios['mantenimiento'] ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

