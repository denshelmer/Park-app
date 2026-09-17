<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 d-print-none">
    <div>
        <h2 class="fw-bold mb-1" style="color: #152b47;">
            <i class="bi bi-pie-chart-fill text-warning me-2"></i>Reporte de Ocupación y Rotación Vehicular
        </h2>
        <p class="text-muted small mb-0">Análisis cuantitativo de afluencia, picos horarios de demanda y tiempo promedio de permanencia</p>
    </div>
    <button class="btn btn-outline-dark fw-bold shadow-sm" onclick="window.print()">
        <i class="bi bi-printer-fill me-1"></i>Imprimir Reporte
    </button>
</div>

<!-- Membrete Oficial para Impresión -->
<div class="d-none d-print-block mb-4 text-center border-bottom pb-3">
    <h3 class="fw-bold mb-0" style="color: #152b47;">PARKAPP &mdash; GESTIÓN DE PARQUEOS EN TIEMPO REAL</h3>
    <p class="mb-1 text-secondary small">Gobierno Autónomo Municipal de El Alto &bull; Informe Estadístico de Ocupación</p>
    <div class="small text-muted mt-2">
        <span><strong>Período:</strong> <?= htmlspecialchars($fechaInicio) ?> al <?= htmlspecialchars($fechaFin) ?></span> &bull;
        <span><strong>Emisión:</strong> <?= date('d/m/Y H:i:s') ?></span> &bull;
        <span><strong>Emitido por:</strong> <?= htmlspecialchars(Auth::user()['nombre_completo'] ?? 'Administrador') ?></span>
    </div>
</div>

<!-- Filtros de Consulta -->
<div class="card shadow-sm border-0 mb-4 p-3 bg-white d-print-none">
    <form method="GET" action="<?= BASE_URL ?>/reportes/ocupacion" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($fechaInicio) ?>" required>
        </div>
        <div class="col-md-4">
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
        <div class="col-md-1">
            <button type="submit" class="btn text-white w-100 fw-bold" style="background-color: #152b47;" title="Generar estadística">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>
</div>

