# ==============================================================================
# SEEDER MODO PRUEBAS / TESTING - PARKAPP
# Este script restablece e inyecta un ecosistema completo de datos ficticios
# para pruebas de desarrollo, edicion, eliminacion y validacion de relaciones.
# ==============================================================================

$dbPath = Join-Path $PSScriptRoot "ParkApp_DB.accdb"

if (-not (Test-Path $dbPath)) {
    Write-Error "No se encontro el archivo de base de datos en: $dbPath"
    exit 1
}

Write-Host "--------------------------------------------------------" -ForegroundColor Yellow
Write-Host " INYECTANDO DATOS COMPLETOS DE PRUEBAS / TESTING        " -ForegroundColor Yellow
Write-Host "--------------------------------------------------------" -ForegroundColor Yellow

$connStr = "Provider=Microsoft.ACE.OLEDB.12.0;Data Source=$dbPath;"
$conn = New-Object -ComObject ADODB.Connection
$conn.Open($connStr)

try {
    # 1. Limpieza total previa en orden de relaciones
    $tablas = @("PAGOS", "INGRESOS_SALIDAS", "RESERVAS", "VEHICULOS", "TARIFAS", "ESPACIOS", "PARQUEOS", "USUARIOS", "TIPOS_VEHICULO", "ROLES")
    foreach ($tabla in $tablas) {
        $conn.Execute("DELETE FROM [$tabla];") | Out-Null
    }
    Write-Host "[OK] Base de datos vaciada para recarga limpia." -ForegroundColor Gray

    # 2. ROLES
    $conn.Execute("INSERT INTO ROLES (id_rol, nombre_rol, descripcion) VALUES (1, 'Administrador', 'Control total y reportes del sistema');") | Out-Null
    $conn.Execute("INSERT INTO ROLES (id_rol, nombre_rol, descripcion) VALUES (2, 'Operador', 'Control de entradas, salidas, cobros y validacion QR');") | Out-Null
    $conn.Execute("INSERT INTO ROLES (id_rol, nombre_rol, descripcion) VALUES (3, 'Conductor', 'Usuario cliente que reserva espacios y consulta disponibilidad');") | Out-Null

    # 3. TIPOS_VEHICULO
    $conn.Execute("INSERT INTO TIPOS_VEHICULO (id_tipo_vehiculo, nombre_tipo, descripcion) VALUES (1, 'Automóvil', 'Vehiculos livianos, vagonetas y sedanes');") | Out-Null
    $conn.Execute("INSERT INTO TIPOS_VEHICULO (id_tipo_vehiculo, nombre_tipo, descripcion) VALUES (2, 'Motocicleta', 'Motos de dos y tres ruedas');") | Out-Null
    $conn.Execute("INSERT INTO TIPOS_VEHICULO (id_tipo_vehiculo, nombre_tipo, descripcion) VALUES (3, 'Minibús', 'Transporte de pasajeros / furgones medianos');") | Out-Null
    $conn.Execute("INSERT INTO TIPOS_VEHICULO (id_tipo_vehiculo, nombre_tipo, descripcion) VALUES (4, 'Camioneta', 'Pickups y utilitarios');") | Out-Null

    # 4. USUARIOS DE PRUEBA
    # Admin
    $conn.Execute("INSERT INTO USUARIOS (id_usuario, id_rol, nombre_completo, ci_nit, telefono, email, password_hash, estado, fecha_registro) VALUES (1, 1, 'Super Administrador', '8849201 LP', '70123456', 'admin@parkapp.bo', 'admin123', True, Now());") | Out-Null
    # Operadores (Manana y Tarde)
    $conn.Execute("INSERT INTO USUARIOS (id_usuario, id_rol, nombre_completo, ci_nit, telefono, email, password_hash, estado, fecha_registro) VALUES (2, 2, 'Juan Carlos Choque', '6938202 LP', '71987654', 'operador1@parkapp.bo', 'operador123', True, Now());") | Out-Null
    $conn.Execute("INSERT INTO USUARIOS (id_usuario, id_rol, nombre_completo, ci_nit, telefono, email, password_hash, estado, fecha_registro) VALUES (3, 2, 'Maria Rene Quispe', '7102931 LP', '72345678', 'operador2@parkapp.bo', 'operador123', True, Now());") | Out-Null
    # Conductores
    $conn.Execute("INSERT INTO USUARIOS (id_usuario, id_rol, nombre_completo, ci_nit, telefono, email, password_hash, estado, fecha_registro) VALUES (4, 3, 'Carlos Mamani Huanca', '5938201 LP', '76543210', 'carlos.mamani@gmail.com', 'conductor123', True, Now());") | Out-Null
    $conn.Execute("INSERT INTO USUARIOS (id_usuario, id_rol, nombre_completo, ci_nit, telefono, email, password_hash, estado, fecha_registro) VALUES (5, 3, 'Ana Laura Flores', '6102948 LP', '77889900', 'ana.flores@gmail.com', 'conductor123', True, Now());") | Out-Null
    $conn.Execute("INSERT INTO USUARIOS (id_usuario, id_rol, nombre_completo, ci_nit, telefono, email, password_hash, estado, fecha_registro) VALUES (6, 3, 'David Condori Yujra', '7492018 LP', '78901234', 'david.condori@gmail.com', 'conductor123', True, Now());") | Out-Null

    # 5. PARQUEOS EN EL ALTO
    $conn.Execute("INSERT INTO PARQUEOS (id_parqueo, nombre_parqueo, direccion, zona, capacidad_total, hora_apertura, hora_cierre, id_administrador, estado) VALUES (1, 'Parqueo Ceja Central', 'Av. 6 de Marzo esq. Calle 2', 'La Ceja - El Alto', 20, #06:00:00#, #23:00:00#, 1, True);") | Out-Null
    $conn.Execute("INSERT INTO PARQUEOS (id_parqueo, nombre_parqueo, direccion, zona, capacidad_total, hora_apertura, hora_cierre, id_administrador, estado) VALUES (2, 'Parqueo Satelite Real', 'Av. del Policia #450', 'Ciudad Satelite - El Alto', 15, #07:00:00#, #22:00:00#, 1, True);") | Out-Null

    # 6. TARIFAS
    # Parqueo 1 (Ceja)
    $conn.Execute("INSERT INTO TARIFAS (id_tarifa, id_parqueo, id_tipo_vehiculo, precio_hora, precio_fraccion, precio_dia, tolerancia_minutos, vigente) VALUES (1, 1, 1, 5.00, 2.50, 40.00, 10, True);") | Out-Null
    $conn.Execute("INSERT INTO TARIFAS (id_tarifa, id_parqueo, id_tipo_vehiculo, precio_hora, precio_fraccion, precio_dia, tolerancia_minutos, vigente) VALUES (2, 1, 2, 3.00, 1.50, 20.00, 10, True);") | Out-Null
    $conn.Execute("INSERT INTO TARIFAS (id_tarifa, id_parqueo, id_tipo_vehiculo, precio_hora, precio_fraccion, precio_dia, tolerancia_minutos, vigente) VALUES (3, 1, 3, 7.00, 3.50, 60.00, 10, True);") | Out-Null
    # Parqueo 2 (Satelite)
    $conn.Execute("INSERT INTO TARIFAS (id_tarifa, id_parqueo, id_tipo_vehiculo, precio_hora, precio_fraccion, precio_dia, tolerancia_minutos, vigente) VALUES (4, 2, 1, 4.00, 2.00, 35.00, 10, True);") | Out-Null
    $conn.Execute("INSERT INTO TARIFAS (id_tarifa, id_parqueo, id_tipo_vehiculo, precio_hora, precio_fraccion, precio_dia, tolerancia_minutos, vigente) VALUES (5, 2, 2, 2.50, 1.00, 18.00, 10, True);") | Out-Null

    # 7. ESPACIOS (Parqueo 1 - Ceja Central con diferentes estados para testing)
    # Autos Disponibles
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (1, 1, 1, 'A-01', 'Sector A', 'Disponible');") | Out-Null
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (2, 1, 1, 'A-02', 'Sector A', 'Disponible');") | Out-Null
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (3, 1, 1, 'A-03', 'Sector A', 'Disponible');") | Out-Null
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (4, 1, 1, 'A-04', 'Sector A', 'Disponible');") | Out-Null
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (5, 1, 1, 'A-05', 'Sector A', 'Disponible');") | Out-Null
    # Autos Ocupados (Con ticket activo)
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (6, 1, 1, 'A-06', 'Sector A', 'Ocupado');") | Out-Null
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (7, 1, 1, 'A-07', 'Sector A', 'Ocupado');") | Out-Null
    # Auto Reservado (Con codigo QR)
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (8, 1, 1, 'A-08', 'Sector A', 'Reservado');") | Out-Null
    # Auto en Mantenimiento
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (9, 1, 1, 'A-09', 'Sector A', 'Mantenimiento');") | Out-Null
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (10, 1, 1, 'A-10', 'Sector A', 'Disponible');") | Out-Null
    # Motos
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (11, 1, 2, 'M-01', 'Sector Motos', 'Disponible');") | Out-Null
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (12, 1, 2, 'M-02', 'Sector Motos', 'Disponible');") | Out-Null
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (13, 1, 2, 'M-03', 'Sector Motos', 'Disponible');") | Out-Null
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (14, 1, 2, 'M-04', 'Sector Motos', 'Ocupado');") | Out-Null
    $conn.Execute("INSERT INTO ESPACIOS (id_espacio, id_parqueo, id_tipo_vehiculo, codigo_espacio, piso_sector, estado) VALUES (15, 1, 2, 'M-05', 'Sector Motos', 'Disponible');") | Out-Null

    # 8. VEHICULOS DE CONDUCTORES
    $conn.Execute("INSERT INTO VEHICULOS (id_vehiculo, id_usuario, id_tipo_vehiculo, placa, color, marca_modelo) VALUES (1, 4, 1, '4829-ABC', 'Plateado', 'Toyota Corolla');") | Out-Null
    $conn.Execute("INSERT INTO VEHICULOS (id_vehiculo, id_usuario, id_tipo_vehiculo, placa, color, marca_modelo) VALUES (2, 5, 1, '3192-KLP', 'Rojo', 'Suzuki Swift');") | Out-Null
    $conn.Execute("INSERT INTO VEHICULOS (id_vehiculo, id_usuario, id_tipo_vehiculo, placa, color, marca_modelo) VALUES (3, 6, 2, '5541-MNO', 'Negro', 'Honda CB 160');") | Out-Null

    # 9. RESERVA ACTIVA (Con codigo QR de prueba)
    $conn.Execute("INSERT INTO RESERVAS (id_reserva, codigo_qr_token, id_usuario, id_espacio, placa_vehiculo, fecha_hora_reserva, fecha_hora_prevista_llegada, minutos_tolerancia, estado_reserva, monto_adelanto, pago_confirmado) VALUES (1, 'QR-ALTO-2026-001', 4, 8, '4829-ABC', Now(), DateAdd('n', 20, Now()), 15, 'Confirmada', 5.00, True);") | Out-Null

    # 10. INGRESOS / SALIDAS
    # Autos actualmente estacionados (para probar cobro de salida)
    $horaHace2H = [DateTime]::Now.AddHours(-2).ToString("yyyy-MM-dd HH:mm:ss")
    $horaHace1H = [DateTime]::Now.AddMinutes(-75).ToString("yyyy-MM-dd HH:mm:ss")
    $horaHace30M = [DateTime]::Now.AddMinutes(-30).ToString("yyyy-MM-dd HH:mm:ss")

    $conn.Execute("INSERT INTO INGRESOS_SALIDAS (id_ingreso_salida, numero_ticket, id_reserva, id_espacio, id_operador_entrada, placa, fecha_hora_entrada, estado_estancia) VALUES (1, 'TCK-2026-001', NULL, 6, 2, '2049-ZXY', #$horaHace2H#, 'En Parqueo');") | Out-Null
    $conn.Execute("INSERT INTO INGRESOS_SALIDAS (id_ingreso_salida, numero_ticket, id_reserva, id_espacio, id_operador_entrada, placa, fecha_hora_entrada, estado_estancia) VALUES (2, 'TCK-2026-002', NULL, 7, 2, '3192-KLP', #$horaHace1H#, 'En Parqueo');") | Out-Null
    $conn.Execute("INSERT INTO INGRESOS_SALIDAS (id_ingreso_salida, numero_ticket, id_reserva, id_espacio, id_operador_entrada, placa, fecha_hora_entrada, estado_estancia) VALUES (3, 'TCK-2026-003', NULL, 14, 2, '5541-MNO', #$horaHace30M#, 'En Parqueo');") | Out-Null

    # Tickets Historicos ya cobrados (para reportes y graficas)
    $ayerEntrada1 = [DateTime]::Now.AddDays(-1).Date.AddHours(9).ToString("yyyy-MM-dd HH:mm:ss")
    $ayerSalida1  = [DateTime]::Now.AddDays(-1).Date.AddHours(11).AddMinutes(30).ToString("yyyy-MM-dd HH:mm:ss")
    $ayerEntrada2 = [DateTime]::Now.AddDays(-1).Date.AddHours(14).ToString("yyyy-MM-dd HH:mm:ss")
    $ayerSalida2  = [DateTime]::Now.AddDays(-1).Date.AddHours(15).ToString("yyyy-MM-dd HH:mm:ss")

    $conn.Execute("INSERT INTO INGRESOS_SALIDAS (id_ingreso_salida, numero_ticket, id_reserva, id_espacio, id_operador_entrada, id_operador_salida, placa, fecha_hora_entrada, fecha_hora_salida, minutos_totales, total_a_pagar, estado_estancia) VALUES (4, 'TCK-HIST-001', NULL, 1, 2, 2, '1820-BBC', #$ayerEntrada1#, #$ayerSalida1#, 150, 15.00, 'Finalizado');") | Out-Null
    $conn.Execute("INSERT INTO INGRESOS_SALIDAS (id_ingreso_salida, numero_ticket, id_reserva, id_espacio, id_operador_entrada, id_operador_salida, placa, fecha_hora_entrada, fecha_hora_salida, minutos_totales, total_a_pagar, estado_estancia) VALUES (5, 'TCK-HIST-002', NULL, 2, 2, 3, '9921-DFG', #$ayerEntrada2#, #$ayerSalida2#, 60, 5.00, 'Finalizado');") | Out-Null

    # 11. PAGOS DE PRUEBA
    $conn.Execute("INSERT INTO PAGOS (id_pago, id_ingreso_salida, id_reserva, monto, metodo_pago, referencia_transaccion, fecha_hora_pago, id_operador_cobro) VALUES (1, 4, NULL, 15.00, 'Efectivo', 'PAGO-EFE-001', #$ayerSalida1#, 2);") | Out-Null
    $conn.Execute("INSERT INTO PAGOS (id_pago, id_ingreso_salida, id_reserva, monto, metodo_pago, referencia_transaccion, fecha_hora_pago, id_operador_cobro) VALUES (2, 5, NULL, 5.00, 'QR Simple', 'BMSC-8492019', #$ayerSalida2#, 3);") | Out-Null

    Write-Host "`n[OK] Datos de prueba inyectados exitosamente!" -ForegroundColor Green
    Write-Host "Usuarios disponibles: admin@parkapp.bo, operador1@parkapp.bo, operador2@parkapp.bo, carlos.mamani@gmail.com" -ForegroundColor White
    Write-Host "Codigo QR de prueba listo para escanear: QR-ALTO-2026-001 (Espacio A-08)" -ForegroundColor White
    Write-Host "3 vehiculos estacionados actualmente listos para probar salida y cobro.`n" -ForegroundColor White
}
catch {
    Write-Error "Ocurrio un error al inyectar datos de prueba: $_"
}
finally {
    $conn.Close()
    [System.Runtime.InteropServices.Marshal]::ReleaseComObject($conn) | Out-Null
}
