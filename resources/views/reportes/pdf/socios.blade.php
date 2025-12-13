<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Socios - Sistema APR</title>
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
            padding: 10px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            text-align: center;
            width: 25%;
        }

        .stat-card h3 {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #3b82f6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge.activo {
            background: #d1fae5;
            color: #065f46;
        }

        .badge.inactivo {
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
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE SOCIOS</h1>
        <p>Sistema APR - Generado el {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-card">
                <h3>TOTAL SOCIOS</h3>
                <div class="value">{{ $estadisticas['total'] }}</div>
            </div>
            <div class="stat-card">
                <h3>ACTIVOS</h3>
                <div class="value">{{ $estadisticas['activos'] }}</div>
            </div>
            <div class="stat-card">
                <h3>INACTIVOS</h3>
                <div class="value">{{ $estadisticas['inactivos'] }}</div>
            </div>
            <div class="stat-card">
                <h3>SECTORES</h3>
                <div class="value">{{ $estadisticas['por_sector']->count() }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Socio</th>
                <th>RUT</th>
                <th>Nombre Completo</th>
                <th>Sector</th>
                <th>Teléfono</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($socios as $socio)
            <tr>
                <td><strong>{{ $socio->numero_socio }}</strong></td>
                <td>{{ $socio->rut }}</td>
                <td>{{ $socio->nombre_completo }}</td>
                <td>{{ $socio->sector ?? 'N/A' }}</td>
                <td>{{ $socio->telefono ?? 'N/A' }}</td>
                <td>
                    <span class="badge {{ $socio->estado }}">
                        {{ ucfirst($socio->estado) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    No hay socios para mostrar
                </td>
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
