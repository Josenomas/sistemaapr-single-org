<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Consulta - Sistema APR</title>
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
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .label {
            font-weight: 600;
            color: #4b5563;
            display: block;
            margin-bottom: 5px;
        }
        .value {
            color: #111827;
        }
        .mensaje-box {
            background: #eff6ff;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
            margin-top: 20px;
        }
        .footer {
            background: #f3f4f6;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>💧 Nueva Consulta - Sistema APR</h1>
    </div>

    <div class="content">
        <p>Has recibido una nueva consulta desde el sitio web de Sistema APR:</p>

        <div class="info-box">
            <div class="info-item">
                <span class="label">Nombre:</span>
                <span class="value">{{ $datos['nombre'] }}</span>
            </div>

            <div class="info-item">
                <span class="label">Email:</span>
                <span class="value"><a href="mailto:{{ $datos['email'] }}">{{ $datos['email'] }}</a></span>
            </div>

            <div class="info-item">
                <span class="label">Teléfono:</span>
                <span class="value">{{ $datos['telefono'] }}</span>
            </div>

            <div class="info-item">
                <span class="label">Nombre del APR:</span>
                <span class="value"><strong>{{ $datos['apr'] }}</strong></span>
            </div>
        </div>

        <div class="mensaje-box">
            <span class="label">Mensaje:</span>
            <p class="value">{{ $datos['mensaje'] }}</p>
        </div>

        <p style="margin-top: 30px;">
            <strong>Acción recomendada:</strong> Responde a este email o contacta directamente al cliente para ofrecer más información sobre Sistema APR.
        </p>
    </div>

    <div class="footer">
        <p>Este es un correo automático generado desde el formulario de contacto de Sistema APR.</p>
        <p>Para responder, simplemente responde a este email.</p>
    </div>
</body>
</html>
