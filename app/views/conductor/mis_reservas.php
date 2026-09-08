<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-clock-history text-primary me-2"></i>Mis Reservas</h3>
        <p class="text-muted mb-0">Historial de reservas activas y finalizadas.</p>
    </div>
    <a href="<?= BASE_URL ?>/disponibilidad" class="btn btn-warning fw-semibold">
        <i class="bi bi-plus-circle me-1"></i>Nueva Reserva
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th># Token</th>
                    <th>Parqueo</th>
                    <th>Espacio</th>
                    <th>Placa</th>
                    <th>Hora Prevista</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>QR-ALTO-2026-001</code></td>
                    <td>Parqueo Ceja Central</td>
                    <td><span class="badge bg-secondary">A-08</span></td>
                    <td><strong>4829-ABC</strong></td>
                    <td>Hoy, 16:30</td>
                    <td><span class="badge bg-warning text-dark">Confirmada</span></td>
                    <td class="text-center">
                        <a href="<?= BASE_URL ?>/reserva/qr?token=QR-ALTO-2026-001" class="btn btn-sm btn-outline-dark">
                            <i class="bi bi-qr-code me-1"></i>Ver QR
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
