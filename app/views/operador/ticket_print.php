<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Parqueo - ParkApp</title>
    <style>
        body { font-family: monospace; width: 280px; margin: 0 auto; padding: 10px; font-size: 13px; text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Imprimir Ticket</button>
        <button onclick="window.close()">Cerrar</button>
    </div>

    <div class="fw-bold" style="font-size: 15px;">PARKAPP EL ALTO</div>
    <div>Parqueo Ceja Central</div>
    <div style="font-size: 11px;">Av. 6 de Marzo esq. Calle 2</div>
    <div class="divider"></div>

    <div class="fw-bold" style="font-size: 16px;">TICKET: TCK-2026-001</div>
    <div class="divider"></div>

    <div class="text-start">
        <div><strong>Fecha/Hora:</strong> <?= date('d/m/Y H:i') ?></div>
        <div><strong>Placa:</strong> 2049-ZXY</div>
        <div><strong>Espacio:</strong> A-06 (Sector A)</div>
        <div><strong>Operador:</strong> Juan Carlos Choque</div>
    </div>
    <div class="divider"></div>

    <div style="font-size: 11px;">
        Conserve este ticket para retirar su vehículo.<br>
        En caso de extravío se cobrará penalización.
    </div>
</body>
</html>
