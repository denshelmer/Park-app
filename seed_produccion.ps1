# ==============================================================================
# SEEDER MODO PRODUCCION - PARKAPP
# Restablece la base de datos para entrega en produccion:
# - Deja UNICAMENTE las tablas maestras obligatorias (ROLES, TIPOS_VEHICULO, ADMIN).
# - Deja completamente vacias las tablas operativas.
# ==============================================================================

$dbPath = Join-Path $PSScriptRoot "ParkApp_DB.accdb"

if (-not (Test-Path $dbPath)) {
    Write-Error "No se encontro el archivo de base de datos en: $dbPath"
    exit 1
}

Write-Host "--------------------------------------------------------" -ForegroundColor Cyan
Write-Host " RESTABLECIENDO BASE DE DATOS A MODO PRODUCCION (LIMPIA)" -ForegroundColor Cyan
Write-Host "--------------------------------------------------------" -ForegroundColor Cyan

$connStr = "Provider=Microsoft.ACE.OLEDB.12.0;Data Source=$dbPath;"
$conn = New-Object -ComObject ADODB.Connection
$conn.Open($connStr)

try {
    # 1. Limpiar tablas en orden inverso de dependencias
    $tablas = @("PAGOS", "INGRESOS_SALIDAS", "RESERVAS", "VEHICULOS", "TARIFAS", "ESPACIOS", "PARQUEOS", "USUARIOS", "TIPOS_VEHICULO", "ROLES")
    
    foreach ($tabla in $tablas) {
        Write-Host "Vaciando tabla $tabla..." -ForegroundColor Gray
        $conn.Execute("DELETE FROM [$tabla];") | Out-Null
    }

    # 2. Insertar Catalogos Maestros Obligatorios
    Write-Host "`nInsertando datos base obligatorios..." -ForegroundColor Yellow

    # ROLES
    $conn.Execute("INSERT INTO ROLES (id_rol, nombre_rol, descripcion) VALUES (1, 'Administrador', 'Control total y reportes del sistema');") | Out-Null
    $conn.Execute("INSERT INTO ROLES (id_rol, nombre_rol, descripcion) VALUES (2, 'Operador', 'Control de entradas, salidas, cobros y validacion QR');") | Out-Null
    $conn.Execute("INSERT INTO ROLES (id_rol, nombre_rol, descripcion) VALUES (3, 'Conductor', 'Usuario cliente que reserva espacios y consulta disponibilidad');") | Out-Null

    # TIPOS DE VEHICULO
    $conn.Execute("INSERT INTO TIPOS_VEHICULO (id_tipo_vehiculo, nombre_tipo, descripcion) VALUES (1, 'Automóvil', 'Vehiculos livianos, vagonetas y sedanes');") | Out-Null
    $conn.Execute("INSERT INTO TIPOS_VEHICULO (id_tipo_vehiculo, nombre_tipo, descripcion) VALUES (2, 'Motocicleta', 'Motos de dos y tres ruedas');") | Out-Null
    $conn.Execute("INSERT INTO TIPOS_VEHICULO (id_tipo_vehiculo, nombre_tipo, descripcion) VALUES (3, 'Minibús', 'Transporte de pasajeros / furgones medianos');") | Out-Null
    $conn.Execute("INSERT INTO TIPOS_VEHICULO (id_tipo_vehiculo, nombre_tipo, descripcion) VALUES (4, 'Camioneta', 'Pickups y utilitarios');") | Out-Null

    # USUARIO SUPER ADMINISTRADOR (Para primer acceso al sistema)
    $conn.Execute("INSERT INTO USUARIOS (id_usuario, id_rol, nombre_completo, ci_nit, telefono, email, password_hash, estado, fecha_registro) VALUES (1, 1, 'Super Administrador', '0000000 LP', '70000000', 'admin@parkapp.bo', 'admin123', True, Now());") | Out-Null

    Write-Host "`n[OK] Base de datos en MODO PRODUCCION lista para entrega." -ForegroundColor Green
    Write-Host "Usuario Administrador inicial: admin@parkapp.bo / admin123" -ForegroundColor White
    Write-Host "Todas las tablas de parqueos, espacios, reservas y cobros estan en 0 registros.`n" -ForegroundColor White
}
catch {
    Write-Error "Ocurrio un error al ejecutar el seed de produccion: $_"
}
finally {
    $conn.Close()
    [System.Runtime.InteropServices.Marshal]::ReleaseComObject($conn) | Out-Null
}
