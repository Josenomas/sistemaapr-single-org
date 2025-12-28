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
    </style>
</head>
<body>
    <div class="boleta-container">
        <!-- Banda superior -->
        <div class="header-band">
            BOLETA DE CONSUMO - AGUA POTABLE RURAL PITRELAHUE
        </div>

        <!-- Header principal -->
        <div class="header-main">
            <div class="header-top">
                <div class="empresa-info">
                    <h1>SISTEMA APR</h1>
                    <p><strong>AGUA POTABLE RURAL PITRELAHUE</strong></p>
                    <p>Captación, tratamiento y distribución de agua</p>
                    <p>RUT: 65.552.000-7 | Email: sistemaapr@gmail.com</p>
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
                @if($boleta->socio->subsidio_porcentaje > 0 || $boleta->socio->descuento_monto > 0)
                <div class="cliente-row" style="background: #e3f2fd; padding: 8px; border-left: 3px solid #1565c0;">
                    <div class="cliente-label" style="color: #1565c0; font-weight: bold;">
                        <i class="fas fa-hand-holding-usd"></i> Beneficio:
                    </div>
                    <div class="cliente-value" colspan="3" style="color: #1565c0; font-weight: bold;">
                        @if($boleta->socio->subsidio_porcentaje > 0)
                            Subsidio {{ number_format($boleta->socio->subsidio_porcentaje, 0) }}%
                        @endif
                        @if($boleta->socio->descuento_monto > 0)
                            @if($boleta->socio->subsidio_porcentaje > 0) + @endif
                            Descuento ${{ number_format($boleta->socio->descuento_monto, 0, ',', '.') }}
                        @endif
                        @if($boleta->socio->observaciones_subsidio)
                            <br><span style="font-size: 8px; font-weight: normal; color: #555;">({{ $boleta->socio->observaciones_subsidio }})</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Historial de Consumo (últimos 12 meses) -->
        @if($historialConsumo && $historialConsumo->count() > 0)
        <div style="padding: 15px; border-bottom: 2px solid #000; background: #f9f9f9;">
            <h2 style="font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; text-align: center; background: #000; color: #fff; padding: 5px;">
                Historial de Consumo (Últimos 12 Meses)
            </h2>

            <?php
                $maxConsumo = $historialConsumo->max('consumo') ?: 10;
            ?>

            <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                <!-- Fila de valores de consumo -->
                <tr>
                    @foreach($historialConsumo as $item)
                    <td style="width: {{ 100 / $historialConsumo->count() }}%; text-align: center; vertical-align: bottom; padding: 2px; font-size: 7px; font-weight: bold;">
                        {{ number_format($item['consumo'], 1) }}
                    </td>
                    @endforeach
                </tr>
                <!-- Fila de barras -->
                <tr style="height: 80px; vertical-align: bottom;">
                    @foreach($historialConsumo as $item)
                    <?php
                        $altura = $maxConsumo > 0 ? ($item['consumo'] / $maxConsumo) * 100 : 0;
                        $esActual = $item['mes'] == $boleta->mes;
                    ?>
                    <td style="text-align: center; vertical-align: bottom; padding: 2px;">
                        <div style="background: {{ $esActual ? '#000' : '#ccc' }}; border: 1px solid #000; height: {{ $altura }}px; max-height: 80px; margin: 0 auto; width: 80%;">
                        </div>
                    </td>
                    @endforeach
                </tr>
                <!-- Fila de meses -->
                <tr>
                    @foreach($historialConsumo as $item)
                    <td style="text-align: center; padding: 5px 2px; font-size: 6px; border-top: 2px solid #000;">
                        {{ substr($item['mes_texto'], 0, 3) }}
                    </td>
                    @endforeach
                </tr>
            </table>

            <div style="text-align: center; font-size: 8px; color: #666; margin-top: 5px;">
                * La barra negra indica el período actual | Consumo máximo: {{ number_format($maxConsumo, 1) }} m³
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
                        // Calcular subtotal inicial
                        $subtotalInicial = $boleta->cargo_consumo + $boleta->cargo_fijo + $boleta->otros_cargos;

                        // Obtener datos de subsidio/descuento del socio
                        $subsidio_porcentaje = $boleta->socio->subsidio_porcentaje ?? 0;
                        $descuento_monto = $boleta->socio->descuento_monto ?? 0;

                        // Calcular subsidio por porcentaje
                        $monto_subsidio = 0;
                        if ($subsidio_porcentaje > 0) {
                            $monto_subsidio = round($subtotalInicial * ($subsidio_porcentaje / 100), 0);
                        }

                        // Monto descuento fijo
                        $monto_descuento = $descuento_monto;

                        // Calcular subtotal después de subsidios/descuentos (para IVA)
                        $subtotal = $subtotalInicial - $monto_subsidio - $monto_descuento;

                        // Verificar si el socio está exento de IVA
                        $exentoIva = $boleta->socio->exento_iva ?? 0;
                        $iva = ($exentoIva == 0) ? ($subtotal * 0.19) : 0;
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
                    @if($monto_subsidio > 0)
                    <div class="total-row" style="color: #1565c0;">
                        <div class="label">Subsidio ({{ $subsidio_porcentaje }}%):</div>
                        <div class="value">-${{ number_format($monto_subsidio, 0, ',', '.') }}</div>
                    </div>
                    @endif
                    @if($monto_descuento > 0)
                    <div class="total-row" style="color: #1565c0;">
                        <div class="label">Descuento Fijo:</div>
                        <div class="value">-${{ number_format($monto_descuento, 0, ',', '.') }}</div>
                    </div>
                    @endif
                    <div class="total-row" style="border-top: 2px solid #999; padding-top: 8px; margin-top: 5px;">
                        <div class="label">Subtotal:</div>
                        <div class="value">${{ number_format($subtotal, 0, ',', '.') }}</div>
                    </div>
                    <div class="total-row">
                        <div class="label">IVA (19%){{ $exentoIva == 1 ? ' - EXENTO' : '' }}:</div>
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
                            @php
                                $totalPagado = $bp->pagos->sum('monto_pagado');
                                $saldoPendiente = $bp->total - $totalPagado;
                            @endphp
                            • {{ $bp->mes_texto }} - ${{ number_format($saldoPendiente, 0, ',', '.') }}
                            @if($totalPagado > 0)
                                <span style="color: #666; font-size: 7px;">(abonado: ${{ number_format($totalPagado, 0, ',', '.') }})</span>
                            @endif
                            <br>
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

        @if($boleta->observaciones || ($boleta->socio->observaciones_subsidio && ($subsidio_porcentaje > 0 || $descuento_monto > 0)))
        <!-- Observaciones -->
        <div class="observaciones-section">
            <h3>Observaciones:</h3>
            @if($boleta->socio->observaciones_subsidio && ($subsidio_porcentaje > 0 || $descuento_monto > 0))
            <p style="margin-bottom: 5px;"><strong style="color: #1565c0;">💰 {{ $boleta->socio->observaciones_subsidio }}</strong></p>
            @endif
            @if($boleta->observaciones)
            <p>{{ $boleta->observaciones }}</p>
            @endif
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
                            <strong>Lugar:</strong> Oficina APR<br>
                            <strong>Días de atención:</strong> Sábado y Domingo<br>
                            <strong>Sábado:</strong> 09:00 a 14:00 hrs.<br>
                            <strong>Domingo:</strong> 09:00 a 14:00 hrs.
                        </div>
                    </div>
                    <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 10px; border-left: 1px solid #1976d2;">
                        <strong style="font-size: 9px; color: #0d47a1; text-transform: uppercase;">💳 Transferencia Bancaria:</strong>
                        <div style="font-size: 8px; margin-top: 5px; line-height: 1.5;">
                            <strong>Banco:</strong> [Nombre del Banco]<br>
                            <strong>Cuenta Corriente:</strong> [Número de cuenta]<br>
                            <strong>RUT:</strong> [RUT de la APR]<br>
                            <strong>Email comprobante:</strong> sistemaapr@gmail.com<br>
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
</body>
</html>
