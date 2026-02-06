<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Pago - Boleta Vencida</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f3f4f6;
        }
        .container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .alert {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert.danger {
            background: #fee2e2;
            border-left-color: #ef4444;
        }
        .alert.warning {
            background: #fef3c7;
            border-left-color: #f59e0b;
        }
        .alert h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #991b1b;
        }
        .alert.warning h3 {
            color: #92400e;
        }
        .boletas-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .boletas-table th {
            background: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #4b5563;
            font-size: 14px;
            border-bottom: 2px solid #e5e7eb;
        }
        .boletas-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .boletas-table tr:last-child td {
            border-bottom: none;
        }
        .boletas-table tr:hover {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .total-box {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 20px;
            border-radius: 6px;
            text-align: center;
            margin: 20px 0;
        }
        .total-box .label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .total-box .amount {
            font-size: 32px;
            font-weight: bold;
        }
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #1e40af;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
            color: #1e3a8a;
        }
        .payment-methods {
            background: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .payment-methods h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #111827;
        }
        .payment-methods ul {
            margin: 0;
            padding-left: 20px;
        }
        .payment-methods li {
            margin: 8px 0;
            color: #4b5563;
            font-size: 14px;
        }
        .footer {
            background: #f3f4f6;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Recordatorio de Pago</h1>
            <p>Boleta{{ $cantidadBoletas > 1 ? 's' : '' }} Vencida{{ $cantidadBoletas > 1 ? 's' : '' }}</p>
        </div>

        <div class="content">
            <p>Estimado(a) <strong>{{ $socio->nombre_completo }}</strong>,</p>

            @if($diasVencimiento > 30)
                <div class="alert danger">
                    <h3>⚠️ Boleta con más de {{ $diasVencimiento }} días de vencimiento</h3>
                    <p>Le informamos que tiene {{ $cantidadBoletas }} boleta{{ $cantidadBoletas > 1 ? 's' : '' }} vencida{{ $cantidadBoletas > 1 ? 's' : '' }} sin pagar. Esta situación puede resultar en la suspensión temporal del servicio de agua potable.</p>
                </div>
            @elseif($diasVencimiento > 15)
                <div class="alert warning">
                    <h3>⚠️ Recordatorio de pago</h3>
                    <p>Le recordamos que tiene {{ $cantidadBoletas }} boleta{{ $cantidadBoletas > 1 ? 's' : '' }} vencida{{ $cantidadBoletas > 1 ? 's' : '' }} con {{ $diasVencimiento }} días de vencimiento. Le solicitamos regularizar su situación a la brevedad.</p>
                </div>
            @else
                <div class="alert warning">
                    <h3>📋 Aviso de pago pendiente</h3>
                    <p>Le informamos que tiene {{ $cantidadBoletas }} boleta{{ $cantidadBoletas > 1 ? 's' : '' }} vencida{{ $cantidadBoletas > 1 ? 's' : '' }}. Le agradeceríamos regularizar su situación a la brevedad posible.</p>
                </div>
            @endif

            <h3 style="color: #111827; margin-top: 30px;">Detalle de Boletas Vencidas:</h3>

            <table class="boletas-table">
                <thead>
                    <tr>
                        <th>N° Boleta</th>
                        <th>Mes</th>
                        <th>Vencimiento</th>
                        <th>Monto</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boletasVencidas as $boleta)
                    <tr>
                        <td><strong>{{ $boleta->numero_boleta }}</strong></td>
                        <td>{{ $boleta->mes_texto }}</td>
                        <td>{{ $boleta->fecha_vencimiento_formateada }}</td>
                        <td><strong>${{ number_format($boleta->total, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="badge badge-danger">Vencida</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-box">
                <div class="label">TOTAL ADEUDADO</div>
                <div class="amount">${{ number_format($totalAdeudado, 0, ',', '.') }}</div>
            </div>

            <div class="info-box">
                <h3>💡 Información Importante</h3>
                <p><strong>N° Socio:</strong> {{ $socio->numero_socio }}</p>
                <p><strong>RUT:</strong> {{ $socio->rut }}</p>
                <p><strong>Dirección:</strong> {{ $socio->direccion }}</p>
                @if($socio->telefono)
                <p><strong>Teléfono:</strong> {{ $socio->telefono }}</p>
                @endif
            </div>

            <div class="payment-methods">
                <h3>💳 Formas de Pago Disponibles:</h3>
                <ul>
                    <li><strong>Transferencia Bancaria:</strong> Banco Estado | Cuenta Corriente N° XXXXXXXX | RUT: XX.XXX.XXX-X</li>
                    <li><strong>Efectivo:</strong> Oficina APR Pitrelahue (Lunes a Viernes, 9:00 - 17:00 hrs)</li>
                    <li><strong>Pago en Línea:</strong> <a href="{{ url('/consultar-cuenta') }}">www.sistemaapr.cl/consultar-cuenta</a></li>
                </ul>
            </div>

            @if($diasVencimiento > 30)
            <div class="alert danger">
                <h3>⚠️ Aviso Importante</h3>
                <p>El no pago de sus boletas puede resultar en:</p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Suspensión temporal del servicio de agua potable</li>
                    <li>Aplicación de intereses moratorios</li>
                    <li>Cambio de estado del socio a "moroso"</li>
                </ul>
            </div>
            @endif

            <p style="margin-top: 30px;">Si ya realizó el pago, por favor ignore este mensaje o envíenos el comprobante para actualizar su estado.</p>

            <p style="margin-top: 20px;">Para consultas o aclaraciones, puede contactarnos:</p>
            <ul style="color: #4b5563; font-size: 14px;">
                <li><strong>Email:</strong> contacto@aprpitrelahue.cl</li>
                <li><strong>Teléfono:</strong> +56 9 XXXX XXXX</li>
                <li><strong>Horario:</strong> Lunes a Viernes, 9:00 - 17:00 hrs</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>APR Pitrelahue</strong></p>
            <p>Agua Potable Rural</p>
            <p style="margin-top: 10px; font-size: 12px; color: #9ca3af;">
                Este es un mensaje automático, por favor no responder a este correo.<br>
                Para comunicarse con nosotros, utilice los canales indicados arriba.
            </p>
        </div>
    </div>
</body>
</html>
