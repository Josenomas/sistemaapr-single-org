<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Pago</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #059669;
        }

        .logo {
            max-width: 120px;
            max-height: 100px;
            margin-bottom: 10px;
        }

        .org-name {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
            margin-top: 15px;
        }

        .comprobante-number {
            font-size: 16px;
            color: #6b7280;
            margin-top: 5px;
        }

        .success-badge {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 10px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-grid {
            display: table;
            width: 100%;
            background: #f9fafb;
            padding: 15px;
            border-radius: 4px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            padding: 8px 0;
            font-weight: 600;
            color: #6b7280;
        }

        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #1f2937;
        }

        .amount-box {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin: 30px 0;
        }

        .amount-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .amount-value {
            font-size: 36px;
            font-weight: bold;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .detail-table th,
        .detail-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #6b7280;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .stamp-box {
            margin-top: 40px;
            padding: 20px;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            text-align: center;
        }

        .stamp-box p {
            color: #6b7280;
            font-size: 11px;
            margin-bottom: 40px;
        }

        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 0 auto;
            padding-top: 5px;
            font-size: 11px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(isset($organizacion->logo))
        <img src="{{ public_path('storage/' . $organizacion->logo) }}" alt="Logo" class="logo">
        @endif
        <div class="org-name">{{ $organizacion->nombre_apr }}</div>
        <div class="document-title">COMPROBANTE DE PAGO</div>
        <div class="comprobante-number">N° {{ $pago->numero_comprobante ?? $pago->id }}</div>
        <div class="success-badge">✓ PAGO RECIBIDO</div>
    </div>

    <div class="section">
        <div class="section-title">Información del Pago</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Fecha de Pago:</div>
                <div class="info-value">{{ date('d/m/Y H:i', strtotime($pago->fecha_pago)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Método de Pago:</div>
                <div class="info-value">{{ ucfirst($pago->metodo_pago) }}</div>
            </div>
            @if($pago->numero_transaccion)
            <div class="info-row">
                <div class="info-label">N° Transacción:</div>
                <div class="info-value">{{ $pago->numero_transaccion }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Estado:</div>
                <div class="info-value"><strong style="color: #059669;">COMPLETADO</strong></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Datos del Socio</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">N° Socio:</div>
                <div class="info-value">{{ $pago->boleta->socio->numero_socio ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value">{{ $pago->boleta->socio->nombre_completo ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">RUT:</div>
                <div class="info-value">{{ $pago->boleta->socio->rut ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Dirección:</div>
                <div class="info-value">{{ $pago->boleta->socio->direccion ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detalle de la Boleta</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th>N° Boleta</th>
                    <th>Período</th>
                    <th>Consumo</th>
                    <th class="text-right">Monto Original</th>
                    <th class="text-right">Descuento</th>
                    <th class="text-right">Total Boleta</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $pago->boleta->numero_boleta ?? 'N/A' }}</td>
                    <td>{{ $pago->boleta->periodo ? date('m/Y', strtotime($pago->boleta->periodo)) : 'N/A' }}</td>
                    <td>{{ $pago->boleta->consumo ?? 0 }} m³</td>
                    <td class="text-right">${{ number_format($pago->boleta->total ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">${{ number_format($pago->descuento ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>${{ number_format($pago->boleta->total ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="amount-box">
        <div class="amount-label">TOTAL PAGADO</div>
        <div class="amount-value">${{ number_format($pago->monto_pagado, 0, ',', '.') }}</div>
    </div>

    @if($pago->observaciones)
    <div class="section">
        <div class="section-title">Observaciones</div>
        <div class="info-grid">
            <p>{{ $pago->observaciones }}</p>
        </div>
    </div>
    @endif

    <div class="stamp-box">
        <p><strong>Firma y Timbre</strong></p>
        <div class="signature-line">
            {{ $organizacion->nombre_apr }}
        </div>
    </div>

    <div class="footer">
        <p><strong>{{ $organizacion->nombre_apr }}</strong></p>
        @if($organizacion->direccion)
        <p>{{ $organizacion->direccion }}</p>
        @endif
        @if($organizacion->telefono_contacto || $organizacion->email_contacto)
        <p>
            @if($organizacion->telefono_contacto)
                Tel: {{ $organizacion->telefono_contacto }}
            @endif
            @if($organizacion->telefono_contacto && $organizacion->email_contacto)
                |
            @endif
            @if($organizacion->email_contacto)
                Email: {{ $organizacion->email_contacto }}
            @endif
        </p>
        @endif
        <p style="margin-top: 15px;">Este documento es generado automáticamente y tiene validez sin firma ni timbre</p>
        <p>Fecha de emisión: {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
