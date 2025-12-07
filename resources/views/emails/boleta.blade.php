<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta de Agua</title>
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
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
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
            border-left: 4px solid #3b82f6;
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
            background: #3b82f6;
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
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert.danger {
            background: #fee2e2;
            border-left-color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>💧 Boleta de Agua</h1>
        <p style="margin: 10px 0 0 0;">Sistema APR</p>
    </div>

    <div class="content">
        <p>Estimado(a) <strong>{{ $socio->nombre_completo }}</strong>,</p>

        <p>Le enviamos adjunta su boleta de agua correspondiente al mes de <strong>{{ $boleta->mes_texto }}</strong>.</p>

        <div class="info-box">
            <div class="info-item">
                <span class="label">Número de Boleta:</span>
                <span class="value">{{ $boleta->numero_boleta }}</span>
            </div>
            <div class="info-item">
                <span class="label">Mes:</span>
                <span class="value">{{ $boleta->mes_texto }}</span>
            </div>
            <div class="info-item">
                <span class="label">Fecha de Emisión:</span>
                <span class="value">{{ $boleta->fecha_emision_formateada }}</span>
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
        <div class="alert">
            <strong>⏰ Recordatorio:</strong> Esta boleta vence el {{ $boleta->fecha_vencimiento_formateada }}.
        </div>
        @endif

        <p>Puede realizar su pago en las siguientes modalidades:</p>
        <ul>
            <li>En nuestras oficinas</li>
            <li>Transferencia bancaria</li>
            <li>Pago en línea (si está disponible)</li>
        </ul>

        <p>El comprobante de pago adjunto en formato PDF contiene toda la información detallada.</p>

        <p style="margin-top: 30px;">Atentamente,<br><strong>Sistema APR</strong></p>
    </div>

    <div class="footer">
        <p>Este es un correo automático, por favor no responder.</p>
        <p>Si tiene alguna consulta, contáctenos a través de nuestros canales oficiales.</p>
    </div>
</body>
</html>
