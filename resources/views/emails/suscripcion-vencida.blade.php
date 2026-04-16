<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción Vencida - Cuenta Suspendida</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc2626;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #dc2626;
            margin: 0;
            font-size: 24px;
        }
        .icon {
            font-size: 64px;
            margin-bottom: 10px;
        }
        .alert-danger {
            background: #fee2e2;
            border: 3px solid #dc2626;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
        .alert-danger strong {
            color: #991b1b;
            font-size: 20px;
            display: block;
            margin-bottom: 10px;
        }
        .info-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #64748b;
        }
        .info-value {
            color: #1e293b;
        }
        .button {
            display: inline-block;
            background: #dc2626;
            color: white !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
        }
        .button:hover {
            background: #b91c1c;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 14px;
        }
        .suspended-notice {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🚨</div>
            <h1>CUENTA SUSPENDIDA</h1>
            <p style="color: #dc2626; font-weight: bold;">Sistema APR</p>
        </div>

        <div class="alert-danger">
            <strong>⚠️ TU SUSCRIPCIÓN HA VENCIDO ⚠️</strong>
            <p style="margin: 10px 0 0 0; color: #7f1d1d;">
                Tu cuenta ha sido suspendida automáticamente
            </p>
        </div>

        <p>Hola <strong>{{ $pago->organizacion->nombre_apr }}</strong>,</p>

        <p>
            Lamentamos informarte que tu suscripción al plan <strong>{{ $pago->suscripcion->nombre }}</strong>
            ha vencido el <strong>{{ $pago->fecha_vencimiento->format('d/m/Y') }}</strong>.
        </p>

        <div class="suspended-notice">
            <h3 style="margin-top: 0; color: #991b1b;">📛 Tu cuenta ha sido suspendida</h3>
            <p style="margin: 10px 0;">
                Debido al vencimiento del pago, tu acceso al Sistema APR ha sido suspendido temporalmente.
                No podrás acceder a ninguna funcionalidad hasta que se regularice el pago.
            </p>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #1e293b;">Detalles del Pago Pendiente</h3>

            <div class="info-row">
                <span class="info-label">Plan:</span>
                <span class="info-value">{{ $pago->suscripcion->nombre }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Monto Adeudado:</span>
                <span class="info-value" style="font-size: 24px; font-weight: bold; color: #dc2626;">
                    ${{ number_format($pago->monto, 0, ',', '.') }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Período:</span>
                <span class="info-value">
                    {{ $pago->periodo_inicio->format('d/m/Y') }} - {{ $pago->periodo_fin->format('d/m/Y') }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Fecha de Vencimiento:</span>
                <span class="info-value" style="color: #dc2626; font-weight: bold;">
                    {{ $pago->fecha_vencimiento->format('d/m/Y') }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Días Vencido:</span>
                <span class="info-value" style="color: #dc2626; font-weight: bold;">
                    {{ now()->diffInDays($pago->fecha_vencimiento) }} días
                </span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/login') }}" class="button">
                💳 Pagar Ahora y Reactivar Cuenta
            </a>
        </div>

        <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #1e293b;">🔓 ¿Cómo reactivar tu cuenta?</h4>
            <ol style="margin: 10px 0; padding-left: 20px; line-height: 1.8;">
                <li>Realiza el pago del monto adeudado</li>
                <li>Tu cuenta se reactivará automáticamente en minutos</li>
                <li>Podrás volver a acceder a todas las funcionalidades del sistema</li>
                <li>No se perderá ningún dato, todo estará tal como lo dejaste</li>
            </ol>
        </div>

        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong style="color: #92400e;">⏰ Importante:</strong>
            <p style="margin: 5px 0 0 0; color: #78350f;">
                Si no realizas el pago en los próximos <strong>30 días</strong>, tus datos podrían ser eliminados
                permanentemente según nuestros términos y condiciones.
            </p>
        </div>

        <div style="background: #dbeafe; border-left: 4px solid #2563eb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong style="color: #1e3a8a;">💬 ¿Necesitas ayuda?</strong>
            <p style="margin: 5px 0 0 0; color: #1e40af;">
                Si tienes problemas para realizar el pago o necesitas asistencia, contáctanos a
                <a href="mailto:soportesistemaapr@gmail.com" style="color: #2563eb;">soportesistemaapr@gmail.com</a>
            </p>
        </div>

        <div class="footer">
            <p><strong>{{ $pago->organizacion->nombre_apr }}</strong></p>
            <p>{{ $pago->organizacion->email_contacto }}</p>
            <p style="margin-top: 20px;">
                <small>
                    Este es un correo automático, por favor no respondas a este mensaje.<br>
                    Para cualquier consulta, contáctanos a soportesistemaapr@gmail.com
                </small>
            </p>
            <p style="margin-top: 15px;">
                <small style="color: #94a3b8;">
                    © {{ date('Y') }} Sistema APR - Gestión Integral de Agua Potable Rural
                </small>
            </p>
        </div>
    </div>
</body>
</html>
