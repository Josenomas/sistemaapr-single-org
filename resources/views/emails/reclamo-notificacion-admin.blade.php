<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Reclamo Recibido - Sistema APR</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0;">⚠️ Nuevo Reclamo Recibido</h1>
        <p style="margin: 10px 0 0 0;">Libro de Reclamos - Sistema APR</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2>Administrador de Sistema APR</h2>

        <p>Se ha registrado un nuevo reclamo en el Libro de Reclamos Digital que requiere tu atención.</p>

        <div style="background: #dc3545; color: white; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;">
            <h3 style="margin: 0 0 10px 0;">Número de Reclamo</h3>
            <div style="font-size: 32px; font-weight: bold; letter-spacing: 2px;">{{ $reclamo->numero_reclamo }}</div>
        </div>

        <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>⏱️ Plazo Legal:</strong> Debes responder este reclamo dentro de <strong>5 días hábiles</strong> según la Ley 19.496.
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc3545;">
            <h3 style="margin-top: 0; color: #dc3545;">Datos del Reclamante</h3>
            <p style="margin: 10px 0;"><strong style="color: #dc3545;">Nombre:</strong> {{ $reclamo->nombre_completo }}</p>
            <p style="margin: 10px 0;"><strong style="color: #dc3545;">RUT:</strong> {{ $reclamo->rut }}</p>
            <p style="margin: 10px 0;"><strong style="color: #dc3545;">Email:</strong> {{ $reclamo->email }}</p>
            @if($reclamo->telefono)
            <p style="margin: 10px 0;"><strong style="color: #dc3545;">Teléfono:</strong> {{ $reclamo->telefono }}</p>
            @endif
            @if($reclamo->id_organizacion && $reclamo->organizacion)
            <p style="margin: 10px 0;"><strong style="color: #dc3545;">Organización:</strong> {{ $reclamo->organizacion->nombre_apr ?? 'N/A' }}</p>
            @endif
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
            <h3 style="margin-top: 0; color: #ffc107;">Detalles del Reclamo</h3>
            <p style="margin: 10px 0;"><strong style="color: #ffc107;">Tipo:</strong> {{ $reclamo->tipo_reclamo_nombre }}</p>
            <p style="margin: 10px 0;"><strong style="color: #ffc107;">Fecha:</strong> {{ $reclamo->created_at->format('d/m/Y H:i') }}</p>
            <p style="margin: 10px 0;"><strong style="color: #ffc107;">Detalle del Reclamo:</strong></p>
            <div style="padding: 15px; background: #f9f9f9; border-radius: 5px; line-height: 1.8;">
                {{ $reclamo->detalle_reclamo }}
            </div>
            @if($reclamo->solucion_solicitada)
            <p style="margin: 15px 0 10px 0;"><strong style="color: #ffc107;">Solución Solicitada:</strong></p>
            <div style="padding: 15px; background: #f9f9f9; border-radius: 5px; line-height: 1.8;">
                {{ $reclamo->solucion_solicitada }}
            </div>
            @endif
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #6c757d;">
            <h3 style="margin-top: 0; color: #6c757d;">Metadatos Técnicos</h3>
            <p style="margin: 10px 0; font-size: 13px; color: #666;"><strong>IP:</strong> {{ $reclamo->ip_address }}</p>
            <p style="margin: 10px 0; font-size: 13px; color: #666;"><strong>Navegador:</strong> {{ $reclamo->user_agent }}</p>
            <p style="margin: 10px 0; font-size: 13px; color: #666;"><strong>Estado:</strong> {{ ucfirst($reclamo->estado) }}</p>
        </div>

        <center>
            <a href="{{ url('/reclamos/' . $reclamo->id) }}"
               style="display: inline-block; background: #2563eb; color: white !important; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 20px 0; font-weight: bold;">
                 Ver y Responder Reclamo
            </a>
        </center>

        <div style="background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>⚠️ Importante:</strong> Según la Ley 19.496 Art. 17D, las multas por no responder reclamos pueden alcanzar hasta <strong>50 UTM (aprox. $3.000.000 CLP)</strong>.
        </div>

        <p style="margin-top: 30px; font-size: 14px; color: #666;">
            <strong>Recomendaciones:</strong>
        </p>
        <ul style="color: #666; font-size: 14px;">
            <li>Responder dentro de 5 días hábiles</li>
            <li>Ser claro y específico en la respuesta</li>
            <li>Ofrecer soluciones concretas cuando sea posible</li>
            <li>Mantener tono profesional y respetuoso</li>
        </ul>
    </div>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
        <p>Este es un correo automático generado por Sistema APR.</p>
        <p>&copy; {{ date('Y') }} Sistema APR. Todos los derechos reservados.</p>
    </div>
</body>
</html>
