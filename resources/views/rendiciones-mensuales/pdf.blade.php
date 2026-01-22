<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Rendición Mensual - {{ $rendicion->codigo_rendicion }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header h2 {
            color: #666;
            font-size: 14px;
            font-weight: normal;
        }
        .info-box {
            background: #f3f4f6;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #2563eb;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
        .resumen-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
        }
        .card {
            flex: 1;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
        }
        .card-saldo {
            background: #e0e7ff;
            border: 2px solid #6366f1;
        }
        .card-ingresos {
            background: #d1fae5;
            border: 2px solid #10b981;
        }
        .card-egresos {
            background: #fee2e2;
            border: 2px solid #ef4444;
        }
        .card-final {
            background: #dbeafe;
            border: 2px solid #3b82f6;
        }
        .card-label {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .card-value {
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            border-bottom: 2px solid #e5e7eb;
            text-transform: uppercase;
        }
        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #10b981;
        }
        .text-danger {
            color: #ef4444;
        }
        .text-muted {
            color: #9ca3af;
        }
        .section-title {
            background: #2563eb;
            color: white;
            padding: 8px 12px;
            margin-top: 15px;
            margin-bottom: 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
        .total-row {
            background: #f9fafb;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>RENDICIÓN MENSUAL</h1>
        <h2>Sistema APR - Agua Potable Rural</h2>
    </div>

    {{-- Información General --}}
    <div class="info-box">
        <div class="info-row">
            <span class="label">Código:</span>
            <span class="value">{{ $rendicion->codigo_rendicion }}</span>
        </div>
        <div class="info-row">
            <span class="label">Periodo:</span>
            <span class="value">{{ $rendicion->periodo_texto }}</span>
        </div>
        <div class="info-row">
            <span class="label">Estado:</span>
            <span class="value">
                @if($rendicion->estado === 'cerrado')
                    <span class="badge badge-danger">CERRADO</span>
                @else
                    <span class="badge badge-success">ABIERTO</span>
                @endif
            </span>
        </div>
        @if($rendicion->fecha_cierre)
        <div class="info-row">
            <span class="label">Fecha de Cierre:</span>
            <span class="value">{{ $rendicion->fecha_cierre_formateada }}</span>
        </div>
        @endif
    </div>

    {{-- Resumen de Saldos --}}
    <div class="resumen-cards">
        <div class="card card-saldo">
            <div class="card-label">Saldo Anterior</div>
            <div class="card-value">${{ number_format($rendicion->saldo_anterior, 0, ',', '.') }}</div>
        </div>
        <div class="card card-ingresos">
            <div class="card-label">Total Ingresos</div>
            <div class="card-value text-success">${{ number_format($rendicion->total_ingresos, 0, ',', '.') }}</div>
        </div>
        <div class="card card-egresos">
            <div class="card-label">Total Egresos</div>
            <div class="card-value text-danger">${{ number_format($rendicion->total_egresos, 0, ',', '.') }}</div>
        </div>
        <div class="card card-final">
            <div class="card-label">Saldo Final</div>
            <div class="card-value">${{ number_format($rendicion->saldo_final, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Detalle de Ingresos --}}
    <div class="section-title">DETALLE DE INGRESOS</div>
    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Monto</th>
                <th class="text-right">%</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Consumo de Agua</td>
                <td class="text-right">${{ number_format($rendicion->ingresos_consumo_agua, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_ingresos > 0 ? number_format(($rendicion->ingresos_consumo_agua / $rendicion->total_ingresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Subsidios</td>
                <td class="text-right">${{ number_format($rendicion->ingresos_subsidios, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_ingresos > 0 ? number_format(($rendicion->ingresos_subsidios / $rendicion->total_ingresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Aportes de Socios</td>
                <td class="text-right">${{ number_format($rendicion->ingresos_aportes_socios, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_ingresos > 0 ? number_format(($rendicion->ingresos_aportes_socios / $rendicion->total_ingresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Multas</td>
                <td class="text-right">${{ number_format($rendicion->ingresos_multas, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_ingresos > 0 ? number_format(($rendicion->ingresos_multas / $rendicion->total_ingresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Incorporaciones</td>
                <td class="text-right">${{ number_format($rendicion->ingresos_incorporaciones, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_ingresos > 0 ? number_format(($rendicion->ingresos_incorporaciones / $rendicion->total_ingresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Otros Ingresos</td>
                <td class="text-right">${{ number_format($rendicion->ingresos_otros, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_ingresos > 0 ? number_format(($rendicion->ingresos_otros / $rendicion->total_ingresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr class="total-row">
                <td><strong>TOTAL INGRESOS</strong></td>
                <td class="text-right"><strong class="text-success">${{ number_format($rendicion->total_ingresos, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>100%</strong></td>
            </tr>
        </tbody>
    </table>

    {{-- Detalle de Egresos --}}
    <div class="section-title">DETALLE DE EGRESOS</div>
    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Monto</th>
                <th class="text-right">%</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Energía Eléctrica</td>
                <td class="text-right">${{ number_format($rendicion->egresos_energia_electrica, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_egresos > 0 ? number_format(($rendicion->egresos_energia_electrica / $rendicion->total_egresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Productos Químicos</td>
                <td class="text-right">${{ number_format($rendicion->egresos_productos_quimicos, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_egresos > 0 ? number_format(($rendicion->egresos_productos_quimicos / $rendicion->total_egresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Reparaciones</td>
                <td class="text-right">${{ number_format($rendicion->egresos_reparaciones, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_egresos > 0 ? number_format(($rendicion->egresos_reparaciones / $rendicion->total_egresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Remuneraciones</td>
                <td class="text-right">${{ number_format($rendicion->egresos_remuneraciones, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_egresos > 0 ? number_format(($rendicion->egresos_remuneraciones / $rendicion->total_egresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Gastos Administrativos</td>
                <td class="text-right">${{ number_format($rendicion->egresos_gastos_administrativos, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_egresos > 0 ? number_format(($rendicion->egresos_gastos_administrativos / $rendicion->total_egresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Otros Egresos</td>
                <td class="text-right">${{ number_format($rendicion->egresos_otros, 0, ',', '.') }}</td>
                <td class="text-right">{{ $rendicion->total_egresos > 0 ? number_format(($rendicion->egresos_otros / $rendicion->total_egresos) * 100, 1) : 0 }}%</td>
            </tr>
            <tr class="total-row">
                <td><strong>TOTAL EGRESOS</strong></td>
                <td class="text-right"><strong class="text-danger">${{ number_format($rendicion->total_egresos, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>100%</strong></td>
            </tr>
        </tbody>
    </table>

    {{-- Observaciones --}}
    @if($rendicion->observaciones)
    <div class="section-title">OBSERVACIONES</div>
    <div style="padding: 10px; background: #f9fafb; border-radius: 5px; margin-bottom: 15px;">
        {{ $rendicion->observaciones }}
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>Documento generado el {{ date('d/m/Y H:i') }}</p>
        <p>Sistema APR - Rendición Mensual {{ $rendicion->codigo_rendicion }}</p>
    </div>
</body>
</html>
