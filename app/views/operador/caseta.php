<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <div class="d-flex align-items-center">
            <div class="brand-badge me-3" style="width: 48px; height: 48px; font-size: 1.4rem;">
                <i class="bi bi-speedometer2"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0" style="color: var(--park-primary);">
                    Control de Caseta - <?= htmlspecialchars($parqueo['nombre_parqueo'] ?? 'Parqueo Central') ?>
                </h3>
                <p class="text-muted small mb-0">
                    <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($parqueo['direccion'] ?? 'El Alto') ?> (<?= htmlspecialchars($parqueo['zona'] ?? 'La Paz') ?>)
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <div class="d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
            <?php if (count($parqueos) > 1): ?>
                <form action="<?= BASE_URL ?>/caseta" method="GET" class="d-inline-block">
                    <select name="id_parqueo" class="form-select form-select-sm" onchange="this.form.submit()" style="max-width: 180px;">
                        <?php foreach ($parqueos as $p): ?>
                            <option value="<?= $p['id_parqueo'] ?>" <?= $p['id_parqueo'] == $idParqueo ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nombre_parqueo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/caseta/ingreso?id_parqueo=<?= $idParqueo ?>" class="btn btn-primary fw-bold">
                <i class="bi bi-box-arrow-in-right me-1"></i>Nuevo Ingreso (Ticket / QR)
            </a>
            <a href="<?= BASE_URL ?>/caseta/salida?id_parqueo=<?= $idParqueo ?>" class="btn btn-danger fw-bold">
                <i class="bi bi-box-arrow-left me-1"></i>Salida y Cobro
            </a>
        </div>
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
        <i class="bi bi-check-circle-fill alert-icon"></i>
        <div><?= htmlspecialchars($success) ?></div>
    </div>
<?php endif; ?>

<!-- Fila de Estadísticas y Métricas en Tiempo Real -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center bg-white" style="border-left: 4px solid var(--park-accent) !important;">
            <div class="text-muted small fw-semibold text-uppercase">Espacios Libres</div>
            <div class="fs-2 fw-bold text-success mt-1"><?= $stats['disponibles'] ?></div>
            <small class="text-muted">de <?= $stats['total'] ?> cajones totales</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center bg-white" style="border-left: 4px solid #dc3545 !important;">
            <div class="text-muted small fw-semibold text-uppercase">En Parqueo</div>
            <div class="fs-2 fw-bold text-danger mt-1"><?= $stats['ocupados'] ?></div>
            <small class="text-muted">vehículos dentro</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center bg-white" style="border-left: 4px solid #f59e0b !important;">
            <div class="text-muted small fw-semibold text-uppercase">Reservas QR</div>
            <div class="fs-2 fw-bold text-warning text-dark mt-1"><?= $stats['reservados'] ?></div>
            <small class="text-muted">en espera de llegada</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center bg-white" style="border-left: 4px solid var(--park-primary) !important;">
            <div class="text-muted small fw-semibold text-uppercase">Ocupación</div>
            <div class="fs-2 fw-bold mt-1" style="color: var(--park-primary);"><?= $stats['porcentaje_ocupacion'] ?>%</div>
            <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar <?= $stats['porcentaje_ocupacion'] > 85 ? 'bg-danger' : ($stats['porcentaje_ocupacion'] > 60 ? 'bg-warning' : 'bg-success') ?>" 
                     role="progressbar" style="width: <?= $stats['porcentaje_ocupacion'] ?>%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Leyenda de Estados Formal -->
<div class="card p-2 mb-4 bg-white shadow-sm border-0">
    <div class="d-flex flex-wrap justify-content-around align-items-center small py-1">
        <div class="d-flex align-items-center">
            <span class="d-inline-block rounded-circle bg-success me-2" style="width: 12px; height: 12px;"></span>
            <strong>Disponible</strong>&nbsp;<span class="text-muted">(Libre para asignación)</span>
        </div>
        <div class="d-flex align-items-center">
            <span class="d-inline-block rounded-circle bg-danger me-2" style="width: 12px; height: 12px;"></span>
            <strong>Ocupado</strong>&nbsp;<span class="text-muted">(Vehículo en estancia)</span>
        </div>
        <div class="d-flex align-items-center">
            <span class="d-inline-block rounded-circle bg-warning me-2" style="width: 12px; height: 12px;"></span>
            <strong>Reservado</strong>&nbsp;<span class="text-muted">(QR Confirmado)</span>
        </div>
        <div class="d-flex align-items-center">
            <span class="d-inline-block rounded-circle bg-secondary me-2" style="width: 12px; height: 12px;"></span>
            <strong>Mantenimiento</strong>&nbsp;<span class="text-muted">(No disponible)</span>
        </div>
    </div>
