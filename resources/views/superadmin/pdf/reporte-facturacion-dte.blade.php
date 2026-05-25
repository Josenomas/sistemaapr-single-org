<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Facturación DTE</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        h1 {
            color: #1e3a8a;
            margin-bottom: 10px;
            font-size: 24px;
        }
        h2 {
            color: #3b82f6;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        .fecha-generacion {
            color: #6b7280;
            font-size: 11px;
            margin-top: 5px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }
        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .stat-subtitle {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        thead {
            background-color: #f3f4f6;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        tr:hover {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .text-success {
            color: #10b981;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-primary {
            background-color: #3b82f6;
            color: white;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
        @media print {
            body {
                padding: 0;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Facturación Electrónica (DTE)</h1>
        <div class="fecha-generacion">
            Generado el {{ now()->format('d/m/Y H:i:s') }}
            @if(isset($filtros['fecha_desde']) || isset($filtros['fecha_hasta']))
                <br>
                Período:
                {{ isset($filtros['fecha_desde']) ? \Carbon\Carbon::parse($filtros['fecha_desde'])->format('d/m/Y') : 'Inicio' }}
                -
                {{ isset($filtros['fecha_hasta']) ? \Carbon\Carbon::parse($filtros['fecha_hasta'])->format('d/m/Y') : 'Hoy' }}
            @endif
        </div>
    </div>

    <!-- Resumen General -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total DTEs</div>
            <div class="stat-value">{{ number_format($resumenGeneral['total_dtes_emitidos']) }}</div>
            <div class="stat-subtitle">Histórico</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Ingresos Totales</div>
            <div class="stat-value">${{ number_format($resumenGeneral['ingresos_totales'] / 1000000, 1) }}M</div>
            <div class="stat-subtitle">Millones de pesos</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Este Mes</div>
            <div class="stat-value">${{ number_format($resumenGeneral['ingresos_este_mes'] / 1000, 0) }}K</div>
            <div class="stat-subtitle">Miles de pesos</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Adopción DTE</div>
            <div class="stat-value">{{ $resumenGeneral['porcentaje_adopcion'] }}%</div>
            <div class="stat-subtitle">{{ $resumenGeneral['organizaciones_con_dte'] }} de {{ $resumenGeneral['total_organizaciones'] }} APRs</div>
        </div>
    </div>

    <!-- Top 10 Organizaciones -->
    <h2>Top 10 Organizaciones por Facturación</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Organización</th>
                <th class="text-center">Total DTEs</th>
                <th class="text-right">Ingresos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($top10Organizaciones as $index => $org)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="fw-bold">{{ $org->nombre_organizacion }}</td>
                <td class="text-center">
                    <span class="badge badge-primary">{{ $org->total_dtes }}</span>
                </td>
                <td class="text-right text-success fw-bold">
                    ${{ number_format($org->ingresos_totales, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Facturación Detallada -->
    <h2>Facturación Detallada por Organización</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Organización</th>
                <th class="text-center">Boletas</th>
                <th class="text-center">Facturas</th>
                <th class="text-center">NC</th>
                <th class="text-center">ND</th>
                <th class="text-right">Ingresos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facturacionPorOrg->take(50) as $index => $org)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $org->nombre_organizacion }}</td>
                <td class="text-center">{{ $org->total_boletas }}</td>
                <td class="text-center">{{ $org->total_facturas }}</td>
                <td class="text-center">{{ $org->total_nc }}</td>
                <td class="text-center">{{ $org->total_nd }}</td>
                <td class="text-right text-success fw-bold">
                    ${{ number_format($org->ingresos_totales, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema de Gestión de APR - Reporte Generado Automáticamente
    </div>
</body>
</html>
