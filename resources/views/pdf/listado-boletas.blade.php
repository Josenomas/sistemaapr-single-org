<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Boletas</title>
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
            padding: 10px 6px;
            text-align: left;
            font-size: 9px;
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
            padding: 8px 6px;
            font-size: 9px;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 8px;
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

        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 4px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(isset($organizacion->logo))
        <img src="{{ public_path('storage/' . $organizacion->logo) }}" alt="Logo" class="logo">
        @endif
        <h1>{{ $organizacion->nombre_apr }}</h1>
        <h2>Listado de Boletas</h2>
    </div>

    <div class="info-section">
        <p><strong>Fecha de generación:</strong> {{ date('d/m/Y H:i') }}</p>
        <p><strong>Total de boletas:</strong> {{ count($boletas) }}</p>
        @if(isset($periodo))
            <p><strong>Período:</strong> {{ $periodo }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Boleta</th>
                <th>Socio</th>
                <th>Período</th>
                <th>Emisión</th>
                <th>Vencimiento</th>
                <th>Consumo</th>
                <th class="text-right">Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalGeneral = 0;
                $totalPagadas = 0;
                $totalPendientes = 0;
            @endphp
            @foreach($boletas as $boleta)
            <tr>
                <td>{{ $boleta->numero_boleta }}</td>
                <td>{{ $boleta->socio->nombre_completo ?? '-' }}</td>
                <td>{{ $boleta->periodo ? date('m/Y', strtotime($boleta->periodo)) : '-' }}</td>
                <td>{{ date('d/m/Y', strtotime($boleta->fecha_emision)) }}</td>
                <td>{{ date('d/m/Y', strtotime($boleta->fecha_vencimiento)) }}</td>
                <td>{{ $boleta->consumo ?? 0 }} m³</td>
                <td class="text-right">${{ number_format($boleta->total, 0, ',', '.') }}</td>
                <td>
                    @if($boleta->estado == 'pagada')
                        <span class="badge badge-success">Pagada</span>
                        @php $totalPagadas += $boleta->total; @endphp
                    @elseif($boleta->estado == 'pendiente')
                        <span class="badge badge-warning">Pendiente</span>
                        @php $totalPendientes += $boleta->total; @endphp
                    @else
                        <span class="badge badge-danger">{{ ucfirst($boleta->estado) }}</span>
                    @endif
                </td>
            </tr>
            @php $totalGeneral += $boleta->total; @endphp
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total General</div>
                <div class="value">${{ number_format($totalGeneral, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Pagado</div>
                <div class="value" style="color: #059669;">${{ number_format($totalPagadas, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Pendiente</div>
                <div class="value" style="color: #f59e0b;">${{ number_format($totalPendientes, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>{{ $organizacion->nombre_apr }} - Sistema APR</p>
        <p>Generado automáticamente el {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