</div>

<!-- Cuadrícula Interactiva de Espacios por Sector -->
<?php if (empty($espaciosPorSector)): ?>
    <div class="card p-5 text-center text-muted border-0 shadow-sm mb-4">
        <i class="bi bi-grid-3x3 mb-3" style="font-size: 3rem;"></i>
        <h5>No hay espacios configurados para este parqueo.</h5>
        <p class="small">Contacte al administrador para registrar los cajones de estacionamiento.</p>
    </div>
<?php else: ?>
    <?php foreach ($espaciosPorSector as $sector => $listaEspacios): ?>
        <div class="card formal-card mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0" style="color: var(--park-primary);">
                    <i class="bi bi-grid-3x3-gap me-2 text-warning"></i><?= htmlspecialchars($sector) ?>
                </h5>
                <span class="badge bg-light text-dark border">
                    <?= count($listaEspacios) ?> cajones
                </span>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">
                    <?php foreach ($listaEspacios as $esp): ?>
                        <?php 
                            $idEsp = (int)$esp['id_espacio'];
                            $estado = $esp['estado'];
                            $activo = $activosPorEspacio[$idEsp] ?? null;
                            $reserva = $reservasPorEspacio[$idEsp] ?? null;
                        ?>

                        <?php if ($estado === 'Disponible'): ?>
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="card text-center p-3 h-100 shadow-sm border-2 border-success bg-light-success position-relative" style="background-color: #f0fdf4;">
                                    <div class="fs-4 fw-bold text-success"><?= htmlspecialchars($esp['codigo_espacio']) ?></div>
                                    <div class="small text-muted mb-2"><?= htmlspecialchars($esp['nombre_tipo'] ?? 'Vehículo') ?></div>
                                    <div class="badge bg-success mb-2 py-1">LIBRE</div>
                                    <a href="<?= BASE_URL ?>/caseta/ingreso?id_parqueo=<?= $idParqueo ?>&espacio_id=<?= $idEsp ?>" 
                                       class="btn btn-sm btn-outline-success fw-semibold mt-auto" title="Registrar ingreso en este cajón">
                                        <i class="bi bi-plus-circle me-1"></i>Ingreso
                                    </a>
                                </div>
                            </div>

                        <?php elseif ($estado === 'Ocupado'): ?>
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="card text-center p-3 h-100 shadow-sm border-2 border-danger position-relative" style="background-color: #fef2f2;">
                                    <div class="fs-4 fw-bold text-danger"><?= htmlspecialchars($esp['codigo_espacio']) ?></div>
                                    <div class="fw-bold text-dark font-monospace fs-6">
                                        <?= htmlspecialchars($activo['placa'] ?? 'EN PARQUEO') ?>
                                    </div>
                                    <div class="badge bg-danger my-1 py-1">OCUPADO</div>
                                    <?php if ($activo): ?>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            <i class="bi bi-clock me-1"></i><?= date('H:i', strtotime($activo['fecha_hora_entrada'])) ?>
                                        </div>
                                        <a href="<?= BASE_URL ?>/caseta/salida?buscar=<?= urlencode($activo['numero_ticket']) ?>" 
                                           class="btn btn-sm btn-danger fw-semibold mt-2" title="Cobrar y liberar cajón">
                                            <i class="bi bi-cash me-1"></i>Cobrar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                        <?php elseif ($estado === 'Reservado'): ?>
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="card text-center p-3 h-100 shadow-sm border-2 border-warning position-relative" style="background-color: #fffbeb;">
                                    <div class="fs-4 fw-bold text-dark"><?= htmlspecialchars($esp['codigo_espacio']) ?></div>
                                    <div class="fw-bold text-dark font-monospace fs-6">
                                        <?= htmlspecialchars($reserva['placa_vehiculo'] ?? 'RESERVA') ?>
                                    </div>
                                    <div class="badge bg-warning text-dark my-1 py-1">QR RESERVADO</div>
                                    <?php if ($reserva): ?>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            Llegada: <?= date('H:i', strtotime($reserva['fecha_hora_prevista_llegada'])) ?>
                                        </div>
                                        <a href="<?= BASE_URL ?>/caseta/ingreso?tab=qr&token=<?= urlencode($reserva['codigo_qr_token']) ?>" 
                                           class="btn btn-sm btn-outline-dark fw-semibold mt-2" title="Validar QR de reserva">
                                            <i class="bi bi-qr-code-scan me-1"></i>Validar QR
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="card text-center p-3 h-100 shadow-sm border-2 border-secondary bg-light position-relative">
                                    <div class="fs-4 fw-bold text-secondary"><?= htmlspecialchars($esp['codigo_espacio']) ?></div>
                                    <div class="small text-muted mb-2">Bloqueado</div>
                                    <div class="badge bg-secondary mb-2 py-1">MANTENIMIENTO</div>
                                    <span class="text-muted small mt-auto" style="font-size: 0.72rem;">Fuera de servicio</span>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Tabla de Vehículos Actualmente en Parqueo -->
