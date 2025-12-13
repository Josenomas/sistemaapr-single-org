<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Financiero - Sistema APR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #3b82f6;
        }

        .header h1 {
            color: #3b82f6;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 10px;
        }

        .period-info {
            text-align: center;
            background: #f3f4f6;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: bold;
            color: #374151;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-row {
            display: table-row;
        }

        .stat-card {
            display: table-cell;
            padding: 15px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            text-align: center;
            width: 33.33%;
        }

        .stat-card h3 {
            font-size: 9px;
            color: #666;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
        }

        .stat-card.success .value {
            color: #10b981;
        }

        .stat-card.danger .value {
            color: #ef4444;
        }

        .stat-card.primary .value {
            color: #3b82f6;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        thead {
            background: #3b82f6;
            color: white;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
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

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #999;
        }

        .two-columns {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }

        .column:first-child {
            padding-right: 2%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE FINANCIERO</h1>
        <p>Sistema APR - Generado el {{ date('d/m/Y H:i') }}</p>
    </div>

    @if($fecha_inicio && $fecha_fin)
    <div class="period-info">
        Período: {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
    </div>
    @endif

    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-card success">
                <h3>TOTAL INGRESOS</h3>
                <div class="value">${{ number_format($estadisticas['total_ingresos'], 0, ',', '.') }}</div>
            </div>
            <div class="stat-card danger">
                <h3>TOTAL EGRESOS</h3>
                <div class="value">${{ number_format($estadisticas['total_egresos'], 0, ',', '.') }}</div>
            </div>
            <div class="stat-card primary">
                <h3>BALANCE</h3>
                <div class="value">${{ number_format($estadisticas['balance'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="two-columns">
        <div class="column">
            <h2 class="section-title">INGRESOS POR MÉTODO DE PAGO</h2>
            <table>
                <thead>
                    <tr>
                        <th>Método</th>
                        <th class="text-right">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estadisticas['ingresos_por_metodo'] as $metodo => $monto)
                    <tr>
                        <td>{{ ucfirst($metodo) }}</td>
                        <td class="text-right">${{ number_format($monto, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center">Sin datos</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="column">
            <h2 class="section-title">EGRESOS POR TIPO</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th class="text-right">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estadisticas['egresos_por_tipo'] as $tipo => $monto)
                    <tr>
                        <td>{{ ucfirst($tipo) }}</td>
                        <td class="text-right">${{ number_format($monto, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center">Sin datos</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <h2 class="section-title">DETALLE DE INGRESOS</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>N° Comprobante</th>
                <th>Socio</th>
                <th>Método</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ingresos as $pago)
            <tr>
                <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                <td>{{ $pago->numero_comprobante ?? '-' }}</td>
                <td>{{ $pago->socio->nombre_completo ?? 'N/A' }}</td>
                <td>{{ ucfirst($pago->metodo_pago) }}</td>
                <td class="text-right">${{ number_format($pago->monto_pagado, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No hay ingresos en este período</td>
            </tr>
            @endforelse
            @if($ingresos->count() > 0)
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL:</td>
                <td class="text-right">${{ number_format($estadisticas['total_ingresos'], 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <h2 class="section-title">DETALLE DE EGRESOS</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($egresos as $egreso)
            <tr>
                <td>{{ \Carbon\Carbon::parse($egreso->fecha_compra)->format('d/m/Y') }}</td>
                <td>{{ ucfirst($egreso->tipo_compra ?? 'General') }}</td>
                <td>{{ $egreso->descripcion ?? '-' }}</td>
                <td class="text-right">${{ number_format($egreso->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No hay egresos en este período</td>
            </tr>
            @endforelse
            @if($egresos->count() > 0)
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL:</td>
                <td class="text-right">${{ number_format($estadisticas['total_egresos'], 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema APR - Reporte generado automáticamente</p>
        <p>Página 1 de 1</p>
    </div>
</body>
</html>
