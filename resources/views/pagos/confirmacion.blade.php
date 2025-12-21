<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Confirmado - APR Pitrilahue</title>
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

        .icon-error {
            width: 80px;
            height: 80px;
            background: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-out;
        }

        .icon-error::before {
            content: "✕";
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

        .descarga-info {
            background: #dbeafe;
            color: #1e40af;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>

    @if($isMobile)
    <script>
        // Descargar automáticamente el PDF al cargar la página (solo móvil)
        window.onload = function() {
            // Crear un enlace temporal para descargar el PDF
            const link = document.createElement('a');
            link.href = '{{ route("comprobante.descargar", $pago->id) }}';
            link.download = 'Comprobante-{{ $pago->numero_recibo }}.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };
    </script>
    @endif
</head>
<body>
    <div class="container">
        @if($exito)
            <div class="icon-success"></div>
            <h1>¡Pago Exitoso!</h1>
            <p class="mensaje">
                Tu pago ha sido procesado correctamente.
                @if($isMobile)
                    El comprobante se ha descargado automáticamente.
                @else
                    Puedes descargar tu comprobante haciendo clic en el botón de abajo.
                @endif
            </p>

            @if($isMobile)
            <div class="descarga-info">
                📥 Si la descarga no comenzó automáticamente, revisa tus descargas o notificaciones.
            </div>
            @endif

            <div class="detalle">
                <div class="detalle-item">
                    <span class="detalle-label">N° Recibo</span>
                    <span class="detalle-valor">{{ $pago->numero_recibo }}</span>
                </div>
                <div class="detalle-item">
                    <span class="detalle-label">Fecha de pago</span>
                    <span class="detalle-valor">{{ $pago->fecha_pago_formateada }}</span>
                </div>
                <div class="detalle-item">
                    <span class="detalle-label">Monto pagado</span>
                    <span class="detalle-valor">{{ $pago->monto_pagado_formateado }}</span>
                </div>
                <div class="detalle-item">
                    <span class="detalle-label">Método de pago</span>
                    <span class="detalle-valor">{{ strtoupper($pago->metodo_pago) }}</span>
                </div>
            </div>

            @if($isMobile)
                <a href="{{ route('landing') }}" class="btn-volver">Volver al Inicio</a>
            @else
                <a href="{{ route('comprobante.descargar', $pago->id) }}" class="btn-volver" style="margin-bottom: 15px;">
                    📥 Descargar Comprobante PDF
                </a>
                <br>
                <a href="{{ route('landing') }}" class="btn-volver" style="background: #6b7280;">
                    Volver al Inicio
                </a>
            @endif

            <div class="footer">
                <strong>APR Pitrilahue - Agua Potable Rural</strong><br>
                Conserva tu comprobante para futuras consultas
            </div>
        @else
            <div class="icon-error"></div>
            <h1>Pago Rechazado</h1>
            <p class="mensaje">
                Lo sentimos, tu pago no pudo ser procesado. Por favor, intenta nuevamente o contacta con nosotros.
            </p>

            <div class="detalle">
                <div class="detalle-item">
                    <span class="detalle-label">Razón</span>
                    <span class="detalle-valor">{{ $mensaje ?? 'Error en el procesamiento del pago' }}</span>
                </div>
            </div>

            <a href="{{ route('consulta.pago') }}" class="btn-volver">Intentar Nuevamente</a>

            <div class="footer">
                <strong>APR Pitrilahue - Agua Potable Rural</strong><br>
                Horario de atención: Sábado y Domingo 09:00-14:00 hrs.<br>
                Email: apr.pitrilahue@gmail.com
            </div>
        @endif
    </div>
</body>
</html>
