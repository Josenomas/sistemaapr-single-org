<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Caja - {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3b82f6;
        }

        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 18px;
            color: #64748b;
            font-weight: normal;
            margin-bottom: 10px;
        }

        .header .fecha {
            font-size: 14px;
            color: #1e40af;
            font-weight: 600;
        }

        .resumen {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .resumen-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            border-left: 3px solid #3b82f6;
            text-align: center;
        }

        .resumen-value {
            font-size: 24px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 3px;
        }

        .resumen-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e40af;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .metodos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 25px;
        }

        .metodo-box {
            background: #f1f5f9;
            padding: 12px;
            border-radius: 6px;
            border-left: 3px solid #10b981;
        }

        .metodo-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .metodo-value {
            font-size: 16px;
            font-weight: 700;
            color: #1e40af;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #3b82f6;
            color: white;
        }

        thead th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody td {
            padding: 8px;
            font-size: 11px;
        }

        tfoot {
            background: #f1f5f9;
            border-top: 3px solid #3b82f6;
        }

        tfoot td {
            padding: 12px 8px;
            font-weight: 700;
            font-size: 12px;
        }

        .total-final {
            color: #1e40af;
            font-size: 16px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-efectivo {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-transferencia {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-cheque {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-debito, .badge-credito {
            background: #e0e7ff;
            color: #3730a3;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #64748b;
        }

        .print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transition: all 0.3s;
        }

        .print-button:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #64748b;
            font-style: italic;
        }

        @media print {
            .print-button {
                display: none;
            }

            @page {
                margin: 15mm;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button">🖨️ Imprimir</button>

    <div class="header">
        <h1>Sistema APR</h1>
        <h2>Reporte de Caja</h2>
        <div class="fecha">
            {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </div>
    </div>

    <div class="resumen">
        <div class="resumen-item">
            <div class="resumen-value">{{ $pagos->count() }}</div>
            <div class="resumen-label">Total Pagos</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-value">${{ number_format($totalDia, 0, ',', '.') }}</div>
            <div class="resumen-label">Total Recaudado</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-value">{{ $totalesPorMetodo->count() }}</div>
            <div class="resumen-label">Métodos de Pago</div>
        </div>
    </div>

    @if($totalesPorMetodo->isNotEmpty())
        <div class="section-title">💳 Totales por Método de Pago</div>
        <div class="metodos-grid">
            @foreach($totalesPorMetodo as $metodo)
                <div class="metodo-box">
                    <div class="metodo-label">
                        @switch($metodo->metodo_pago)
                            @case('efectivo') 💵 Efectivo @break
                            @case('transferencia') 🔄 Transferencia @break
                            @case('cheque') 📝 Cheque @break
                            @case('debito') 💳 Débito @break
                            @case('credito') 💳 Crédito @break
                            @default {{ ucfirst($metodo->metodo_pago) }}
                        @endswitch
                    </div>
                    <div class="metodo-value">${{ number_format($metodo->total, 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>
    @endif

    @if($pagos->count() > 0)
        <div class="section-title">📋 Detalle de Pagos del Día</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>N° Recibo</th>
                    <th>Socio</th>
                    <th>N° Boleta</th>
                    <th>Método</th>
                    <th class="text-right">Monto</th>
                    <th>Comprobante</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $index => $pago)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $pago->numero_recibo }}</strong></td>
                        <td>{{ $pago->socio->nombre_completo }}</td>
                        <td>{{ $pago->boleta->numero_boleta }}</td>
                        <td>
                            <span class="badge badge-{{ $pago->metodo_pago }}">
                                {{ ucfirst($pago->metodo_pago) }}
                            </span>
                        </td>
                        <td class="text-right"><strong>${{ number_format($pago->monto_pagado, 0, ',', '.') }}</strong></td>
                        <td>{{ $pago->numero_comprobante ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">TOTAL DEL DÍA:</td>
                    <td colspan="2" class="text-right">
                        <span class="total-final">${{ number_format($totalDia, 0, ',', '.') }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data">
            📭 No hay pagos registrados para la fecha {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </div>
    @endif

    <div class="footer">
        <p><strong>Sistema APR</strong> - Gestión de Agua Potable Rural</p>
        <p>Reporte generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
