<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparación de Consumo entre Socios - Sistema APR</title>
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
            width: 20%;
        }

        .stat-card h3 {
            font-size: 9px;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 16px;
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

        table thead {
            background: #3b82f6;
            color: white;
        }

        table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }

        table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        table tbody tr:hover {
            background: #f3f4f6;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .consumo-alto {
            color: #dc2626;
            font-weight: bold;
        }

        .consumo-bajo {
            color: #2563eb;
            font-weight: bold;
        }

        .consumo-normal {
            color: #059669;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 8px;
        }

        .summary-box {
            background: #f0f9ff;
            border: 1px solid #3b82f6;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }

        .summary-box h3 {
            color: #1e40af;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .summary-box p {
            font-size: 9px;
            line-height: 1.6;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comparación de Consumo entre Socios</h1>
        <p>Sistema APR - Reporte Generado el {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="period-info">
        Período de Comparación: {{ $periodo }}
    </div>

    <!-- Estadísticas Resumen -->
    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-card">
                <h3>Socios Comparados</h3>
                <div class="value">{{ count($comparacion) }}</div>
            </div>
            <div class="stat-card">
                <h3>Promedio Grupo</h3>
                <div class="value">{{ number_format($estadisticasComparacion['promedio_grupo'], 2) }}</div>
                <div class="unit">m³</div>
            </div>
            <div class="stat-card">
                <h3>Consumo Total</h3>
                <div class="value">{{ number_format($estadisticasComparacion['total'], 2) }}</div>
                <div class="unit">m³</div>
            </div>
            <div class="stat-card">
                <h3>Consumo Máximo</h3>
                <div class="value">{{ number_format($estadisticasComparacion['maximo'], 2) }}</div>
                <div class="unit">m³</div>
            </div>
            <div class="stat-card">
                <h3>Consumo Mínimo</h3>
                <div class="value">{{ number_format($estadisticasComparacion['minimo'], 2) }}</div>
                <div class="unit">m³</div>
            </div>
        </div>
    </div>

    <!-- Análisis de Desviación -->
    <div class="summary-box">
        <h3>Análisis Estadístico</h3>
        <p>
            <strong>Desviación Estándar:</strong> {{ number_format($estadisticasComparacion['desviacion'], 2) }} m³<br>
            La desviación estándar indica la variabilidad del consumo entre los socios seleccionados.
            Un valor bajo sugiere consumos similares, mientras que un valor alto indica mayor dispersión.
        </p>
    </div>

    <!-- Detalle de Comparación -->
    <h2 class="section-title">Detalle de Comparación</h2>

    <table>
        <thead>
            <tr>
                <th>Socio</th>
                <th>RUT</th>
                <th class="text-center">Lectura Ant.</th>
                <th class="text-center">Lectura Act.</th>
                <th class="text-center">Consumo (m³)</th>
                <th class="text-center">Desviación vs Promedio</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($comparacion as $item)
                @php
                    $desviacion = $item->consumo_m3 - $estadisticasComparacion['promedio_grupo'];
                    $porcentaje = $estadisticasComparacion['promedio_grupo'] > 0
                        ? ($desviacion / $estadisticasComparacion['promedio_grupo']) * 100
                        : 0;

                    if ($item->consumo_m3 > $estadisticasComparacion['promedio_grupo'] * 1.2) {
                        $consumoClass = 'consumo-alto';
                    } elseif ($item->consumo_m3 < $estadisticasComparacion['promedio_grupo'] * 0.8) {
                        $consumoClass = 'consumo-bajo';
                    } else {
                        $consumoClass = 'consumo-normal';
                    }
                @endphp
                <tr>
                    <td><strong>{{ $item->socio->nombre_completo }}</strong></td>
                    <td>{{ $item->socio->rut }}</td>
                    <td class="text-center">{{ number_format($item->lectura_anterior, 2) }}</td>
                    <td class="text-center">{{ number_format($item->lectura_actual, 2) }}</td>
                    <td class="text-center {{ $consumoClass }}">{{ number_format($item->consumo_m3, 2) }} m³</td>
                    <td class="text-center">
                        @if($desviacion > 0)
                            <span class="badge badge-warning">
                                +{{ number_format(abs($desviacion), 2) }} m³ (+{{ number_format(abs($porcentaje), 1) }}%)
                            </span>
                        @elseif($desviacion < 0)
                            <span class="badge badge-info">
                                -{{ number_format(abs($desviacion), 2) }} m³ (-{{ number_format(abs($porcentaje), 1) }}%)
                            </span>
                        @else
                            <span class="badge badge-success">En promedio</span>
                        @endif
                    </td>
                    <td>
                        @if($item->anomalia == 'normal')
                            <span class="badge badge-success">Normal</span>
                        @elseif($item->anomalia == 'alto')
                            <span class="badge badge-warning">Alto</span>
                        @elseif($item->anomalia == 'bajo')
                            <span class="badge badge-info">Bajo</span>
                        @elseif($item->anomalia == 'cero')
                            <span class="badge badge-danger">Sin Consumo</span>
                        @else
                            <span class="badge badge-success">Normal</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #f3f4f6; font-weight: bold;">
                <td colspan="4" class="text-right"><strong>Promedio del Grupo:</strong></td>
                <td class="text-center" style="color: #3b82f6;">{{ number_format($estadisticasComparacion['promedio_grupo'], 2) }} m³</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <!-- Observaciones -->
    <div class="summary-box" style="margin-top: 25px;">
        <h3>Observaciones</h3>
        <p>
            • Socios con consumo <strong>por encima del promedio (+20%)</strong> están marcados en <span class="consumo-alto">rojo</span>.<br>
            • Socios con consumo <strong>por debajo del promedio (-20%)</strong> están marcados en <span class="consumo-bajo">azul</span>.<br>
            • Socios con consumo <strong>dentro del rango normal (±20%)</strong> están marcados en <span class="consumo-normal">verde</span>.<br>
            • La desviación vs promedio indica cuánto se aleja cada socio del consumo promedio del grupo.
        </p>
    </div>

    <div class="footer">
        Sistema APR - Comparación de Consumo entre Socios | Generado el {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
