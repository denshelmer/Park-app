<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 d-print-none">
    <div>
        <h2 class="fw-bold mb-1" style="color: #152b47;">
            <i class="bi bi-file-earmark-bar-graph text-warning me-2"></i>Reporte de Recaudación Económica
        </h2>
        <p class="text-muted small mb-0">Consolidado oficial de ingresos por caja, métodos de pago y detalle de cobros</p>
    </div>
    <button class="btn btn-outline-dark fw-bold shadow-sm" onclick="window.print()">
        <i class="bi bi-printer-fill me-1"></i>Imprimir Reporte Oficial
    </button>
</div>

<!-- Membrete Oficial para Impresión -->
<div class="d-none d-print-block mb-4 text-center border-bottom pb-3">
    <h3 class="fw-bold mb-0" style="color: #152b47;">PARKAPP &mdash; GESTIÓN DE PARQUEOS EN TIEMPO REAL</h3>
    <p class="mb-1 text-secondary small">Gobierno Autónomo Municipal de El Alto &bull; Informe Económico Oficial</p>
    <div class="small text-muted mt-2">
        <span><strong>Período:</strong> <?= htmlspecialchars($fechaInicio) ?> al <?= htmlspecialchars($fechaFin) ?></span> &bull;
        <span><strong>Emisión:</strong> <?= date('d/m/Y H:i:s') ?></span> &bull;
        <span><strong>Emitido por:</strong> <?= htmlspecialchars(Auth::user()['nombre_completo'] ?? 'Administrador') ?></span>
    </div>
</div>

<!-- Formulario de Filtros (Oculto en Impresión) -->
<div class="card shadow-sm border-0 mb-4 p-3 bg-white d-print-none">
    <form method="GET" action="<?= BASE_URL ?>/reportes/ingresos" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-bold text-secondary">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($fechaInicio) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-secondary">Fecha Fin</label>
            <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fechaFin) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-secondary">Sede de Parqueo</label>
            <select name="id_parqueo" class="form-select">
                <option value="">-- Todas las Sedes --</option>
                <?php foreach ($parqueos as $p): ?>
                    <option value="<?= $p['id_parqueo'] ?>" <?= (isset($idParqueo) && (int)$idParqueo === (int)$p['id_parqueo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre_parqueo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold text-secondary">Método de Pago</label>
            <select name="metodo_pago" class="form-select">
                <option value="">Todos</option>
                <option value="Efectivo" <?= ($metodoPago === 'Efectivo') ? 'selected' : '' ?>>Efectivo</option>
                <option value="QR Simple" <?= ($metodoPago === 'QR Simple' || $metodoPago === 'QR') ? 'selected' : '' ?>>QR Simple</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn text-white w-100 fw-bold" style="background-color: #152b47;" title="Generar informe">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>
</div>

<!-- Resumen de Recaudación KPI -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-success h-100">
            <div class="text-muted small fw-semibold text-uppercase">Total Recaudado</div>
            <h3 class="fw-bold my-1 text-success">Bs. <?= number_format($totales['total_recaudado'], 2) ?></h3>
            <small class="text-muted"><?= $totales['cantidad_transacciones'] ?> transacción(es) realizadas</small>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary h-100">
            <div class="text-muted small fw-semibold text-uppercase">Ingresos en Efectivo</div>
            <h3 class="fw-bold my-1 text-primary">Bs. <?= number_format($totales['total_efectivo'], 2) ?></h3>
            <small class="text-muted">Cobros manuales de caja física</small>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-warning h-100">
            <div class="text-muted small fw-semibold text-uppercase">Ingresos por Código QR</div>
            <h3 class="fw-bold my-1" style="color: #c89234;">Bs. <?= number_format($totales['total_qr'], 2) ?></h3>
            <small class="text-muted">Pagos electrónicos verificados</small>
        </div>
    </div>
</div>

<!-- Tabla de Registros Detallados -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead style="background-color: #152b47; color: #ffffff;">
                <tr>
                    <th class="py-3 ps-3">Fecha / Hora</th>
                    <th class="py-3">Ticket</th>
                    <th class="py-3">Placa</th>
                    <th class="py-3">Espacio / Parqueo</th>
                    <th class="py-3 text-center">Método</th>
                    <th class="py-3">Ref. Transacción</th>
                    <th class="py-3">Operador</th>
                    <th class="py-3 pe-3 text-end">Monto (Bs.)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($registros)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            No se encontraron transacciones registradas para el rango y filtros especificados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($registros as $r): ?>
                        <tr>
                            <td class="ps-3 text-nowrap">
                                <?= !empty($r['fecha_hora_pago']) ? date('d/m/Y H:i', strtotime($r['fecha_hora_pago'])) : '-' ?>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($r['numero_ticket'] ?? 'S/N') ?></code>
                            </td>
                            <td>
                                <span class="badge bg-dark px-2 py-1" style="letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($r['placa'] ?? 'S/P') ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($r['codigo_espacio'] ?? '-') ?></div>
                                <small class="text-muted"><?= htmlspecialchars($r['nombre_parqueo'] ?? '-') ?></small>
                            </td>
                            <td class="text-center">
                                <?php if ($r['metodo_pago'] === 'Efectivo'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-cash me-1"></i>Efectivo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-qr-code me-1"></i>QR Simple
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars($r['referencia_transaccion'] ?? '-') ?></small>
                            </td>
                            <td class="small">
                                <?= htmlspecialchars($r['operador_cobro'] ?? 'Sistema') ?>
                            </td>
                            <td class="pe-3 text-end fw-bold text-success fs-6">
                                Bs. <?= number_format((float)$r['monto'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($registros)): ?>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="7" class="text-end py-3 fs-6">TOTAL GENERAL RECAUDADO:</td>
                        <td class="text-end py-3 text-success fs-5 pe-3">
                            Bs. <?= number_format($totales['total_recaudado'], 2) ?>
                        </td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<style>
@media print {
    .navbar, footer, .d-print-none, .btn {
        display: none !important;
    }
    body {
        background-color: #ffffff !important;
        font-size: 11pt;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
}
</style>

