@extends('emails.layouts.base')

@section('title', 'Bienvenido a Sistema APR')

@section('email-title')
    🎉 ¡Bienvenido a Sistema APR!
@endsection

@section('email-subtitle')
    Tu cuenta ha sido activada exitosamente
@endsection

@section('content')
    <div class="greeting">
        ¡Hola, {{ $usuario->nombre }}!
    </div>

    <div class="alert-box alert-success">
        <h2>✅ ¡Tu cuenta está lista!</h2>
        <p>
            Nos complace darte la bienvenida a <strong>Sistema APR</strong>, la plataforma integral
            para la gestión de tu organización de agua potable rural.
        </p>
    </div>

    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 15px;">📋 Datos de tu cuenta</h3>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Organización:</span>
                <span class="info-value">{{ $organizacion->nombre_apr }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Usuario:</span>
                <span class="info-value">{{ $usuario->nombre_usuario }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $usuario->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Plan:</span>
                <span class="info-value">{{ $organizacion->suscripcion->nombre }}</span>
            </div>
            @if($organizacion->fecha_fin_prueba)
            <div class="info-row">
                <span class="info-label">Período de Prueba:</span>
                <span class="info-value">Hasta {{ $organizacion->fecha_fin_prueba->format('d/m/Y') }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 15px;">🚀 Primeros pasos</h3>
        <div style="background: #f9fafb; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <ol style="margin: 0; padding-left: 20px; color: #4b5563;">
                <li style="margin-bottom: 12px;">
                    <strong>Configura tu organización:</strong> Personaliza tu perfil, agrega tu logo y ajusta los colores del sistema.
                </li>
                <li style="margin-bottom: 12px;">
                    <strong>Registra tus socios:</strong> Comienza ingresando los datos de tus socios y medidores.
                </li>
                <li style="margin-bottom: 12px;">
                    <strong>Configura tarifas:</strong> Define las tarifas de agua según tu sistema de cobro.
                </li>
                <li style="margin-bottom: 12px;">
                    <strong>Ingresa lecturas:</strong> Registra las lecturas mensuales de consumo de agua.
                </li>
                <li>
                    <strong>Genera boletas:</strong> Crea automáticamente las boletas de cobro mensual.
                </li>
            </ol>
        </div>
    </div>

    <div class="btn-center">
        <a href="{{ url('/login') }}" class="btn btn-primary">
            🔐 Iniciar Sesión
        </a>
    </div>

    <div class="divider"></div>

    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 15px;">💡 Recursos útiles</h3>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 8px;">📚</div>
                <strong style="color: #1f2937;">Guía de Usuario</strong>
                <p style="margin: 5px 0 0 0; font-size: 13px; color: #6b7280;">Aprende a usar todas las funciones</p>
            </div>
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 8px;">🎥</div>
                <strong style="color: #1f2937;">Video Tutoriales</strong>
                <p style="margin: 5px 0 0 0; font-size: 13px; color: #6b7280;">Mira cómo funciona el sistema</p>
            </div>
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 8px;">💬</div>
                <strong style="color: #1f2937;">Soporte Técnico</strong>
                <p style="margin: 5px 0 0 0; font-size: 13px; color: #6b7280;">Estamos aquí para ayudarte</p>
            </div>
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 8px;">📞</div>
                <strong style="color: #1f2937;">Contacto</strong>
                <p style="margin: 5px 0 0 0; font-size: 13px; color: #6b7280;">Contáctanos cuando lo necesites</p>
            </div>
        </div>
    </div>

    <div class="alert-box alert-info" style="margin-top: 30px;">
        <h2>💳 Información sobre tu plan</h2>
        <p>
            @if($organizacion->estado_suscripcion === 'prueba')
                Actualmente estás en el <strong>período de prueba gratuito</strong> que vence el
                {{ $organizacion->fecha_fin_prueba->format('d/m/Y') }}. Puedes actualizar tu plan en
                cualquier momento desde la configuración de tu organización.
            @else
                Tu plan <strong>{{ $organizacion->suscripcion->nombre }}</strong> incluye:
                <ul style="margin: 10px 0 0 20px; padding: 0;">
                    <li>Hasta {{ $organizacion->suscripcion->limite_socios == 0 ? 'ilimitados' : $organizacion->suscripcion->limite_socios }} socios</li>
                    <li>Hasta {{ $organizacion->suscripcion->limite_usuarios == 0 ? 'ilimitados' : $organizacion->suscripcion->limite_usuarios }} usuarios</li>
                    @if($organizacion->suscripcion->permite_dominio_personalizado)
                    <li>Dominio personalizado</li>
                    @endif
                    @if($organizacion->suscripcion->nombre === 'enterprise')
                    <li>Módulo de noticias públicas</li>
                    @endif
                </ul>
            @endif
        </p>
    </div>

    <p class="text-muted text-center" style="margin-top: 30px;">
        Si tienes alguna pregunta, no dudes en contactarnos. ¡Estamos aquí para ayudarte!
    </p>
@endsection

@section('footer-extra')
    <p style="margin-top: 15px; font-size: 13px; color: #6b7280;">
        ¿Necesitas ayuda? Visita nuestro centro de soporte o contáctanos directamente.
    </p>
@endsection
