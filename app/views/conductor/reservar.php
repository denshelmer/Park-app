<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-bookmark-check-fill me-2"></i>Nueva Reserva de Espacio</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small">Selecciona el horario estimado de llegada y tu vehículo para reservar tu lugar.</p>
                <form action="<?= BASE_URL ?>/reservar" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Parqueo Seleccionado</label>
                            <select class="form-select" name="id_parqueo" required>
                                <option value="">Seleccione un parqueo...</option>
                                <option value="1">Parqueo Ceja Central - La Ceja</option>
                                <option value="2">Parqueo Satélite Real - Ciudad Satélite</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Vehículo</label>
                            <select class="form-select" name="id_tipo_vehiculo" required>
                                <option value="1">Automóvil (Sedán/Vagoneta)</option>
                                <option value="2">Motocicleta</option>
                                <option value="3">Minibús</option>
                                <option value="4">Camioneta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Placa del Vehículo</label>
                            <input type="text" class="form-control text-uppercase" name="placa" placeholder="Ej. 4829-ABC" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hora Prevista de Llegada</label>
                            <input type="datetime-local" class="form-control" name="fecha_hora_prevista_llegada" required>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4 mb-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Tolerancia de Reserva:</strong> Dispones de 15 a 20 minutos de tolerancia a partir de la hora estimada antes de que el espacio sea liberado.
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="<?= BASE_URL ?>/disponibilidad" class="btn btn-outline-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning fw-bold px-4">
                            <i class="bi bi-qr-code me-1"></i>Confirmar y Generar Código QR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
