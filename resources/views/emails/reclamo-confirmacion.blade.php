<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamo Recibido - Sistema APR</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0;">📋 Reclamo Recibido</h1>
        <p style="margin: 10px 0 0 0;">Libro de Reclamos - Sistema APR</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2>Estimado/a {{ $reclamo->nombre_completo }}</h2>

        <p>Hemos recibido tu reclamo exitosamente y ha sido registrado en nuestro <strong>Libro de Reclamos Digital</strong> conforme a la Ley 19.496 de Protección de los Derechos de los Consumidores.</p>

        <div style="background: #28a745; color: white; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;">
            <h3 style="margin: 0 0 10px 0;">Número de Reclamo</h3>
            <div style="font-size: 32px; font-weight: bold; letter-spacing: 2px;">{{ $reclamo->numero_reclamo }}</div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc3545;">
            <h3 style="margin-top: 0; color: #dc3545;">Detalles de tu Reclamo</h3>
            <p style="margin: 10px 0;"><strong style="color: #dc3545;">Tipo:</strong> {{ $reclamo->tipo_reclamo_nombre }}</p>
            <p style="margin: 10px 0;"><strong style="color: #dc3545;">Fecha:</strong> {{ $reclamo->created_at->format('d/m/Y H:i') }}</p>
            <p style="margin: 10px 0;"><strong style="color: #dc3545;">Detalle:</strong></p>
            <p style="margin: 10px 0; padding: 10px; background: #f9f9f9; border-radius: 5px;">{{ $reclamo->detalle_reclamo }}</p>
            @if($reclamo->solucion_solicitada)
            <p style="margin: 10px 0;"><strong style="color: #dc3545;">Solución solicitada:</strong></p>
            <p style="margin: 10px 0; padding: 10px; background: #f9f9f9; border-radius: 5px;">{{ $reclamo->solucion_solicitada }}</p>
            @endif
        </div>

        <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>⏱️ Plazo de Respuesta:</strong> Según la Ley 19.496, daremos respuesta a tu reclamo dentro de un plazo máximo de <strong>5 días hábiles</strong>.
        </div>

        <div style="background: #d1ecf1; border: 1px solid #0c5460; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>📧 Notificación:</strong> Recibirás un correo electrónico cuando tu reclamo haya sido respondido.
        </div>

        <p style="margin-top: 30px;">Para cualquier consulta sobre tu reclamo, puedes contactarnos a:</p>
        <ul style="color: #666;">
            <li><strong>Email:</strong> soportesistemaapr@gmail.com</li>
            <li><strong>Número de Reclamo:</strong> {{ $reclamo->numero_reclamo }}</li>
        </ul>

        <p style="margin-top: 30px; font-size: 14px; color: #666;">
            Te recomendamos guardar este correo como comprobante de tu reclamo.
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
        <p>Este es un correo automático, por favor no responder.</p>
        <p>Para consultas sobre tu reclamo, escribe a: soportesistemaapr@gmail.com</p>
        <p>&copy; {{ date('Y') }} Sistema APR. Todos los derechos reservados.</p>
    </div>
</body>
</html>
