@extends('layouts.app')

@section('title', 'Bienvenido - Sistema APR')

@section('content')
<div style="max-width: 900px; margin: 50px auto; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
    <div style="text-align: center; margin-bottom: 40px;">
        <div style="font-size: 4rem; color: #2563eb; margin-bottom: 20px;">
            <i class="fas fa-party-horn"></i>
        </div>
        <h1 style="color: #1f2937; font-size: 2.5rem; margin-bottom: 10px;">
            ¡Bienvenido a Sistema APR!
        </h1>
        <p style="color: #6b7280; font-size: 1.2rem;">
            Tu cuenta ha sido activada exitosamente
        </p>
    </div>

    <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 30px; border-radius: 10px; margin-bottom: 40px; border-left: 4px solid #2563eb;">
        <h3 style="color: #1e40af; margin-bottom: 15px;">
            <i class="fas fa-gift"></i> Tu período de prueba gratis
        </h3>
        <p style="color: #1e40af; font-size: 1.1rem; margin: 0;">
            Tienes <strong>30 días gratis</strong> para explorar todas las funcionalidades del sistema
        </p>
    </div>

    <h3 style="color: #1f2937; margin-bottom: 25px;">
        <i class="fas fa-rocket"></i> Primeros pasos recomendados:
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <a href="{{ route('organizacion.edit') }}" style="text-decoration: none; color: inherit;">
            <div style="padding: 25px; border: 2px solid #e5e7eb; border-radius: 10px; transition: all 0.3s; cursor: pointer;">
                <div style="font-size: 2rem; color: #2563eb; margin-bottom: 15px;">
                    <i class="fas fa-building"></i>
                </div>
                <h4 style="color: #1f2937; margin-bottom: 10px;">1. Personaliza tu organización</h4>
                <p style="color: #6b7280; font-size: 0.9rem; margin: 0;">
                    Agrega tu logo y colores personalizados
                </p>
            </div>
        </a>

        <a href="{{ route('socios.index') }}" style="text-decoration: none; color: inherit;">
            <div style="padding: 25px; border: 2px solid #e5e7eb; border-radius: 10px; transition: all 0.3s; cursor: pointer;">
                <div style="font-size: 2rem; color: #10b981; margin-bottom: 15px;">
                    <i class="fas fa-users"></i>
                </div>
                <h4 style="color: #1f2937; margin-bottom: 10px;">2. Agrega tus primeros socios</h4>
                <p style="color: #6b7280; font-size: 0.9rem; margin: 0;">
                    Comienza registrando los socios de tu APR
                </p>
            </div>
        </a>

        <a href="{{ route('configuraciones-tarifas.index') }}" style="text-decoration: none; color: inherit;">
            <div style="padding: 25px; border: 2px solid #e5e7eb; border-radius: 10px; transition: all 0.3s; cursor: pointer;">
                <div style="font-size: 2rem; color: #f59e0b; margin-bottom: 15px;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h4 style="color: #1f2937; margin-bottom: 10px;">3. Configura tus tarifas</h4>
                <p style="color: #6b7280; font-size: 0.9rem; margin: 0;">
                    Define los precios del agua para tu APR
                </p>
            </div>
        </a>
    </div>

    <div style="background: #fef3c7; padding: 20px; border-radius: 10px; border-left: 4px solid #f59e0b; margin-bottom: 30px;">
        <h4 style="color: #92400e; margin-bottom: 10px;">
            <i class="fas fa-lightbulb"></i> ¿Necesitas ayuda?
        </h4>
        <p style="color: #92400e; margin: 0;">
            Visita nuestra <a href="#" style="color: #b45309; font-weight: 600;">guía de inicio rápido</a> o 
            contáctanos en <a href="mailto:soporte@sistemaapr.cl" style="color: #b45309; font-weight: 600;">soporte@sistemaapr.cl</a>
        </p>
    </div>

    <div style="text-align: center;">
        <a href="{{ route('dashboard') }}" style="display: inline-block; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 1.1rem;">
            <i class="fas fa-home"></i> Ir al Dashboard
        </a>
    </div>
</div>

<style>
    a > div:hover {
        border-color: #2563eb !important;
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.2);
    }
</style>
@endsection
