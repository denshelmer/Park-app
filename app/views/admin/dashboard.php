<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="bi bi-speedometer text-primary me-2"></i>Dashboard Gerencial</h2>
        <p class="text-muted small mb-0">Resumen operativo y financiero de parqueos</p>
    </div>
    <span class="badge bg-primary p-2">Actualizado: <?= date('d/m/Y H:i') ?></span>
</div>

<!-- Tarjetas Métricas KPI -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 border-start border-primary border-4">
            <div class="text-muted small">Vehículos Estacionados</div>
            <h3 class="fw-bold my-1 text-primary">3</h3>
            <small class="text-success"><i class="bi bi-arrow-up"></i> Flujo activo</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 border-start border-success border-4">
            <div class="text-muted small">Ingresos de Hoy</div>
            <h3 class="fw-bold my-1 text-success">Bs. 320.00</h3>
            <small class="text-muted">Caja en tiempo real</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 border-start border-warning border-4">
            <div class="text-muted small">Reservas Pendientes</div>
            <h3 class="fw-bold my-1 text-warning">1</h3>
            <small class="text-muted">Con código QR</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white p-3 border-start border-info border-4">
            <div class="text-muted small">Ocupación General</div>
            <h3 class="fw-bold my-1 text-info">28%</h3>
            <small class="text-muted">10 de 35 espacios</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-clock-history me-2"></i>Últimos Movimientos de Hoy
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket</th>
                            <th>Placa</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>TCK-HIST-001</code></td>
                            <td><strong>1820-BBC</strong></td>
                            <td>09:00</td>
                            <td>11:30</td>
                            <td>Bs. 15.00</td>
                            <td><span class="badge bg-success">Cobrado</span></td>
                        </tr>
                        <tr>
                            <td><code>TCK-HIST-002</code></td>
                            <td><strong>9921-DFG</strong></td>
                            <td>14:00</td>
                            <td>15:00</td>
                            <td>Bs. 5.00</td>
                            <td><span class="badge bg-success">Cobrado</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-lightning-charge me-2"></i>Acciones Rápidas
            </div>
            <div class="card-body d-grid gap-2">
                <a href="<?= BASE_URL ?>/admin/espacios" class="btn btn-outline-primary text-start">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Gestionar Matriz de Espacios
                </a>
                <a href="<?= BASE_URL ?>/admin/tarifas" class="btn btn-outline-success text-start">
                    <i class="bi bi-currency-dollar me-2"></i>Ajustar Tarifas por Hora
                </a>
                <a href="<?= BASE_URL ?>/reportes/ingresos" class="btn btn-outline-dark text-start">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Ver Reporte Económico
                </a>
            </div>
        </div>
    </div>
</div>