<!-- Tarjetas KPI Estadísticas -->
<?php
$promedioMin = (int)$estadisticas['promedio_minutos'];
$horasProm = floor($promedioMin / 60);
$minProm = $promedioMin % 60;
$textoPromedio = $horasProm > 0 ? "{$horasProm}h {$minProm}m" : "{$minProm} min";
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm bg-white border-start border-4 border-primary h-100">
            <div class="text-muted small fw-semibold text-uppercase">Total Ingresos Registrados</div>
            <h2 class="fw-bold my-2" style="color: #152b47;"><?= (int)$estadisticas['total_ingresos'] ?></h2>
            <div class="small text-muted">
                <span class="text-primary fw-semibold"><?= (int)$estadisticas['en_parqueo'] ?> activos</span> &bull; 
                <span class="text-success fw-semibold"><?= (int)$estadisticas['total_finalizados'] ?> concluidos</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm bg-white border-start border-4 border-info h-100">
            <div class="text-muted small fw-semibold text-uppercase">Tiempo Promedio de Estancia</div>
            <h2 class="fw-bold my-2 text-info"><?= $promedioMin > 0 ? $textoPromedio : '0 min' ?></h2>
            <div class="small text-muted">
                Basado en <?= (int)$estadisticas['total_finalizados'] ?> estancias liquidadas
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm bg-white border-start border-4 border-warning h-100">
            <div class="text-muted small fw-semibold text-uppercase">Vehículos en Rotación Actual</div>
            <h2 class="fw-bold my-2" style="color: #c89234;"><?= (int)$estadisticas['en_parqueo'] ?></h2>
            <div class="small text-muted">Ocupación dinámica en tiempo real</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Distribución por Tipo de Vehículo -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 fw-bold" style="color: #152b47;">
                <i class="bi bi-pie-chart text-primary me-2"></i>Afluencia por Tipo de Vehículo
            </div>
            <div class="card-body p-4">
                <?php if (empty($estadisticas['por_tipo_vehiculo'])): ?>
                    <p class="text-muted text-center py-4 mb-0">Sin datos de vehículos en el período.</p>
                <?php else: ?>
                    <?php 
                    $totalV = max(1, $estadisticas['total_ingresos']);
                    foreach ($estadisticas['por_tipo_vehiculo'] as $tipo => $cant): 
                        $pct = round(($cant / $totalV) * 100);
                    ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold small"><?= htmlspecialchars($tipo) ?></span>
                                <span class="badge bg-light text-dark border"><?= $cant ?> (<?= $pct ?>%)</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar" role="progressbar" style="width: <?= $pct ?>%; background-color: #152b47;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Picos de Demanda por Horario -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 fw-bold" style="color: #152b47;">
                <i class="bi bi-graph-up-arrow text-warning me-2"></i>Horarios Pico de Ingreso (06:00 - 22:00)
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless align-middle mb-0 text-center small">
                        <thead>
                            <tr class="text-muted border-bottom">
                                <?php for ($h = 6; $h <= 21; $h++): ?>
                                    <th style="font-size: 0.72rem;"><?= str_pad((string)$h, 2, '0', STR_PAD_LEFT) ?>h</th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php 
                                $maxIngresos = max(1, max($estadisticas['por_hora'] ?? [0]));
                                for ($h = 6; $h <= 21; $h++): 
                                    $cnt = $estadisticas['por_hora'][$h] ?? 0;
                                    $esPico = ($cnt > 0 && $cnt === $maxIngresos);
                                ?>
                                    <td class="py-2">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge <?= $esPico ? 'bg-danger text-white' : ($cnt > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted') ?> rounded-pill px-2 py-1">
                                                <?= $cnt ?>
                                            </span>
                                        </div>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-muted small mt-3 text-center">
                    <span class="badge bg-danger me-1">Pico</span> Horas de mayor afluencia vehicular para previsión de personal en caseta.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla Detallada de Estancias -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 fw-bold" style="color: #152b47;">
        <i class="bi bi-list-check text-success me-2"></i>Detalle de Estancias Vehiculares (<?= count($estadisticas['movimientos']) ?>)
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead style="background-color: #152b47; color: #ffffff;">
                <tr>
                    <th class="py-3 ps-3">Ticket</th>
                    <th class="py-3">Placa</th>
                    <th class="py-3">Tipo Vehículo</th>
                    <th class="py-3">Espacio / Sede</th>
                    <th class="py-3">Hora Entrada</th>
                    <th class="py-3">Hora Salida</th>
                    <th class="py-3 text-center">Permanencia</th>
                    <th class="py-3 pe-3 text-end">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estadisticas['movimientos'])): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No se registran movimientos en el período consultado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($estadisticas['movimientos'] as $m): ?>
                        <tr>
                            <td class="ps-3">
                                <code><?= htmlspecialchars($m['numero_ticket']) ?></code>
                            </td>
                            <td>
                                <span class="badge bg-dark px-2 py-1" style="letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($m['placa']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($m['nombre_tipo'] ?? 'Automóvil') ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($m['codigo_espacio']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($m['nombre_parqueo']) ?></small>
                            </td>
                            <td><?= !empty($m['fecha_hora_entrada']) ? date('d/m/Y H:i', strtotime($m['fecha_hora_entrada'])) : '-' ?></td>
                            <td><?= !empty($m['fecha_hora_salida']) ? date('d/m/Y H:i', strtotime($m['fecha_hora_salida'])) : '<span class="text-muted fst-italic">En curso</span>' ?></td>
                            <td class="text-center">
                                <?php if (!empty($m['minutos_totales'])): ?>
                                    <span class="badge bg-light text-dark border">
                                        <?= (int)$m['minutos_totales'] ?> min
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-3 text-end">
                                <?php if ($m['estado_estancia'] === 'En Parqueo'): ?>
                                    <span class="badge bg-primary">En Parqueo</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Finalizado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
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

