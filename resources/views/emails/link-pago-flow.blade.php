<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link de Pago - Sistema APR</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .info-box p {
            margin: 10px 0;
        }
        .info-box strong {
            color: #667eea;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .button:hover {
            background: #5568d3;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>💧 Sistema APR</h1>
        <p>Link de Pago Disponible</p>
    </div>

    <div class="content">
        <h2>Hola {{ $socio->nombre_completo }}</h2>

        <p>Se ha generado un link de pago para tu cuenta en el Sistema APR.</p>

        <div class="info-box">
            <p><strong>Boleta:</strong> {{ $boleta->numero_boleta }}</p>
            <p><strong>Período:</strong> {{ $boleta->mes }} {{ $boleta->anio }}</p>
            <p><strong>Monto a Pagar:</strong> ${{ number_format($monto, 0, ',', '.') }} CLP</p>
        </div>

        <div class="alert">
            <strong>⚠️ Importante:</strong> Este link de pago es único y seguro. Puedes pagar con tarjeta de débito o crédito a través de Flow.
        </div>

        <center>
            <a href="{{ $linkPago }}" class="button">
                💳 Pagar Ahora
            </a>
        </center>

        <p style="margin-top: 30px; font-size: 14px; color: #666;">
            Si el botón no funciona, puedes copiar y pegar este enlace en tu navegador:
            <br><br>
            <a href="{{ $linkPago }}" style="color: #667eea; word-break: break-all;">{{ $linkPago }}</a>
        </p>

        <p style="margin-top: 30px; font-size: 14px; color: #666;">
            Si tienes alguna pregunta o problema con el pago, no dudes en contactarnos.
        </p>
    </div>

    <div class="footer">
        <p>Este es un correo automático, por favor no responder.</p>
        <p>&copy; {{ date('Y') }} Sistema APR. Todos los derechos reservados.</p>
    </div>
</body>
</html>
