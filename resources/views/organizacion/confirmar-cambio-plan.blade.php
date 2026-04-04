@extends('layouts.app')

@section('title', 'Confirmar Cambio de Plan - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exchange-alt"></i>
        Confirmar Cambio de Plan
    </h2>
</div>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('suscripcion.renovar') }}">Renovación</a></li>
        <li class="breadcrumb-item active">Confirmar cambio de plan</li>
    </ol>
</nav>

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle"></i>
            Resumen del Cambio
        </h3>
    </div>
    <div class="card-body">
        <!-- Comparación de Planes -->
        <div class="plan-comparison">
            <div class="plan-card plan-current">
                <div class="plan-label">
                    <i class="fas fa-check-circle"></i>
                    Plan Actual
                </div>
                <h4 class="plan-name">{{ $planActual->nombre }}</h4>
                <div class="plan-price">${{ number_format($planActual->precio_mensual, 0, ',', '.') }}</div>
                <div class="plan-period">por mes</div>
            </div>

            <div class="plan-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>

            <div class="plan-card plan-new">
                <div class="plan-label plan-label-{{ $tipo }}">
                    <i class="fas fa-{{ $tipo == 'upgrade' ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ $tipo == 'upgrade' ? 'Upgrade' : 'Downgrade' }}
                </div>
                <h4 class="plan-name">{{ $planNuevo->nombre }}</h4>
                <div class="plan-price">${{ number_format($planNuevo->precio_mensual, 0, ',', '.') }}</div>
                <div class="plan-period">por mes</div>
            </div>
        </div>

        <!-- Características del Plan Nuevo -->
        <div class="features-section">
            <h4 class="features-title">
                <i class="fas fa-list-check"></i>
                Características del plan {{ $planNuevo->nombre }}
            </h4>
            <ul class="features-list">
                @if($planNuevo->socios_ilimitados)
                <li class="feature-item">
                    <i class="fas fa-check feature-icon"></i>
                    <span>Socios ilimitados</span>
                </li>
                @else
                <li class="feature-item">
                    <i class="fas fa-check feature-icon"></i>
                    <span>Hasta {{ $planNuevo->max_socios }} socios</span>
                </li>
                @endif

                @if($planNuevo->usuarios_ilimitados)
                <li class="feature-item">
                    <i class="fas fa-check feature-icon"></i>
                    <span>Usuarios ilimitados</span>
                </li>
                @else
                <li class="feature-item">
                    <i class="fas fa-check feature-icon"></i>
                    <span>Hasta {{ $planNuevo->max_usuarios }} usuarios</span>
                </li>
                @endif

                @if($planNuevo->permite_dominio_personalizado)
                <li class="feature-item">
                    <i class="fas fa-check feature-icon"></i>
                    <span>Dominio personalizado</span>
                </li>
                @endif

                <li class="feature-item">
                    <i class="fas fa-check feature-icon"></i>
                    <span>Soporte técnico prioritario</span>
                </li>
            </ul>
        </div>

        <!-- Monto a Pagar -->
        <div class="payment-summary">
            <div class="payment-header">
                <i class="fas fa-credit-card"></i>
                Monto a Pagar
            </div>
            <div class="payment-amount">
                ${{ number_format($montoPagar, 0, ',', '.') }}
            </div>
            <div class="payment-description">
                @if($organizacion->suscripcionVencida())
                    <i class="fas fa-info-circle"></i>
                    Precio completo del plan (suscripción vencida)
                @else
                    <i class="fas fa-calculator"></i>
                    Diferencia prorrateada por {{ $diasRestantes ?? 0 }} días restantes
                @endif
            </div>
        </div>

        <!-- Nota importante -->
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Upgrade de plan:</strong> El cambio se aplicará inmediatamente después de confirmar el pago.
        </div>

        <!-- Botones de acción -->
        <div class="action-buttons">
            <a href="{{ route('suscripcion.renovar') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Cancelar
            </a>
            <form action="{{ route('organizacion.cambiar-plan', $planNuevo->id) }}" method="POST" class="form-inline">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i>
                    Confirmar y Pagar con Flow
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .page-title i {
        color: var(--primary);
    }

    .breadcrumb {
        background: var(--gray-100);
        padding: 12px 16px;
        border-radius: var(--radius);
        margin-bottom: 24px;
        display: flex;
        list-style: none;
        gap: 8px;
        font-size: 0.875rem;
    }

    .breadcrumb-item {
        color: var(--gray-600);
    }

    .breadcrumb-item a {
        color: var(--primary);
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: var(--gray-700);
        font-weight: 600;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: '/';
        margin-right: 8px;
        color: var(--gray-400);
    }

    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #dc2626;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 2px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .card-title i {
        color: var(--primary);
    }

    .card-body {
        padding: 24px;
    }

    .plan-comparison {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 24px;
        margin-bottom: 32px;
        align-items: center;
    }

    .plan-card {
        background: var(--gray-50);
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 24px;
        text-align: center;
    }

    .plan-current {
        border-color: var(--gray-300);
    }

    .plan-new {
        border-color: var(--primary);
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }

    .plan-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--gray-200);
        color: var(--gray-700);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 12px;
    }

    .plan-label-upgrade {
        background: #d1fae5;
        color: #065f46;
    }

    .plan-label-downgrade {
        background: #fee2e2;
        color: #991b1b;
    }

    .plan-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin: 12px 0;
        text-transform: capitalize;
    }

    .plan-price {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary);
        margin: 8px 0;
    }

    .plan-period {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .plan-arrow {
        font-size: 2rem;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .features-section {
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 24px;
        margin-bottom: 32px;
    }

    .features-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 16px 0;
    }

    .features-title i {
        color: var(--primary);
    }

    .features-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 12px;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        font-size: 0.9375rem;
        color: var(--gray-700);
    }

    .feature-icon {
        color: #059669;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .payment-summary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border-radius: var(--radius);
        padding: 32px;
        text-align: center;
        margin-bottom: 24px;
        box-shadow: var(--shadow-md);
    }

    .payment-header {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .payment-amount {
        font-size: 3rem;
        font-weight: 700;
        margin: 16px 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .payment-description {
        font-size: 0.9375rem;
        opacity: 0.95;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .form-inline {
        display: inline;
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.9375rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-outline {
        background: white;
        color: var(--gray-700);
        border: 2px solid var(--gray-300);
    }

    .btn-outline:hover {
        background: var(--gray-50);
        border-color: var(--gray-400);
    }

    @media (max-width: 768px) {
        .plan-comparison {
            grid-template-columns: 1fr;
        }

        .plan-arrow {
            transform: rotate(90deg);
        }

        .action-buttons {
            flex-direction: column-reverse;
        }

        .action-buttons .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection
