<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
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
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 6px;
        }
        .alert-box p {
            margin: 0;
            color: #1e40af;
            font-size: 15px;
            line-height: 1.6;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.4);
        }
        .info-box {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 0 0 10px 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }
        .info-box p:last-child {
            margin-bottom: 0;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .warning p {
            margin: 0;
            color: #78350f;
            font-size: 14px;
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
            .btn {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 Recuperar Contraseña</h1>
            <p>Solicitud de restablecimiento</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Alert -->
            <div class="alert-box">
                <p><strong>Hemos recibido una solicitud para restablecer tu contraseña.</strong></p>
            </div>

            <p style="color: #374151; font-size: 15px; line-height: 1.6; margin-bottom: 20px;">
                Hola,
            </p>

            <p style="color: #374151; font-size: 15px; line-height: 1.6; margin-bottom: 20px;">
                Recibimos una solicitud para restablecer la contraseña de tu cuenta asociada a <strong>{{ $email }}</strong>.
            </p>

            <p style="color: #374151; font-size: 15px; line-height: 1.6; margin-bottom: 30px;">
                Para continuar con el proceso de recuperación, haz clic en el siguiente botón:
            </p>

            <!-- Button -->
            <div class="button-container">
                <a href="{{ url('/reset-password/' . $token . '?email=' . urlencode($email)) }}" class="btn">
                    Restablecer Contraseña
                </a>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p><strong>Si no puedes hacer clic en el botón, copia y pega el siguiente enlace en tu navegador:</strong></p>
                <p style="word-break: break-all; color: #3b82f6;">
                    {{ url('/reset-password/' . $token . '?email=' . urlencode($email)) }}
                </p>
            </div>

            <!-- Warning -->
            <div class="warning">
                <p><strong>⚠️ Importante:</strong> Este enlace expirará en <strong>60 minutos</strong>. Si no solicitaste este cambio, ignora este correo y tu contraseña permanecerá sin cambios.</p>
            </div>

            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin-top: 30px;">
                Por tu seguridad, nunca compartas este enlace con nadie.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Sistema APR</strong></p>
            <p style="margin-top: 15px; font-size: 12px;">Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
