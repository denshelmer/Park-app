<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
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
                                <select class="form-select" name="id_parqueo" id="select_parqueo" required>
                                    <option value="">Seleccione un parqueo...</option>
                                    <?php foreach ($parqueos as $parq): ?>
                                        <?php 
                                            $apertura = !empty($parq['hora_apertura']) ? date('H:i', strtotime($parq['hora_apertura'])) : '06:00';
                                            $cierre = !empty($parq['hora_cierre']) ? date('H:i', strtotime($parq['hora_cierre'])) : '23:00';
                                        ?>
                                        <option value="<?= $parq['id_parqueo'] ?>" 
                                                data-apertura="<?= $apertura ?>" 
                                                data-cierre="<?= $cierre ?>"
                                                <?= ((int)$parqueoSeleccionado === (int)$parq['id_parqueo']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($parq['nombre_parqueo']) ?> (<?= htmlspecialchars($parq['zona']) ?>) - Horario: <?= $apertura ?> a <?= $cierre ?>
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
                                <select class="form-select" name="id_tipo_vehiculo" id="select_tipo_vehiculo" required>
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
                                <input type="text" class="form-control text-uppercase fw-bold" name="placa" id="input_placa"
                                       placeholder="Ej. 4829-ABC" maxlength="8" autocomplete="off"
                                       title="Ingrese una placa boliviana válida (ej. 4829-ABC o 123-XYZ)" required>
                                <span class="input-group-text bg-white" id="placa_feedback_icon">
                                    <i class="bi bi-dash-circle text-muted"></i>
                                </span>
                            </div>
                            <div class="form-text small" id="placa_helper_text">Formato boliviano: 3 o 4 dígitos seguidos de guión y 3 letras (ej. 4829-ABC).</div>
                        </div>

                        <!-- Fecha y Hora Prevista de Llegada -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Hora Estimada de Llegada</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <input type="datetime-local" class="form-control" name="fecha_hora_prevista_llegada" id="input_fecha_hora"
                                       value="<?= date('Y-m-d\TH:i', strtotime('+5 minutes')) ?>"
                                       min="<?= date('Y-m-d\TH:i') ?>"
                                       max="<?= date('Y-m-d\TH:i', strtotime('+48 hours')) ?>" required>
                            </div>
                            <div class="form-text text-muted small">
                                <i class="bi bi-calendar-check text-success me-1"></i>Válido para <strong>hoy</strong> o hasta 48 hrs. Dispone de <strong>15 min de tolerancia</strong> tras esta hora.
                            </div>
                        </div>
                    </div>

                    <!-- SELECTOR GRÁFICO INTERACTIVO DE ESPACIOS (ESTILO CINE / BAHÍAS DE PARQUEO) -->
                    <input type="hidden" name="id_espacio" id="input_id_espacio" value="">

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold mb-0" style="color: var(--park-primary); font-size: 1rem;">
                                <i class="bi bi-grid-3x3-gap-fill me-1" style="color: var(--park-accent);"></i> Distribución de Espacios (Mapa Interactivo)
                            </label>
                            <span class="badge bg-light text-secondary border px-3 py-2" id="map-status-badge">
                                <i class="bi bi-info-circle me-1"></i>Haga clic en un cajón libre
                            </span>
                        </div>
                        <p class="text-muted small mb-3">
                            Seleccione visualmente el cajón que prefiera (como al elegir asientos de cine). Si prefiere que el sistema le asigne automáticamente el mejor lugar disponible, simplemente complete sus datos y confirme su reserva.
                        </p>

                        <!-- Barra de Leyenda Estilo Sala de Cine -->
                        <div class="parking-legend-bar">
                            <div class="parking-legend-item">
                                <span class="parking-legend-badge legend-available"></span>
                                <span>Disponible</span>
                            </div>
                            <div class="parking-legend-item">
                                <span class="parking-legend-badge legend-selected"></span>
                                <span>Tu Selección</span>
                            </div>
                            <div class="parking-legend-item">
                                <span class="parking-legend-badge legend-occupied"></span>
                                <span>Ocupado</span>
                            </div>
                            <div class="parking-legend-item">
                                <span class="parking-legend-badge legend-reserved"></span>
                                <span>Reservado</span>
                            </div>
                            <div class="parking-legend-item">
                                <span class="parking-legend-badge legend-maint"></span>
                                <span>Taller</span>
                            </div>
                        </div>

                        <!-- Contenedor del Mapa -->
                        <div class="parking-map-container" id="parking-map-box">
                            <div class="text-center py-4 text-muted" id="map-loading-placeholder">
                                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                                <div>Cargando distribución de espacios del establecimiento...</div>
                            </div>
                            <div id="parking-map-content" style="display: none;"></div>
                        </div>

                        <!-- Banner de Confirmación del Espacio Seleccionado -->
                        <div class="active-selection-callout" id="active-selection-box" style="display: none;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                <div>
                                    <strong class="text-dark">Espacio Seleccionado:</strong>
                                    <span class="badge fs-6 ms-2 px-3 py-1" style="background-color: var(--park-primary); color: #ffffff;" id="selected-space-name">A-02</span>
                                    <span class="text-muted small ms-2" id="selected-space-sector">(Sector A)</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-clear-selection" title="Cambiar a asignación automática">
                                <i class="bi bi-x-circle me-1"></i>Desmarcar (Auto)
                            </button>
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

<!-- ESTILOS INLINE DE LA CUADRÍCULA ESTILO CINE (Inmunes a caché de navegador) -->
<style>
.parking-map-container {
    background-color: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 20px;
    margin-top: 15px;
}

.parking-legend-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding: 12px 16px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.85rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.parking-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #334155;
}

