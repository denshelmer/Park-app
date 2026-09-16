<?php
$isAuth = Auth::check();
$rol = Auth::roleId();
$userName = Auth::user()['nombre_completo'] ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: #152b47; border-bottom: 2px solid #c89234;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" style="color: #ffffff; letter-spacing: 0.5px;" href="<?= BASE_URL ?>/">
            <i class="bi bi-p-square-fill me-2" style="color: #c89234;"></i>ParkApp <span class="badge bg-light text-dark fw-normal ms-1" style="font-size: 0.72rem; vertical-align: middle;">El Alto</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Vistas de Conductor -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/disponibilidad">
                        <i class="bi bi-geo-alt me-1"></i>Disponibilidad
                    </a>
                </li>
                <?php if ($isAuth && $rol === 3): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/reservar">
                            <i class="bi bi-plus-circle me-1"></i>Reservar Espacio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/mis-reservas">
                            <i class="bi bi-calendar-check me-1"></i>Mis Reservas
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Vistas de Operador de Caseta -->
                <?php if ($isAuth && ($rol === 1 || $rol === 2)): ?>
                    <li class="nav-item">
                        <a class="nav-link text-info fw-semibold" href="<?= BASE_URL ?>/caseta">
                            <i class="bi bi-speedometer2 me-1"></i>Control Caseta
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Vistas de Administrador -->
                <?php if ($isAuth && $rol === 1): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-warning" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear-fill me-1"></i>Administración
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/dashboard"><i class="bi bi-graph-up me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/parqueos"><i class="bi bi-building me-2"></i>Parqueos</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/espacios"><i class="bi bi-grid-3x3-gap me-2"></i>Espacios</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/tarifas"><i class="bi bi-cash-coin me-2"></i>Tarifas</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/usuarios"><i class="bi bi-people me-2"></i>Usuarios & Operadores</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/reportes/ingresos"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reporte de Ingresos</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/reportes/ocupacion"><i class="bi bi-pie-chart me-2"></i>Reporte de Ocupación</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($isAuth): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($userName) ?>
                            <span class="badge bg-secondary ms-1"><?= htmlspecialchars(Auth::roleName()) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/registro"><i class="bi bi-person-plus me-1"></i>Registrarse</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-sm ms-2 px-3 fw-semibold text-white" style="background-color: #c89234;" href="<?= BASE_URL ?>/login"><i class="bi bi-box-arrow-in-right me-1"></i>Ingresar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container my-4 flex-grow-1">
