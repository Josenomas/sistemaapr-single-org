@extends('emails.layouts.base')

@section('title', 'Confirmación de Pago')

@section('email-title')
    ✅ Pago Confirmado
@endsection

@section('email-subtitle')
    Tu pago ha sido procesado exitosamente
@endsection

@section('extra-styles')
<style>
    .success-badge {
        display: inline-block;
        background: #d1fae5;
        color: #065f46;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        margin: 15px 0;
    }

    .amount-highlight {
        font-size: 36px;
        font-weight: 700;
        color: #059669;
        margin: 20px 0;
        text-align: center;
    }
</style>
@endsection

@section('content')
    <div class="greeting">
        Hola, {{ $pago->usuario->nombre ?? $organizacion->nombre_apr }}
    </div>

    <div class="alert-box alert-success">
        <h2>🎉 ¡Pago Procesado Exitosamente!</h2>
        <p>
            Hemos recibido y confirmado tu pago. Gracias por mantener tu suscripción activa.
        </p>
    </div>

    <div class="text-center">
        <div class="amount-highlight">
            ${{ number_format($pago->monto, 0, ',', '.') }} CLP
        </div>
        <span class="success-badge">✓ PAGADO</span>
    </div>

    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 15px;">📄 Detalles del Pago</h3>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">N° de Transacción:</span>
                <span class="info-value">#{{ $pago->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Pago:</span>
                <span class="info-value">{{ $pago->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Método de Pago:</span>
                <span class="info-value">{{ ucfirst($pago->metodo_pago ?? 'Flow') }}</span>
            </div>
            @if(isset($pago->suscripcion))
            <div class="info-row">
                <span class="info-label">Plan:</span>
                <span class="info-value">{{ $pago->suscripcion->nombre }}</span>
            </div>
            @endif
            @if(isset($pago->periodo_inicio) && isset($pago->periodo_fin))
            <div class="info-row">
                <span class="info-label">Período:</span>
                <span class="info-value">
                    {{ $pago->periodo_inicio->format('d/m/Y') }} - {{ $pago->periodo_fin->format('d/m/Y') }}
                </span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="info-value" style="color: #059669;">✓ Confirmado</span>
            </div>
        </div>
    </div>

    @if(isset($organizacion))
    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 15px;">🏢 Información de la Organización</h3>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Organización:</span>
                <span class="info-value">{{ $organizacion->nombre_apr }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">RUT:</span>
                <span class="info-value">{{ $organizacion->rut }}</span>
            </div>
            @if($organizacion->email_contacto)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $organizacion->email_contacto }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="divider"></div>

    <div class="alert-box alert-info">
        <h2>📧 Comprobante de Pago</h2>
        <p>
            Este email sirve como comprobante oficial de tu pago. Te recomendamos guardarlo
            para tus registros contables.
        </p>
    </div>

    <div class="btn-center">
        <a href="{{ url('/organizacion/pagos-suscripcion') }}" class="btn btn-primary">
            📊 Ver Historial de Pagos
        </a>
    </div>

    <div class="content-section" style="margin-top: 30px;">
        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #d1fae5 100%); border-radius: 12px; padding: 20px; border: 2px solid #059669;">
            <div style="text-align: center; margin-bottom: 15px;">
                <span style="font-size: 32px;">🎁</span>
            </div>
            <h3 style="color: #065f46; margin: 0 0 10px 0; text-align: center;">¡Gracias por confiar en nosotros!</h3>
            <p style="color: #047857; margin: 0; text-align: center; font-size: 14px;">
                Tu pago nos permite seguir mejorando el sistema para ti
            </p>
        </div>
    </div>

    <p class="text-muted text-center" style="margin-top: 30px;">
        Si tienes alguna pregunta sobre este pago, no dudes en contactarnos.
    </p>
@endsection

@section('footer-extra')
    <p style="margin-top: 15px; font-size: 13px; color: #6b7280;">
        Este comprobante fue generado automáticamente. Para consultas, contáctanos a través del sistema.
    </p>
@endsection
