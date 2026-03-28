<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirma tu email - Sistema APR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { max-width: 600px; background: white; border-radius: 15px; padding: 60px 40px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        .icon { font-size: 4rem; color: #2563eb; margin-bottom: 20px; }
        h1 { color: #1f2937; font-size: 2rem; margin-bottom: 20px; }
        p { color: #6b7280; line-height: 1.6; margin-bottom: 15px; }
        .email-box { background: #f0f9ff; padding: 20px; border-radius: 8px; margin: 30px 0; border-left: 4px solid #2563eb; }
        .email-box strong { color: #2563eb; }
        .btn { display: inline-block; background: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-envelope-circle-check"></i>
        </div>
        <h1>¡Revisa tu correo electrónico!</h1>
        <p>Hemos enviado un correo de verificación a tu dirección de email.</p>
        
        @if(session('success'))
        <div class="email-box">
            {{ session('success') }}
        </div>
        @endif

        <p><strong>¿Qué sigue?</strong></p>
        <ol style="text-align: left; color: #6b7280; line-height: 2;">
            <li>Abre tu correo electrónico</li>
            <li>Busca el mensaje de "Sistema APR"</li>
            <li>Haz clic en el enlace de verificación</li>
            <li>¡Listo! Podrás comenzar a usar el sistema</li>
        </ol>

        <p style="margin-top: 30px; font-size: 0.9rem; color: #9ca3af;">
            <i class="fas fa-info-circle"></i> El enlace expirará en 48 horas
        </p>

        <a href="{{ route('login') }}" class="btn">
            Ir al inicio de sesión
        </a>
    </div>
</body>
</html>
