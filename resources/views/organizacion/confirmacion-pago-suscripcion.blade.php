<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Confirmado - Sistema APR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }

        .icon-success {
            width: 80px;
            height: 80px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-out;
        }

        .icon-success::before {
            content: "✓";
            color: white;
            font-size: 48px;
            font-weight: bold;
        }

        h1 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .mensaje {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .detalle {
            background: #f3f4f6;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detalle-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detalle-item:last-child {
            border-bottom: none;
        }

        .detalle-label {
            color: #6b7280;
            font-size: 14px;
        }

        .detalle-valor {
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
        }

        .plan-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-volver {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-volver:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 13px;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .success-info {
            background: #d1fae5;
            color: #065f46;
            padding: 15px 20px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-success"></div>
        <h1>¡Pago Exitoso!</h1>
        <p class="mensaje">
            Tu suscripción ha sido renovada exitosamente.
        </p>

        <div class="success-info">
            ✅ Tu organización <strong>{{ $pago->organizacion->nombre_apr }}</strong> ahora tiene acceso completo al sistema hasta el <strong>{{ $pago->organizacion->fecha_fin_suscripcion ? \Carbon\Carbon::parse($pago->organizacion->fecha_fin_suscripcion)->format('d/m/Y') : 'N/A' }}</strong>
        </div>

        <div class="detalle">
            <div class="detalle-item">
                <span class="detalle-label">Organización</span>
                <span class="detalle-valor">{{ $pago->organizacion->nombre_apr }}</span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">Plan</span>
                <span class="detalle-valor">
                    <span class="plan-badge">{{ $pago->suscripcion->nombre_mostrar }}</span>
                </span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">Monto pagado</span>
                <span class="detalle-valor">${{ number_format($pago->monto, 0, ',', '.') }}</span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">Fecha de pago</span>
                <span class="detalle-valor">{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">Período</span>
                <span class="detalle-valor">{{ $pago->periodo_inicio->format('d/m/Y') }} - {{ $pago->periodo_fin->format('d/m/Y') }}</span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">Método de pago</span>
                <span class="detalle-valor">{{ strtoupper($pago->metodo_pago) }}</span>
            </div>
            @if($pago->orden_compra)
            <div class="detalle-item">
                <span class="detalle-label">N° Orden</span>
                <span class="detalle-valor">{{ $pago->orden_compra }}</span>
            </div>
            @endif
        </div>

        <a href="{{ route('dashboard') }}" class="btn-volver">
            Ir al Panel de Control
        </a>

        <div class="footer">
            <strong>Sistema APR - Agua Potable Rural</strong><br>
            Gracias por tu pago. Tu suscripción está activa.
        </div>
    </div>
</body>
</html>
