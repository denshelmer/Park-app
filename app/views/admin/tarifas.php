<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #152b47;">
            <i class="bi bi-cash-coin text-warning me-2"></i>Políticas Tarifarias y Tolerancias
        </h2>
        <p class="text-muted small mb-0">Configuración oficial de cobro por hora, fracción de 30 min, día completo y tiempo de cortesía</p>
    </div>
    <button class="btn fw-semibold text-white shadow-sm" style="background-color: #c89234;" data-bs-toggle="modal" data-bs-target="#modalTarifa" onclick="abrirModalNuevaTarifa()">
        <i class="bi bi-plus-circle-fill me-1"></i>Nueva Tarifa
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
                    <th class="py-3 ps-3">Parqueo / Sede</th>
                    <th class="py-3">Tipo Vehículo</th>
                    <th class="py-3 text-center">Precio / Hora</th>
                    <th class="py-3 text-center">Fracción (30 min)</th>
                    <th class="py-3 text-center">Día Completo</th>
                    <th class="py-3 text-center">Tolerancia</th>
                    <th class="py-3 text-center">Estado</th>
                    <th class="py-3 pe-3 text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tarifas)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No existen tarifas registradas. Configure la primera tarifa para su parqueo.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tarifas as $t): ?>
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold" style="color: #152b47;"><?= htmlspecialchars($t['nombre_parqueo']) ?></div>
                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($t['zona']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-car-front me-1 text-primary"></i><?= htmlspecialchars($t['nombre_tipo'] ?? 'Automóvil') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <strong class="text-success fs-6">Bs. <?= number_format((float)$t['precio_hora'], 2) ?></strong>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">Bs. <?= number_format((float)$t['precio_fraccion'], 2) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="fw-semibold">Bs. <?= number_format((float)$t['precio_dia'], 2) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                    <i class="bi bi-clock-history me-1"></i><?= (int)$t['tolerancia_minutos'] ?> min
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($t['vigente'])): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle me-1"></i>Vigente
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                        Histórica
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                        title="Editar tarifa"
                                        onclick="editarTarifa(<?= htmlspecialchars(json_encode($t)) ?>)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST" action="<?= BASE_URL ?>/admin/tarifas/estado" class="d-inline" onsubmit="return confirm('¿Desea cambiar la vigencia de esta tarifa?');">
                                    <input type="hidden" name="id_tarifa" value="<?= $t['id_tarifa'] ?>">
                                    <input type="hidden" name="vigente" value="<?= !empty($t['vigente']) ? '0' : '1' ?>">
                                    <?php if (!empty($t['vigente'])): ?>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Poner en histórico">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Activar como vigente">
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

<!-- Modal Registro / Edición de Tarifa -->
<div class="modal fade" id="modalTarifa" tabindex="-1" aria-labelledby="modalTarifaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #152b47;">
                <h5 class="modal-title fw-bold" id="modalTarifaLabel">
                    <i class="bi bi-cash-coin me-2 text-warning"></i><span>Nueva Regla Tarifaria</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/admin/tarifas/guardar">
                <input type="hidden" name="id_tarifa" id="form_id_tarifa" value="0">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Parqueo / Sede *</label>
                        <select name="id_parqueo" id="form_id_parqueo" class="form-select" required>
                            <?php foreach ($parqueos as $p): ?>
                                <option value="<?= $p['id_parqueo'] ?>">
                                    <?= htmlspecialchars($p['nombre_parqueo']) ?> &mdash; <?= htmlspecialchars($p['zona']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Tipo de Vehículo *</label>
                        <select name="id_tipo_vehiculo" id="form_tarifa_tipo" class="form-select" required>
                            <?php foreach ($tiposVehiculo as $tv): ?>
                                <option value="<?= $tv['id_tipo_vehiculo'] ?>">
                                    <?= htmlspecialchars($tv['nombre_tipo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Precio por Hora (Bs.) *</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs.</span>
                                <input type="number" step="0.50" min="1" name="precio_hora" id="form_precio_hora" class="form-control fw-bold" placeholder="5.00" required onchange="sugerirValores()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Fracción (30 min) (Bs.)</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs.</span>
                                <input type="number" step="0.50" min="0" name="precio_fraccion" id="form_precio_fraccion" class="form-control" placeholder="2.50">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Día Completo / 24h (Bs.)</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs.</span>
                                <input type="number" step="1.00" min="0" name="precio_dia" id="form_precio_dia" class="form-control" placeholder="40.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Tolerancia Gratuita</label>
                            <div class="input-group">
                                <input type="number" min="0" max="60" name="tolerancia_minutos" id="form_tolerancia" class="form-control" value="10">
                                <span class="input-group-text">minutos</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="vigente" id="form_vigente" value="1" checked>
                        <label class="form-check-label small fw-semibold" for="form_vigente">
                            Marcar como Tarifa Vigente Oficial (reemplazará tarifas anteriores activas para este parqueo y tipo)
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold" style="background-color: #152b47;">
                        <i class="bi bi-save me-1"></i>Guardar Tarifa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalNuevaTarifa() {
    document.getElementById('modalTarifaLabel').innerHTML = '<i class="bi bi-cash-coin me-2 text-warning"></i>Nueva Regla Tarifaria';
    document.getElementById('form_id_tarifa').value = '0';
    document.getElementById('form_precio_hora').value = '5.00';
    document.getElementById('form_precio_fraccion').value = '2.50';
    document.getElementById('form_precio_dia').value = '40.00';
    document.getElementById('form_tolerancia').value = '10';
    document.getElementById('form_vigente').checked = true;
    document.getElementById('form_id_parqueo').disabled = false;
    document.getElementById('form_tarifa_tipo').disabled = false;
}

function sugerirValores() {
    var hora = parseFloat(document.getElementById('form_precio_hora').value) || 0;
    if (hora > 0) {
        var frac = document.getElementById('form_precio_fraccion');
        if (!frac.value || parseFloat(frac.value) === 0) {
            frac.value = (hora / 2).toFixed(2);
        }
        var dia = document.getElementById('form_precio_dia');
        if (!dia.value || parseFloat(dia.value) === 0) {
            dia.value = (hora * 8).toFixed(2);
        }
    }
}

function editarTarifa(t) {
    document.getElementById('modalTarifaLabel').innerHTML = '<i class="bi bi-pencil-square me-2 text-warning"></i>Editar Tarifa';
    document.getElementById('form_id_tarifa').value = t.id_tarifa;
    document.getElementById('form_id_parqueo').value = t.id_parqueo;
    document.getElementById('form_tarifa_tipo').value = t.id_tipo_vehiculo;
    document.getElementById('form_precio_hora').value = parseFloat(t.precio_hora).toFixed(2);
    document.getElementById('form_precio_fraccion').value = parseFloat(t.precio_fraccion).toFixed(2);
    document.getElementById('form_precio_dia').value = parseFloat(t.precio_dia).toFixed(2);
    document.getElementById('form_tolerancia').value = t.tolerancia_minutos || 10;
    document.getElementById('form_vigente').checked = (t.vigente == 1 || t.vigente === true);
    
    var modal = new bootstrap.Modal(document.getElementById('modalTarifa'));
    modal.show();
}
</script>

