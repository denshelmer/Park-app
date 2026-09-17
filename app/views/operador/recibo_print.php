<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Cobro - <?= htmlspecialchars($estancia['numero_ticket']) ?></title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 290px; 
            margin: 0 auto; 
            padding: 12px; 
            font-size: 13px; 
            color: #000;
            line-height: 1.35;
        }
        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .double-divider { border-top: 2px dashed #000; margin: 8px 0; }
        .d-flex { display: flex; justify-content: space-between; }
        @media print { 
            .no-print { display: none !important; }
            body { width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 14px; font-weight: bold; cursor: pointer; background: #198754; color: #fff; border: none; border-radius: 4px;">
            Imprimir Recibo
        </button>
        <button onclick="window.location.href='<?= BASE_URL ?>/caseta'" style="padding: 6px 14px; cursor: pointer; background: #6c757d; color: #fff; border: none; border-radius: 4px; margin-left: 6px;">
            Volver a Caseta
        </button>
    </div>

    <div class="text-center">
        <div class="fw-bold" style="font-size: 16px;">PARKAPP BOLIVIA</div>
        <div class="fw-bold" style="font-size: 14px;"><?= htmlspecialchars(strtoupper($estancia['nombre_parqueo'] ?? 'PARQUEO CENTRAL')) ?></div>
        <div style="font-size: 11px;"><?= htmlspecialchars($estancia['direccion'] ?? 'Av. 6 de Marzo') ?></div>
        <div style="font-size: 11px;"><?= htmlspecialchars($estancia['zona'] ?? 'El Alto, La Paz') ?></div>
    </div>

    <div class="divider"></div>

    <div class="text-center">
        <div style="font-size: 11px;">COMPROBANTE DE PAGO Y SALIDA</div>
        <div class="fw-bold" style="font-size: 16px; margin-top: 2px;"><?= htmlspecialchars($estancia['numero_ticket']) ?></div>
        <div style="font-size: 11px;">ID Pago: #<?= htmlspecialchars($pago['id_pago'] ?? '1') ?></div>
    </div>

    <div class="divider"></div>

    <div class="text-start">
        <div class="d-flex">
            <span>PLACA:</span>
            <span class="fw-bold" style="font-size: 15px;"><?= htmlspecialchars($estancia['placa']) ?></span>
        </div>
        <div class="d-flex">
            <span>TIPO:</span>
            <span><?= htmlspecialchars($estancia['nombre_tipo'] ?? 'Automóvil') ?></span>
        </div>
        <div class="d-flex">
            <span>CAJÓN / ESPACIO:</span>
            <span class="fw-bold"><?= htmlspecialchars($estancia['codigo_espacio']) ?></span>
        </div>
        <div class="d-flex">
            <span>ENTRADA:</span>
            <span><?= date('d/m/Y H:i', strtotime($estancia['fecha_hora_entrada'])) ?></span>
        </div>
        <div class="d-flex">
            <span>SALIDA:</span>
            <span><?= !empty($estancia['fecha_hora_salida']) ? date('d/m/Y H:i', strtotime($estancia['fecha_hora_salida'])) : date('d/m/Y H:i') ?></span>
        </div>
        <?php 
            $minTot = (int)($estancia['minutos_totales'] ?? 0);
            $h = floor($minTot / 60);
            $m = $minTot % 60;
            $txtTiempo = ($h > 0 ? "{$h}h " : "") . "{$m}m ({$minTot} min)";
        ?>
        <div class="d-flex">
            <span>TIEMPO TOTAL:</span>
            <span class="fw-bold"><?= $txtTiempo ?></span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="text-start">
        <div class="d-flex" style="font-size: 15px; margin: 4px 0;">
            <span class="fw-bold">TOTAL PAGADO:</span>
            <span class="fw-bold" style="font-size: 17px;">Bs. <?= number_format((float)($pago['monto'] ?? $estancia['total_a_pagar'] ?? 0), 2) ?></span>
        </div>
        <div class="d-flex">
            <span>MÉTODO DE PAGO:</span>
            <span class="fw-bold"><?= htmlspecialchars($pago['metodo_pago'] ?? 'Efectivo') ?></span>
        </div>
        <div class="d-flex">
            <span>REFERENCIA:</span>
            <span style="font-size: 11px;"><?= htmlspecialchars($pago['referencia_transaccion'] ?? 'EFE-001') ?></span>
        </div>
        <div class="d-flex">
            <span>OPERADOR COBRO:</span>
            <span><?= htmlspecialchars($pago['operador_cobro'] ?? $estancia['operador_salida'] ?? 'Caseta') ?></span>
        </div>
        <div class="d-flex">
            <span>FECHA/HORA PAGO:</span>
            <span><?= !empty($pago['fecha_hora_pago']) ? date('d/m/Y H:i', strtotime($pago['fecha_hora_pago'])) : date('d/m/Y H:i') ?></span>
        </div>
    </div>

    <div class="double-divider"></div>

    <div class="text-center" style="font-size: 10.5px;">
        <div class="fw-bold">ESTANCIA CONCLUIDA CON ÉXITO</div>
        <div>Este comprobante certifica la salida regular y liberación de cajón.</div>
        <div style="margin-top: 6px; font-size: 10px;">¡Gracias por preferir ParkApp El Alto!</div>
    </div>
</body>
</html>
