<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Financiero</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }

        .logo {
            max-width: 100px;
            max-height: 80px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 20px;
            color: #667eea;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }

        .info-section {
            margin-bottom: 20px;
            background: #f9fafb;
            padding: 15px;
            border-radius: 4px;
        }

        .info-section p {
            margin-bottom: 5px;
        }

        .info-section strong {
            color: #667eea;
        }

        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-right: 10px;
        }

        .summary-card:last-child {
            margin-right: 0;
        }

        .summary-card.income {
            background: #d1fae5;
            border-color: #059669;
        }

        .summary-card.expense {
            background: #fee2e2;
            border-color: #dc2626;
        }

        .summary-card.pending {
            background: #fef3c7;
            border-color: #f59e0b;
        }

        .summary-card.balance {
            background: #dbeafe;
            border-color: #2563eb;
        }

        .card-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .card-value {
            font-size: 20px;
            font-weight: bold;
        }

        .summary-card.income .card-value {
            color: #065f46;
        }

        .summary-card.expense .card-value {
            color: #991b1b;
        }

        .summary-card.pending .card-value {
            color: #92400e;
        }

        .summary-card.balance .card-value {
            color: #1e40af;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #667eea;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #667eea;
            color: white;
        }

        thead th {
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        tbody td {
            padding: 8px;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            background: #e5e7eb !important;
            font-weight: bold;
        }

        .chart-placeholder {
            height: 200px;
            background: #f9fafb;
            border: 2px dashed #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
            border-radius: 8px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(isset($organizacion->logo))
        <img src="{{ public_path('storage/' . $organizacion->logo) }}" alt="Logo" class="logo">
        @endif
        <h1>{{ $organizacion->nombre_apr }}</h1>
        <h2>Reporte Financiero</h2>
    </div>

    <div class="info-section">
        <p><strong>Período:</strong> {{ $periodo }}</p>
        <p><strong>Fecha de generación:</strong> {{ date('d/m/Y H:i') }}</p>
    </div>

    <!-- Resumen General -->
    <div class="summary-cards">
        <div class="summary-card income">
            <div class="card-label">Total Ingresos</div>
            <div class="card-value">${{ number_format($datos['total_ingresos'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card expense">
            <div class="card-label">Total Egresos</div>
            <div class="card-value">${{ number_format($datos['total_egresos'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card pending">
            <div class="card-label">Pendientes</div>
            <div class="card-value">${{ number_format($datos['total_pendiente'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card balance">
            <div class="card-label">Balance</div>
            <div class="card-value">${{ number_format(($datos['total_ingresos'] ?? 0) - ($datos['total_egresos'] ?? 0), 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Detalle de Ingresos -->
    <div class="section-title">Detalle de Ingresos</div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Concepto</th>
                <th>N° Comprobante</th>
                <th>Socio</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal_ingresos = 0; @endphp
            @if(isset($datos['ingresos']) && count($datos['ingresos']) > 0)
                @foreach($datos['ingresos'] as $ingreso)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($ingreso->fecha_pago)) }}</td>
                    <td>Pago de Boleta</td>
                    <td>{{ $ingreso->numero_comprobante ?? '-' }}</td>
                    <td>{{ $ingreso->boleta->socio->nombre_completo ?? '-' }}</td>
                    <td class="text-right">${{ number_format($ingreso->monto_pagado, 0, ',', '.') }}</td>
                </tr>
                @php $subtotal_ingresos += $ingreso->monto_pagado; @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL INGRESOS:</td>
                    <td class="text-right">${{ number_format($subtotal_ingresos, 0, ',', '.') }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="5" class="text-center">No hay ingresos en este período</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Detalle de Egresos -->
    <div class="section-title">Detalle de Egresos</div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Concepto</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal_egresos = 0; @endphp
            @if(isset($datos['egresos']) && count($datos['egresos']) > 0)
                @foreach($datos['egresos'] as $egreso)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($egreso->fecha)) }}</td>
                    <td>{{ $egreso->concepto ?? 'Egreso General' }}</td>
                    <td>{{ $egreso->categoria ?? '-' }}</td>
                    <td>{{ $egreso->descripcion ?? '-' }}</td>
                    <td class="text-right">${{ number_format($egreso->monto, 0, ',', '.') }}</td>
                </tr>
                @php $subtotal_egresos += $egreso->monto; @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL EGRESOS:</td>
                    <td class="text-right">${{ number_format($subtotal_egresos, 0, ',', '.') }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="5" class="text-center">No hay egresos en este período</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Boletas Pendientes -->
    @if(isset($datos['boletas_pendientes']) && count($datos['boletas_pendientes']) > 0)
    <div class="section-title">Boletas Pendientes de Pago</div>
    <table>
        <thead>
            <tr>
                <th>N° Boleta</th>
                <th>Socio</th>
                <th>Período</th>
                <th>Fecha Vencimiento</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal_pendientes = 0; @endphp
            @foreach($datos['boletas_pendientes'] as $boleta)
            <tr>
                <td>{{ $boleta->numero_boleta }}</td>
                <td>{{ $boleta->socio->nombre_completo ?? '-' }}</td>
                <td>{{ $boleta->periodo ? date('m/Y', strtotime($boleta->periodo)) : '-' }}</td>
                <td>{{ date('d/m/Y', strtotime($boleta->fecha_vencimiento)) }}</td>
                <td class="text-right">${{ number_format($boleta->total, 0, ',', '.') }}</td>
            </tr>
            @php $subtotal_pendientes += $boleta->total; @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL PENDIENTE:</td>
                <td class="text-right">${{ number_format($subtotal_pendientes, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>{{ $organizacion->nombre_apr }} - Sistema APR</p>
        <p>Generado automáticamente el {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
