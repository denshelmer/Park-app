<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5 text-center">
        <div class="card shadow-sm border-0 p-4">
            <div class="mb-3">
                <span class="badge bg-success px-3 py-2 fs-6">Reserva Confirmada</span>
            </div>
            <h4 class="fw-bold mb-1">Comprobante de Ingreso</h4>
            <p class="text-muted small">Presenta este código QR en la caseta del parqueo</p>

            <div class="my-3 p-3 bg-light rounded d-inline-block shadow-sm">
                <!-- Código QR visual generado mediante API gratuita segura -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=QR-ALTO-2026-001" alt="Código QR Reserva" class="img-fluid rounded">
            </div>

            <h5 class="fw-bold text-primary mt-2">QR-ALTO-2026-001</h5>
            
            <div class="list-group list-group-flush text-start my-3 small">
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Establecimiento:</span>
                    <strong>Parqueo Ceja Central</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Espacio Reservado:</span>
                    <strong class="text-success">A-08 (Sector A)</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Vehículo (Placa):</span>
                    <strong>4829-ABC</strong>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Tolerancia:</span>
                    <span>15 minutos</span>
                </div>
            </div>

            <a href="<?= BASE_URL ?>/mis-reservas" class="btn btn-outline-dark w-100">
                <i class="bi bi-arrow-left me-1"></i>Volver a Mis Reservas
            </a>
        </div>
    </div>
</div>
