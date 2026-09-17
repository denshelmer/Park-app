<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #152b47;">
            <i class="bi bi-building text-warning me-2"></i>Gestión de Parqueos y Sedes
        </h2>
        <p class="text-muted small mb-0">Administración de sucursales operativas en la ciudad de El Alto</p>
    </div>
    <button class="btn fw-semibold text-white shadow-sm" style="background-color: #c89234;" data-bs-toggle="modal" data-bs-target="#modalParqueo" onclick="abrirModalNuevoParqueo()">
        <i class="bi bi-plus-circle-fill me-1"></i>Nuevo Parqueo
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

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #152b47; color: #ffffff;">
                <tr>
                    <th class="py-3 ps-3"># ID</th>
                    <th class="py-3">Nombre de Sede</th>
                    <th class="py-3">Zona</th>
                    <th class="py-3">Dirección</th>
                    <th class="py-3 text-center">Capacidad</th>
                    <th class="py-3 text-center">Horario</th>
                    <th class="py-3 text-center">Estado</th>
                    <th class="py-3 pe-3 text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($parqueos)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No hay parqueos registrados en el sistema.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($parqueos as $p): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-muted"><?= $p['id_parqueo'] ?></td>
                            <td>
                                <div class="fw-bold" style="color: #152b47;"><?= htmlspecialchars($p['nombre_parqueo']) ?></div>
                            </td>
                            <td><span class="badge bg-secondary-subtle text-secondary border"><?= htmlspecialchars($p['zona']) ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($p['direccion']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-car-front me-1"></i><?= $p['capacidad_total'] ?> cajones
                                </span>
                            </td>
                            <td class="text-center small">
                                <?= htmlspecialchars($p['hora_apertura'] ?? '06:00') ?> - <?= htmlspecialchars($p['hora_cierre'] ?? '23:00') ?>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($p['estado'])): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle me-1"></i>Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-x-circle me-1"></i>Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                        title="Editar sede"
                                        onclick="editarParqueo(<?= htmlspecialchars(json_encode($p)) ?>)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <form method="POST" action="<?= BASE_URL ?>/admin/parqueos/estado" class="d-inline" onsubmit="return confirm('¿Está seguro de cambiar el estado de este parqueo?');">
                                    <input type="hidden" name="id_parqueo" value="<?= $p['id_parqueo'] ?>">
                                    <input type="hidden" name="estado" value="<?= !empty($p['estado']) ? '0' : '1' ?>">
                                    <?php if (!empty($p['estado'])): ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Desactivar parqueo">
                                            <i class="bi bi-slash-circle"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Activar parqueo">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Registro / Edición de Parqueo -->
<div class="modal fade" id="modalParqueo" tabindex="-1" aria-labelledby="modalParqueoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #152b47;">
                <h5 class="modal-title fw-bold" id="modalParqueoLabel">
                    <i class="bi bi-building me-2 text-warning"></i><span>Nuevo Parqueo</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/admin/parqueos/guardar">
                <input type="hidden" name="id_parqueo" id="form_id_parqueo" value="0">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nombre de la Sede *</label>
                        <input type="text" name="nombre_parqueo" id="form_nombre_parqueo" class="form-control" placeholder="Ej. Parqueo Ceja Central" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Zona / Sector *</label>
                        <input type="text" name="zona" id="form_zona" class="form-control" placeholder="Ej. La Ceja, Ciudad Satélite, Villa Adela" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Dirección Física *</label>
                        <input type="text" name="direccion" id="form_direccion" class="form-control" placeholder="Ej. Av. 6 de Marzo esq. Calle 2" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Capacidad *</label>
                            <input type="number" name="capacidad_total" id="form_capacidad_total" class="form-control" min="1" max="500" value="20" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Apertura</label>
                            <input type="time" name="hora_apertura" id="form_hora_apertura" class="form-control" value="06:00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Cierre</label>
                            <input type="time" name="hora_cierre" id="form_hora_cierre" class="form-control" value="23:00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold" style="background-color: #152b47;">
                        <i class="bi bi-save me-1"></i>Guardar Sede
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalNuevoParqueo() {
    document.getElementById('modalParqueoLabel').innerHTML = '<i class="bi bi-building me-2 text-warning"></i>Nuevo Parqueo';
    document.getElementById('form_id_parqueo').value = '0';
    document.getElementById('form_nombre_parqueo').value = '';
    document.getElementById('form_zona').value = '';
    document.getElementById('form_direccion').value = '';
    document.getElementById('form_capacidad_total').value = '20';
    document.getElementById('form_hora_apertura').value = '06:00';
    document.getElementById('form_hora_cierre').value = '23:00';
}

function editarParqueo(p) {
    document.getElementById('modalParqueoLabel').innerHTML = '<i class="bi bi-pencil-square me-2 text-warning"></i>Editar Parqueo';
    document.getElementById('form_id_parqueo').value = p.id_parqueo;
    document.getElementById('form_nombre_parqueo').value = p.nombre_parqueo || '';
    document.getElementById('form_zona').value = p.zona || '';
    document.getElementById('form_direccion').value = p.direccion || '';
    document.getElementById('form_capacidad_total').value = p.capacidad_total || 20;
    document.getElementById('form_hora_apertura').value = p.hora_apertura || '06:00';
    document.getElementById('form_hora_cierre').value = p.hora_cierre || '23:00';
    
    var modal = new bootstrap.Modal(document.getElementById('modalParqueo'));
    modal.show();
}
</script>

