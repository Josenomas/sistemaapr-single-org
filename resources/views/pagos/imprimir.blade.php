<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Pago - {{ $pago->numero_recibo }}</title>
    <style>
        @page {
            size: letter;
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            color: #1f2937;
            background: #fff;
            line-height: 1.5;
        }

        .comprobante {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 2px solid #e5e7eb;
        }

        /* Header simple y profesional */
        .header {
            background: #1e40af;
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left h1 {
            font-size: 24pt;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .header-left p {
            font-size: 10pt;
            opacity: 0.9;
        }

        .header-right {
            text-align: right;
        }

        .recibo-numero {
            background: white;
            color: #1e40af;
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 18pt;
        }

        .recibo-label {
            font-size: 9pt;
            color: white;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        /* Sello pagado simple */
        .estado-pagado {
            background: #10b981;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 4px;
        }

        /* Monto destacado */
        .monto-principal {
            text-align: center;
            padding: 40px 30px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .monto-label {
            font-size: 10pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .monto-valor {
            font-size: 48pt;
            font-weight: 700;
            color: #1e40af;
        }

        /* Secciones de información */
        .info-section {
            padding: 25px 30px;
            border-bottom: 1px solid #e5e7eb;
        }

        .section-title {
            font-size: 11pt;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #1e40af;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .info-row {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-row.full {
            grid-column: 1 / -1;
        }

        .info-label {
            font-size: 9pt;
            color: #6b7280;
            font-weight: 600;
        }

        .info-value {
            font-size: 11pt;
            color: #1f2937;
            font-weight: 500;
        }

        /* Box de boleta */
        .boleta-box {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 6px;
            padding: 20px;
            margin: 0 30px 25px;
        }

        .boleta-box .section-title {
            color: #92400e;
            border-bottom-color: #f59e0b;
            margin-bottom: 12px;
        }

        .boleta-box .info-value {
            font-weight: 600;
        }

        /* Método de pago badge */
        .metodo-pago {
            display: inline-block;
            background: #1e40af;
            color: white;
            padding: 8px 20px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 10pt;
        }

        /* Observaciones */
        .observaciones {
            background: #f9fafb;
            border-left: 4px solid #1e40af;
            padding: 15px 20px;
            margin: 0 30px 25px;
            font-style: italic;
            color: #4b5563;
        }

        /* Footer */
        .footer {
            background: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            border-top: 2px solid #e5e7eb;
        }

        .footer-title {
            font-size: 12pt;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 10px;
        }

        .footer-text {
            font-size: 9pt;
            color: #6b7280;
            line-height: 1.6;
        }

        .footer-fecha {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 8pt;
            color: #9ca3af;
        }

        @media print {
            body {
                background: white;
            }
            .comprobante {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="comprobante">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>SISTEMA APR</h1>
                <p>Agua Potable Rural</p>
                <p>Teléfono: (XX) XXXX-XXXX</p>
            </div>
            <div class="header-right">
                <div class="recibo-label">COMPROBANTE DE PAGO</div>
                <div class="recibo-numero">{{ $pago->numero_recibo }}</div>
            </div>
        </div>

        <!-- Estado Pagado -->
        <div class="estado-pagado">✓ PAGADO</div>

        <!-- Monto Principal -->
        <div class="monto-principal">
            <div class="monto-label">Monto Pagado</div>
            <div class="monto-valor">{{ $pago->monto_pagado_formateado }}</div>
        </div>

        <!-- Información del Pago -->
        <div class="info-section">
            <div class="section-title">Información del Pago</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Fecha de Pago</span>
                    <span class="info-value">{{ $pago->fecha_pago_formateada }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Método de Pago</span>
                    <span class="info-value">
                        <span class="metodo-pago">{{ strtoupper($pago->metodo_pago) }}</span>
                    </span>
                </div>
                @if($pago->numero_comprobante)
                <div class="info-row">
                    <span class="info-label">N° Comprobante</span>
                    <span class="info-value">{{ $pago->numero_comprobante }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Fecha de Registro</span>
                    <span class="info-value">{{ $pago->fecha_creacion->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Datos del Socio -->
        <div class="info-section">
            <div class="section-title">Datos del Cliente</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">N° Socio</span>
                    <span class="info-value">{{ $pago->socio->numero_socio }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">RUT</span>
                    <span class="info-value">{{ $pago->socio->rut }}</span>
                </div>
                <div class="info-row full">
                    <span class="info-label">Nombre Completo</span>
                    <span class="info-value" style="font-size: 12pt; font-weight: 600;">{{ $pago->socio->nombre_completo }}</span>
                </div>
                <div class="info-row full">
                    <span class="info-label">Dirección</span>
                    <span class="info-value">{{ $pago->socio->direccion ?? 'No especificada' }}</span>
                </div>
            </div>
        </div>

        <!-- Detalle de Boleta -->
        <div class="boleta-box">
            <div class="section-title">Detalle de la Boleta Pagada</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">N° Boleta</span>
                    <span class="info-value">{{ $pago->boleta->numero_boleta }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Período</span>
                    <span class="info-value">{{ $pago->boleta->mes_texto }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Boleta</span>
                    <span class="info-value">{{ $pago->boleta->total_formateado }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado</span>
                    <span class="info-value" style="color: #10b981; font-weight: 700;">{{ ucfirst($pago->boleta->estado) }}</span>
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        @if($pago->observaciones)
        <div class="observaciones">
            <strong style="font-style: normal; color: #1e40af;">Observaciones:</strong><br>
            {{ $pago->observaciones }}
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-title">Gracias por su pago</div>
            <div class="footer-text">
                Este comprobante certifica el pago realizado.<br>
                Conserve este documento para cualquier consulta futura.
            </div>
            <div class="footer-fecha">
                Documento generado el {{ now()->format('d/m/Y H:i:s') }} | Sistema APR
            </div>
        </div>
    </div>
</body>
</html>
