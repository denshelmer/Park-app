<!-- Encabezado de la Sección Estandarizado -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #152b47;">
            Historial de Mis Reservas
        </h2>
        <p class="text-muted small mb-0">Consulte el estado de sus reservas activas, descargue comprobantes QR o cancele si no podrá asistir</p>
    </div>
    <a href="<?= BASE_URL ?>/disponibilidad" class="btn fw-semibold text-white shadow-sm" style="background-color: #c89234;">
        <i class="bi bi-plus-circle-fill me-1"></i>Nueva Reserva
    </a>
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

<!-- Tabla de Reservas -->
<div class="card shadow-sm border-0">
    <?php if (empty($reservas)): ?>
        <div class="p-5 text-center">
            <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
            <h5 class="fw-bold text-secondary">Aún no tiene reservas registradas</h5>
            <p class="text-muted small mb-4">Puede consultar la disponibilidad en tiempo real y asegurar su espacio de forma rápida.</p>
            <a href="<?= BASE_URL ?>/disponibilidad" class="btn fw-semibold text-white shadow-sm px-4" style="background-color: #c89234;">
                <i class="bi bi-geo-alt me-1"></i>Explorar Parqueos Disponibles
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #152b47; color: #ffffff;">
                    <tr>
                        <th class="ps-3 py-3">Código QR / Token</th>
                        <th class="py-3">Establecimiento</th>
                        <th class="py-3">Espacio</th>
                        <th class="py-3">Placa</th>
                        <th class="py-3">Hora Prevista</th>
                        <th class="py-3">Estado</th>
                        <th class="text-center py-3 pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $res): ?>
                        <?php 
                        $estado = $res['estado_reserva'] ?? 'Confirmada';
                        $fechaPrevista = !empty($res['fecha_hora_prevista_llegada']) ? date('d/m/Y H:i', strtotime($res['fecha_hora_prevista_llegada'])) : 'N/A';
                        ?>
                        <tr>
                            <td class="ps-3">
                                <code class="fw-bold" style="color: var(--park-primary); font-size: 0.9rem;"><?= htmlspecialchars($res['codigo_qr_token']) ?></code>
                            </td>
                            <td>
                                <strong class="text-dark"><?= htmlspecialchars($res['nombre_parqueo']) ?></strong>
                                <div class="text-muted small"><?= htmlspecialchars($res['zona']) ?></div>
                            </td>
                            <td>
                                <span class="badge px-2 py-1" style="background-color: #f1f5f9; color: var(--park-primary); border: 1px solid #cbd5e1; font-weight: 600;">
                                    <?= htmlspecialchars($res['codigo_espacio']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">
                                    <?= htmlspecialchars($res['placa_vehiculo']) ?>
                                </span>
                            </td>
                            <td>
                                <i class="bi bi-clock me-1 text-muted"></i><?= $fechaPrevista ?>
                            </td>
                            <td>
                                <?php if ($estado === 'Confirmada'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Confirmada</span>
                                <?php elseif ($estado === 'En Parqueo'): ?>
                                    <span class="badge bg-primary"><i class="bi bi-p-square me-1"></i>En Parqueo</span>
                                <?php elseif ($estado === 'Finalizada'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>Finalizada</span>
                                <?php elseif ($estado === 'Cancelada'): ?>
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Cancelada</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark"><?= htmlspecialchars($estado) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?= BASE_URL ?>/reserva/qr?token=<?= urlencode($res['codigo_qr_token']) ?>" 
                                       class="btn btn-sm btn-outline-dark fw-semibold" title="Ver Comprobante QR">
                                        <i class="bi bi-qr-code me-1"></i>QR
                                    </a>

                                    <?php if ($estado === 'Confirmada'): ?>
                                        <form action="<?= BASE_URL ?>/reserva/cancelar" method="POST" class="d-inline" 
                                              onsubmit="return confirm('¿Está seguro de que desea cancelar esta reserva? El espacio <?= htmlspecialchars($res['codigo_espacio']) ?> será liberado inmediatamente.');">
                                            <input type="hidden" name="id_reserva" value="<?= $res['id_reserva'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar Reserva">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

