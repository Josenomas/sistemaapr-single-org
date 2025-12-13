<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Consumo - Sistema APR</title>
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
            width: 25%;
        }

        .stat-card h3 {
            font-size: 9px;
            color: #666;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #3b82f6;
        }

        .stat-card .unit {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
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

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge.normal {
            background: #d1fae5;
            color: #065f46;
        }

        .badge.anomalia {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #999;
        }

        .anomalias-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .anomalia-row {
            display: table-row;
        }

        .anomalia-card {
            display: table-cell;
            padding: 10px;
            background: #fef3c7;
            border: 1px solid #fbbf24;
            text-align: center;
            width: 33.33%;
        }

        .anomalia-card h4 {
            font-size: 8px;
            color: #92400e;
            margin-bottom: 5px;
        }

        .anomalia-card .value {
            font-size: 16px;
            font-weight: bold;
            color: #b45309;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE CONSUMO</h1>
        <p>Sistema APR - Generado el {{ date('d/m/Y H:i') }}</p>
    </div>

    @if($mes)
    <div class="period-info">
        Período: {{ \Carbon\Carbon::parse($mes . '-01')->format('F Y') }}
    </div>
    @endif

    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-card">
                <h3>CONSUMO TOTAL</h3>
                <div class="value">{{ number_format($estadisticas['consumo_total'], 2, ',', '.') }}</div>
                <div class="unit">m³</div>
            </div>
            <div class="stat-card">
                <h3>CONSUMO PROMEDIO</h3>
                <div class="value">{{ number_format($estadisticas['consumo_promedio'], 2, ',', '.') }}</div>
                <div class="unit">m³</div>
            </div>
            <div class="stat-card">
                <h3>CONSUMO MÁXIMO</h3>
                <div class="value">{{ number_format($estadisticas['consumo_maximo'], 2, ',', '.') }}</div>
                <div class="unit">m³</div>
            </div>
            <div class="stat-card">
                <h3>CONSUMO MÍNIMO</h3>
                <div class="value">{{ number_format($estadisticas['consumo_minimo'], 2, ',', '.') }}</div>
                <div class="unit">m³</div>
            </div>
        </div>
    </div>

    <h2 class="section-title">ANOMALÍAS DETECTADAS</h2>
    <div class="anomalias-grid">
        <div class="anomalia-row">
            <div class="anomalia-card">
                <h4>CONSUMO CERO</h4>
                <div class="value">{{ $estadisticas['anomalias']['cero'] ?? 0 }}</div>
            </div>
            <div class="anomalia-card">
                <h4>ALTO CONSUMO</h4>
                <div class="value">{{ $estadisticas['anomalias']['alto'] ?? 0 }}</div>
            </div>
            <div class="anomalia-card">
                <h4>LECTURA INFERIOR</h4>
                <div class="value">{{ $estadisticas['anomalias']['inferior'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <h2 class="section-title">DISTRIBUCIÓN POR RANGOS DE CONSUMO</h2>
    <table>
        <thead>
            <tr>
                <th>Rango (m³)</th>
                <th class="text-center">Cantidad de Socios</th>
                <th class="text-right">Consumo Total (m³)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rangos = [
                    '0-10' => ['min' => 0, 'max' => 10],
                    '10-20' => ['min' => 10, 'max' => 20],
                    '20-30' => ['min' => 20, 'max' => 30],
                    '30-50' => ['min' => 30, 'max' => 50],
                    '50+' => ['min' => 50, 'max' => 99999],
                ];
            @endphp
            @foreach($rangos as $nombre => $rango)
                @php
                    $enRango = $consumos->filter(function($c) use ($rango) {
                        return $c->consumo_m3 >= $rango['min'] && $c->consumo_m3 < $rango['max'];
                    });
                    $cantidad = $enRango->count();
                    $total = $enRango->sum('consumo_m3');
                @endphp
                <tr>
                    <td>{{ $nombre }}</td>
                    <td class="text-center">{{ $cantidad }}</td>
                    <td class="text-right">{{ number_format($total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="section-title">DETALLE DE CONSUMOS</h2>
    <table>
        <thead>
            <tr>
                <th>N° Socio</th>
                <th>Nombre</th>
                <th>Sector</th>
                <th class="text-right">Lect. Anterior</th>
                <th class="text-right">Lect. Actual</th>
                <th class="text-right">Consumo (m³)</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consumos as $historial)
            <tr>
                <td><strong>{{ $historial->socio->numero_socio ?? 'N/A' }}</strong></td>
                <td>{{ $historial->socio->nombre_completo ?? 'N/A' }}</td>
                <td>{{ $historial->socio->sector ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($historial->lectura_anterior, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($historial->lectura_actual, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($historial->consumo_m3, 2, ',', '.') }}</td>
                <td class="text-center">
                    @if($historial->anomalia && in_array($historial->anomalia, ['alto', 'bajo', 'cero']))
                        <span class="badge anomalia">Anomalía</span>
                    @else
                        <span class="badge normal">Normal</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No hay consumos para mostrar</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema APR - Reporte generado automáticamente</p>
        <p>Página 1 de 1</p>
    </div>
</body>
</html>
