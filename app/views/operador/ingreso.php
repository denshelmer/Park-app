<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>Registrar Ingreso a Parqueo</h5>
            </div>
            <div class="card-body p-4">
                <ul class="nav nav-pills mb-4 nav-justified" id="ingresoTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" id="manual-tab" data-bs-toggle="pill" data-bs-target="#manual">
                            <i class="bi bi-ticket-perforated me-1"></i>Ingreso Directo (Ticket Normal)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="qr-tab" data-bs-toggle="pill" data-bs-target="#qr">
                            <i class="bi bi-qr-code-scan me-1"></i>Validar Reserva con QR
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="ingresoTabContent">
                    <!-- Ingreso Directo -->
                    <div class="tab-pane fade show active" id="manual">
                        <form action="<?= BASE_URL ?>/caseta/ingreso" method="POST">
                            <input type="hidden" name="tipo_ingreso" value="directo">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Placa del Vehículo</label>
                                    <input type="text" class="form-control form-control-lg text-uppercase fw-bold" name="placa" placeholder="Ej. 2049-ZXY" required autofocus>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tipo de Vehículo</label>
                                    <select class="form-select form-select-lg" name="id_tipo_vehiculo" required>
                                        <option value="1">Automóvil</option>
                                        <option value="2">Motocicleta</option>
                                        <option value="3">Minibús</option>
                                        <option value="4">Camioneta</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Asignar Espacio Libre</label>
                                    <select class="form-select" name="id_espacio" required>
                                        <option value="1">A-01 (Sector A)</option>
                                        <option value="2">A-02 (Sector A)</option>
                                        <option value="3">A-03 (Sector A)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 text-end">
                                <a href="<?= BASE_URL ?>/caseta" class="btn btn-outline-secondary me-2">Cancelar</a>
                                <button type="submit" class="btn btn-success fw-bold px-4">
                                    <i class="bi bi-printer me-1"></i>Registrar e Imprimir Ticket
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Escaneo QR -->
                    <div class="tab-pane fade" id="qr">
                        <form action="<?= BASE_URL ?>/caseta/ingreso" method="POST">
                            <input type="hidden" name="tipo_ingreso" value="qr">
                            <div class="mb-3 text-center">
                                <div class="p-4 border rounded bg-light mb-3">
                                    <i class="bi bi-qr-code-scan text-primary" style="font-size: 3rem;"></i>
                                    <p class="text-muted small mt-2">Usa el lector de código de barras o escribe el token</p>
                                </div>
                                <label class="form-label fw-semibold">Token / Código QR</label>
                                <input type="text" class="form-control form-control-lg text-center fw-bold" name="codigo_qr_token" placeholder="QR-ALTO-2026-..." required>
                            </div>
                            <div class="text-end">
                                <a href="<?= BASE_URL ?>/caseta" class="btn btn-outline-secondary me-2">Cancelar</a>
                                <button type="submit" class="btn btn-primary fw-bold px-4">
                                    <i class="bi bi-check2-circle me-1"></i>Validar Reserva y Dar Paso
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
