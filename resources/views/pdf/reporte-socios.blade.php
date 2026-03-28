<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Socios</title>
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
            margin-bottom: 15px;
            background: #f9fafb;
            padding: 10px;
            border-radius: 4px;
        }

        .info-section p {
            margin-bottom: 5px;
        }

        .info-section strong {
            color: #667eea;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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

        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
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
        <h2>Reporte de Socios</h2>
    </div>

    <div class="info-section">
        <p><strong>Fecha de generación:</strong> {{ date('d/m/Y H:i') }}</p>
        <p><strong>Total de socios:</strong> {{ count($socios) }}</p>
        @if(isset($filtros) && count($filtros) > 0)
            <p><strong>Filtros aplicados:</strong>
                @foreach($filtros as $key => $value)
                    {{ ucfirst($key) }}: {{ $value }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Socio</th>
                <th>RUT</th>
                <th>Nombre Completo</th>
                <th>Dirección</th>
                <th>Sector</th>
                <th>Teléfono</th>
                <th>Tipo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($socios as $socio)
            <tr>
                <td>{{ $socio->numero_socio }}</td>
                <td>{{ $socio->rut }}</td>
                <td>{{ $socio->nombre_completo }}</td>
                <td>{{ $socio->direccion }}</td>
                <td>{{ $socio->sector ?? '-' }}</td>
                <td>{{ $socio->telefono ?? '-' }}</td>
                <td>{{ ucfirst($socio->tipo_cliente) }}</td>
                <td>
                    @if($socio->estado == 'activo')
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">{{ ucfirst($socio->estado) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ $organizacion->nombre_apr }} - Sistema APR</p>
        <p>Generado automáticamente el {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
