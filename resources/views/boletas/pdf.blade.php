<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta {{ $boleta->numero_boleta }}</title>
    <style>
        @page {
            size: legal; /* Tamaño oficio: 8.5" x 14" (216mm x 356mm) */
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
            padding: 0;
            background: #fff;
        }

        .boleta-container {
            width: 100%;
            max-width: 750px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 0;
        }

        /* Header con banda superior */
        .header-band {
            background: #000;
            color: #fff;
            padding: 8px 15px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            letter-spacing: 1px;
        }

        .header-main {
            padding: 15px;
            border-bottom: 2px solid #000;
        }

        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .empresa-info {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .empresa-info h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .empresa-info p {
            font-size: 9px;
            line-height: 1.3;
            margin: 2px 0;
        }

        .boleta-numero-box {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .boleta-numero-inner {
            border: 3px double #000;
            padding: 10px;
            display: inline-block;
        }

        .boleta-numero-inner .tipo {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .boleta-numero-inner .numero {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Fechas importantes en línea */
        .fechas-importantes {
            background: #f0f0f0;
            padding: 8px 15px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            display: table;
            width: 100%;
        }

        .fecha-item {
            display: table-cell;
            width: 33.33%;
            font-size: 10px;
            padding: 0 5px;
        }

        .fecha-item strong {
            display: block;
            font-size: 8px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .fecha-item span {
            font-weight: bold;
            font-size: 11px;
        }

        /* Datos del cliente */
        .cliente-section {
            padding: 12px 15px;
            border-bottom: 2px solid #000;
            background: #fafafa;
        }

        .cliente-section h2 {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        .cliente-grid {
            display: table;
            width: 100%;
        }

        .cliente-row {
            display: table-row;
        }

        .cliente-label {
            display: table-cell;
            width: 25%;
            padding: 3px 5px 3px 0;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .cliente-value {
            display: table-cell;
            padding: 3px 0;
            font-size: 10px;
        }

        /* Detalle de consumo */
        .consumo-section {
            padding: 15px;
            border-bottom: 2px solid #000;
        }

        .consumo-section h2 {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            text-align: center;
            background: #000;
            color: #fff;
            padding: 5px;
        }

        .detalle-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .detalle-table th {
            background: #e0e0e0;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        .detalle-table td {
            padding: 6px 8px;
            border: 1px solid #000;
            font-size: 10px;
        }

        .detalle-table .text-right {
            text-align: right;
        }

        .detalle-table .text-center {
            text-align: center;
        }

        .detalle-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Totales */
        .totales-section {
            padding: 0;
            background: #f5f5f5;
        }

        .totales-grid {
            display: table;
            width: 100%;
        }

        .totales-left {
            display: table-cell;
            width: 50%;
            padding: 15px;
            vertical-align: top;
            border-right: 1px solid #ccc;
        }

        .totales-right {
            display: table-cell;
            width: 50%;
            padding: 15px;
            vertical-align: top;
        }

        .total-row {
            padding: 5px 0;
            border-bottom: 1px dotted #999;
            display: table;
            width: 100%;
        }

        .total-row .label {
            display: table-cell;
            font-size: 10px;
            text-transform: uppercase;
        }

        .total-row .value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            font-size: 11px;
        }

        .total-final {
            background: #000;
            color: #fff;
            padding: 10px;
            margin-top: 10px;
            display: table;
            width: 100%;
        }

        .total-final .label {
            display: table-cell;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .total-final .value {
            display: table-cell;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Alerta de vencimiento */
        .alerta-vencida {
            background: #000;
            color: #fff;
            padding: 10px 15px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            letter-spacing: 1px;
            border-top: 3px solid #000;
            border-bottom: 3px solid #000;
        }

        /* Observaciones */
        .observaciones-section {
            padding: 12px 15px;
            border-top: 1px solid #000;
            background: #fffacd;
        }

        .observaciones-section h3 {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .observaciones-section p {
            font-size: 10px;
            line-height: 1.4;
        }

        /* Footer */
        .footer {
            padding: 12px 15px;
            border-top: 2px solid #000;
            background: #fafafa;
        }

        .footer-info {
            font-size: 8px;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .footer-info strong {
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
            font-size: 9px;
        }

        .footer-note {
            text-align: center;
            font-size: 7px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            margin-top: 8px;
        }

        /* Línea de corte */
        .linea-corte {
            border-top: 1px dashed #000;
            margin: 15px 0;
            padding-top: 10px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        .estado-badge {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #000;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Salto de página */
        .page-break {
            page-break-before: always;
            page-break-after: always;
        }

        /* Comprobante en segunda página */
        .comprobante-page {
            page-break-before: always;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="boleta-container">
        <!-- Banda superior -->
        <div class="header-band">
            BOLETA DE CONSUMO - AGUA POTABLE RURAL
        </div>

        <!-- Header principal -->
        <div class="header-main">
            <div class="header-top">
                <div class="empresa-info">
                    <h1>SISTEMA APR</h1>
                    <p><strong>AGUA POTABLE RURAL</strong></p>
                    <p>ASOCIACIÓN DE AGUA POTABLE RURAL</p>
                    <p>Teléfono: (XX) XXXX-XXXX | Email: contacto@apr.cl</p>
                </div>
                <div class="boleta-numero-box">
                    <div class="boleta-numero-inner">
                        <div class="tipo">BOLETA Nº</div>
                        <div class="numero">{{ $boleta->numero_boleta }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fechas importantes -->
        <div class="fechas-importantes">
            <div class="fecha-item">
                <strong>Período Facturado:</strong>
                <span>{{ $boleta->mes_texto }}</span>
            </div>
            <div class="fecha-item" style="border-left: 1px solid #000; border-right: 1px solid #000;">
                <strong>Fecha Emisión:</strong>
                <span>{{ $boleta->fecha_emision_formateada }}</span>
            </div>
            <div class="fecha-item">
                <strong>Fecha Vencimiento:</strong>
                <span>{{ $boleta->fecha_vencimiento_formateada }}</span>
            </div>
        </div>

        <!-- Alerta si está vencida -->
        @if($boleta->estado === 'vencida')
        <div class="alerta-vencida">
            *** BOLETA VENCIDA - {{ $boleta->dias_atraso }} DÍAS DE ATRASO ***
        </div>
        @endif

        <!-- Datos del cliente -->
        <div class="cliente-section">
            <h2>Datos del Cliente</h2>
            <div class="cliente-grid">
                <div class="cliente-row">
                    <div class="cliente-label">N° Socio:</div>
                    <div class="cliente-value">{{ $boleta->socio->numero_socio }}</div>
                    <div class="cliente-label" style="padding-left: 20px;">RUT:</div>
                    <div class="cliente-value">{{ $boleta->socio->rut }}</div>
                </div>
                <div class="cliente-row">
                    <div class="cliente-label">Nombre:</div>
                    <div class="cliente-value" colspan="3">{{ $boleta->socio->nombre_completo }}</div>
                </div>
                <div class="cliente-row">
                    <div class="cliente-label">Dirección:</div>
                    <div class="cliente-value" colspan="3">{{ $boleta->socio->direccion ?? 'No especificada' }}</div>
                </div>
                @if($boleta->socio->telefono)
                <div class="cliente-row">
                    <div class="cliente-label">Teléfono:</div>
                    <div class="cliente-value" colspan="3">{{ $boleta->socio->telefono }}</div>
                </div>
                @endif
                <div class="cliente-row">
                    <div class="cliente-label">Estado:</div>
                    <div class="cliente-value" colspan="3">
                        <span class="estado-badge">{{ $boleta->estado_texto }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Consumo (últimos 12 meses) -->
        @if($historialConsumo && $historialConsumo->count() > 0)
        <div style="padding: 15px; border-bottom: 2px solid #000; background: #f9f9f9;">
            <h2 style="font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; text-align: center; background: #000; color: #fff; padding: 5px;">
                Historial de Consumo (Últimos 12 Meses)
            </h2>

            <div style="position: relative; height: 120px; margin: 10px 0;">
                <?php
                    $maxConsumo = $historialConsumo->max('consumo') ?: 10;
                    $anchoTotal = 100;
                    $anchoBarra = ($anchoTotal / max($historialConsumo->count(), 1)) - 1;
                ?>

                <!-- Eje Y (valores) -->
                <div style="position: absolute; left: 0; top: 0; bottom: 20px; width: 30px; border-right: 1px solid #000;">
                    <div style="position: absolute; top: 0; right: 5px; font-size: 7px;">{{ number_format($maxConsumo, 0) }} m³</div>
                    <div style="position: absolute; top: 50%; right: 5px; font-size: 7px; transform: translateY(-50%);">{{ number_format($maxConsumo/2, 0) }} m³</div>
                    <div style="position: absolute; bottom: 0; right: 5px; font-size: 7px;">0 m³</div>
                </div>

                <!-- Área de barras -->
                <div style="position: absolute; left: 35px; right: 0; top: 0; bottom: 20px; border-bottom: 2px solid #000;">
                    @foreach($historialConsumo as $index => $item)
                        <?php
                            $altura = $maxConsumo > 0 ? ($item['consumo'] / $maxConsumo) * 100 : 0;
                            $left = ($index * ($anchoBarra + 1));
                        ?>
                        <div style="position: absolute; left: {{ $left }}%; bottom: 0; width: {{ $anchoBarra }}%; height: {{ $altura }}%; background: {{ $item['mes'] == $boleta->mes ? '#000' : '#666' }}; border: 1px solid #000;">
                            <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); font-size: 7px; font-weight: bold; white-space: nowrap;">
                                {{ number_format($item['consumo'], 1) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Eje X (meses) -->
                <div style="position: absolute; left: 35px; right: 0; bottom: 0; height: 20px; display: flex; justify-content: space-between;">
                    @foreach($historialConsumo as $item)
                        <div style="font-size: 6px; text-align: center; flex: 1; transform: rotate(-45deg); transform-origin: top left; margin-top: 8px;">
                            {{ substr($item['mes_texto'], 0, 3) }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div style="text-align: center; font-size: 8px; color: #666; margin-top: 5px;">
                * La barra negra indica el período actual
            </div>
        </div>
        @endif

        <!-- Detalle de consumo -->
        <div class="consumo-section">
            <h2>Detalle de Consumo y Cargos</h2>

            <!-- Lecturas del medidor -->
            @if($boleta->id_lectura && $boleta->lectura)
            <div style="background: #fffbeb; border: 1px solid #d97706; padding: 8px; margin-bottom: 10px; font-size: 9px;">
                <strong>📊 LECTURAS DEL MEDIDOR:</strong> &nbsp;&nbsp;
                Lectura Anterior: <strong>{{ number_format($boleta->lectura->lectura_anterior, 2, ',', '.') }} m³</strong> &nbsp;|&nbsp;
                Lectura Actual: <strong>{{ number_format($boleta->lectura->lectura_actual, 2, ',', '.') }} m³</strong> &nbsp;|&nbsp;
                Consumo: <strong>{{ number_format($boleta->consumo_m3, 2, ',', '.') }} m³</strong>
            </div>
            @endif

            <table class="detalle-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">DESCRIPCIÓN</th>
                        <th class="text-center" style="width: 20%;">CANTIDAD</th>
                        <th class="text-center" style="width: 15%;">UNIDAD</th>
                        <th class="text-right" style="width: 15%;">MONTO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CONSUMO DE AGUA POTABLE</td>
                        <td class="text-center">{{ number_format($boleta->consumo_m3, 2, ',', '.') }}</td>
                        <td class="text-center">m³</td>
                        <td class="text-right"><strong>{{ $boleta->cargo_consumo_formateado }}</strong></td>
                    </tr>
                    <tr>
                        <td>CARGO FIJO MENSUAL</td>
                        <td class="text-center">1</td>
                        <td class="text-center">mes</td>
                        <td class="text-right"><strong>{{ $boleta->cargo_fijo_formateado }}</strong></td>
                    </tr>
                    @if($boleta->otros_cargos > 0)
                    <tr>
                        <td>OTROS CARGOS Y SERVICIOS</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-right"><strong>{{ $boleta->otros_cargos_formateado }}</strong></td>
                    </tr>
                    @endif
                    @if($boleta->descuentos > 0)
                    <tr style="background: #e8f5e9;">
                        <td>DESCUENTOS APLICADOS</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-right"><strong>-{{ $boleta->descuentos_formateado }}</strong></td>
                    </tr>
                    @endif
                    @if($boleta->subsidio > 0)
                    <tr style="background: #e3f2fd;">
                        <td>SUBSIDIO APLICADO</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-right"><strong>-{{ $boleta->subsidio_formateado }}</strong></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="totales-section">
            <div class="totales-grid">
                <div class="totales-left">
                    <?php
                        // Calcular subtotal (sin IVA)
                        $subtotal = $boleta->cargo_consumo + $boleta->cargo_fijo + $boleta->otros_cargos - $boleta->descuentos - $boleta->subsidio;
                        $iva = $subtotal * 0.19;
                        $totalConIva = $subtotal + $iva;
                    ?>
                    <div class="total-row">
                        <div class="label">Subtotal Consumo:</div>
                        <div class="value">{{ $boleta->cargo_consumo_formateado }}</div>
                    </div>
                    <div class="total-row">
                        <div class="label">Cargo Fijo:</div>
                        <div class="value">{{ $boleta->cargo_fijo_formateado }}</div>
                    </div>
                    @if($boleta->otros_cargos > 0)
                    <div class="total-row">
                        <div class="label">Otros Cargos:</div>
                        <div class="value">{{ $boleta->otros_cargos_formateado }}</div>
                    </div>
                    @endif
                    @if($boleta->descuentos > 0)
                    <div class="total-row">
                        <div class="label">Descuentos:</div>
                        <div class="value">-{{ $boleta->descuentos_formateado }}</div>
                    </div>
                    @endif
                    @if($boleta->subsidio > 0)
                    <div class="total-row">
                        <div class="label">Subsidio:</div>
                        <div class="value">-{{ $boleta->subsidio_formateado }}</div>
                    </div>
                    @endif
                    <div class="total-row" style="border-top: 2px solid #999; padding-top: 8px; margin-top: 5px;">
                        <div class="label">Subtotal:</div>
                        <div class="value">${{ number_format($subtotal, 0, ',', '.') }}</div>
                    </div>
                    <div class="total-row">
                        <div class="label">IVA (19%):</div>
                        <div class="value">${{ number_format($iva, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="totales-right">
                    <div class="total-final">
                        <div class="label">TOTAL A PAGAR</div>
                        <div class="value">${{ number_format($totalConIva, 0, ',', '.') }}</div>
                    </div>

                    @if($mesesAdeudados > 0)
                    <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border: 2px solid #856404;">
                        <div style="font-size: 9px; text-transform: uppercase; font-weight: bold; color: #856404; margin-bottom: 5px;">
                            Saldo Total Adeudado:
                        </div>
                        <div style="font-size: 16px; font-weight: bold; color: #856404; text-align: center;">
                            ${{ number_format($totalAdeudado, 0, ',', '.') }}
                        </div>
                        <div style="font-size: 8px; text-align: center; color: #856404; margin-top: 3px;">
                            (Incluye {{ $mesesAdeudados }} {{ $mesesAdeudados == 1 ? 'mes' : 'meses' }} pendiente{{ $mesesAdeudados == 1 ? '' : 's' }})
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información de Pagos y Deudas -->
        <div style="display: table; width: 100%; border-bottom: 2px solid #000;">
            <!-- Último Pago -->
            <div style="display: table-cell; width: 50%; padding: 12px 15px; vertical-align: top; border-right: 1px solid #000; background: #e8f5e9;">
                <h3 style="font-size: 9px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #2e7d32; padding-bottom: 3px; color: #1b5e20;">
                    💰 Último Pago Registrado
                </h3>
                @if($ultimoPago)
                <div style="font-size: 9px; line-height: 1.5;">
                    <div style="margin-bottom: 3px;">
                        <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($ultimoPago->fecha_pago)->format('d/m/Y') }}
                    </div>
                    <div style="margin-bottom: 3px;">
                        <strong>Monto:</strong> ${{ number_format($ultimoPago->monto_pagado, 0, ',', '.') }}
                    </div>
                    <div style="margin-bottom: 3px;">
                        <strong>Método:</strong> {{ ucfirst($ultimoPago->metodo_pago) }}
                    </div>
                    @if($ultimoPago->numero_comprobante)
                    <div style="margin-bottom: 3px;">
                        <strong>N° Comprobante:</strong> {{ $ultimoPago->numero_comprobante }}
                    </div>
                    @endif
                </div>
                @else
                <div style="font-size: 9px; color: #666; font-style: italic;">
                    No se registran pagos anteriores
                </div>
                @endif
            </div>

            <!-- Deuda Pendiente -->
            <div style="display: table-cell; width: 50%; padding: 12px 15px; vertical-align: top; background: {{ $mesesAdeudados > 0 ? '#ffebee' : '#f1f8e9' }};">
                <h3 style="font-size: 9px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid {{ $mesesAdeudados > 0 ? '#c62828' : '#558b2f' }}; padding-bottom: 3px; color: {{ $mesesAdeudados > 0 ? '#b71c1c' : '#33691e' }};">
                    {{ $mesesAdeudados > 0 ? '⚠️ Deuda Pendiente' : '✓ Estado de Cuenta' }}
                </h3>
                @if($mesesAdeudados > 0)
                <div style="font-size: 9px; line-height: 1.5;">
                    <div style="margin-bottom: 3px;">
                        <strong>Meses adeudados:</strong> <span style="color: #c62828; font-weight: bold;">{{ $mesesAdeudados }}</span>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <strong>Total adeudado:</strong> <span style="color: #c62828; font-weight: bold; font-size: 11px;">${{ number_format($totalAdeudado, 0, ',', '.') }}</span>
                    </div>
                    <div style="margin-top: 8px; padding: 5px; background: #fff; border: 1px solid #c62828; font-size: 8px;">
                        <strong>Períodos pendientes:</strong><br>
                        @foreach($boletasPendientes as $bp)
                            • {{ $bp->mes_texto }} - ${{ number_format($bp->total, 0, ',', '.') }}<br>
                        @endforeach
                    </div>
                </div>
                @else
                <div style="font-size: 9px; color: #2e7d32; font-weight: bold;">
                    ✓ No presenta deudas pendientes
                </div>
                <div style="font-size: 8px; color: #558b2f; margin-top: 5px;">
                    Sus pagos están al día
                </div>
                @endif
            </div>
        </div>

        @if($boleta->observaciones)
        <!-- Observaciones -->
        <div class="observaciones-section">
            <h3>Observaciones:</h3>
            <p>{{ $boleta->observaciones }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-info">
                <strong>Instrucciones de Pago:</strong>
                • Pague antes de la fecha de vencimiento para evitar recargos por mora.<br>
                • Conserve este documento como comprobante de pago.<br>
                • Ante cualquier consulta o reclamo, comuníquese con nosotros presentando esta boleta.
            </div>

            <!-- Información de Pago Presencial -->
            <div style="margin-top: 10px; padding: 10px; background: #e3f2fd; border: 1px solid #1976d2; border-radius: 3px;">
                <div style="display: table; width: 100%;">
                    <div style="display: table-cell; width: 50%; vertical-align: top; padding-right: 10px;">
                        <strong style="font-size: 9px; color: #0d47a1; text-transform: uppercase;">📍 Pago Presencial:</strong>
                        <div style="font-size: 8px; margin-top: 5px; line-height: 1.5;">
                            <strong>Lugar:</strong> Oficina APR - [Dirección de la oficina]<br>
                            <strong>Días de atención:</strong> Lunes a Viernes<br>
                            <strong>Horario:</strong> 09:00 a 13:00 hrs. y 14:00 a 17:00 hrs.<br>
                            <strong>Sábados:</strong> 09:00 a 12:00 hrs.
                        </div>
                    </div>
                    <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 10px; border-left: 1px solid #1976d2;">
                        <strong style="font-size: 9px; color: #0d47a1; text-transform: uppercase;">💳 Transferencia Bancaria:</strong>
                        <div style="font-size: 8px; margin-top: 5px; line-height: 1.5;">
                            <strong>Banco:</strong> [Nombre del Banco]<br>
                            <strong>Cuenta Corriente:</strong> [Número de cuenta]<br>
                            <strong>RUT:</strong> [RUT de la APR]<br>
                            <strong>Email comprobante:</strong> pagos@apr.cl<br>
                            <strong>Referencia:</strong> Boleta N° {{ $boleta->numero_boleta }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Espacio para Timbre Electrónico SII -->
            <div style="margin-top: 15px; padding: 10px; border: 2px dashed #000; text-align: center; background: #fff;">
                <div style="font-size: 8px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;">
                    Timbre Electrónico SII
                </div>
                <div style="min-height: 80px; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                    <!-- Aquí se insertará el timbre electrónico del SII -->
                    <div style="font-size: 7px; color: #666; text-align: center; padding: 10px;">
                        [ESPACIO RESERVADO PARA TIMBRE ELECTRÓNICO DEL SII]<br>
                        <div style="margin-top: 5px; font-size: 6px;">
                            Este documento debe ser timbrado electrónicamente<br>
                            según lo establecido en la Resolución del SII
                        </div>
                    </div>
                </div>
                <div style="font-size: 7px; margin-top: 5px; color: #333;">
                    Boleta Electrónica | Resolución N° [Número] del [Fecha]
                </div>
            </div>

            <div class="footer-note">
                DOCUMENTO GENERADO ELECTRÓNICAMENTE - {{ now()->format('d/m/Y H:i:s') }}<br>
                ESTE DOCUMENTO ES VÁLIDO SIN FIRMA NI TIMBRE
            </div>
        </div>
    </div>

    <!-- SEGUNDA PÁGINA: COMPROBANTE PARA EL CLIENTE -->
    <div class="comprobante-page">
        <div style="text-align: center; margin-bottom: 15px;">
            <div style="border: 2px dashed #000; padding: 5px; display: inline-block; margin-bottom: 10px;">
                ✂ - - - - - CORTE AQUÍ - COMPROBANTE PARA EL CLIENTE - - - - - ✂
            </div>
        </div>

        <!-- Comprobante completo en segunda página -->
        <div style="border: 3px solid #000; padding: 20px; background: #fff; max-width: 700px; margin: 0 auto;">
            <div style="text-align: center; background: #000; color: #fff; padding: 10px; margin: -20px -20px 20px -20px; font-weight: bold; font-size: 14px; letter-spacing: 2px;">
                COMPROBANTE DE PAGO - SISTEMA APR
            </div>

            <table style="width: 100%; font-size: 12px; margin-bottom: 20px;">
                <tr>
                    <td style="width: 50%; padding: 10px; border: 1px solid #000; background: #f0f0f0;">
                        <strong>Boleta Nº:</strong><br>
                        <span style="font-size: 18px; font-weight: bold;">{{ $boleta->numero_boleta }}</span>
                    </td>
                    <td style="width: 50%; padding: 10px; border: 1px solid #000; background: #f0f0f0;">
                        <strong>Socio Nº:</strong><br>
                        <span style="font-size: 18px; font-weight: bold;">{{ $boleta->socio->numero_socio }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 10px; border: 1px solid #000;">
                        <strong>Nombre Completo:</strong><br>
                        <span style="font-size: 14px; font-weight: bold;">{{ $boleta->socio->nombre_completo }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #000;">
                        <strong>Período Facturado:</strong><br>
                        <span style="font-size: 14px; font-weight: bold;">{{ $boleta->mes_texto }}</span>
                    </td>
                    <td style="padding: 10px; border: 1px solid #000;">
                        <strong>Fecha de Vencimiento:</strong><br>
                        <span style="font-size: 14px; font-weight: bold; color: #c62828;">{{ $boleta->fecha_vencimiento_formateada }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 15px; border: 3px solid #000; text-align: center; background: #000; color: #fff;">
                        <div style="font-size: 14px; margin-bottom: 5px;">TOTAL A PAGAR</div>
                        <div style="font-size: 28px; font-weight: bold; letter-spacing: 2px;">{{ $boleta->total_formateado }}</div>
                    </td>
                </tr>
            </table>

            <div style="padding: 15px; border: 2px dashed #1976d2; background: #e3f2fd; margin-bottom: 20px;">
                <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px; color: #0d47a1;">
                    📍 INSTRUCCIONES DE PAGO:
                </div>
                <div style="font-size: 10px; line-height: 1.6;">
                    ✓ Pague antes de la fecha de vencimiento para evitar recargos por mora.<br>
                    ✓ Conserve este comprobante como respaldo de pago.<br>
                    ✓ Presente este documento al momento de realizar el pago.<br>
                    ✓ Ante cualquier consulta, comuníquese con nosotros.
                </div>
            </div>

            <table style="width: 100%; font-size: 9px; margin-bottom: 20px;">
                <tr>
                    <td style="width: 50%; padding: 10px; vertical-align: top; border: 1px solid #000;">
                        <strong style="font-size: 10px;">💳 TRANSFERENCIA BANCARIA:</strong><br><br>
                        <strong>Banco:</strong> [Nombre del Banco]<br>
                        <strong>Cuenta:</strong> [Número de cuenta]<br>
                        <strong>RUT:</strong> [RUT de la APR]<br>
                        <strong>Email:</strong> pagos@apr.cl<br>
                        <strong>Referencia:</strong> {{ $boleta->numero_boleta }}
                    </td>
                    <td style="width: 50%; padding: 10px; vertical-align: top; border: 1px solid #000;">
                        <strong style="font-size: 10px;">🏢 PAGO PRESENCIAL:</strong><br><br>
                        <strong>Lugar:</strong> Oficina APR<br>
                        <strong>Días:</strong> Lunes a Viernes<br>
                        <strong>Horario:</strong> 09:00 - 17:00 hrs<br>
                        <strong>Sábados:</strong> 09:00 - 12:00 hrs
                    </td>
                </tr>
            </table>

            @if($mesesAdeudados > 0)
            <div style="padding: 15px; border: 3px solid #c62828; background: #ffebee; margin-bottom: 20px;">
                <div style="font-size: 12px; font-weight: bold; margin-bottom: 10px; color: #b71c1c; text-align: center;">
                    ⚠️ ATENCIÓN: DEUDA PENDIENTE
                </div>
                <div style="font-size: 11px; text-align: center;">
                    <strong>Meses adeudados:</strong> {{ $mesesAdeudados }}<br>
                    <strong style="font-size: 14px; color: #c62828;">Total adeudado: ${{ number_format($totalAdeudado, 0, ',', '.') }}</strong>
                </div>
            </div>
            @endif

            <div style="border-top: 2px solid #000; padding-top: 15px; font-size: 8px; text-align: center; color: #666;">
                DOCUMENTO GENERADO ELECTRÓNICAMENTE - {{ now()->format('d/m/Y H:i:s') }}<br>
                CONSERVE ESTE COMPROBANTE HASTA CONFIRMAR SU PAGO
            </div>
        </div>
    </div>
</body>
</html>