.parking-legend-badge {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    display: inline-block;
    border: 2px solid;
}

.legend-available { background-color: #d1fae5; border-color: #10b981; }
.legend-selected  { background-color: #1e3a5f; border-color: #c89234; box-shadow: 0 0 0 2px #c89234; }
.legend-occupied  { background-color: #fee2e2; border-color: #ef4444; }
.legend-reserved  { background-color: #fef3c7; border-color: #f59e0b; }
.legend-maint     { background-color: #e2e8f0; border-color: #94a3b8; }

.sector-wrapper {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.02);
}

.sector-title-badge {
    font-size: 0.88rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #1e3a5f;
    background: #edf2f7;
    padding: 6px 14px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
}

/* Cuadrícula estilo slots de cine / bahías de estacionamiento */
.parking-slots-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(115px, 1fr)) !important;
    gap: 14px !important;
}

/* Cajón individual de estacionamiento */
.parking-slot-item {
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 6px;
    text-align: center;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 110px;
}

.parking-slot-code {
    font-size: 1.25rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 2px;
    font-family: 'Consolas', 'Monaco', monospace, sans-serif;
}

.parking-slot-type {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #64748b;
    display: block;
    margin-bottom: 6px;
}

.parking-slot-badge {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* 1. Estado: DISPONIBLE (Verde, interactivo) */
.parking-slot-item.is-available {
    border: 2px solid #10b981 !important;
    background: #ecfdf5 !important;
    cursor: pointer !important;
}
.parking-slot-item.is-available:hover {
    transform: translateY(-4px) scale(1.04);
    border-color: #059669 !important;
    background: #d1fae5 !important;
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.25) !important;
}
.parking-slot-item.is-available .parking-slot-code {
    color: #065f46 !important;
}
.parking-slot-item.is-available .slot-icon {
    color: #10b981 !important;
}
.parking-slot-item.is-available .parking-slot-badge {
    background: #a7f3d0 !important;
    color: #065f46 !important;
}

/* 2. Estado: SELECCIONADO (Dorado & Marino institucional) */
.parking-slot-item.is-selected {
    border: 3px solid #c89234 !important;
    background: linear-gradient(145deg, #1e3a5f, #152b47) !important;
    color: #ffffff !important;
    cursor: pointer !important;
    transform: translateY(-4px) scale(1.06);
    box-shadow: 0 10px 22px rgba(30, 58, 95, 0.4), 0 0 0 3px rgba(200, 146, 52, 0.5) !important;
}
.parking-slot-item.is-selected .parking-slot-code {
    color: #ffffff !important;
}
.parking-slot-item.is-selected .slot-icon {
    color: #c89234 !important;
}
.parking-slot-item.is-selected .parking-slot-type {
    color: #cbd5e1 !important;
}
.parking-slot-item.is-selected .parking-slot-badge {
    background: #c89234 !important;
    color: #ffffff !important;
}

/* 3. Estado: OCUPADO (Rojo deshabilitado) */
.parking-slot-item.is-occupied {
    border: 2px dashed #f87171 !important;
    background: #fef2f2 !important;
    opacity: 0.78;
    cursor: not-allowed !important;
}
.parking-slot-item.is-occupied .parking-slot-code {
    color: #b91c1c !important;
}
.parking-slot-item.is-occupied .slot-icon {
    color: #ef4444 !important;
}
.parking-slot-item.is-occupied .parking-slot-badge {
    background: #fee2e2 !important;
    color: #b91c1c !important;
}

/* 4. Estado: RESERVADO (Ámbar deshabilitado) */
.parking-slot-item.is-reserved {
    border: 2px dashed #fbbf24 !important;
    background: #fffbeb !important;
    opacity: 0.78;
    cursor: not-allowed !important;
}
.parking-slot-item.is-reserved .parking-slot-code {
    color: #b45309 !important;
}
.parking-slot-item.is-reserved .slot-icon {
    color: #f59e0b !important;
}
.parking-slot-item.is-reserved .parking-slot-badge {
    background: #fef3c7 !important;
    color: #b45309 !important;
}

/* 5. Estado: MANTENIMIENTO (Gris deshabilitado) */
.parking-slot-item.is-maintenance {
    border: 2px dashed #cbd5e1 !important;
    background: #f8fafc !important;
    opacity: 0.65;
    cursor: not-allowed !important;
}
.parking-slot-item.is-maintenance .parking-slot-code {
    color: #64748b !important;
}
.parking-slot-item.is-maintenance .slot-icon {
    color: #94a3b8 !important;
}
.parking-slot-item.is-maintenance .parking-slot-badge {
    background: #e2e8f0 !important;
    color: #64748b !important;
}

/* Banner de Notificación de Selección Activa */
.active-selection-callout {
    background: #f0fdf4;
    border: 1px solid #86efac;
    border-left: 5px solid #16a34a;
    border-radius: 8px;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 16px;
    font-size: 0.92rem;
    box-shadow: 0 2px 8px rgba(22, 163, 74, 0.08);
}
</style>

<!-- SCRIPT INTERACTIVO DEL MAPA DE ESPACIOS (ESTILO CINE) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectParqueo = document.querySelector('select[name="id_parqueo"]');
    const selectTipo = document.querySelector('select[name="id_tipo_vehiculo"]');
    const inputEspacio = document.getElementById('input_id_espacio');
    const mapPlaceholder = document.getElementById('map-loading-placeholder');
    const mapContent = document.getElementById('parking-map-content');
    const selectionBox = document.getElementById('active-selection-box');
    const selectedName = document.getElementById('selected-space-name');
    const selectedSector = document.getElementById('selected-space-sector');
    const btnClearSelection = document.getElementById('btn-clear-selection');
    const mapStatusBadge = document.getElementById('map-status-badge');

    function getVehicleIcon(nombreTipo) {
        const nombre = (nombreTipo || '').toLowerCase();
        if (nombre.includes('moto')) return 'bi-bicycle';
        if (nombre.includes('camion') || nombre.includes('mini') || nombre.includes('bus')) return 'bi-truck';
        return 'bi-car-front-fill';
    }

    function desmarcarEspacio() {
        inputEspacio.value = '';
        document.querySelectorAll('.parking-slot-item.is-selected').forEach(el => {
            el.classList.remove('is-selected');
            el.classList.add('is-available');
            const badge = el.querySelector('.parking-slot-badge');
            if (badge) {
                badge.textContent = 'Libre';
            }
        });
        selectionBox.style.display = 'none';
        mapStatusBadge.className = 'badge bg-light text-secondary border px-3 py-2';
        mapStatusBadge.innerHTML = '<i class="bi bi-info-circle me-1"></i>Asignación automática';
    }

    if (btnClearSelection) {
        btnClearSelection.addEventListener('click', desmarcarEspacio);
    }

    function cargarMapaEspacios() {
        const parqueoId = selectParqueo.value;
        const tipoId = selectTipo.value;

        if (!parqueoId) {
            mapPlaceholder.innerHTML = '<div class="text-muted"><i class="bi bi-arrow-up-circle me-1"></i>Seleccione un parqueo para ver la distribución de espacios.</div>';
            mapPlaceholder.style.display = 'block';
            mapContent.style.display = 'none';
            desmarcarEspacio();
            return;
        }

        mapPlaceholder.innerHTML = '<div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div><div>Cargando espacios del establecimiento...</div>';
        mapPlaceholder.style.display = 'block';
        mapContent.style.display = 'none';

        let url = '<?= BASE_URL ?>/api/espacios?parqueo=' + encodeURIComponent(parqueoId);
        if (tipoId) {
            url += '&tipo=' + encodeURIComponent(tipoId);
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                mapPlaceholder.style.display = 'none';
                mapContent.innerHTML = '';

                if (!data.success || data.total_espacios === 0) {
                    mapContent.innerHTML = `
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-exclamation-circle text-warning fs-3 d-block mb-2"></i>
                            <div>No se encontraron espacios registrados para este parqueo con el tipo de vehículo seleccionado.</div>
                            <small class="text-secondary">El sistema intentará asignar un cajón automáticamente al confirmar.</small>
                        </div>
                    `;
                    mapContent.style.display = 'block';
                    desmarcarEspacio();
                    return;
                }

                // Renderizar sectores en contenedores estilizados
                for (const [sector, espacios] of Object.entries(data.sectores)) {
                    const sectorDiv = document.createElement('div');
                    sectorDiv.className = 'sector-wrapper mb-4';

                    const headerBadge = document.createElement('div');
                    headerBadge.className = 'sector-title-badge';
                    headerBadge.innerHTML = `<i class="bi bi-layers-half"></i> ${sector} <span class="badge bg-secondary ms-1">${espacios.length} cajones</span>`;
                    sectorDiv.appendChild(headerBadge);

                    const gridDiv = document.createElement('div');
                    gridDiv.className = 'parking-slots-grid';

                    espacios.forEach(esp => {
                        const slot = document.createElement('div');
                        slot.className = 'parking-slot-item';
                        slot.dataset.id = esp.id_espacio;
                        slot.dataset.codigo = esp.codigo_espacio;
                        slot.dataset.sector = sector;
                        slot.dataset.estado = esp.estado;
                        slot.dataset.tipo = esp.nombre_tipo;

                        const iconClass = getVehicleIcon(esp.nombre_tipo);

                        let badgeText = esp.estado;
                        if (esp.estado === 'Disponible') {
                            slot.classList.add('is-available');
                            badgeText = 'Libre';
                        } else if (esp.estado === 'Ocupado') {
                            slot.classList.add('is-occupied');
                            badgeText = 'Ocupado';
                        } else if (esp.estado === 'Reservado') {
                            slot.classList.add('is-reserved');
                            badgeText = 'Reservado';
                        } else {
                            slot.classList.add('is-maintenance');
                            badgeText = 'Taller';
                        }

                        // Si este espacio ya estaba seleccionado previamente
                        if (inputEspacio.value && parseInt(inputEspacio.value) === esp.id_espacio && esp.estado === 'Disponible') {
                            slot.classList.remove('is-available');
                            slot.classList.add('is-selected');
                            badgeText = 'Tu Selección';
                        }

                        slot.innerHTML = `
                            <div class="slot-icon mb-1" style="font-size: 1.25rem;"><i class="bi ${iconClass}"></i></div>
                            <span class="parking-slot-code">${esp.codigo_espacio}</span>
                            <span class="parking-slot-type">${esp.nombre_tipo || 'General'}</span>
                            <span class="parking-slot-badge">${badgeText}</span>
                        `;

                        // Interacción tipo cine con un clic
                        if (esp.estado === 'Disponible') {
                            slot.addEventListener('click', function() {
                                // Si ya está seleccionado, desmarcar
                                if (slot.classList.contains('is-selected')) {
                                    desmarcarEspacio();
                                    return;
                                }

                                // Desmarcar cualquier otro slot seleccionado
                                document.querySelectorAll('.parking-slot-item.is-selected').forEach(el => {
                                    el.classList.remove('is-selected');
                                    el.classList.add('is-available');
                                    const b = el.querySelector('.parking-slot-badge');
                                    if (b) {
                                        b.textContent = 'Libre';
                                    }
                                });

                                // Marcar el seleccionado actual
                                slot.classList.remove('is-available');
                                slot.classList.add('is-selected');
                                const badge = slot.querySelector('.parking-slot-badge');
                                if (badge) {
                                    badge.textContent = 'Tu Selección';
                                }

                                inputEspacio.value = esp.id_espacio;
                                selectedName.textContent = esp.codigo_espacio;
                                selectedSector.textContent = `(${sector} - ${esp.nombre_tipo || 'Vehículo'})`;
                                selectionBox.style.display = 'flex';

                                mapStatusBadge.className = 'badge bg-success text-white px-3 py-2';
                                mapStatusBadge.innerHTML = `<i class="bi bi-check2-circle me-1"></i>Espacio ${esp.codigo_espacio} seleccionado`;
                            });
                        }

                        gridDiv.appendChild(slot);
                    });

                    sectorDiv.appendChild(gridDiv);
                    mapContent.appendChild(sectorDiv);
                }

                mapContent.style.display = 'block';
            })
            .catch(err => {
                console.error('Error al cargar mapa:', err);
                mapPlaceholder.innerHTML = '<div class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>No se pudo cargar la distribución de espacios.</div>';
                mapPlaceholder.style.display = 'block';
            });
    }

    // -------------------------------------------------------------
    // FORMATEO Y VALIDACIÓN EN TIEMPO REAL DE PLACA BOLIVIANA
    // -------------------------------------------------------------
    const inputPlaca = document.getElementById('input_placa');
    const placaFeedback = document.getElementById('placa_feedback_icon');
    const placaHelper = document.getElementById('placa_helper_text');

    if (inputPlaca) {
        inputPlaca.addEventListener('input', function() {
            let val = this.value.toUpperCase().replace(/[^0-9A-Z]/g, '');
            // Formatear automáticamente con guión: 3 o 4 dígitos + guión + letras
            if (val.length > 3) {
                if (/^[0-9]{4}/.test(val)) {
                    val = val.substring(0, 4) + (val.length > 4 ? '-' + val.substring(4, 7) : '');
                } else if (/^[0-9]{3}/.test(val)) {
                    val = val.substring(0, 3) + (val.length > 3 ? '-' + val.substring(3, 6) : '');
                }
            }
            this.value = val;

            const esValida = /^[0-9]{3,4}-[A-Z]{3}$/.test(val);
            if (esValida) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                if (placaFeedback) placaFeedback.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
                if (placaHelper) {
                    placaHelper.className = 'form-text text-success small';
                    placaHelper.textContent = 'Placa con formato boliviano válido.';
                }
            } else {
                this.classList.remove('is-valid');
                if (val.length >= 7) {
                    this.classList.add('is-invalid');
                    if (placaFeedback) placaFeedback.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i>';
                } else {
                    this.classList.remove('is-invalid');
                    if (placaFeedback) placaFeedback.innerHTML = '<i class="bi bi-dash-circle text-muted"></i>';
                }
                if (placaHelper) {
                    placaHelper.className = 'form-text text-muted small';
                    placaHelper.textContent = 'Formato boliviano: 3 o 4 dígitos seguidos de guión y 3 letras (ej. 4829-ABC).';
                }
            }
        });
    }

    // -------------------------------------------------------------
    // VALIDACIÓN ESTRICTA DE FECHA Y HORA (DESDE AHORA HACIA ADELANTE)
    // -------------------------------------------------------------
    const inputFechaHora = document.getElementById('input_fecha_hora');
    if (inputFechaHora) {
        inputFechaHora.addEventListener('change', function() {
            const ahora = new Date();
            // Margen de tolerancia de 3 minutos respecto al reloj del cliente
            const margenMinimo = new Date(ahora.getTime() - (3 * 60 * 1000));
            const seleccionada = new Date(this.value);

            if (isNaN(seleccionada.getTime()) || seleccionada < margenMinimo) {
                alert('La hora estimada de llegada no puede ser anterior al momento actual. Se ha reajustado a la hora actual.');
                const reajuste = new Date(ahora.getTime() + (5 * 60 * 1000));
                const year = reajuste.getFullYear();
                const month = String(reajuste.getMonth() + 1).padStart(2, '0');
                const day = String(reajuste.getDate()).padStart(2, '0');
                const hours = String(reajuste.getHours()).padStart(2, '0');
                const mins = String(reajuste.getMinutes()).padStart(2, '0');
                this.value = `${year}-${month}-${day}T${hours}:${mins}`;
            }

            validarHorarioParqueo();
        });
    }

    function validarHorarioParqueo() {
        if (!selectParqueo || !inputFechaHora || !inputFechaHora.value) return;
        const option = selectParqueo.options[selectParqueo.selectedIndex];
        if (!option || !option.value) return;

        const apertura = option.getAttribute('data-apertura');
        const cierre = option.getAttribute('data-cierre');
        if (!apertura || !cierre) return;

        const timePart = inputFechaHora.value.split('T')[1];
        if (timePart && (timePart < apertura || timePart > cierre)) {
            alert(`Atención: El establecimiento seleccionado atiende de ${apertura} a ${cierre}. Su hora estimada de llegada (${timePart}) se encuentra fuera de ese horario.`);
        }
    }

    selectParqueo.addEventListener('change', function() {
        desmarcarEspacio();
        validarHorarioParqueo();
        cargarMapaEspacios();
    });

    selectTipo.addEventListener('change', function() {
        desmarcarEspacio();
        cargarMapaEspacios();
    });

    // Carga inicial al entrar si ya hay un parqueo preseleccionado
    if (selectParqueo.value) {
        cargarMapaEspacios();
    }
});
</script>

