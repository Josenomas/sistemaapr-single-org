<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Pago - {{ $pago->numero_recibo }}</title>
    <style>
        @page {
            size: letter;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
        }

        .comprobante {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #000;
        }

        /* Título principal */
        .titulo-principal {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 25px;
            padding: 15px 0;
            border-bottom: 3px solid #000;
            letter-spacing: 2px;
        }

        /* Tablas estilo formulario */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: top;
        }

        .label-cell {
            background: #e0e0e0;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
            width: 35%;
        }

        .value-cell {
            background: #fff;
            font-size: 11pt;
            min-height: 30px;
        }

        .header-row {
            background: #d0d0d0;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
            text-transform: uppercase;
        }

        /* Sección de encabezado */
        .seccion-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            padding-right: 10px;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            padding-left: 10px;
        }

        /* Monto destacado */
        .monto-destacado {
            background: #f0f0f0;
            border: 2px solid #000;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .monto-label {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .monto-valor {
            font-size: 36pt;
            font-weight: bold;
        }

        /* Tabla de detalle */
        .tabla-detalle {
            margin: 20px 0;
        }

        .tabla-detalle td {
            text-align: center;
            font-size: 10pt;
        }

        .tabla-detalle .total-row {
            background: #e0e0e0;
            font-weight: bold;
        }

        /* Sección de firma */
        .seccion-firma {
            margin-top: 40px;
        }

        .firma-box {
            border: 1px solid #000;
            min-height: 80px;
            padding: 10px;
            text-align: center;
        }

        .firma-label {
            background: #d0d0d0;
            font-weight: bold;
            font-size: 9pt;
            padding: 5px;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 9pt;
        }

        @media print {
            body {
                background: white;
            }
            .comprobante {
                border: 2px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="comprobante">
        <!-- Título Principal -->
        <div class="titulo-principal">COMPROBANTE DE PAGO</div>

        <!-- Sección Header: Info del comprobante y fecha -->
        <div class="seccion-header">
            <div class="header-left">
                <table>
                    <tr>
                        <td class="label-cell">COMPROBANTE NRO:</td>
                        <td class="value-cell" style="font-weight: bold; font-size: 12pt;">{{ $pago->numero_recibo }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">FUNCIONAMIENTO MES:</td>
                        <td class="value-cell">{{ strtoupper($pago->boleta->mes_texto) }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">AÑO:</td>
                        <td class="value-cell">{{ date('Y', strtotime($pago->boleta->mes . '-01')) }}</td>
                    </tr>
                </table>
            </div>
            <div class="header-right">
                <table>
                    <tr>
                        <td class="label-cell">FECHA DE IMPRESIÓN:</td>
                        <td class="value-cell">{{ now()->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">HORA DE IMPRESIÓN:</td>
                        <td class="value-cell">{{ now()->format('H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Beneficiario -->
        <table>
            <tr>
                <td class="label-cell">BENEFICIARIO:</td>
                <td class="value-cell" colspan="3" style="font-weight: bold; font-size: 12pt;">{{ strtoupper($pago->socio->nombre_completo) }}</td>
            </tr>
            <tr>
                <td class="label-cell">N° SOCIO:</td>
                <td class="value-cell">{{ $pago->socio->numero_socio }}</td>
                <td class="label-cell">RUT:</td>
                <td class="value-cell">{{ $pago->socio->rut }}</td>
            </tr>
            <tr>
                <td class="label-cell">DIRECCIÓN:</td>
                <td class="value-cell" colspan="3">{{ $pago->socio->direccion ?? 'No especificada' }}</td>
            </tr>
            <tr>
                <td class="label-cell">CONCEPTO:</td>
                <td class="value-cell" colspan="3">PAGO DE BOLETA {{ $pago->boleta->numero_boleta }} - PERÍODO {{ $pago->boleta->mes_texto }}</td>
            </tr>
        </table>

        <!-- Transacción -->
        <table>
            <tr>
                <td class="label-cell">FECHA DE PAGO:</td>
                <td class="value-cell">{{ $pago->fecha_pago_formateada }}</td>
                <td class="label-cell">MÉTODO DE PAGO:</td>
                <td class="value-cell" style="font-weight: bold;">{{ strtoupper($pago->metodo_pago) }}</td>
            </tr>
            @if($pago->numero_comprobante)
            <tr>
                <td class="label-cell">N° COMPROBANTE/TRANSFERENCIA:</td>
                <td class="value-cell" colspan="3" style="font-weight: bold;">{{ $pago->numero_comprobante }}</td>
            </tr>
            @endif
        </table>

        <!-- Monto Destacado -->
        <div class="monto-destacado">
            <div class="monto-label">MONTO TOTAL PAGADO</div>
            <div class="monto-valor">{{ $pago->monto_pagado_formateado }}</div>
        </div>

        <!-- Tabla de Detalle -->
        <table class="tabla-detalle">
            <tr class="header-row">
                <td style="width: 15%;">N° BOLETA</td>
                <td style="width: 40%;">CONCEPTO DE PAGO</td>
                <td style="width: 20%;">PERÍODO</td>
                <td style="width: 25%;">MONTO</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">{{ $pago->boleta->numero_boleta }}</td>
                <td>SERVICIO DE AGUA POTABLE</td>
                <td>{{ $pago->boleta->mes_texto }}</td>
                <td style="font-weight: bold;">{{ $pago->monto_pagado_formateado }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" style="text-align: right; padding-right: 15px;">TOTAL:</td>
                <td style="font-size: 12pt;">{{ $pago->monto_pagado_formateado }}</td>
            </tr>
        </table>

        <!-- Observaciones -->
        @if($pago->observaciones)
        <table>
            <tr>
                <td class="label-cell" style="width: 25%;">OBSERVACIONES:</td>
                <td class="value-cell">{{ $pago->observaciones }}</td>
            </tr>
        </table>
        @endif

        <!-- Información adicional -->
        <table style="margin-top: 20px;">
            <tr>
                <td class="label-cell">TOTAL BOLETA:</td>
                <td class="value-cell">{{ $pago->boleta->total_formateado }}</td>
                <td class="label-cell">ESTADO BOLETA:</td>
                <td class="value-cell" style="font-weight: bold; color: #059669;">{{ strtoupper($pago->boleta->estado) }}</td>
            </tr>
        </table>

        <!-- Sección de Firmas -->
        <div class="seccion-firma">
            <table>
                <tr>
                    <td style="width: 50%; padding: 5px;">
                        <div class="firma-label">APROBADO POR:</div>
                        <div class="firma-box"></div>
                    </td>
                    <td style="width: 50%; padding: 5px;">
                        <div class="firma-label">SELLO DE LA EMPRESA:</div>
                        <div class="firma-box"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 5px;">
                        <div class="firma-label">ELABORADO POR:</div>
                        <div class="firma-box">
                            {{ $pago->usuarioRegistro->nombre_usuario ?? $pago->usuarioRegistro->name ?? 'SISTEMA APR' }}
                            <br>
                            <small>Fecha: {{ $pago->fecha_creacion->format('d/m/Y H:i') }}</small>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>SISTEMA APR - AGUA POTABLE RURAL</strong><br>
            Este documento certifica el pago realizado. Conserve este comprobante para cualquier consulta futura.<br>
            Documento generado el {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
