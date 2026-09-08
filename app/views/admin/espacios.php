<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap text-primary me-2"></i>Matriz de Espacios Físicos</h3>
        <p class="text-muted small mb-0">Configuración de cajones, numeración, tipo de vehículo y mantenimiento</p>
    </div>
    <button class="btn btn-primary fw-bold">
        <i class="bi bi-plus-circle me-1"></i>Añadir Espacio
    </button>
</div>

<div class="card shadow-sm border-0 mb-4 p-3 bg-white">
    <div class="row g-2 align-items-center">
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Filtrar por Parqueo</label>
            <select class="form-select">
                <option value="1">Parqueo Ceja Central</option>
                <option value="2">Parqueo Satélite Real</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Filtrar por Tipo</label>
            <select class="form-select">
                <option value="">Todos los tipos</option>
                <option value="1">Automóvil</option>
                <option value="2">Motocicleta</option>
                <option value="3">Minibús</option>
            </select>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Sector / Piso</th>
                    <th>Tipo Admitido</th>
                    <th>Estado Actual</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>A-01</strong></td>
                    <td>Sector A</td>
                    <td>Automóvil</td>
                    <td><span class="badge bg-success">Disponible</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-warning">Mantenimiento</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>M-01</strong></td>
                    <td>Sector Motos</td>
                    <td>Motocicleta</td>
                    <td><span class="badge bg-success">Disponible</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-warning">Mantenimiento</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
