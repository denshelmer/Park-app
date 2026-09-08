<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-cash-coin me-2"></i>Liquidación de Salida y Cobro</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/caseta/salida" method="GET" class="mb-4">
                    <label class="form-label fw-semibold">Buscar Ticket o Placa</label>
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" name="buscar" placeholder="TCK-2026-001 o placa..." required autofocus>
                        <button class="btn btn-dark" type="submit"><i class="bi bi-search me-1"></i>Buscar</button>
                    </div>
                </form>

                <!-- Tarjeta de Resumen de Cobro -->
                <div class="p-3 bg-light rounded border mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Ticket:</span>
                        <strong>TCK-2026-001</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Placa / Vehículo:</span>
                        <strong>2049-ZXY (Automóvil)</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Espacio:</span>
                        <span class="badge bg-secondary">A-06</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tiempo de Estancia:</span>
                        <strong>2 horas 15 min</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold text-dark">Total a Cobrar:</span>
                        <span class="fs-3 fw-bold text-success">Bs. 12.50</span>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>/caseta/salida" method="POST">
                    <input type="hidden" name="id_ingreso_salida" value="1">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Método de Pago</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="metodo_pago" id="efectivo" value="Efectivo" checked>
                                <label class="btn btn-outline-success w-100 py-2 fw-semibold" for="efectivo">
                                    <i class="bi bi-cash me-1"></i>Efectivo
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="metodo_pago" id="qr" value="QR Simple">
                                <label class="btn btn-outline-primary w-100 py-2 fw-semibold" for="qr">
                                    <i class="bi bi-qr-code me-1"></i>QR Simple
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Referencia de Transacción (Opcional)</label>
                        <input type="text" class="form-control" name="referencia" placeholder="Ej. BMSC-8492019">
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= BASE_URL ?>/caseta" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success fw-bold px-4">
                            <i class="bi bi-check-circle me-1"></i>Confirmar Pago y Liberar Espacio
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
