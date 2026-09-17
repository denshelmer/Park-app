<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #152b47;">
            <i class="bi bi-people text-warning me-2"></i>Gestión de Personal y Usuarios
        </h2>
        <p class="text-muted small mb-0">Control de operadores de caseta, administradores del sistema y cuentas de conductores</p>
    </div>
    <button class="btn fw-semibold text-white shadow-sm" style="background-color: #c89234;" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="abrirModalNuevoUsuario()">
        <i class="bi bi-person-plus-fill me-1"></i>Nuevo Personal / Usuario
    </button>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #152b47; color: #ffffff;">
                <tr>
                    <th class="py-3 ps-3"># ID</th>
                    <th class="py-3">Nombre Completo</th>
                    <th class="py-3">C.I. / NIT</th>
                    <th class="py-3">Teléfono</th>
                    <th class="py-3">Correo Electrónico</th>
                    <th class="py-3 text-center">Rol Asignado</th>
                    <th class="py-3 text-center">Estado</th>
                    <th class="py-3 pe-3 text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $currentUserId = (int)(Auth::user()['id_usuario'] ?? 0);
                    foreach ($usuarios as $u): 
                    ?>
                        <tr>
                            <td class="ps-3 fw-bold text-muted"><?= $u['id_usuario'] ?></td>
                            <td>
                                <div class="fw-bold" style="color: #152b47;">
                                    <?= htmlspecialchars($u['nombre_completo']) ?>
                                    <?php if ($u['id_usuario'] == $currentUserId): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 0.7rem;">Tú</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-person-vcard me-1"></i><?= htmlspecialchars($u['ci_nit'] ?? 'S/N') ?>
                                </span>
                            </td>
                            <td class="small">
                                <?= !empty($u['telefono']) ? '<i class="bi bi-telephone me-1 text-muted"></i>' . htmlspecialchars($u['telefono']) : '<span class="text-muted fst-italic">No registrado</span>' ?>
                            </td>
                            <td class="small">
                                <i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($u['email']) ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $rolBadge = match((int)$u['id_rol']) {
                                    1 => 'bg-danger text-white',
                                    2 => 'bg-info text-dark',
                                    3 => 'bg-secondary text-white',
                                    default => 'bg-light text-dark'
                                };
                                ?>
                                <span class="badge <?= $rolBadge ?> px-2 py-1">
                                    <?= htmlspecialchars($u['nombre_rol'] ?? 'Usuario') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($u['estado'])): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle me-1"></i>Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-x-circle me-1"></i>Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-3 text-end">
                                <!-- Editar Usuario -->
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                        title="Editar datos de usuario"
                                        onclick="editarUsuario(<?= htmlspecialchars(json_encode($u)) ?>)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Cambiar Contraseña -->
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1" 
                                        title="Cambiar contraseña"
                                        onclick="abrirModalPassword(<?= $u['id_usuario'] ?>, '<?= htmlspecialchars(addslashes($u['nombre_completo'])) ?>')">
                                    <i class="bi bi-key"></i>
                                </button>

                                <!-- Activar / Desactivar -->
                                <?php if ($u['id_usuario'] != $currentUserId): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/usuarios/estado" class="d-inline" onsubmit="return confirm('¿Está seguro de cambiar el estado de acceso de este usuario?');">
                                        <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                        <input type="hidden" name="estado" value="<?= !empty($u['estado']) ? '0' : '1' ?>">
                                        <?php if (!empty($u['estado'])): ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Desactivar cuenta">
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Activar cuenta">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear / Editar Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #152b47;">
                <h5 class="modal-title fw-bold" id="modalUsuarioLabel">
                    <i class="bi bi-person-plus me-2 text-warning"></i><span>Nuevo Usuario</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/admin/usuarios/guardar">
                <input type="hidden" name="id_usuario" id="form_id_usuario" value="0">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nombre Completo *</label>
                        <input type="text" name="nombre_completo" id="form_nombre_completo" class="form-control" placeholder="Ej. Juan Pérez Mamani" required>
                    </div>

                    <!-- C.I. y Departamento Boliviano -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Cédula de Identidad (C.I.) *</label>
                        <div class="row g-2">
                            <div class="col-7">
                                <input type="text" name="ci_numero" id="form_ci_numero" class="form-control" placeholder="Ej. 8492041" required>
                            </div>
                            <div class="col-5">
                                <select name="ci_depto" id="form_ci_depto" class="form-select">
                                    <option value="LP" selected>LP (La Paz)</option>
                                    <option value="CB">CB (Cochabamba)</option>
                                    <option value="SC">SC (Santa Cruz)</option>
                                    <option value="OR">OR (Oruro)</option>
                                    <option value="PT">PT (Potosí)</option>
                                    <option value="TJ">TJ (Tarija)</option>
                                    <option value="CH">CH (Chuquisaca)</option>
                                    <option value="BN">BN (Beni)</option>
                                    <option value="PA">PA (Pando)</option>
                                    <option value="">Sin extensión</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Teléfono / Celular</label>
                            <input type="text" name="telefono" id="form_telefono" class="form-control" placeholder="Ej. 71234567">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Rol Asignado *</label>
                            <select name="id_rol" id="form_id_rol" class="form-select" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id_rol'] ?>">
                                        <?= htmlspecialchars($r['nombre_rol']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Correo Electrónico (Acceso) *</label>
                        <input type="email" name="email" id="form_email" class="form-control" placeholder="correo@ejemplo.com" required>
                    </div>

                    <div class="mb-3" id="div_password_field">
                        <label class="form-label small fw-bold text-secondary">
                            Contraseña de Acceso <span id="span_pass_required">*</span>
                        </label>
                        <input type="password" name="password" id="form_password" class="form-control" placeholder="Mínimo 6 caracteres" minlength="6">
                        <small class="text-muted" id="help_password">Para nuevos usuarios la contraseña es obligatoria.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold" style="background-color: #152b47;">
                        <i class="bi bi-save me-1"></i>Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cambio Rápido de Contraseña -->
<div class="modal fade" id="modalPassword" tabindex="-1" aria-labelledby="modalPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #152b47;">
                <h6 class="modal-title fw-bold" id="modalPasswordLabel">
                    <i class="bi bi-key-fill text-warning me-2"></i>Cambiar Clave
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/admin/usuarios/password">
                <input type="hidden" name="id_usuario" id="pass_id_usuario" value="0">
                <div class="modal-body p-3">
                    <p class="small text-muted mb-2">Usuario: <strong id="pass_user_name" class="text-dark"></strong></p>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary">Nueva Contraseña *</label>
                        <input type="password" name="nuevo_password" class="form-control" placeholder="Mínimo 6 caracteres" minlength="6" required>
                    </div>
                </div>
                <div class="modal-footer bg-light p-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm text-white fw-bold" style="background-color: #152b47;">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalNuevoUsuario() {
    document.getElementById('modalUsuarioLabel').innerHTML = '<i class="bi bi-person-plus me-2 text-warning"></i>Nuevo Personal / Usuario';
    document.getElementById('form_id_usuario').value = '0';
    document.getElementById('form_nombre_completo').value = '';
    document.getElementById('form_ci_numero').value = '';
    document.getElementById('form_ci_depto').value = 'LP';
    document.getElementById('form_telefono').value = '';
    document.getElementById('form_email').value = '';
    document.getElementById('form_id_rol').value = '2'; // Por defecto Operador
    document.getElementById('form_password').value = '';
    document.getElementById('form_password').required = true;
    document.getElementById('span_pass_required').style.display = 'inline';
    document.getElementById('help_password').textContent = 'Obligatoria para usuarios nuevos.';
}

function editarUsuario(u) {
    document.getElementById('modalUsuarioLabel').innerHTML = '<i class="bi bi-pencil-square me-2 text-warning"></i>Editar Usuario';
    document.getElementById('form_id_usuario').value = u.id_usuario;
    document.getElementById('form_nombre_completo').value = u.nombre_completo || '';
    
    // Parsear CI y Departamento
    var ciStr = (u.ci_nit || '').trim();
    var partes = ciStr.split(' ');
    if (partes.length >= 2) {
        document.getElementById('form_ci_numero').value = partes[0];
        document.getElementById('form_ci_depto').value = partes[1].toUpperCase();
    } else {
        document.getElementById('form_ci_numero').value = ciStr;
        document.getElementById('form_ci_depto').value = 'LP';
    }

    document.getElementById('form_telefono').value = u.telefono || '';
    document.getElementById('form_email').value = u.email || '';
    document.getElementById('form_id_rol').value = u.id_rol || '3';
    
    // Contraseña opcional al editar
    document.getElementById('form_password').value = '';
    document.getElementById('form_password').required = false;
    document.getElementById('span_pass_required').style.display = 'none';
    document.getElementById('help_password').textContent = 'Dejar vacío si no desea modificar la contraseña.';
    
    var modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
    modal.show();
}

function abrirModalPassword(id, nombre) {
    document.getElementById('pass_id_usuario').value = id;
    document.getElementById('pass_user_name').textContent = nombre;
    var modal = new bootstrap.Modal(document.getElementById('modalPassword'));
    modal.show();
}
</script>

