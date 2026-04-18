<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuesta a tu Reclamo - Sistema APR</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, {{ $reclamo->estado === 'resuelto' ? '#28a745' : '#ffc107' }} 0%, {{ $reclamo->estado === 'resuelto' ? '#218838' : '#e0a800' }} 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0;">✅ Respuesta a tu Reclamo</h1>
        <p style="margin: 10px 0 0 0;">Libro de Reclamos - Sistema APR</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2>Estimado/a {{ $reclamo->nombre_completo }}</h2>

        <p>Hemos procesado tu reclamo <strong>{{ $reclamo->numero_reclamo }}</strong> y queremos informarte sobre la resolución del mismo.</p>

        <div style="background: {{ $reclamo->estado === 'resuelto' ? '#d4edda' : '#fff3cd' }}; border: 1px solid {{ $reclamo->estado === 'resuelto' ? '#28a745' : '#ffc107' }}; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: {{ $reclamo->estado === 'resuelto' ? '#155724' : '#856404' }};">
                Estado: {{ $reclamo->estado === 'resuelto' ? '✅ Resuelto' : '⚠️ Rechazado' }}
            </h3>
            <p style="margin: 0;"><strong>Fecha de Respuesta:</strong> {{ $reclamo->fecha_respuesta->format('d/m/Y H:i') }}</p>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2563eb;">
            <h3 style="margin-top: 0; color: #2563eb;">Tu Reclamo Original</h3>
            <p style="margin: 10px 0;"><strong style="color: #2563eb;">Tipo:</strong> {{ $reclamo->tipo_reclamo_nombre }}</p>
            <p style="margin: 10px 0;"><strong style="color: #2563eb;">Fecha:</strong> {{ $reclamo->created_at->format('d/m/Y H:i') }}</p>
            <p style="margin: 10px 0;"><strong style="color: #2563eb;">Detalle:</strong></p>
            <p style="margin: 10px 0; padding: 10px; background: #f9f9f9; border-radius: 5px;">{{ $reclamo->detalle_reclamo }}</p>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid {{ $reclamo->estado === 'resuelto' ? '#28a745' : '#ffc107' }};">
            <h3 style="margin-top: 0; color: {{ $reclamo->estado === 'resuelto' ? '#28a745' : '#856404' }};">Nuestra Respuesta</h3>
            <div style="padding: 15px; background: #f9f9f9; border-radius: 5px; line-height: 1.8;">
                {{ $reclamo->respuesta }}
            </div>
        </div>

        @if($reclamo->estado === 'resuelto')
        <div style="background: #d4edda; border: 1px solid #28a745; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>✅ Agradecimiento:</strong> Gracias por tu comprensión. Esperamos haber resuelto tu inquietud satisfactoriamente.
        </div>
        @else
        <div style="background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>⚠️ Disconformidad:</strong> Si no estás de acuerdo con esta respuesta, puedes presentar tu caso ante <strong>SERNAC</strong> (Servicio Nacional del Consumidor) en <a href="https://www.sernac.cl" style="color: #dc3545;">www.sernac.cl</a>
        </div>
        @endif

        <p style="margin-top: 30px;">Si tienes alguna duda adicional, puedes contactarnos a:</p>
        <ul style="color: #666;">
            <li><strong>Email:</strong> soportesistemaapr@gmail.com</li>
            <li><strong>Número de Reclamo:</strong> {{ $reclamo->numero_reclamo }}</li>
        </ul>

        <p style="margin-top: 30px; font-size: 14px; color: #666;">
            Te recomendamos guardar este correo como comprobante de la respuesta a tu reclamo.
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
        <p>Este es un correo automático, por favor no responder.</p>
        <p>Para consultas, escribe a: soportesistemaapr@gmail.com</p>
        <p>&copy; {{ date('Y') }} Sistema APR. Todos los derechos reservados.</p>
    </div>
</body>
</html>
