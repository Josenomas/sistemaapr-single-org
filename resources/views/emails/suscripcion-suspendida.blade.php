<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción Suspendida</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 6px;
        }
        .alert-box h2 {
            margin: 0 0 10px 0;
            color: #991b1b;
            font-size: 20px;
        }
        .alert-box p {
            margin: 0;
            color: #7f1d1d;
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
        .consequences-box {
            background: #fef3c7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .consequences-box h3 {
            margin: 0 0 15px 0;
            color: #92400e;
            font-size: 16px;
        }
        .consequences-box ul {
            margin: 0;
            padding-left: 20px;
            color: #78350f;
        }
        .consequences-box li {
            margin: 8px 0;
            line-height: 1.5;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            <h1>🚫 Suscripción Suspendida</h1>
            <p>Sistema APR - Gestión de Agua Potable</p>
        </div>

        <div class="content">
            <div class="alert-box">
                <h2>⚠️ Tu acceso ha sido suspendido</h2>
                <p>
                    Lamentamos informarte que tu suscripción al Sistema APR ha sido suspendida
                    debido a la falta de pago de la renovación vencida.
                </p>
            </div>

            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Organización:</span>
                    <span class="info-value">{{ $renovacion->organizacion->nombre_apr }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Plan:</span>
                    <span class="info-value">{{ $renovacion->organizacion->suscripcion->nombre }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de Vencimiento:</span>
                    <span class="info-value">{{ $renovacion->fecha_vencimiento->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Monto Adeudado:</span>
                    <span class="info-value">${{ number_format($renovacion->monto, 0, ',', '.') }} CLP</span>
                </div>
            </div>

            <div class="consequences-box">
                <h3>📋 ¿Qué significa esto?</h3>
                <ul>
                    <li>No podrás acceder al sistema hasta que realices el pago</li>
                    <li>Tus datos están seguros y no se perderán</li>
                    <li>Tus usuarios no podrán iniciar sesión</li>
                    <li>No se podrán generar boletas ni gestionar pagos</li>
                </ul>
            </div>

            <p style="color: #4b5563; line-height: 1.6; margin: 20px 0;">
                <strong>¿Cómo reactivar tu cuenta?</strong><br>
                Simplemente realiza el pago pendiente y tu acceso será restaurado automáticamente.
                Todos tus datos permanecerán intactos.
            </p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/organizacion/mi-suscripcion') }}" class="btn">
                    💳 Pagar y Reactivar
                </a>
            </div>

            <p style="font-size: 13px; color: #9ca3af; margin-top: 30px; padding: 15px; background: #f3f4f6; border-radius: 6px;">
                <strong>¿Necesitas ayuda?</strong><br>
                Si tienes algún problema con el pago o necesitas asistencia, por favor contáctanos
                y estaremos encantados de ayudarte.
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
