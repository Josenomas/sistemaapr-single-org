<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Respuesta en Ticket - Sistema APR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        .header .subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 20px;
        }
        .ticket-info {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .ticket-info .label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .ticket-info .number {
            font-size: 20px;
            font-weight: 700;
            color: #059669;
        }
        .ticket-info .title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-top: 10px;
        }
        .response-box {
            background-color: #ffffff;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .response-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 15px;
        }
        .response-author {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .response-author .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }
        .response-author .name {
            font-weight: 600;
            color: #1e293b;
            font-size: 15px;
        }
        .response-author .role {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .response-date {
            font-size: 13px;
            color: #64748b;
        }
        .response-message {
            color: #334155;
            font-size: 15px;
            line-height: 1.7;
            white-space: pre-wrap;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #64748b;
            width: 150px;
            font-size: 14px;
        }
        .info-value {
            color: #1e293b;
            flex: 1;
            font-size: 14px;
        }
        .cta-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .cta-box h3 {
            font-size: 16px;
            color: #065f46;
            margin-bottom: 10px;
        }
        .cta-box p {
            color: #047857;
            font-size: 14px;
            margin: 5px 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            color: #64748b;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
        }
        .footer a {
            color: #10b981;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">💬</div>
            <h1>Nueva Respuesta en Ticket</h1>
            <div class="subtitle">Sistema de Agua Potable Rural</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hola,
            </div>

            <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
                Se ha agregado una nueva respuesta al ticket. A continuación encontrarás los detalles:
            </p>

            <!-- Ticket Info -->
            <div class="ticket-info">
                <div class="label">Número de Ticket</div>
                <div class="number">{{ $respuesta->ticket->numero_ticket }}</div>
                <div class="title">{{ $respuesta->ticket->titulo }}</div>
            </div>

            <!-- Response Box -->
            <div class="response-box">
                <div class="response-header">
                    <div class="response-author">
                        <div class="avatar">
                            {{ substr($respuesta->autor_nombre, 0, 1) }}
                        </div>
                        <div>
                            <div class="name">{{ $respuesta->autor_nombre }}</div>
                            <div class="role">{{ $respuesta->tipo_autor }}</div>
                        </div>
                    </div>
                    <div class="response-date">
                        {{ $respuesta->fecha_creacion_formateada }}
                    </div>
                </div>
                <div class="response-message">{{ $respuesta->mensaje }}</div>
            </div>

            <!-- Información del Ticket -->
            <div class="info-box">
                <h3 style="font-size: 16px; color: #475569; margin-bottom: 15px;">📋 Información del Ticket</h3>
                <div class="info-row">
                    <div class="info-label">Estado:</div>
                    <div class="info-value">{{ $respuesta->ticket->estado_texto }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tipo:</div>
                    <div class="info-value">{{ $respuesta->ticket->tipo_ticket_texto }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Prioridad:</div>
                    <div class="info-value">{{ $respuesta->ticket->prioridad_texto }}</div>
                </div>
                @if($respuesta->ticket->asignado)
                <div class="info-row">
                    <div class="info-label">Asignado a:</div>
                    <div class="info-value">{{ $respuesta->ticket->asignado->nombre_completo }}</div>
                </div>
                @endif
            </div>

            <!-- CTA Box -->
            <div class="cta-box">
                <h3>¿Tienes más información que compartir?</h3>
                <p>Puedes contactarnos directamente para continuar con la conversación sobre este ticket.</p>
                <p><strong>Número de Ticket:</strong> {{ $respuesta->ticket->numero_ticket }}</p>
            </div>

            <p style="color: #64748b; font-size: 13px; margin-top: 30px; text-align: center;">
                Gracias por utilizar el Sistema APR. Estamos comprometidos en brindarte el mejor servicio.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Sistema APR - Agua Potable Rural</strong></p>
            <p>Este es un correo automático, por favor no responder a este mensaje.</p>
            <p style="margin-top: 10px;">
                © {{ date('Y') }} Sistema APR. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
