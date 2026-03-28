<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.95;
        }
        .content {
            padding: 40px 30px;
        }
        .success-badge {
            background: #d1fae5;
            border: 2px solid #10b981;
            color: #065f46;
            padding: 15px 25px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
            font-size: 18px;
            font-weight: 600;
        }
        .success-badge .icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .info-grid {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 14px;
        }
        .info-value {
            color: #1f2937;
            font-weight: 500;
            font-size: 14px;
            text-align: right;
        }
        .total-box {
            background: #eff6ff;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .total-label {
            font-size: 16px;
            color: #1e40af;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .total-amount {
            font-size: 32px;
            color: #1e40af;
            font-weight: 700;
        }
        .message-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .message-box p {
            margin: 0;
            color: #78350f;
            font-size: 14px;
            line-height: 1.6;
        }
        .footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 13px;
        }
        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .container {
                border-radius: 0;
            }
            body {
                padding: 0;
            }
            .header, .content, .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>✓ Pago Recibido</h1>
            <p>Recibo N° {{ $pago->numero_recibo }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Success Badge -->
            <div class="success-badge">
                <div class="icon">✓</div>
                ¡Su pago ha sido registrado exitosamente!
            </div>

            <!-- Información del Pago -->
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Recibo N°:</span>
                    <span class="info-value">{{ $pago->numero_recibo }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de Pago:</span>
                    <span class="info-value">{{ $pago->fecha_pago_formateada }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Socio:</span>
                    <span class="info-value">{{ $pago->socio->nombre_completo }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">N° Socio:</span>
                    <span class="info-value">{{ $pago->socio->numero_socio }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Boleta N°:</span>
                    <span class="info-value">{{ $pago->boleta->numero_boleta }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mes:</span>
                    <span class="info-value">{{ $pago->boleta->mes_texto }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Método de Pago:</span>
                    <span class="info-value">{{ $pago->metodo_pago_texto }}</span>
                </div>
                @if($pago->numero_comprobante)
                <div class="info-row">
                    <span class="info-label">N° Comprobante:</span>
                    <span class="info-value">{{ $pago->numero_comprobante }}</span>
                </div>
                @endif
            </div>

            <!-- Total -->
            <div class="total-box">
                <div class="total-label">Monto Pagado:</div>
                <div class="total-amount">{{ $pago->monto_pagado_formateado }}</div>
            </div>

            <!-- Mensaje -->
            <div class="message-box">
                <p><strong>Gracias por su pago.</strong> Este correo confirma que hemos recibido su pago correctamente. Conserve este recibo para sus registros.</p>
            </div>

            @if($pago->observaciones)
            <div style="margin-top: 20px;">
                <p style="color: #6b7280; font-size: 14px; margin: 0 0 8px 0;"><strong>Observaciones:</strong></p>
                <p style="color: #374151; font-size: 14px; margin: 0;">{{ $pago->observaciones }}</p>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            @if($organizacion)
            <p><strong>{{ $organizacion->nombre_apr }}</strong></p>
            @if($organizacion->email_contacto)
            <p>Email: <a href="mailto:{{ $organizacion->email_contacto }}">{{ $organizacion->email_contacto }}</a></p>
            @endif
            @if($organizacion->telefono)
            <p>Teléfono: {{ $organizacion->telefono }}</p>
            @endif
            @if($organizacion->direccion)
            <p>{{ $organizacion->direccion }}</p>
            @endif
            @else
            <p><strong>Sistema APR</strong></p>
            @endif
            <p style="margin-top: 15px; font-size: 12px;">Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
