<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Registrado - Sistema APR</title>
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
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
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
        .greeting strong {
            color: #2563eb;
        }
        .ticket-number {
            background-color: #dbeafe;
            border-left: 4px solid #2563eb;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .ticket-number .label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .ticket-number .number {
            font-size: 24px;
            font-weight: 700;
            color: #2563eb;
        }
        .tipo-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 10px 0;
        }
        .tipo-badge.consulta { background-color: #e0f2fe; color: #0369a1; }
        .tipo-badge.reclamo { background-color: #fee2e2; color: #991b1b; }
        .tipo-badge.solicitud { background-color: #fef3c7; color: #92400e; }
        .tipo-badge.averia { background-color: #fecaca; color: #991b1b; }
        .tipo-badge.fuga { background-color: #fee2e2; color: #991b1b; }
        .tipo-badge.corte { background-color: #fecaca; color: #7f1d1d; }
        .tipo-badge.reconexion { background-color: #d1fae5; color: #065f46; }
        .tipo-badge.lectura { background-color: #dbeafe; color: #1e40af; }
        .tipo-badge.otro { background-color: #e5e7eb; color: #374151; }
        .prioridad-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 10px;
        }
        .prioridad-badge.baja { background-color: #f0fdf4; color: #15803d; }
        .prioridad-badge.media { background-color: #fef3c7; color: #92400e; }
        .prioridad-badge.alta { background-color: #fed7aa; color: #9a3412; }
        .prioridad-badge.urgente { background-color: #fecaca; color: #991b1b; }
        h2 {
            font-size: 20px;
            color: #1e293b;
            margin: 25px 0 15px 0;
        }
        .message {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            color: #334155;
            font-size: 15px;
            line-height: 1.7;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
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
        .estado-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .estado-badge.abierto { background-color: #dbeafe; color: #1e40af; }
        .estado-badge.en_proceso { background-color: #fef3c7; color: #92400e; }
        .estado-badge.pendiente { background-color: #fed7aa; color: #9a3412; }
        .estado-badge.resuelto { background-color: #d1fae5; color: #065f46; }
        .estado-badge.cerrado { background-color: #e5e7eb; color: #374151; }
        .estado-badge.cancelado { background-color: #fee2e2; color: #991b1b; }
        .alert-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-box .alert-title {
            font-weight: 700;
            color: #92400e;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .alert-box .alert-text {
            color: #92400e;
            font-size: 13px;
            line-height: 1.5;
        }
        .contact-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .contact-box h3 {
            font-size: 16px;
            color: #0c4a6e;
            margin-bottom: 10px;
        }
        .contact-box p {
            color: #075985;
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
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">🎫</div>
            <h1>Ticket Registrado</h1>
            <div class="subtitle">Sistema de Agua Potable Rural</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hola, <strong>{{ $socio->nombre_completo }}</strong>
            </div>

            <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
                Se ha registrado exitosamente un nuevo ticket en nuestro sistema. A continuación encontrarás los detalles:
            </p>

            <!-- Número de Ticket -->
            <div class="ticket-number">
                <div class="label">Número de Ticket</div>
                <div class="number">{{ $ticket->numero_ticket }}</div>
            </div>

            <!-- Tipo y Prioridad -->
            <div style="margin: 20px 0;">
                <span class="tipo-badge {{ $ticket->tipo_ticket }}">
                    {{ strtoupper(str_replace('_', ' ', $ticket->tipo_ticket)) }}
                </span>
                <span class="prioridad-badge {{ $ticket->prioridad }}">
                    {{ strtoupper($ticket->prioridad) }}
                </span>
            </div>

            <!-- Título -->
            <h2>{{ $ticket->titulo }}</h2>

            <!-- Descripción -->
            <div class="message">
                <strong style="display: block; margin-bottom: 10px; color: #475569;">Descripción del ticket:</strong>
                {!! nl2br(e($ticket->descripcion)) !!}
            </div>

            <!-- Detalles del Ticket -->
            <div class="info-box">
                <div class="info-row">
                    <div class="info-label">Estado:</div>
                    <div class="info-value">
                        <span class="estado-badge {{ $ticket->estado }}">
                            {{ strtoupper(str_replace('_', ' ', $ticket->estado)) }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de Reporte:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($ticket->fecha_reporte)->format('d/m/Y') }}</div>
                </div>
                @if($ticket->ubicacion)
                <div class="info-row">
                    <div class="info-label">Ubicación:</div>
                    <div class="info-value">{{ $ticket->ubicacion }}</div>
                </div>
                @endif
                @if($ticket->contacto_telefono)
                <div class="info-row">
                    <div class="info-label">Teléfono Contacto:</div>
                    <div class="info-value">{{ $ticket->contacto_telefono }}</div>
                </div>
                @endif
                @if($ticket->observaciones)
                <div class="info-row">
                    <div class="info-label">Observaciones:</div>
                    <div class="info-value">{{ $ticket->observaciones }}</div>
                </div>
                @endif
            </div>

            <!-- Información del Socio -->
            <div class="info-box">
                <h3 style="font-size: 16px; color: #475569; margin-bottom: 15px;">📋 Información del Socio</h3>
                <div class="info-row">
                    <div class="info-label">N° Socio:</div>
                    <div class="info-value">{{ $socio->numero_socio }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">RUT:</div>
                    <div class="info-value">{{ $socio->rut }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dirección:</div>
                    <div class="info-value">{{ $socio->direccion }}</div>
                </div>
                @if($socio->sector)
                <div class="info-row">
                    <div class="info-label">Sector:</div>
                    <div class="info-value">{{ $socio->sector }}</div>
                </div>
                @endif
            </div>

            <!-- Alert Box -->
            @if($ticket->prioridad === 'urgente' || $ticket->prioridad === 'alta')
            <div class="alert-box">
                <div class="alert-title">⚠️ Atención</div>
                <div class="alert-text">
                    Este ticket tiene prioridad <strong>{{ strtoupper($ticket->prioridad) }}</strong>.
                    Nuestro equipo se pondrá en contacto contigo a la brevedad posible.
                </div>
            </div>
            @else
            <div class="alert-box">
                <div class="alert-title">📌 Información</div>
                <div class="alert-text">
                    Tu ticket ha sido registrado correctamente. Nuestro equipo lo revisará y se pondrá en contacto contigo según la prioridad asignada.
                </div>
            </div>
            @endif

            <!-- Contact Box -->
            <div class="contact-box">
                <h3>¿Necesitas ayuda adicional?</h3>
                <p>Puedes contactarnos directamente si tienes alguna consulta sobre tu ticket.</p>
                <p><strong>Número de Ticket:</strong> {{ $ticket->numero_ticket }}</p>
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