<div class="card formal-card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0" style="color: var(--park-primary);">
                <i class="bi bi-car-front-fill me-2 text-primary"></i>Vehículos Actualmente en Parqueo
            </h5>
            <small class="text-muted">Control de estancia activa para cobro o liquidación</small>
        </div>
        <span class="badge bg-danger rounded-pill px-3 py-2">
            <?= count($activosLista) ?> Vehículos Activos
        </span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($activosLista)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-check2-circle text-success" style="font-size: 3rem;"></i>
                <h6 class="mt-2 fw-bold">El parqueo no tiene vehículos en estadía en este momento.</h6>
                <p class="small mb-0">Los nuevos ingresos registrados aparecerán en esta lista.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Ticket</th>
                            <th>Placa</th>
                            <th>Tipo</th>
                            <th>Espacio</th>
                            <th>Hora Entrada</th>
                            <th>Tiempo Activo</th>
                            <th>Operador</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activosLista as $item): ?>
                            <?php 
                                $tsEntrada = strtotime($item['fecha_hora_entrada']);
                                $minTrans = max(1, (int)ceil((time() - $tsEntrada) / 60));
                                $hTrans = floor($minTrans / 60);
                                $mTrans = $minTrans % 60;
                                $tiempoTxt = ($hTrans > 0 ? "{$hTrans}h " : "") . "{$mTrans}m";
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($item['numero_ticket']) ?></span>
                                    <?php if (!empty($item['id_reserva'])): ?>
                                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">QR</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-dark px-2 py-1 fs-6 font-monospace">
                                        <?= htmlspecialchars($item['placa']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted"><?= htmlspecialchars($item['nombre_tipo'] ?? 'Vehículo') ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($item['codigo_espacio']) ?>
                                    </span>
                                    <small class="text-muted ms-1"><?= htmlspecialchars($item['piso_sector'] ?? '') ?></small>
                                </td>
                                <td>
                                    <span class="small"><?= date('d/m/Y H:i', $tsEntrada) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <i class="bi bi-stopwatch me-1"></i><?= $tiempoTxt ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted"><?= htmlspecialchars($item['operador_entrada'] ?? 'Operador') ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>/caseta/salida?buscar=<?= urlencode($item['numero_ticket']) ?>" 
                                           class="btn btn-danger fw-semibold" title="Cobrar y Registrar Salida">
                                            <i class="bi bi-cash-coin me-1"></i>Cobrar Salida
                                        </a>
                                        <a href="<?= BASE_URL ?>/caseta/ticket?id=<?= $item['id_ingreso_salida'] ?>" 
                                           target="_blank" class="btn btn-outline-secondary" title="Reimprimir Ticket">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

