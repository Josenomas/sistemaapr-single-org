<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Reactivación - Sistema APR</title>
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
            background: linear-gradient(135deg, #f59e0b, #d97706);
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
        .alert-box {
            background: #fef3c7;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #f59e0b;
            margin-bottom: 20px;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
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
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-suspendida {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-cancelada {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔄 Solicitud de Reactivación</h1>
    </div>

    <div class="content">
        <div class="alert-box">
            <strong>⚠️ Atención:</strong> Una organización con suscripción {{ $organizacion->estado_suscripcion }} ha solicitado reactivación.
        </div>

        <p>Se ha recibido una solicitud de reactivación de cuenta desde el sistema:</p>

        <div class="info-box">
            <div class="info-item">
                <span class="label">Organización:</span>
                <span class="value"><strong>{{ $organizacion->nombre_apr }}</strong></span>
            </div>

            <div class="info-item">
                <span class="label">RUT:</span>
                <span class="value">{{ $organizacion->rut }}</span>
            </div>

            <div class="info-item">
                <span class="label">Email de contacto:</span>
                <span class="value"><a href="mailto:{{ $organizacion->email_contacto }}">{{ $organizacion->email_contacto }}</a></span>
            </div>

            <div class="info-item">
                <span class="label">Estado actual:</span>
                <span class="value">
                    <span class="badge badge-{{ $organizacion->estado_suscripcion }}">
                        {{ strtoupper($organizacion->estado_suscripcion) }}
                    </span>
                </span>
            </div>

            <div class="info-item">
                <span class="label">Usuario solicitante:</span>
                <span class="value">{{ $usuario->name }} ({{ $usuario->email }})</span>
            </div>

            <div class="info-item">
                <span class="label">Fecha de solicitud:</span>
                <span class="value">{{ now()->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>

        <div class="info-box">
            <div class="label">Motivo de la solicitud:</div>
            <p class="value">Solicitud de reactivación de cuenta {{ $organizacion->estado_suscripcion }}</p>
        </div>

        @if($mensajeAdicional)
        <div class="mensaje-box">
            <span class="label">📝 Mensaje adicional del usuario:</span>
            <p class="value" style="white-space: pre-wrap;">{{ $mensajeAdicional }}</p>
        </div>
        @endif

        <p style="margin-top: 30px;">
            <strong>Próximos pasos:</strong>
        </p>
        <ul>
            <li>Revisar el estado de pagos de la organización</li>
            <li>Verificar la razón de la suspensión/cancelación</li>
            <li>Contactar a la organización para resolver la situación</li>
            <li>Reactivar la cuenta si corresponde</li>
        </ul>
    </div>

    <div class="footer">
        <p>Este es un correo automático generado desde Sistema APR.</p>
        <p>Para responder al usuario, contacta a: <strong>{{ $organizacion->email_contacto }}</strong></p>
    </div>
</body>
</html>
