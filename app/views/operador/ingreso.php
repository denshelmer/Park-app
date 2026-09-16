<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card formal-card">
            <div class="formal-header p-4" style="background-color: #f8fafc; border-bottom: 1px solid var(--park-border);">
                <div class="d-flex align-items-center">
                    <div class="brand-badge me-3" style="width: 44px; height: 44px; font-size: 1.3rem;">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0" style="color: var(--park-primary);">Registrar Ingreso a Parqueo</h4>
                        <p class="text-muted small mb-0">Emisión de ticket físico directo o validación de pase digital por reserva anticipada.</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
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

                <ul class="nav nav-pills mb-4 nav-justified" id="ingresoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'manual' ? 'active' : '' ?> fw-semibold py-2" 
                                id="manual-tab" data-bs-toggle="pill" data-bs-target="#manual" type="button" role="tab">
                            <i class="bi bi-ticket-perforated me-1"></i>Ingreso Directo (Ticket Normal)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'qr' ? 'active' : '' ?> fw-semibold py-2" 
                                id="qr-tab" data-bs-toggle="pill" data-bs-target="#qr" type="button" role="tab">
                            <i class="bi bi-qr-code-scan me-1"></i>Validar Reserva con QR
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="ingresoTabContent">
                    <!-- 1. INGRESO DIRECTO -->
                    <div class="tab-pane fade <?= $activeTab === 'manual' ? 'show active' : '' ?>" id="manual" role="tabpanel">
                        <form action="<?= BASE_URL ?>/caseta/ingreso" method="POST" id="formIngresoDirecto">
                            <input type="hidden" name="tipo_ingreso" value="directo">

                            <?php if (count($parqueos) > 1): ?>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Parqueo de Entrada</label>
                                    <select class="form-select" name="id_parqueo" onchange="window.location.href='<?= BASE_URL ?>/caseta/ingreso?id_parqueo=' + this.value">
                                        <?php foreach ($parqueos as $p): ?>
                                            <option value="<?= $p['id_parqueo'] ?>" <?= $p['id_parqueo'] == $idParqueo ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($p['nombre_parqueo']) ?> (<?= htmlspecialchars($p['zona']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="id_parqueo" value="<?= $idParqueo ?>">
                            <?php endif; ?>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Placa del Vehículo <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light"><i class="bi bi-card-text"></i></span>
                                        <input type="text" class="form-control text-uppercase fw-bold font-monospace" 
                                               name="placa" id="placaInput" placeholder="Ej. 2049-ZXY" maxlength="9" required autofocus>
                                    </div>
                                    <small class="text-muted">Formato estándar boliviano (3 o 4 números y 3 letras).</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tipo de Vehículo <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" name="id_tipo_vehiculo" required>
                                        <?php foreach ($tiposVehiculo as $tv): ?>
                                            <option value="<?= $tv['id_tipo_vehiculo'] ?>">
                                                <?= htmlspecialchars($tv['nombre_tipo']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Asignar Cajón Libre <span class="text-danger">*</span></label>
                                    <?php if (empty($espaciosDisponibles)): ?>
                                        <div class="alert alert-warning mb-0">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            <strong>No hay cajones disponibles</strong> en este momento en el parqueo seleccionado.
                                        </div>
                                    <?php else: ?>
                                        <select class="form-select form-select-lg" name="id_espacio" required>
                                            <option value="">-- Seleccionar Espacio Libre --</option>
                                            <?php foreach ($espaciosDisponibles as $esp): ?>
                                                <option value="<?= $esp['id_espacio'] ?>" <?= $esp['id_espacio'] == $preEspacioId ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($esp['codigo_espacio']) ?> - <?= htmlspecialchars($esp['piso_sector']) ?> (<?= htmlspecialchars($esp['nombre_tipo'] ?? 'Vehículo') ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Se listan solo los espacios en estado 'Disponible'.</small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                <a href="<?= BASE_URL ?>/caseta" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Volver a Caseta
                                </a>
                                <button type="submit" class="btn btn-success btn-lg fw-bold px-4" <?= empty($espaciosDisponibles) ? 'disabled' : '' ?>>
                                    <i class="bi bi-printer me-2"></i>Registrar e Imprimir Ticket
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- 2. VALIDAR RESERVA CON QR -->
                    <div class="tab-pane fade <?= $activeTab === 'qr' ? 'show active' : '' ?>" id="qr" role="tabpanel">
                        <form action="<?= BASE_URL ?>/caseta/ingreso" method="POST">
                            <input type="hidden" name="tipo_ingreso" value="qr">

                            <div class="text-center p-4 bg-light rounded border mb-4">
                                <div class="mb-3">
                                    <i class="bi bi-qr-code-scan text-primary" style="font-size: 3.5rem;"></i>
                                </div>
                                <h6 class="fw-bold mb-1" style="color: var(--park-primary);">Lector de Código QR / Código Alfanumérico</h6>
                                <p class="text-muted small mb-0">
                                    Escanee con la pistola óptica el pase digital del conductor o ingrese manualmente el token de reserva.
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Token / Código de la Reserva <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white"><i class="bi bi-shield-check text-success"></i></span>
                                    <input type="text" class="form-control form-control-lg text-center fw-bold font-monospace" 
                                           name="codigo_qr_token" id="qrTokenInput" 
                                           placeholder="Ej. QR-ALTO-2026-001" 
                                           value="<?= htmlspecialchars($preToken) ?>" 
                                           required <?= $activeTab === 'qr' ? 'autofocus' : '' ?>>
                                </div>
                                <small class="text-muted">El sistema validará automáticamente la vigencia, espacio reservado y placa autorizada.</small>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                <a href="<?= BASE_URL ?>/caseta" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Volver a Caseta
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg fw-bold px-4">
                                    <i class="bi bi-check2-circle me-2"></i>Validar Reserva y Dar Paso
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const placaInput = document.getElementById('placaInput');
    if (placaInput) {
        placaInput.addEventListener('input', function(e) {
            let val = this.value.toUpperCase().replace(/[^0-9A-Z]/g, '');
            if (val.length > 4) {
                // Auto-formatear con guión si tiene formato boliviano (e.g. 4829ABC -> 4829-ABC)
                val = val.substring(0, 4) + '-' + val.substring(4, 7);
            }
            this.value = val;
        });
    }
});
</script>

