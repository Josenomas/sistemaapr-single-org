@extends('emails.layouts.base')

@section('title', 'Solicitud de Registro - Sistema APR')

@section('email-title')
    📋 Actualización de tu Solicitud
@endsection

@section('email-subtitle')
    Información sobre tu registro en Sistema APR
@endsection

@section('content')
    <div class="greeting">
        Hola,
    </div>

    <div class="content-section">
        <p style="color: #4b5563; line-height: 1.6; margin-bottom: 20px;">
            Gracias por tu interés en <strong>Sistema APR</strong>. Te escribimos para informarte
            sobre el estado de tu solicitud de registro.
        </p>
    </div>

    <div class="alert-box alert-warning">
        <h2>ℹ️ Estado de tu Solicitud</h2>
        <p>
            Después de revisar tu solicitud de registro para la organización
            <strong>{{ $registro->nombre_apr }}</strong>, lamentamos informarte que
            en este momento no podemos proceder con la activación de tu cuenta.
        </p>
    </div>

    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 15px;">📋 Datos de la solicitud</h3>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Organización:</span>
                <span class="info-value">{{ $registro->nombre_apr }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">RUT:</span>
                <span class="info-value">{{ $registro->rut }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email de Contacto:</span>
                <span class="info-value">{{ $registro->email_contacto }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Solicitud:</span>
                <span class="info-value">{{ $registro->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 15px;">💬 ¿Tienes dudas?</h3>
        <p style="color: #4b5563; line-height: 1.6; margin-bottom: 15px;">
            Si deseas más información sobre esta decisión o necesitas asistencia, no dudes en contactarnos:
        </p>
        <div style="background: #f9fafb; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <span style="font-size: 24px;">📧</span>
                <div>
                    <strong style="color: #1f2937;">Email de Soporte:</strong><br>
                    <span style="color: #6b7280;">sistemaapr@gmail.com</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 24px;">💻</span>
                <div>
                    <strong style="color: #1f2937;">Sitio Web:</strong><br>
                    <a href="{{ url('/') }}" style="color: #2563eb; text-decoration: none;">{{ url('/') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="alert-box alert-info">
        <h2>🔄 ¿Quieres volver a intentarlo?</h2>
        <p>
            Si consideras que hubo un error o deseas proporcionar información adicional,
            puedes enviar una nueva solicitud de registro o contactarnos directamente
            para aclarar cualquier situación.
        </p>
    </div>

    <div class="btn-center" style="margin-top: 30px;">
        <a href="{{ url('/registro') }}" class="btn btn-primary">
            📝 Nueva Solicitud
        </a>
    </div>

    <p class="text-muted text-center" style="margin-top: 30px;">
        Agradecemos tu comprensión y quedamos a tu disposición para cualquier consulta.
    </p>
@endsection

@section('footer-extra')
    <p style="margin-top: 15px; font-size: 13px; color: #6b7280;">
        Este es un mensaje automático. Si tienes preguntas, por favor responde a este correo.
    </p>
@endsection
