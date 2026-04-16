<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción por Vencer</title>
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
            border-bottom: 3px solid #2563eb;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .alert {
            background: {{ $dias <= 3 ? '#fef3c7' : '#dbeafe' }};
            border-left: 4px solid {{ $dias <= 3 ? '#f59e0b' : '#2563eb' }};
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .alert strong {
            color: {{ $dias <= 3 ? '#92400e' : '#1e3a8a' }};
            font-size: 18px;
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
            background: #2563eb;
            color: white !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .button:hover {
            background: #1d4ed8;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 14px;
        }
        .urgent {
            background: #fee2e2;
            border: 2px solid #dc2626;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .urgent strong {
            color: #991b1b;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">{{ $dias <= 3 ? '⚠️' : '💧' }}</div>
            <h1>Sistema APR</h1>
            <p>Gestión Integral de Agua Potable Rural</p>
        </div>

        @if($dias <= 3)
            <div class="urgent">
                <strong>⚠️ ACCIÓN URGENTE REQUERIDA ⚠️</strong>
            </div>
        @endif

        <div class="alert">
            <strong>Tu suscripción vence en {{ $dias }} {{ $dias == 1 ? 'día' : 'días' }}</strong>
        </div>

        <p>Hola <strong>{{ $pago->organizacion->nombre_apr }}</strong>,</p>

        <p>
            Te recordamos que tu suscripción al plan <strong>{{ $pago->suscripcion->nombre }}</strong>
            vencerá el <strong>{{ $pago->fecha_vencimiento->format('d/m/Y') }}</strong>.
        </p>

        @if($dias <= 3)
            <p style="color: #dc2626; font-weight: bold;">
                ⚠️ Para evitar la suspensión de tu cuenta, debes realizar el pago antes de la fecha de vencimiento.
            </p>
        @endif

        <div class="info-box">
            <h3 style="margin-top: 0; color: #1e293b;">Detalles del Pago</h3>

            <div class="info-row">
                <span class="info-label">Plan:</span>
                <span class="info-value">{{ $pago->suscripcion->nombre }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Monto:</span>
                <span class="info-value" style="font-size: 20px; font-weight: bold; color: #2563eb;">
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
                <span class="info-value" style="color: {{ $dias <= 3 ? '#dc2626' : '#1e293b' }}; font-weight: bold;">
                    {{ $pago->fecha_vencimiento->format('d/m/Y') }}
                </span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/login') }}" class="button">
                💳 Pagar Ahora
            </a>
        </div>

        <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #1e293b;">📌 Importante:</h4>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>El pago debe realizarse antes de la fecha de vencimiento</li>
                <li>Si no se realiza el pago a tiempo, tu cuenta será suspendida automáticamente</li>
                <li>Mientras tu cuenta esté suspendida, no podrás acceder al sistema</li>
                <li>Al realizar el pago, tu cuenta se reactivará automáticamente</li>
            </ul>
        </div>

        <div style="background: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong style="color: #065f46;">💡 Consejo:</strong>
            <p style="margin: 5px 0 0 0; color: #047857;">
                Para evitar interrupciones en el servicio, te recomendamos realizar el pago con anticipación.
            </p>
        </div>

        <div class="footer">
            <p><strong>{{ $pago->organizacion->nombre_apr }}</strong></p>
            <p>{{ $pago->organizacion->email_contacto }}</p>
            <p style="margin-top: 20px;">
                <small>
                    Este es un correo automático, por favor no respondas a este mensaje.<br>
                    Si tienes alguna consulta, ingresa al sistema o contáctanos a soportesistemaapr@gmail.com
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
