<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Solicitud Recibida - Sistema APR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        .message {
            color: #666;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .resumen-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 25px;
            margin: 30px 0;
            border-radius: 8px;
        }

        .resumen-box h3 {
            color: #667eea;
            font-size: 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 600;
            min-width: 120px;
        }

        .info-value {
            color: #333;
            font-weight: 400;
            flex: 1;
            text-align: right;
        }

        .mensaje-row {
            display: block;
            padding: 15px 0;
            margin-top: 10px;
        }

        .mensaje-row .info-label {
            display: block;
            margin-bottom: 10px;
        }

        .mensaje-row .info-value {
            text-align: left;
            background: white;
            padding: 15px;
            border-radius: 6px;
            color: #333;
            line-height: 1.6;
        }

        .estado-row {
            background: white;
            margin-top: 15px;
            padding: 15px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .estado-row .info-label {
            min-width: auto;
        }

        .estado-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff3cd;
            color: #856404;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .next-steps {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 30px 0;
            border-radius: 6px;
        }

        .next-steps h4 {
            color: #1e40af;
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .next-steps p {
            color: #1e3a8a;
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .contact-info {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .contact-info p {
            color: #374151;
            font-size: 14px;
            margin: 8px 0;
        }

        .contact-info a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .divider {
            height: 1px;
            background: #e9ecef;
            margin: 30px 0;
        }

        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 13px;
        }

        .footer strong {
            color: #333;
            font-size: 15px;
        }

        .footer-links {
            margin-top: 15px;
        }

        .footer-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
        }

        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .header {
                padding: 30px 20px;
            }

            .content {
                padding: 30px 20px;
            }

            .info-row {
                flex-direction: column;
                gap: 5px;
            }

            .info-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">💧</div>
            <h1>Sistema APR</h1>
            <p>Confirmación de Solicitud Recibida</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hola {{ $datos['nombre'] }},
            </div>

            <div class="message">
                ¡Gracias por contactarnos! Hemos recibido tu solicitud correctamente y la estamos procesando.
            </div>

            <!-- Resumen de la Solicitud -->
            <div class="resumen-box">
                <h3>
                    📋 Resumen de tu Solicitud
                </h3>

                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value">{{ $datos['nombre'] }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $datos['email'] }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value">{{ $datos['telefono'] }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">APR:</span>
                    <span class="info-value"><strong>{{ $datos['apr'] }}</strong></span>
                </div>

                <div class="mensaje-row">
                    <span class="info-label">Mensaje:</span>
                    <div class="info-value">{{ $datos['mensaje'] }}</div>
                </div>

                <div class="estado-row">
                    <span class="info-label">Estado:</span>
                    <span class="estado-badge">
                        🟡 Pendiente de Respuesta
                    </span>
                </div>
            </div>

            <!-- Próximos Pasos -->
            <div class="next-steps">
                <h4>📌 ¿Qué sigue?</h4>

                <p>
                    Nuestro equipo revisará tu solicitud y te contactará en las <strong>próximas 24-48 horas hábiles</strong>.
                </p>

                <p style="margin-bottom: 0;">
                    Si tu consulta es urgente, puedes contactarnos directamente a:
                </p>

                <div class="contact-info">
                    <p>📧 Email: <a href="mailto:soportesistemaapr@gmail.com">soportesistemaapr@gmail.com</a></p>
                    <p>🌐 Web: <a href="https://sistemaapr.cl" target="_blank">www.sistemaapr.cl</a></p>
                </div>
            </div>

            <div class="divider"></div>

            <p style="color: #666; font-size: 14px; text-align: center;">
                Este correo confirma que hemos recibido tu solicitud.<br>
                No es necesario responder a este mensaje.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>Sistema APR</strong><br>
            Gestión Integral de Agua Potable Rural

            <div class="footer-links">
                <p style="font-size: 12px; color: #999; margin-top: 20px;">
                    © {{ date('Y') }} Sistema APR - Todos los derechos reservados<br>
                    Este es un correo automático, por favor no responder.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
