<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Mensual - {{ \Carbon\Carbon::parse($mes . '-01')->locale('es')->isoFormat('MMMM YYYY') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.4;
        }

        .header {
            background: #1e3a8a;
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
        }

        .resumen {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .resumen-item {
            display: table-cell;
            width: 25%;
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }

        .resumen-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }

        .resumen-value {
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        .seccion-titulo {
            background: #f3f4f6;
            border: 1px solid #000;
            padding: 8px;
            font-weight: bold;
            font-size: 11px;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
        }

        .metodos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .metodos-table th,
        .metodos-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .metodos-table th {
            background: #e5e7eb;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .metodos-table td {
            font-size: 10px;
        }

        .metodos-table .text-right {
            text-align: right;
        }

        .dias-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .dia-row {
            display: table-row;
            border-bottom: 1px solid #e5e7eb;
        }

        .dia-row.con-transacciones {
            background: #f0fdf4;
        }

        .dia-row.sin-transacciones {
            background: #f9fafb;
            opacity: 0.7;
        }

        .dia-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
        }

        .dia-fecha {
            width: 15%;
            font-weight: bold;
            font-size: 10px;
        }

        .dia-info {
            width: 85%;
            font-size: 9px;
        }

        .transaccion {
            padding: 4px 0;
            border-bottom: 1px dotted #ccc;
        }

        .transaccion:last-child {
            border-bottom: none;
        }

        .transaccion-header {
            font-weight: bold;
            color: #1e3a8a;
        }

        .transaccion-detalles {
            color: #666;
            margin-top: 2px;
        }

        .dia-total {
            margin-top: 6px;
            padding-top: 6px;
            border-top: 2px solid #10b981;
            font-weight: bold;
            color: #10b981;
        }

        .sin-transacciones-text {
            color: #999;
            font-style: italic;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            padding: 10px 0;
            border-top: 1px solid #ccc;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>REPORTE MENSUAL DE INGRESOS</h1>
        <p>AGUA POTABLE RURAL PITRELAHUE</p>
        <p>{{ \Carbon\Carbon::parse($mes . '-01')->locale('es')->isoFormat('MMMM YYYY') }}</p>
    </div>

    <!-- Resumen General -->
    <div class="resumen">
        <div class="resumen-item">
            <div class="resumen-label">Período</div>
            <div class="resumen-value">{{ \Carbon\Carbon::parse($mes . '-01')->format('m/Y') }}</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-label">Total Transacciones</div>
            <div class="resumen-value">{{ $totalPagos }}</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-label">Total Recaudado</div>
            <div class="resumen-value">${{ number_format($totalMes, 0, ',', '.') }}</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-label">Promedio Diario</div>
            <div class="resumen-value">${{ number_format($totalMes / count($diasDelMes), 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Totales por Método de Pago -->
    <div class="seccion-titulo">
        <i class="fas fa-chart-pie"></i> Totales por Método de Pago
    </div>

    <table class="metodos-table">
        <thead>
            <tr>
                <th>Método de Pago</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Monto Total</th>
                <th class="text-right">% del Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($totalesPorMetodo as $metodo)
                <tr>
                    <td>{{ ucfirst($metodo->metodo_pago) }}</td>
                    <td class="text-right">{{ $metodo->cantidad }}</td>
                    <td class="text-right"><strong>${{ number_format($metodo->total, 0, ',', '.') }}</strong></td>
                    <td class="text-right">{{ $totalMes > 0 ? number_format(($metodo->total / $totalMes) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
            @if($totalesPorMetodo->isEmpty())
                <tr>
                    <td colspan="4" style="text-align: center; color: #999;">No hay pagos registrados</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Detalle Diario -->
    <div class="seccion-titulo">
        Detalle Diario del Mes
    </div>

    <div class="dias-grid">
        @php
            $contador = 0;
        @endphp

        @foreach($diasDelMes as $dia)
            @php
                $contador++;
            @endphp

            <div class="dia-row {{ $dia['cantidad'] > 0 ? 'con-transacciones' : 'sin-transacciones' }}">
                <div class="dia-cell dia-fecha">
                    <strong>{{ $dia['fecha_formateada'] }}</strong><br>
                    <span style="font-size: 8px;">{{ $dia['dia_semana'] }}</span>
                </div>
                <div class="dia-cell dia-info">
                    @if($dia['cantidad'] > 0)
                        @foreach($dia['pagos'] as $pago)
                            <div class="transaccion">
                                <div class="transaccion-header">
                                    {{ $pago->numero_recibo }} - {{ $pago->socio->nombre_completo }}
                                </div>
                                <div class="transaccion-detalles">
                                    Boleta: {{ $pago->boleta->numero_boleta }} |
                                    Método: {{ ucfirst($pago->metodo_pago) }} |
                                    <strong>${{ number_format($pago->monto_pagado, 0, ',', '.') }}</strong>
                                    @if($pago->numero_comprobante)
                                        | Comprobante: {{ $pago->numero_comprobante }}
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="dia-total">
                            TOTAL DEL DÍA: ${{ number_format($dia['total'], 0, ',', '.') }} ({{ $dia['cantidad'] }} transacción{{ $dia['cantidad'] > 1 ? 'es' : '' }})
                        </div>
                    @else
                        <div class="sin-transacciones-text">
                            Sin transacciones registradas
                        </div>
                    @endif
                </div>
            </div>

            @if($contador % 10 == 0 && !$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    </div>

    <!-- Footer -->
    <div class="footer">
        Generado: {{ now()->format('d/m/Y H:i:s') }} | Sistema APR Pitrelahue | Documento generado electrónicamente
    </div>
</body>
</html>
