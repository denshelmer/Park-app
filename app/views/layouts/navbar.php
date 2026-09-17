<?php
$isAuth = Auth::check();
$rol = Auth::roleId();
$userName = Auth::user()['nombre_completo'] ?? '';
$userEmail = Auth::user()['email'] ?? '';

// Detección de ruta activa
$currentRoute = trim($_GET['url'] ?? '', '/');
$homeUrl = match($rol) {
    1 => BASE_URL . '/admin/dashboard',
    2 => BASE_URL . '/caseta',
    default => BASE_URL . '/'
};
?>
<nav class="navbar navbar-expand-xl navbar-dark park-navbar sticky-top">
    <div class="container-fluid park-nav-container">
        <!-- 1. Marca Institucional (Izquierda) -->
        <a class="navbar-brand park-brand-item me-0" href="<?= $homeUrl ?>">
            <i class="bi bi-p-square-fill me-2" style="color: #c89234; font-size: 1.35rem;"></i>
            <span class="fw-bold">ParkApp</span>
            <span class="park-brand-badge">El Alto</span>
        </a>

        <!-- Botón Móvil Toggler (Tableta / Celular) -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse park-nav-collapse" id="navMain">
            <!-- 2. Enlaces Principales Centrados (Centro de la Pantalla - Todas las secciones) -->
            <div class="park-center-nav-wrapper">
                <ul class="park-main-nav">
                    
                    <?php if ($rol === 1): ?>
                        <!-- NAVEGACIÓN ADMINISTRADOR -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentRoute === 'admin/dashboard') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/dashboard">
                                Dashboard
                            </a>
                        </li>

                        <!-- Dropdown Gestión -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= str_starts_with($currentRoute, 'admin/') && $currentRoute !== 'admin/dashboard' ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Gestión
                            </a>
                            <ul class="dropdown-menu park-dropdown-menu">
                                <li class="park-dropdown-header">Infraestructura</li>
                                <li>
                                    <a class="dropdown-item <?= ($currentRoute === 'admin/parqueos') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/parqueos">
                                        Sedes y Parqueos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= ($currentRoute === 'admin/espacios') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/espacios">
                                        Matriz de Espacios
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li class="park-dropdown-header">Configuración</li>
                                <li>
                                    <a class="dropdown-item <?= ($currentRoute === 'admin/tarifas') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/tarifas">
                                        Tarifas y Tolerancias
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= ($currentRoute === 'admin/usuarios') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/usuarios">
                                        Personal y Usuarios
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Dropdown Reportes -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= str_starts_with($currentRoute, 'reportes/') ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Reportes
                            </a>
                            <ul class="dropdown-menu park-dropdown-menu">
                                <li class="park-dropdown-header">Emisión de Informes</li>
                                <li>
                                    <a class="dropdown-item <?= ($currentRoute === 'reportes/ingresos') ? 'active' : '' ?>" href="<?= BASE_URL ?>/reportes/ingresos">
                                        Recaudación Financiera
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= ($currentRoute === 'reportes/ocupacion') ? 'active' : '' ?>" href="<?= BASE_URL ?>/reportes/ocupacion">
                                        Ocupación y Rotación
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Dropdown Operaciones -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= in_array($currentRoute, ['caseta', 'reservar', 'mis-reservas', 'disponibilidad']) ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Operaciones
                            </a>
                            <ul class="dropdown-menu park-dropdown-menu">
                                <li class="park-dropdown-header">Módulo de Caseta</li>
                                <li>
                                    <a class="dropdown-item <?= ($currentRoute === 'caseta') ? 'active' : '' ?>" href="<?= BASE_URL ?>/caseta">
                                        Control de Caseta
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li class="park-dropdown-header">Portal Conductor</li>
                                <li>
                                    <a class="dropdown-item <?= ($currentRoute === 'disponibilidad') ? 'active' : '' ?>" href="<?= BASE_URL ?>/disponibilidad">
                                        Ver Disponibilidad
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= ($currentRoute === 'reservar') ? 'active' : '' ?>" href="<?= BASE_URL ?>/reservar">
                                        Reservar Espacio
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= ($currentRoute === 'mis-reservas') ? 'active' : '' ?>" href="<?= BASE_URL ?>/mis-reservas">
                                        Mis Reservas
                                    </a>
                                </li>
                            </ul>
                        </li>

                    <?php elseif ($rol === 2): ?>
                        <!-- NAVEGACIÓN OPERADOR DE CASETA -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentRoute === 'caseta') ? 'active' : '' ?>" href="<?= BASE_URL ?>/caseta">
                                Panel Caseta
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentRoute === 'caseta/ingreso') ? 'active' : '' ?>" href="<?= BASE_URL ?>/caseta/ingreso">
                                Registrar Ingreso
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentRoute === 'caseta/salida') ? 'active' : '' ?>" href="<?= BASE_URL ?>/caseta/salida">
                                Salida y Cobro
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentRoute === 'disponibilidad') ? 'active' : '' ?>" href="<?= BASE_URL ?>/disponibilidad">
                                Disponibilidad
                            </a>
                        </li>

                    <?php else: ?>
                        <!-- NAVEGACIÓN CONDUCTOR / VISITANTE -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentRoute === '' || $currentRoute === 'disponibilidad') ? 'active' : '' ?>" href="<?= BASE_URL ?>/disponibilidad">
                                Disponibilidad
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentRoute === 'reservar') ? 'active' : '' ?>" href="<?= BASE_URL ?>/reservar">
                                Reservar Espacio
                            </a>
                        </li>
                        <?php if ($isAuth): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= ($currentRoute === 'mis-reservas' || str_starts_with($currentRoute, 'reserva')) ? 'active' : '' ?>" href="<?= BASE_URL ?>/mis-reservas">
                                    Mis Reservas
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?= ($currentRoute === 'registro') ? 'active' : '' ?>" href="<?= BASE_URL ?>/registro">
                                    Registrarse
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                </ul>
            </div>

            <!-- 3. Perfil de Usuario / Inicio de Sesión (Extremo Derecho) -->
            <div class="park-right-user-wrapper">
                <ul class="park-user-nav">
                    <?php if ($isAuth): ?>
                        <?php
                        $badgeRolClass = match($rol) {
                            1 => 'bg-danger text-white',
                            2 => 'bg-info text-dark',
                            3 => 'bg-warning text-dark',
                            default => 'bg-secondary text-white'
                        };
                        $nombreRol = match($rol) {
                            1 => 'Administrador',
                            2 => 'Operador',
                            3 => 'Conductor',
                            default => 'Usuario'
                        };
                        ?>
                        <li class="nav-item dropdown">
                            <a class="park-user-chip dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5 me-2" style="color: #ffd88a;"></i>
                                <span class="text-truncate" style="max-width: 140px;"><?= htmlspecialchars($userName) ?></span>
                                <span class="badge <?= $badgeRolClass ?> ms-2 py-1 px-2" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    <?= $nombreRol ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end park-dropdown-menu">
                                <li class="park-dropdown-header">Cuenta de Usuario</li>
                                <li class="px-3 py-1">
                                    <div class="fw-bold text-dark small text-truncate" style="max-width: 220px;"><?= htmlspecialchars($userName) ?></div>
                                    <div class="text-muted small text-truncate" style="max-width: 220px; font-size: 0.75rem;"><?= htmlspecialchars($userEmail) ?></div>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item text-danger fw-semibold" href="<?= BASE_URL ?>/logout">
                                        <i class="bi bi-box-arrow-right text-danger me-2"></i>Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-sm px-3 py-2 fw-bold text-white shadow-sm d-inline-flex align-items-center" style="background-color: #c89234; border-radius: 8px;" href="<?= BASE_URL ?>/login">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</nav>
<main class="container my-4 flex-grow-1">

