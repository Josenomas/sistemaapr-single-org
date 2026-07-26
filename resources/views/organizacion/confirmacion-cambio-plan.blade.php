<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Plan Confirmado - Sistema APR</title>
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

        .plan-change-visual {
            background: #f3f4f6;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .plan-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .plan-anterior {
            background: #fee2e2;
            color: #991b1b;
        }

        .plan-nuevo {
            background: #d1fae5;
            color: #065f46;
        }

        .arrow {
            color: #6b7280;
            font-size: 24px;
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

        .upgrade-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: #d1fae5;
            color: #065f46;
        }

        .downgrade-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: #fee2e2;
            color: #991b1b;
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
            background: #dbeafe;
            color: #1e40af;
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
        <h1>¡Cambio de Plan Exitoso!</h1>
        <p class="mensaje">
            Tu plan ha sido actualizado correctamente.
        </p>

        <div class="plan-change-visual">
            <span class="plan-badge plan-anterior">{{ $cambioPlan->suscripcionAnterior->nombre_mostrar }}</span>
            <span class="arrow">→</span>
            <span class="plan-badge plan-nuevo">{{ $cambioPlan->suscripcionNueva->nombre_mostrar }}</span>
        </div>

        <div class="success-info">
            @if($cambioPlan->tipo === 'upgrade')
            🎉 ¡Felicitaciones! Tu organización <strong>{{ $cambioPlan->organizacion->nombre_apr }}</strong> ahora tiene acceso a todas las funcionalidades del plan <strong>{{ $cambioPlan->suscripcionNueva->nombre_mostrar }}</strong>.
            @else
            ✅ Tu organización <strong>{{ $cambioPlan->organizacion->nombre_apr }}</strong> ahora está en el plan <strong>{{ $cambioPlan->suscripcionNueva->nombre_mostrar }}</strong>.
            @endif
        </div>

        <div class="detalle">
            <div class="detalle-item">
                <span class="detalle-label">Organización</span>
                <span class="detalle-valor">{{ $cambioPlan->organizacion->nombre_apr }}</span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">Tipo de cambio</span>
                <span class="detalle-valor">
                    @if($cambioPlan->tipo === 'upgrade')
                        <span class="upgrade-badge">UPGRADE ↑</span>
                    @else
                        <span class="downgrade-badge">DOWNGRADE ↓</span>
                    @endif
                </span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">Plan anterior</span>
                <span class="detalle-valor">{{ $cambioPlan->suscripcionAnterior->nombre_mostrar }} (${{ number_format($cambioPlan->monto_anterior, 0, ',', '.') }}/mes)</span>
            </div>
            <div class="detalle-item">
                <span class="detalle-label">Plan nuevo</span>
                <span class="detalle-valor">{{ $cambioPlan->suscripcionNueva->nombre_mostrar }} (${{ number_format($cambioPlan->monto_nuevo, 0, ',', '.') }}/mes)</span>
            </div>
            @if($cambioPlan->monto_diferencia > 0)
            <div class="detalle-item">
                <span class="detalle-label">Monto pagado</span>
                <span class="detalle-valor">${{ number_format($cambioPlan->monto_diferencia, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="detalle-item">
                <span class="detalle-label">Fecha de cambio</span>
                <span class="detalle-valor">{{ $cambioPlan->fecha_aplicacion ? $cambioPlan->fecha_aplicacion->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</span>
            </div>
            @if($cambioPlan->organizacion->fecha_fin_suscripcion)
            <div class="detalle-item">
                <span class="detalle-label">Próxima renovación</span>
                <span class="detalle-valor">{{ \Carbon\Carbon::parse($cambioPlan->organizacion->fecha_fin_suscripcion)->format('d/m/Y') }}</span>
            </div>
            @endif
            @if($cambioPlan->token_flow)
            <div class="detalle-item">
                <span class="detalle-label">Método de pago</span>
                <span class="detalle-valor">FLOW</span>
            </div>
            @endif
        </div>

        <a href="{{ route('dashboard') }}" class="btn-volver">
            Ir al Panel de Control
        </a>

        <div class="footer">
            <strong>Sistema APR - Agua Potable Rural</strong><br>
            Tu plan ha sido actualizado exitosamente.
        </div>
    </div>
</body>
</html>
