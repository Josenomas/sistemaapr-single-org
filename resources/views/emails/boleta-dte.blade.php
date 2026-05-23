<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tipoDte }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .badge-dte {
            background: #fbbf24;
            color: #78350f;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #4b5563;
        }
        .value {
            color: #111827;
        }
        .total {
            background: #10b981;
            color: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            background: #f3f4f6;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
            color: #6b7280;
        }
        .alert {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert.success {
            background: #d1fae5;
            border-left-color: #10b981;
        }
        .alert.warning {
            background: #fef3c7;
            border-left-color: #f59e0b;
        }
        .alert.danger {
            background: #fee2e2;
            border-left-color: #ef4444;
        }
        .dte-info {
            background: #f0fdf4;
            border: 2px solid #10b981;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .dte-info strong {
            color: #059669;
        }
        .sii-badge {
            background: #1e40af;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📄 {{ $tipoDte }}</h1>
        <p style="margin: 5px 0;">{{ config('app.name') }}</p>
        <span class="badge-dte">✓ DOCUMENTO TRIBUTARIO ELECTRÓNICO</span>
    </div>

    <div class="content">
        <p>Estimado(a) <strong>{{ $socio->nombre_completo }}</strong>,</p>

        <p>Se ha emitido su <strong>{{ $tipoDte }}</strong> correspondiente al mes de <strong>{{ $boleta->mes_texto }}</strong>
        y ha sido enviada electrónicamente al Servicio de Impuestos Internos (SII).</p>

        <div class="dte-info">
            <p style="margin: 0 0 10px 0;">
                <strong>✓ Documento Válido ante el SII</strong>
            </p>
            <p style="margin: 0; font-size: 14px; color: #065f46;">
                Este documento tributario electrónico tiene plena validez legal y cuenta con
                <strong>timbre electrónico</strong> del Servicio de Impuestos Internos de Chile.
            </p>
        </div>

        <div class="info-box">
            <div class="info-item">
                <span class="label">Número de Boleta:</span>
                <span class="value">{{ $boleta->numero_boleta }}</span>
            </div>
            <div class="info-item">
                <span class="label">Folio SII:</span>
                <span class="value"><span class="sii-badge">{{ $boleta->folio_sii }}</span></span>
            </div>
            <div class="info-item">
                <span class="label">Tipo DTE:</span>
                <span class="value">{{ $boleta->tipo_dte_nombre }}</span>
            </div>
            <div class="info-item">
                <span class="label">Estado:</span>
                <span class="value">{{ ucfirst($boleta->estado_dte) }}</span>
            </div>
            <div class="info-item">
                <span class="label">Mes:</span>
                <span class="value">{{ $boleta->mes_texto }}</span>
            </div>
            <div class="info-item">
                <span class="label">Fecha de Emisión DTE:</span>
                <span class="value">{{ $boleta->fecha_emision_dte ? $boleta->fecha_emision_dte->format('d/m/Y H:i') : $boleta->fecha_emision_formateada }}</span>
            </div>
            <div class="info-item">
                <span class="label">Fecha de Vencimiento:</span>
                <span class="value">{{ $boleta->fecha_vencimiento_formateada }}</span>
            </div>
            <div class="info-item">
                <span class="label">Consumo:</span>
                <span class="value">{{ number_format($boleta->consumo_m3, 2) }} m³</span>
            </div>
        </div>

        <div class="total">
            TOTAL A PAGAR: ${{ number_format($boleta->total, 0, ',', '.') }}
        </div>

        @if($boleta->fecha_vencimiento && $boleta->fecha_vencimiento->isPast())
        <div class="alert danger">
            <strong>⚠️ Atención:</strong> Esta boleta se encuentra vencida desde el {{ $boleta->fecha_vencimiento_formateada }}.
            Por favor, regularice su pago a la brevedad.
        </div>
        @elseif($boleta->fecha_vencimiento && $boleta->fecha_vencimiento->diffInDays(now()) <= 5)
        <div class="alert warning">
            <strong>⏰ Recordatorio:</strong> Esta boleta vence el {{ $boleta->fecha_vencimiento_formateada }}.
        </div>
        @endif

        <div class="alert success">
            <strong>📎 Documento Adjunto</strong><br>
            El PDF timbrado electrónicamente está adjunto a este correo.
            Este documento contiene el <strong>timbre electrónico del SII</strong> que valida su autenticidad.
        </div>

        <p><strong>Modalidades de pago disponibles:</strong></p>
        <ul>
            <li>En nuestras oficinas</li>
            <li>Transferencia bancaria</li>
            <li>Pago en línea (si está disponible)</li>
        </ul>

        <div class="alert">
            <strong>ℹ️ Información Importante:</strong><br>
            • Este documento tiene <strong>validez tributaria</strong><br>
            • Conserve este email para sus registros contables<br>
            • El PDF adjunto contiene el timbre electrónico del SII<br>
            • Puede verificar la autenticidad en <a href="https://www4.sii.cl/consdcvinternetui/" target="_blank">www.sii.cl</a>
        </div>

        <p style="margin-top: 30px;">Atentamente,<br><strong>{{ config('app.name') }}</strong></p>
    </div>

    <div class="footer">
        <p><strong>📧 Correo Automático - No Responder</strong></p>
        <p>Este es un correo generado automáticamente por el sistema de facturación electrónica.</p>
        <p>Si tiene alguna consulta, contáctenos a través de nuestros canales oficiales.</p>
        <p style="margin-top: 15px; font-size: 12px; color: #9ca3af;">
            Documento Tributario Electrónico generado conforme a la normativa del SII
        </p>
    </div>
</body>
</html>
