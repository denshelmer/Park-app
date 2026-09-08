<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph text-success me-2"></i>Reporte de Recaudación Económica</h3>
        <p class="text-muted small mb-0">Informes diarios, semanales y mensuales de ingresos por parqueo</p>
    </div>
    <button class="btn btn-outline-dark fw-semibold" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Imprimir Reporte
    </button>
</div>

<div class="card shadow-sm border-0 mb-4 p-3 bg-white">
    <form class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Fecha Inicio</label>
            <input type="date" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Fecha Fin</label>
            <input type="date" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Método de Pago</label>
            <select class="form-select">
                <option value="">Todos los métodos</option>
                <option value="Efectivo">Efectivo</option>
                <option value="QR Simple">QR Simple</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i>Generar Reporte</button>
        </div>
    </form>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Ticket</th>
                    <th>Método</th>
                    <th>Referencia</th>
                    <th>Operador</th>
                    <th class="text-end">Monto Cobrado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Ayer, 11:30</td>
                    <td>TCK-HIST-001</td>
                    <td><span class="badge bg-success">Efectivo</span></td>
                    <td>PAGO-EFE-001</td>
                    <td>Juan Carlos Choque</td>
                    <td class="text-end fw-bold">Bs. 15.00</td>
                </tr>
                <tr>
                    <td>Ayer, 15:00</td>
                    <td>TCK-HIST-002</td>
                    <td><span class="badge bg-primary">QR Simple</span></td>
                    <td>BMSC-8492019</td>
                    <td>Maria Rene Quispe</td>
                    <td class="text-end fw-bold">Bs. 5.00</td>
                </tr>
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="5" class="text-end">Total Recaudado:</td>
                    <td class="text-end text-success fs-5">Bs. 20.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
