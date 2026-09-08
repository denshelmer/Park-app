<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-speedometer2 text-primary me-2"></i>Control de Caseta - Parqueo Ceja Central</h3>
        <p class="text-muted small mb-0">Monitoreo en tiempo real de cuadrícula y flujo vehicular</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/caseta/ingreso" class="btn btn-success fw-bold">
            <i class="bi bi-box-arrow-in-right me-1"></i>Nuevo Ingreso (Ticket / QR)
        </a>
        <a href="<?= BASE_URL ?>/caseta/salida" class="btn btn-danger fw-bold">
            <i class="bi bi-box-arrow-left me-1"></i>Salida y Cobro
        </a>
    </div>
</div>

<!-- Barra de Leyenda de Estados -->
<div class="card p-2 mb-4 bg-white shadow-sm border-0">
    <div class="d-flex flex-wrap justify-content-around align-items-center small">
        <div><span class="badge bg-success me-1">&nbsp;</span> <strong>Disponible</strong> (Libre)</div>
        <div><span class="badge bg-danger me-1">&nbsp;</span> <strong>Ocupado</strong> (En parqueo)</div>
        <div><span class="badge bg-warning text-dark me-1">&nbsp;</span> <strong>Reservado</strong> (Con QR activo)</div>
        <div><span class="badge bg-secondary me-1">&nbsp;</span> <strong>Mantenimiento</strong> (Bloqueado)</div>
    </div>
</div>

<!-- Cuadrícula Interactiva de Espacios -->
<h5 class="fw-bold mb-3"><i class="bi bi-grid-3x3-gap me-2"></i>Sector A - Automóviles</h5>
<div class="row g-3 mb-4">
    <!-- A-01 a A-05 Disponibles -->
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <div class="card text-center p-3 border-success border-2 shadow-sm slot-card slot-available">
                <div class="fs-4 fw-bold text-success">A-0<?= $i ?></div>
                <small class="text-muted">Auto</small>
                <div class="badge bg-success mt-2">Libre</div>
            </div>
        </div>
    <?php endfor; ?>

    <!-- A-06 Ocupado -->
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="card text-center p-3 border-danger border-2 shadow-sm slot-card slot-occupied">
            <div class="fs-4 fw-bold text-danger">A-06</div>
            <strong class="text-dark small">2049-ZXY</strong>
            <div class="badge bg-danger mt-2">Ocupado</div>
        </div>
    </div>

    <!-- A-07 Ocupado -->
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="card text-center p-3 border-danger border-2 shadow-sm slot-card slot-occupied">
            <div class="fs-4 fw-bold text-danger">A-07</div>
            <strong class="text-dark small">3192-KLP</strong>
            <div class="badge bg-danger mt-2">Ocupado</div>
        </div>
    </div>

    <!-- A-08 Reservado -->
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="card text-center p-3 border-warning border-2 shadow-sm slot-card slot-reserved">
            <div class="fs-4 fw-bold text-warning text-dark">A-08</div>
            <strong class="text-dark small">4829-ABC</strong>
            <div class="badge bg-warning text-dark mt-2">QR Reservado</div>
        </div>
    </div>

    <!-- A-09 Mantenimiento -->
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="card text-center p-3 border-secondary border-2 shadow-sm slot-card slot-maintenance">
            <div class="fs-4 fw-bold text-secondary">A-09</div>
            <small class="text-muted">Bloqueado</small>
            <div class="badge bg-secondary mt-2">Mantenimiento</div>
        </div>
    </div>

    <!-- A-10 Libre -->
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="card text-center p-3 border-success border-2 shadow-sm slot-card slot-available">
            <div class="fs-4 fw-bold text-success">A-10</div>
            <small class="text-muted">Auto</small>
            <div class="badge bg-success mt-2">Libre</div>
        </div>
    </div>
</div>
