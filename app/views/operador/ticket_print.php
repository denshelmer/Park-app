<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Ingreso - <?= htmlspecialchars($estancia['numero_ticket']) ?></title>
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
        .barcode {
            letter-spacing: 4px;
            font-size: 18px;
            font-weight: bold;
            padding: 4px 0;
            background: repeating-linear-gradient(90deg, #000 0px, #000 2px, #fff 2px, #fff 4px);
            color: transparent;
            height: 36px;
            margin: 6px auto;
            width: 80%;
        }
        @media print { 
            .no-print { display: none !important; }
            body { width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 14px; font-weight: bold; cursor: pointer; background: #152b47; color: #fff; border: none; border-radius: 4px;">
            Imprimir Ticket
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
        <div style="font-size: 11px;">COMPROBANTE DE CONTROL DE INGRESO</div>
        <div class="fw-bold" style="font-size: 17px; margin-top: 3px;"><?= htmlspecialchars($estancia['numero_ticket']) ?></div>
        <?php if (!empty($estancia['id_reserva'])): ?>
            <div style="font-size: 11px; font-weight: bold; margin-top: 2px;">*** INGRESO CON RESERVA QR ***</div>
        <?php endif; ?>
    </div>

    <div class="divider"></div>

    <div class="text-start">
        <div class="d-flex">
            <span>FECHA/HORA:</span>
            <span class="fw-bold"><?= date('d/m/Y H:i', strtotime($estancia['fecha_hora_entrada'])) ?></span>
        </div>
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
            <span class="fw-bold" style="font-size: 15px;"><?= htmlspecialchars($estancia['codigo_espacio']) ?></span>
        </div>
        <div class="d-flex">
            <span>SECTOR:</span>
            <span><?= htmlspecialchars($estancia['piso_sector'] ?? 'General') ?></span>
        </div>
        <div class="d-flex">
            <span>OPERADOR:</span>
            <span><?= htmlspecialchars($estancia['operador_entrada'] ?? 'Caseta Central') ?></span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="barcode"></div>
    <div class="text-center" style="font-size: 11px; letter-spacing: 1px;">
        *<?= htmlspecialchars($estancia['numero_ticket']) ?>*
    </div>

    <div class="double-divider"></div>

    <div class="text-center" style="font-size: 10.5px;">
        <div>IMPORTANTE: CONSERVE ESTE TICKET</div>
        <div>Debe presentarlo en caseta para liquidar y retirar su vehículo.</div>
        <div style="margin-top: 4px;">En caso de extravío se cobrará penalización administrativa según reglamento.</div>
        <div style="margin-top: 6px; font-size: 10px;">¡Gracias por su preferencia!</div>
    </div>
</body>
</html>

