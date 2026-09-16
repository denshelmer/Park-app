<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Tarjeta Formal de Reserva -->
        <div class="card formal-card">
            <div class="formal-header p-4" style="background-color: #f8fafc; border-bottom: 1px solid var(--park-border);">
                <div class="d-flex align-items-center">
                    <div class="brand-badge me-3" style="width: 44px; height: 44px; font-size: 1.3rem;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0" style="color: var(--park-primary);">Nueva Reserva de Espacio</h4>
                        <p class="text-muted small mb-0">Seleccione el parqueo, vehículo y hora prevista de llegada para asegurar su cajón.</p>
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
                        <i class="bi bi-check-circle alert-icon"></i>
                        <div><?= htmlspecialchars($success) ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/reservar" method="POST">
                    <div class="row g-4">
                        <!-- Parqueo -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Establecimiento / Parqueo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <select class="form-select" name="id_parqueo" required>
                                    <option value="">Seleccione un parqueo...</option>
                                    <?php foreach ($parqueos as $parq): ?>
                                        <option value="<?= $parq['id_parqueo'] ?>" <?= ((int)$parqueoSeleccionado === (int)$parq['id_parqueo']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($parq['nombre_parqueo']) ?> (<?= htmlspecialchars($parq['zona']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tipo de Vehículo -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Tipo de Vehículo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-car-front"></i></span>
                                <select class="form-select" name="id_tipo_vehiculo" required>
                                    <option value="">Seleccione el tipo...</option>
                                    <?php foreach ($tiposVehiculo as $tv): ?>
                                        <option value="<?= $tv['id_tipo_vehiculo'] ?>">
                                            <?= htmlspecialchars($tv['nombre_tipo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Placa Vehicular -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Placa del Vehículo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-credit-card-2-front"></i></span>
                                <input type="text" class="form-control text-uppercase fw-bold" name="placa" 
                                       placeholder="Ej. 4829-ABC" pattern="[0-9A-Za-z\s\-]{5,10}" maxlength="10"
                                       title="Ingrese una placa vehicular válida (ej. 4829-ABC o 1234XYZ)" required>
                            </div>
                            <div class="form-text text-muted small">Formato estándar boliviano (ej: 4829-ABC).</div>
                        </div>

                        <!-- Fecha y Hora Prevista -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Hora Estimada de Llegada</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <input type="datetime-local" class="form-control" name="fecha_hora_prevista_llegada" 
                                       min="<?= date('Y-m-d\TH:i') ?>" required>
                            </div>
                            <div class="form-text text-muted small">Hora a la que planea ingresar al parqueo.</div>
                        </div>
                    </div>

                    <!-- Alerta de Tolerancia Formal -->
                    <div class="classic-alert classic-alert-warning mt-4 mb-4">
                        <i class="bi bi-info-circle alert-icon"></i>
                        <div>
                            <strong>Margen de Tolerancia Oficial:</strong><br>
                            Usted dispone de <strong>15 minutos de tolerancia</strong> a partir de su hora estimada de llegada. De no presentarse dentro de ese lapso, el sistema o el operador podrán reasignar el espacio.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="<?= BASE_URL ?>/disponibilidad" class="btn btn-outline-secondary px-4 py-2">
                            <i class="bi bi-arrow-left me-1"></i>Volver
                        </a>
                        <button type="submit" class="btn btn-primary-formal px-4 py-2">
                            <i class="bi bi-qr-code me-1"></i>Confirmar y Generar Código QR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

