<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu cuenta - Sistema APR</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0;">=§ Bienvenido a Sistema APR</h1>
        <p style="margin: 10px 0 0 0;">Confirma tu dirección de correo electrónico</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2>Hola {{ $registro->admin_nombre }} {{ $registro->admin_apellido }}</h2>

        <p>Gracias por registrar <strong>{{ $registro->nombre_apr }}</strong> en nuestro Sistema APR.</p>

        <p>Para activar tu cuenta y comenzar tu período de prueba de <strong>30 días gratis</strong>, necesitamos que verifiques tu dirección de correo electrónico.</p>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2563eb;">
            <p style="margin: 10px 0;"><strong style="color: #2563eb;">Organización:</strong> {{ $registro->nombre_apr }}</p>
            <p style="margin: 10px 0;"><strong style="color: #2563eb;">RUT:</strong> {{ $registro->rut }}</p>
            <p style="margin: 10px 0;"><strong style="color: #2563eb;">Email:</strong> {{ $registro->email_contacto }}</p>
        </div>

        <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>ð Importante:</strong> Este enlace de verificación expirará en <strong>48 horas</strong>.
        </div>

        <center>
            <a href="{{ url('/registro/verificar/' . $registro->token_verificacion) }}"
               style="display: inline-block; background: #2563eb; color: white !important; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 20px 0; font-weight: bold;">
                 Verificar mi cuenta
            </a>
        </center>

        <p style="margin-top: 30px; font-size: 14px; color: #666;">
            Si el botón no funciona, copia y pega este enlace en tu navegador:
            <br><br>
            <a href="{{ url('/registro/verificar/' . $registro->token_verificacion) }}" style="color: #2563eb; word-break: break-all;">
                {{ url('/registro/verificar/' . $registro->token_verificacion) }}
            </a>
        </p>

        <p style="margin-top: 30px; font-size: 14px; color: #666;">
            Si no solicitaste este registro, puedes ignorar este correo.
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
        <p>Este es un correo automático, por favor no responder.</p>
        <p>&copy; {{ date('Y') }} Sistema APR. Todos los derechos reservados.</p>
    </div>
</body>
</html>
