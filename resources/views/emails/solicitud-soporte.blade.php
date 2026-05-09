<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Soporte</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); padding: 40px 30px; text-align: center;">
                            <div style="background-color: white; width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
                                <span style="font-size: 40px; color: #3b82f6;">💬</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                Solicitud de Soporte
                            </h1>
                            <p style="margin: 10px 0 0; color: #e0e7ff; font-size: 16px;">
                                Nueva solicitud de ayuda desde el sistema
                            </p>
                        </td>
                    </tr>

                    <!-- Alerta -->
                    <tr>
                        <td style="padding: 30px 30px 0;">
                            <div style="background-color: #dbeafe; border-left: 4px solid #3b82f6; padding: 16px 20px; border-radius: 8px;">
                                <p style="margin: 0; color: #1e40af; font-size: 15px;">
                                    <strong style="font-size: 16px;">📢 Atención:</strong> Un usuario ha solicitado ayuda desde el Sistema APR.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Información de la Organización -->
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 20px; font-weight: 600; border-bottom: 2px solid #e5e7eb; padding-bottom: 12px;">
                                📋 Información de la Organización
                            </h2>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                        <strong style="color: #6b7280; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Organización:</strong>
                                        <div style="color: #111827; font-size: 16px; margin-top: 4px; font-weight: 600;">
                                            {{ $organizacion->nombre_apr }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                        <strong style="color: #6b7280; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">RUT:</strong>
                                        <div style="color: #111827; font-size: 16px; margin-top: 4px;">
                                            {{ $organizacion->rut }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                        <strong style="color: #6b7280; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Email de contacto:</strong>
                                        <div style="color: #111827; font-size: 16px; margin-top: 4px;">
                                            <a href="mailto:{{ $organizacion->email }}" style="color: #3b82f6; text-decoration: none;">
                                                {{ $organizacion->email }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                        <strong style="color: #6b7280; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Estado de Suscripción:</strong>
                                        <div style="margin-top: 4px;">
                                            <span style="display: inline-block; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600;
                                                @if($organizacion->suscripcion && $organizacion->suscripcion->estado === 'activa')
                                                    background-color: #d1fae5; color: #065f46;
                                                @elseif($organizacion->suscripcion && $organizacion->suscripcion->estado === 'suspendida')
                                                    background-color: #fed7aa; color: #92400e;
                                                @else
                                                    background-color: #fee2e2; color: #991b1b;
                                                @endif
                                            ">
                                                {{ strtoupper($organizacion->suscripcion?->estado ?? 'N/A') }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Información del Usuario -->
                            <h2 style="margin: 24px 0 20px; color: #1f2937; font-size: 20px; font-weight: 600; border-bottom: 2px solid #e5e7eb; padding-bottom: 12px;">
                                👤 Usuario que Reporta
                            </h2>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                        <strong style="color: #6b7280; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Nombre:</strong>
                                        <div style="color: #111827; font-size: 16px; margin-top: 4px;">
                                            {{ $usuario->nombre }} {{ $usuario->apellido }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                        <strong style="color: #6b7280; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Email:</strong>
                                        <div style="color: #111827; font-size: 16px; margin-top: 4px;">
                                            <a href="mailto:{{ $usuario->email }}" style="color: #3b82f6; text-decoration: none;">
                                                {{ $usuario->email }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                        <strong style="color: #6b7280; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Rol:</strong>
                                        <div style="color: #111827; font-size: 16px; margin-top: 4px;">
                                            <span style="display: inline-block; padding: 4px 10px; background-color: #e0e7ff; color: #3730a3; border-radius: 6px; font-size: 14px; font-weight: 500;">
                                                {{ ucfirst($usuario->rol) }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0;">
                                        <strong style="color: #6b7280; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Fecha y Hora:</strong>
                                        <div style="color: #111827; font-size: 16px; margin-top: 4px;">
                                            {{ now()->format('d/m/Y H:i:s') }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Solicitud del Usuario -->
                            @if($asunto)
                            <h2 style="margin: 24px 0 20px; color: #1f2937; font-size: 20px; font-weight: 600; border-bottom: 2px solid #e5e7eb; padding-bottom: 12px;">
                                📝 Asunto
                            </h2>
                            <div style="background-color: #f9fafb; padding: 16px 20px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-bottom: 24px;">
                                <p style="margin: 0; color: #111827; font-size: 16px; font-weight: 600;">
                                    {{ $asunto }}
                                </p>
                            </div>
                            @endif

                            @if($descripcion)
                            <h2 style="margin: 24px 0 20px; color: #1f2937; font-size: 20px; font-weight: 600; border-bottom: 2px solid #e5e7eb; padding-bottom: 12px;">
                                💬 Descripción del Problema
                            </h2>
                            <div style="background-color: #fffbeb; padding: 20px; border-radius: 8px; border: 1px solid #fcd34d; margin-bottom: 24px;">
                                <p style="margin: 0; color: #78350f; font-size: 15px; line-height: 1.7; white-space: pre-wrap;">{{ $descripcion }}</p>
                            </div>
                            @endif

                            @if(!$asunto && !$descripcion && $mensajeAdicional)
                            <h2 style="margin: 24px 0 20px; color: #1f2937; font-size: 20px; font-weight: 600; border-bottom: 2px solid #e5e7eb; padding-bottom: 12px;">
                                💬 Mensaje
                            </h2>
                            <div style="background-color: #fffbeb; padding: 20px; border-radius: 8px; border: 1px solid #fcd34d; margin-bottom: 24px;">
                                <p style="margin: 0; color: #78350f; font-size: 15px; line-height: 1.7; white-space: pre-wrap;">{{ $mensajeAdicional }}</p>
                            </div>
                            @endif

                        </td>
                    </tr>

                    <!-- Acciones Recomendadas -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <div style="background-color: #f0fdf4; border: 2px solid #86efac; border-radius: 8px; padding: 20px;">
                                <h3 style="margin: 0 0 12px; color: #14532d; font-size: 16px; font-weight: 600;">
                                    ✅ Pasos a Seguir
                                </h3>
                                <ol style="margin: 0; padding-left: 20px; color: #166534; font-size: 14px; line-height: 1.8;">
                                    <li>Revisar el estado de la organización en el panel administrativo</li>
                                    <li>Contactar al usuario vía email o teléfono</li>
                                    <li>Resolver la consulta/problema reportado</li>
                                    <li>Documentar la solución para referencias futuras</li>
                                </ol>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                Este correo fue generado automáticamente por
                            </p>
                            <p style="margin: 0; color: #3b82f6; font-size: 16px; font-weight: 600;">
                                Sistema APR
                            </p>
                            <p style="margin: 12px 0 0; color: #9ca3af; font-size: 12px;">
                                No responder a este correo. Contactar directamente al usuario.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
