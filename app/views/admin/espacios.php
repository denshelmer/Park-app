<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #152b47;">
            <i class="bi bi-grid-3x3-gap text-warning me-2"></i>Matriz de Espacios y Mantenimiento
        </h2>
        <p class="text-muted small mb-0">Gestión de cajones de estacionamiento, asignación de tipo de vehículo y bloqueos</p>
    </div>
    <button class="btn fw-semibold text-white shadow-sm" style="background-color: #c89234;" data-bs-toggle="modal" data-bs-target="#modalEspacio" onclick="abrirModalNuevoEspacio()">
        <i class="bi bi-plus-circle-fill me-1"></i>Nuevo Espacio
    </button>
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

<!-- Selector de Parqueo y Métricas Rápidas -->
<div class="card shadow-sm border-0 mb-4 p-3 bg-white">
    <div class="row g-3 align-items-center">
        <div class="col-md-5">
            <label class="form-label small fw-bold text-secondary mb-1">
                <i class="bi bi-building me-1"></i>Seleccionar Sede de Parqueo:
            </label>
            <form method="GET" action="<?= BASE_URL ?>/admin/espacios">
                <select name="parqueo_id" class="form-select fw-semibold" onchange="this.form.submit()">
                    <?php foreach ($parqueos as $p): ?>
                        <option value="<?= $p['id_parqueo'] ?>" <?= ((int)$idParqueo === (int)$p['id_parqueo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nombre_parqueo']) ?> &mdash; <?= htmlspecialchars($p['zona']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div class="col-md-7">
            <div class="d-flex flex-wrap gap-2 justify-content-md-end mt-2 mt-md-0">
                <span class="badge bg-light text-dark border p-2">
                    Total: <strong><?= $conteo['total'] ?></strong>
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle p-2">
                    <i class="bi bi-check-circle me-1"></i>Libres: <strong><?= $conteo['disponibles'] ?></strong>
                </span>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle p-2">
                    <i class="bi bi-car-front me-1"></i>Ocupados: <strong><?= $conteo['ocupados'] ?></strong>
                </span>
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle p-2">
                    <i class="bi bi-bookmark-check me-1"></i>Reservados: <strong><?= $conteo['reservados'] ?></strong>
                </span>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle p-2">
                    <i class="bi bi-tools me-1"></i>Mantenimiento: <strong><?= $conteo['mantenimiento'] ?></strong>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Espacios -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #152b47; color: #ffffff;">
                <tr>
                    <th class="py-3 ps-3">Código</th>
                    <th class="py-3">Piso / Sector</th>
                    <th class="py-3">Tipo de Vehículo</th>
                    <th class="py-3 text-center">Estado</th>
                    <th class="py-3 pe-3 text-end">Acciones / Mantenimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($espacios)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No hay espacios configurados para este parqueo. ¡Cree el primer cajón con el botón superior!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($espacios as $e): ?>
                        <tr>
                            <td class="ps-3">
                                <span class="badge bg-dark px-3 py-2 fw-bold" style="font-size: 0.95rem; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($e['codigo_espacio']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-secondary">
                                    <i class="bi bi-layers me-1"></i><?= htmlspecialchars($e['piso_sector']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-truck-front me-1 text-primary"></i><?= htmlspecialchars($e['nombre_tipo'] ?? 'Automóvil') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php
                                $badgeClass = match($e['estado']) {
                                    'Disponible' => 'bg-success-subtle text-success border-success-subtle',
                                    'Ocupado' => 'bg-danger-subtle text-danger border-danger-subtle',
                                    'Reservado' => 'bg-warning-subtle text-dark border-warning-subtle',
                                    'Mantenimiento' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                    default => 'bg-light text-dark'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?> border px-3 py-2">
                                    <?= htmlspecialchars($e['estado']) ?>
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                <!-- Editar Cajón -->
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                        title="Editar espacio"
                                        onclick="editarEspacio(<?= htmlspecialchars(json_encode($e)) ?>)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Toggle Mantenimiento -->
                                <?php if ($e['estado'] === 'Disponible'): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/espacios/estado" class="d-inline" onsubmit="return confirm('¿Bloquear este espacio por Mantenimiento?');">
                                        <input type="hidden" name="id_espacio" value="<?= $e['id_espacio'] ?>">
                                        <input type="hidden" name="id_parqueo" value="<?= $idParqueo ?>">
                                        <input type="hidden" name="nuevo_estado" value="Mantenimiento">
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Bloquear por mantenimiento">
                                            <i class="bi bi-tools me-1"></i>Mantenimiento
                                        </button>
                                    </form>
                                <?php elseif ($e['estado'] === 'Mantenimiento'): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/espacios/estado" class="d-inline" onsubmit="return confirm('¿Reactivar este espacio como Disponible?');">
                                        <input type="hidden" name="id_espacio" value="<?= $e['id_espacio'] ?>">
                                        <input type="hidden" name="id_parqueo" value="<?= $idParqueo ?>">
                                        <input type="hidden" name="nuevo_estado" value="Disponible">
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Reactivar espacio">
                                            <i class="bi bi-check2-circle me-1"></i>Habilitar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-light border text-muted" disabled title="Espacio actualmente en uso">
                                        <i class="bi bi-lock"></i> En uso
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Registro / Edición de Espacio -->
<div class="modal fade" id="modalEspacio" tabindex="-1" aria-labelledby="modalEspacioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #152b47;">
                <h5 class="modal-title fw-bold" id="modalEspacioLabel">
                    <i class="bi bi-grid-3x3-gap me-2 text-warning"></i><span>Nuevo Espacio</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/admin/espacios/guardar">
                <input type="hidden" name="id_espacio" id="form_id_espacio" value="0">
                <input type="hidden" name="id_parqueo" value="<?= $idParqueo ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Código del Espacio / Cajón *</label>
                        <input type="text" name="codigo_espacio" id="form_codigo_espacio" class="form-control text-uppercase" placeholder="Ej. A-01, B-12, M-03" required>
                        <small class="text-muted">Debe ser un identificador único dentro de esta sede.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Sector o Piso *</label>
                        <input type="text" name="piso_sector" id="form_piso_sector" class="form-control" placeholder="Ej. Planta Baja, Sector A, Piso 1" value="Planta Baja" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Tipo de Vehículo Admitido *</label>
                        <select name="id_tipo_vehiculo" id="form_id_tipo_vehiculo" class="form-select" required>
                            <?php foreach ($tiposVehiculo as $tv): ?>
                                <option value="<?= $tv['id_tipo_vehiculo'] ?>">
                                    <?= htmlspecialchars($tv['nombre_tipo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3" id="div_estado_espacio">
                        <label class="form-label small fw-bold text-secondary">Estado Inicial</label>
                        <select name="estado" id="form_estado_espacio" class="form-select">
                            <option value="Disponible">Disponible</option>
                            <option value="Mantenimiento">Mantenimiento (Bloqueado)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold" style="background-color: #152b47;">
                        <i class="bi bi-save me-1"></i>Guardar Espacio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalNuevoEspacio() {
    document.getElementById('modalEspacioLabel').innerHTML = '<i class="bi bi-grid-3x3-gap me-2 text-warning"></i>Nuevo Espacio';
    document.getElementById('form_id_espacio').value = '0';
    document.getElementById('form_codigo_espacio').value = '';
    document.getElementById('form_piso_sector').value = 'Planta Baja';
    document.getElementById('form_estado_espacio').value = 'Disponible';
    document.getElementById('form_id_tipo_vehiculo').selectedIndex = 0;
}

function editarEspacio(e) {
    document.getElementById('modalEspacioLabel').innerHTML = '<i class="bi bi-pencil-square me-2 text-warning"></i>Editar Espacio';
    document.getElementById('form_id_espacio').value = e.id_espacio;
    document.getElementById('form_codigo_espacio').value = e.codigo_espacio || '';
    document.getElementById('form_piso_sector').value = e.piso_sector || '';
    document.getElementById('form_id_tipo_vehiculo').value = e.id_tipo_vehiculo;
    document.getElementById('form_estado_espacio').value = e.estado || 'Disponible';
    
    var modal = new bootstrap.Modal(document.getElementById('modalEspacio'));
    modal.show();
}
</script>

