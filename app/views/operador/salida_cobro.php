<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card formal-card">
            <div class="formal-header p-4" style="background-color: #f8fafc; border-bottom: 1px solid var(--park-border);">
                <div class="d-flex align-items-center">
                    <div class="brand-badge me-3" style="width: 44px; height: 44px; font-size: 1.3rem; background-color: #dc3545;">
                        <i class="bi bi-cash-coin text-white"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0" style="color: var(--park-primary);">Liquidación de Salida y Cobro</h4>
                        <p class="text-muted small mb-0">Cálculo automatizado de estancia y registro manual de pago (Efectivo o QR Institucional).</p>
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

                <!-- Formulario de Búsqueda de Estancia -->
                <form action="<?= BASE_URL ?>/caseta/salida" method="GET" class="mb-4">
                    <input type="hidden" name="id_parqueo" value="<?= $idParqueo ?>">
                    <label class="form-label fw-semibold">Buscar Ticket o Placa de Vehículo</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control fw-bold font-monospace" 
                               name="buscar" value="<?= htmlspecialchars($buscar) ?>" 
                               placeholder="Ej. TCK-2026-0001 o 2049-ZXY..." 
                               required autofocus>
                        <button class="btn btn-dark fw-bold px-4" type="submit">
                            Buscar
                        </button>
                    </div>
                    <small class="text-muted">Puede ingresar el número de ticket impreso o la placa vehicular.</small>
                </form>

                <?php if ($estancia): ?>
                    <!-- Resumen Detallado de Liquidación -->
                    <div class="p-4 bg-light rounded border mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <span class="badge bg-danger fs-6 px-3 py-1 font-monospace">
                                    Ticket: <?= htmlspecialchars($estancia['numero_ticket']) ?>
                                </span>
                                <?php if (!empty($estancia['id_reserva'])): ?>
                                    <span class="badge bg-warning text-dark ms-2">Con Reserva Previa</span>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-secondary fs-6">
                                Espacio: <?= htmlspecialchars($estancia['codigo_espacio']) ?> (<?= htmlspecialchars($estancia['piso_sector']) ?>)
                            </span>
                        </div>

                        <div class="row g-3 small">
                            <div class="col-sm-6">
                                <div class="text-muted">Vehículo y Placa:</div>
                                <div class="fs-5 fw-bold text-dark font-monospace"><?= htmlspecialchars($estancia['placa']) ?></div>
                                <div class="text-muted"><?= htmlspecialchars($estancia['nombre_tipo'] ?? 'Automóvil') ?></div>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <div class="text-muted">Parqueo:</div>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($estancia['nombre_parqueo'] ?? 'Parqueo Central') ?></div>
                                <div class="text-muted"><?= htmlspecialchars($estancia['zona'] ?? 'El Alto') ?></div>
                            </div>

                            <div class="col-sm-4 pt-2">
                                <div class="text-muted">Hora de Entrada:</div>
                                <div class="fw-semibold text-dark"><?= date('d/m/Y H:i', strtotime($estancia['fecha_hora_entrada'])) ?></div>
                            </div>
                            <div class="col-sm-4 pt-2">
                                <div class="text-muted">Hora de Salida:</div>
                                <div class="fw-semibold text-dark"><?= date('d/m/Y H:i') ?></div>
                            </div>
                            <div class="col-sm-4 pt-2 text-sm-end">
                                <div class="text-muted">Tiempo Transcurrido:</div>
                                <div class="fw-bold text-primary fs-6"><?= $tiempoFormateado ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;">(<?= $minutosEstancia ?> minutos computables)</div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Desglose de Tarifas -->
                        <div class="bg-white p-3 rounded border mb-2">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="text-muted">Detalle del cómputo:</span>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($calculoTarifa['desglose'] ?? 'Cálculo estándar') ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="text-muted">Subtotal tarifa calculada:</span>
                                <span class="fw-bold text-dark">Bs. <?= number_format($calculoTarifa['total'], 2) ?></span>
                            </div>
                            <?php if ($montoAdelanto > 0): ?>
                                <div class="d-flex justify-content-between align-items-center small text-success mb-1">
                                    <span><i class="bi bi-check-circle me-1"></i>Adelanto pagado en reserva:</span>
                                    <span class="fw-bold">- Bs. <?= number_format($montoAdelanto, 2) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top">
                                <span class="fs-5 fw-bold text-dark">Total a Cobrar:</span>
                                <span class="fs-2 fw-bold text-success" id="displayTotalPagar">
                                    Bs. <?= number_format($totalFinal, 2) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Liquidación y Pago -->
                    <form action="<?= BASE_URL ?>/caseta/salida" method="POST" id="formLiquidacion">
                        <input type="hidden" name="id_ingreso_salida" value="<?= (int)$estancia['id_ingreso_salida'] ?>">
                        <input type="hidden" id="totalAPagarHidden" value="<?= $totalFinal ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold" style="color: var(--park-primary);">
                                Modalidad de Pago <span class="text-danger">*</span>
                            </label>
                            <p class="text-muted small mb-3">
                                Seleccione la forma de cobro manual verificada en ventanilla:
                            </p>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <input type="radio" class="btn-check" name="metodo_pago" id="metodoEfectivo" value="Efectivo" checked onchange="toggleMetodoPago('efectivo')">
                                    <label class="btn btn-outline-success w-100 p-3 text-start d-flex align-items-center h-100" for="metodoEfectivo">
                                        <div class="brand-badge me-3 bg-success text-white" style="width: 38px; height: 38px; font-size: 1.2rem;">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-6">Pago en Efectivo</div>
                                            <small class="text-muted">Cobro directo con cálculo de cambio</small>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <input type="radio" class="btn-check" name="metodo_pago" id="metodoQR" value="QR Estático" onchange="toggleMetodoPago('qr')">
                                    <label class="btn btn-outline-primary w-100 p-3 text-start d-flex align-items-center h-100" for="metodoQR">
                                        <div class="brand-badge me-3 bg-primary text-white" style="width: 38px; height: 38px; font-size: 1.2rem;">
                                            <i class="bi bi-qr-code"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-6">QR Institucional</div>
                                            <small class="text-muted">Transferencia bancaria / Simple QR</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 1: SECCIÓN EFECTIVO -->
                        <div id="seccionEfectivo" class="card p-3 mb-4 border bg-light-subtle">
                            <h6 class="fw-bold mb-3 text-success"><i class="bi bi-calculator me-2"></i>Calculadora de Efectivo y Cambio</h6>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">Monto Recibido del Cliente (Bs.)</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">Bs.</span>
                                        <input type="number" step="0.50" min="0" class="form-control fw-bold" 
                                               id="montoRecibido" name="monto_recibido" 
                                               placeholder="0.00" value="<?= $totalFinal ?>">
                                    </div>
                                    <!-- Botones rápidos de billetes comunes en Bolivia -->
                                    <div class="d-flex gap-1 mt-2 flex-wrap">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setMontoRecibido(<?= $totalFinal ?>)">Exacto</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setMontoRecibido(10)">Bs. 10</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setMontoRecibido(20)">Bs. 20</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setMontoRecibido(50)">Bs. 50</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setMontoRecibido(100)">Bs. 100</button>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold">Cambio / Vuelto a Devolver</label>
                                    <div class="card p-3 text-center border-0 bg-white shadow-sm">
                                        <div class="small text-muted fw-semibold">MONTO CAMBIO</div>
                                        <div class="fs-2 fw-bold text-dark" id="displayCambio">Bs. 0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 2: SECCIÓN QR ESTÁTICO INSTITUCIONAL -->
                        <div id="seccionQR" class="card p-4 mb-4 border bg-light-subtle d-none">
                            <div class="text-center mb-3">
                                <h6 class="fw-bold text-primary mb-1">
                                    <i class="bi bi-qr-code me-1"></i>QR Institucional de Pago ParkApp
                                </h6>
                                <p class="text-muted small mb-3">
                                    Muestre este código al conductor para que realice la transferencia desde su aplicación bancaria móvil (BCP, BNB, Banco Unión, etc.).
                                </p>
                                
                                <div class="d-inline-block p-3 bg-white border rounded shadow-sm mb-3">
                                    <img src="<?= BASE_URL ?>/public/img/qr_pago_estatico.svg" alt="QR Institucional ParkApp" style="width: 180px; height: 180px;">
                                    <div class="fw-bold text-primary mt-2">Monto: Bs. <?= number_format($totalFinal, 2) ?></div>
                                    <small class="text-muted">ParkApp El Alto - Cobros</small>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">
                                        N° Referencia / Comprobante Bancario <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-receipt"></i></span>
                                        <input type="text" class="form-control font-monospace" 
                                               name="referencia_transaccion" id="referenciaQR" 
                                               placeholder="Ej. BCP-789456 o últimos dígitos">
                                    </div>
                                    <small class="text-muted">
                                        Verifique en la pantalla del teléfono del usuario la confirmación de la transferencia y registre el número de transacción.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="<?= BASE_URL ?>/caseta" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success btn-lg fw-bold px-4 shadow-sm" id="btnConfirmarCobro">
                                <i class="bi bi-check-circle-fill me-2"></i>Confirmar Pago y Liberar Cajón
                            </button>
                        </div>
                    </form>

                <?php else: ?>
                    <!-- Estado Inicial / Tabla de Selección Rápida si no hay búsqueda -->
                    <div class="card bg-light border-0 p-4 text-center mb-4">
                        <i class="bi bi-search text-muted mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-bold text-dark">Ingrese el ticket o placa para liquidar</h6>
                        <p class="text-muted small mb-0">
                            También puede seleccionar directamente uno de los vehículos activos en parqueo mostrados a continuación:
                        </p>
                    </div>

                    <?php if (!empty($activos)): ?>
                        <h6 class="fw-bold mb-3" style="color: var(--park-primary);">
                            <i class="bi bi-car-front-fill me-2"></i>Vehículos Pendientes de Salida (<?= count($activos) ?>)
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border bg-white rounded shadow-sm">
                                <thead class="table-light small text-uppercase">
                                    <tr>
                                        <th>Ticket</th>
                                        <th>Placa</th>
                                        <th>Espacio</th>
                                        <th>Entrada</th>
                                        <th class="text-end">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activos as $act): ?>
                                        <tr>
                                            <td class="fw-bold font-monospace"><?= htmlspecialchars($act['numero_ticket']) ?></td>
                                            <td>
                                                <span class="badge bg-dark font-monospace"><?= htmlspecialchars($act['placa']) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($act['codigo_espacio']) ?></span>
                                            </td>
                                            <td class="small"><?= date('d/m/Y H:i', strtotime($act['fecha_hora_entrada'])) ?></td>
                                            <td class="text-end">
                                                <a href="<?= BASE_URL ?>/caseta/salida?buscar=<?= urlencode($act['numero_ticket']) ?>" 
                                                   class="btn btn-sm btn-danger fw-semibold">
                                                    <i class="bi bi-cash-coin me-1"></i>Liquidar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMetodoPago(tipo) {
    const seccionEfe = document.getElementById('seccionEfectivo');
    const seccionQR = document.getElementById('seccionQR');
    const refInput = document.getElementById('referenciaQR');

    if (tipo === 'efectivo') {
        seccionEfe.classList.remove('d-none');
        seccionQR.classList.add('d-none');
        if (refInput) refInput.removeAttribute('required');
    } else {
        seccionEfe.classList.add('d-none');
        seccionQR.classList.remove('d-none');
        if (refInput) refInput.setAttribute('required', 'required');
    }
}

function calcularCambio() {
    const totalElem = document.getElementById('totalAPagarHidden');
    const recibidoElem = document.getElementById('montoRecibido');
    const displayCambio = document.getElementById('displayCambio');

    if (!totalElem || !recibidoElem || !displayCambio) return;

    const total = parseFloat(totalElem.value) || 0;
    const recibido = parseFloat(recibidoElem.value) || 0;
    const cambio = recibido - total;

    if (cambio < 0) {
        displayCambio.textContent = 'Faltan Bs. ' + Math.abs(cambio).toFixed(2);
        displayCambio.className = 'fs-2 fw-bold text-danger';
    } else {
        displayCambio.textContent = 'Bs. ' + cambio.toFixed(2);
        displayCambio.className = 'fs-2 fw-bold text-success';
    }
}

function setMontoRecibido(monto) {
    const recibidoElem = document.getElementById('montoRecibido');
    if (recibidoElem) {
        recibidoElem.value = parseFloat(monto).toFixed(2);
        calcularCambio();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const recibidoElem = document.getElementById('montoRecibido');
    if (recibidoElem) {
        recibidoElem.addEventListener('input', calcularCambio);
        calcularCambio();
    }
});
</script>

