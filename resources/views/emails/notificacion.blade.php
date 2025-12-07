<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notificacion->titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2563eb;
            margin-bottom: 20px;
        }
        .message {
            background: #f9fafb;
            border-left: 4px solid #2563eb;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .tipo-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .tipo-informativa { background: #dbeafe; color: #1e40af; }
        .tipo-importante { background: #fef3c7; color: #92400e; }
        .tipo-urgente { background: #fee2e2; color: #991b1b; }
        .tipo-recordatorio { background: #e0e7ff; color: #3730a3; }
        .tipo-aviso_pago { background: #d1fae5; color: #065f46; }
        .tipo-corte_servicio { background: #fee2e2; color: #991b1b; }
        .footer {
            background: #f3f4f6;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer a {
            color: #2563eb;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: bold;
        }
        .button:hover {
            background: #1d4ed8;
        }
        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .info-box strong {
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">💧</div>
            <h1>Sistema APR</h1>
            <p>Agua Potable Rural</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                Hola, <strong>{{ $socio->nombre_completo }}</strong>
            </div>

            <!-- Tipo Badge -->
            <span class="tipo-badge tipo-{{ $notificacion->tipo }}">
                {{ strtoupper(str_replace('_', ' ', $notificacion->tipo)) }}
            </span>

            <!-- Título -->
            <h2 style="color: #1e293b; margin-top: 10px;">{{ $notificacion->titulo }}</h2>

            <!-- Mensaje -->
            <div class="message">
                {!! nl2br(e($notificacion->mensaje)) !!}
            </div>

            <!-- Información del Socio -->
            <div class="info-box">
                <strong>Información de tu cuenta:</strong><br>
                <strong>N° Socio:</strong> {{ $socio->numero_socio }}<br>
                <strong>RUT:</strong> {{ $socio->rut }}<br>
                <strong>Dirección:</strong> {{ $socio->direccion }}
            </div>

            @if($notificacion->observaciones)
            <!-- Observaciones -->
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <strong>Nota adicional:</strong><br>
                <p style="color: #6b7280;">{{ $notificacion->observaciones }}</p>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Sistema APR - Agua Potable Rural</strong></p>
            <p>Este es un correo automático, por favor no responder.</p>
            <p>Si tienes consultas, contáctanos a través de nuestros canales oficiales.</p>
            <p style="margin-top: 15px;">
                &copy; {{ date('Y') }} Sistema APR. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
