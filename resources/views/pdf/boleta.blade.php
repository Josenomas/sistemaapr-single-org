<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta N° {{ $boleta->numero_boleta }}</title>
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
            display: table;
            width: 100%;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }

        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }

        .logo {
            max-width: 120px;
            max-height: 100px;
            margin-bottom: 10px;
        }

        .org-name {
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .org-info {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.4;
        }

        .boleta-number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .boleta-date {
            font-size: 11px;
            color: #6b7280;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 8px 0;
            font-weight: 600;
            color: #6b7280;
        }

        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #1f2937;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background: #667eea;
            color: white;
        }

        thead th {
            padding: 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody td {
            padding: 10px 12px;
        }

        .text-right {
            text-align: right;
        }

        .totals-section {
            margin-top: 20px;
            float: right;
            width: 50%;
        }

        .total-row {
            display: table;
            width: 100%;
            padding: 8px 0;
        }

        .total-label {
            display: table-cell;
            text-align: right;
            padding-right: 20px;
            font-weight: 600;
            color: #6b7280;
        }

        .total-value {
            display: table-cell;
            text-align: right;
            width: 40%;
            font-size: 14px;
        }

        .total-final {
            background: #667eea;
            color: white;
            padding: 12px;
            margin-top: 10px;
            border-radius: 4px;
        }

        .total-final .total-label {
            color: white;
            font-size: 16px;
        }

        .total-final .total-value {
            font-size: 20px;
            font-weight: bold;
        }

        .payment-info {
            clear: both;
            margin-top: 40px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 4px;
            border-left: 4px solid #667eea;
        }

        .payment-info p {
            margin-bottom: 5px;
            font-size: 11px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if(isset($organizacion->logo))
            <img src="{{ public_path('storage/' . $organizacion->logo) }}" alt="Logo" class="logo">
            @endif
            <div class="org-name">{{ $organizacion->nombre_apr }}</div>
            <div class="org-info">
                @if($organizacion->direccion)
                    {{ $organizacion->direccion }}<br>
                @endif
                @if($organizacion->telefono_contacto)
                    Teléfono: {{ $organizacion->telefono_contacto }}<br>
                @endif
                @if($organizacion->email_contacto)
                    Email: {{ $organizacion->email_contacto }}
                @endif
            </div>
        </div>
        <div class="header-right">
            <div class="boleta-number">N° {{ $boleta->numero_boleta }}</div>
            <div class="boleta-date">
                Fecha: {{ date('d/m/Y', strtotime($boleta->fecha_emision)) }}<br>
                Vence: {{ date('d/m/Y', strtotime($boleta->fecha_vencimiento)) }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Datos del Socio</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">N° Socio:</div>
                <div class="info-value">{{ $boleta->socio->numero_socio }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value">{{ $boleta->socio->nombre_completo }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">RUT:</div>
                <div class="info-value">{{ $boleta->socio->rut }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Dirección:</div>
                <div class="info-value">{{ $boleta->socio->direccion }}</div>
            </div>
            @if($boleta->socio->sector)
            <div class="info-row">
                <div class="info-label">Sector:</div>
                <div class="info-value">{{ $boleta->socio->sector }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">N° Medidor:</div>
                <div class="info-value">{{ $boleta->socio->numero_medidor ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detalle de Consumo</div>
        <table>
            <thead>
                <tr>
                    <th>Período</th>
                    <th>Lectura Anterior</th>
                    <th>Lectura Actual</th>
                    <th>Consumo (m³)</th>
                    <th class="text-right">Monto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $boleta->periodo ? date('m/Y', strtotime($boleta->periodo)) : '-' }}</td>
                    <td>{{ $boleta->lectura_anterior ?? '-' }}</td>
                    <td>{{ $boleta->lectura_actual ?? '-' }}</td>
                    <td>{{ $boleta->consumo ?? 0 }} m³</td>
                    <td class="text-right">${{ number_format($boleta->subtotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="totals-section">
        <div class="total-row">
            <div class="total-label">Subtotal:</div>
            <div class="total-value">${{ number_format($boleta->subtotal, 0, ',', '.') }}</div>
        </div>
        @if($boleta->descuentos > 0)
        <div class="total-row">
            <div class="total-label">Descuentos:</div>
            <div class="total-value" style="color: #059669;">-${{ number_format($boleta->descuentos, 0, ',', '.') }}</div>
        </div>
        @endif
        @if($boleta->recargos > 0)
        <div class="total-row">
            <div class="total-label">Recargos:</div>
            <div class="total-value" style="color: #dc2626;">+${{ number_format($boleta->recargos, 0, ',', '.') }}</div>
        </div>
        @endif
        <div class="total-row total-final">
            <div class="total-label">TOTAL A PAGAR:</div>
            <div class="total-value">${{ number_format($boleta->total, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="payment-info">
        <p><strong>Estado:</strong>
            @if($boleta->estado == 'pagada')
                <span class="badge badge-success">Pagada</span>
            @elseif($boleta->estado == 'pendiente')
                <span class="badge badge-warning">Pendiente</span>
            @else
                <span class="badge badge-danger">{{ ucfirst($boleta->estado) }}</span>
            @endif
        </p>
        <p><strong>Información de Pago:</strong></p>
        <p>Por favor realice el pago antes de la fecha de vencimiento para evitar recargos.</p>
        @if($organizacion->cuenta_bancaria)
        <p><strong>Cuenta Bancaria:</strong> {{ $organizacion->cuenta_bancaria }}</p>
        @endif
    </div>

    <div class="footer">
        <p>{{ $organizacion->nombre_apr }} - Sistema APR</p>
        <p>Este documento es generado automáticamente</p>
        <p>Fecha de emisión: {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
