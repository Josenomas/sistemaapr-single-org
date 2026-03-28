<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renovación Próxima</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .alert-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 6px;
        }
        .alert-box h2 {
            margin: 0 0 10px 0;
            color: #92400e;
            font-size: 20px;
        }
        .alert-box p {
            margin: 0;
            color: #78350f;
            font-size: 15px;
            line-height: 1.6;
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
        }
        .info-value {
            color: #1f2937;
            font-weight: 500;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px 0;
            text-align: center;
        }
        .footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
            .info-row {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Renovación Próxima</h1>
            <p>Sistema APR - Gestión de Agua Potable</p>
        </div>

        <div class="content">
            <div class="alert-box">
                <h2>⏰ Tu suscripción está por vencer</h2>
                <p>
                    Te recordamos que tu suscripción al plan <strong>{{ $renovacion->organizacion->suscripcion->nombre }}</strong>
                    vence en <strong>{{ $diasRestantes }} {{ $diasRestantes == 1 ? 'día' : 'días' }}</strong>.
                </p>
            </div>

            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Organización:</span>
                    <span class="info-value">{{ $renovacion->organizacion->nombre_apr }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Plan Actual:</span>
                    <span class="info-value">{{ $renovacion->organizacion->suscripcion->nombre }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de Vencimiento:</span>
                    <span class="info-value">{{ $renovacion->fecha_vencimiento->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Monto a Renovar:</span>
                    <span class="info-value">${{ number_format($renovacion->monto, 0, ',', '.') }} CLP/mes</span>
                </div>
            </div>

            <p style="color: #4b5563; line-height: 1.6; margin-bottom: 20px;">
                Para evitar la suspensión de tu servicio, te recomendamos realizar el pago de renovación
                antes de la fecha de vencimiento. Si no realizas el pago, tu acceso será suspendido automáticamente.
            </p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/organizacion/mi-suscripcion') }}" class="btn">
                    💳 Renovar Ahora
                </a>
            </div>

            <p style="font-size: 13px; color: #9ca3af; margin-top: 20px;">
                Si ya realizaste el pago, por favor ignora este mensaje. El sistema se actualizará automáticamente.
            </p>
        </div>

        <div class="footer">
            <p><strong>Sistema APR</strong></p>
            <p>Gestión integral para organizaciones de agua potable rural</p>
            <p style="margin-top: 15px; font-size: 12px;">
                Este es un email automático, por favor no responder.
            </p>
        </div>
    </div>
</body>
</html>
