@extends('layouts.app')

@section('title', 'Cambiar Plan - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-arrow-up"></i>
        Cambiar Plan de Suscripción
    </h2>
    <a href="{{ route('organizacion.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<!-- Plan Actual -->
<div class="current-plan-banner">
    <div class="banner-icon">
        <i class="fas fa-crown"></i>
    </div>
    <div class="banner-content">
        <h3>Plan Actual: <strong>{{ ucfirst($organizacion->suscripcion->nombre) }}</strong></h3>
        <p class="price">${{ number_format($organizacion->suscripcion->precio_mensual, 0, ',', '.') }} / mes</p>
    </div>
    @if($organizacion->estado_suscripcion === 'activa')
        <span class="status-badge badge-success">
            <i class="fas fa-check-circle"></i> Activa
        </span>
    @elseif($organizacion->estado_suscripcion === 'prueba')
        <span class="status-badge badge-info">
            <i class="fas fa-clock"></i> Prueba
        </span>
    @endif
</div>

<!-- Planes Disponibles -->
<div class="plans-grid">
    @foreach($planes as $plan)
    <div class="plan-card {{ $plan->id === $organizacion->id_suscripcion ? 'current-plan' : '' }} {{ $plan->nombre === 'enterprise' ? 'featured-plan' : '' }}">
        @if($plan->nombre === 'enterprise')
            <div class="featured-badge">
                <i class="fas fa-star"></i> Más Popular
            </div>
        @endif

        @if($plan->id === $organizacion->id_suscripcion)
            <div class="current-plan-badge">
                <i class="fas fa-check-circle"></i> Plan Actual
            </div>
        @endif

        <div class="plan-header">
            <h2 class="plan-name">{{ ucfirst($plan->nombre) }}</h2>
            <div class="plan-price">
                <span class="currency">$</span>
                <span class="amount">{{ number_format($plan->precio_mensual, 0, ',', '.') }}</span>
                <span class="period">/ mes</span>
            </div>
        </div>

        <div class="plan-features">
            <div class="feature-item">
                <i class="fas fa-users"></i>
                <span>
                    @if($plan->max_socios === -1)
                        Socios ilimitados
                    @else
                        Hasta {{ number_format($plan->max_socios) }} socios
                    @endif
                </span>
            </div>

            <div class="feature-item">
                <i class="fas fa-user-shield"></i>
                <span>
                    @if($plan->max_usuarios === -1)
                        Usuarios ilimitados
                    @else
                        {{ $plan->max_usuarios }} {{ $plan->max_usuarios === 1 ? 'usuario' : 'usuarios' }}
                    @endif
                </span>
            </div>

            @if($plan->permite_dominio_personalizado)
            <div class="feature-item">
                <i class="fas fa-globe"></i>
                <span>Dominio personalizado</span>
            </div>
            @endif

            @if($plan->permite_modulo_noticias)
            <div class="feature-item">
                <i class="fas fa-newspaper"></i>
                <span>Módulo de noticias</span>
            </div>
            @endif

            @if($plan->features)
                @foreach((is_array($plan->features) ? $plan->features : json_decode($plan->features, true)) as $feature)
                <div class="feature-item">
                    <i class="fas fa-check"></i>
                    <span>{{ $feature }}</span>
                </div>
                @endforeach
            @endif
        </div>

        <div class="plan-footer">
            @if($plan->id === $organizacion->id_suscripcion)
                <button class="btn btn-current" disabled>
                    <i class="fas fa-check"></i> Plan Actual
                </button>
            @else
                @if($plan->precio_mensual > $organizacion->suscripcion->precio_mensual)
                    <form action="{{ route('organizacion.cambiar-plan', $plan->id) }}" method="POST" onsubmit="return confirmarUpgrade('{{ $plan->nombre }}', {{ $plan->precio_mensual }})">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-up"></i> Mejorar Plan
                        </button>
                    </form>
                @else
                    <form action="{{ route('organizacion.cambiar-plan', $plan->id) }}" method="POST" onsubmit="return confirmarDowngrade('{{ $plan->nombre }}', {{ $plan->precio_mensual }})">
                        @csrf
                        <button type="submit" class="btn btn-outline">
                            <i class="fas fa-arrow-down"></i> Cambiar a este Plan
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
    @endforeach
</div>

<!-- Información Adicional -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle"></i>
            Información sobre Cambios de Plan
        </h3>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon upgrade">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="info-content">
                    <h4>Mejora de plan</h4>
                    <p>Los cambios se aplicarán inmediatamente. Si tu suscripción está activa, pagarás la diferencia prorrateada por los días restantes. Si está vencida, pagarás el mes completo del nuevo plan.</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon downgrade">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="info-content">
                    <h4>Cambio a plan inferior</h4>
                    <p>El cambio se aplicará al final del período de facturación actual.</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon payment">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="info-content">
                    <h4>Métodos de pago</h4>
                    <p>Aceptamos transferencia bancaria y pago con tarjeta.</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon support">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="info-content">
                    <h4>Soporte</h4>
                    <p>Si tienes dudas, contáctanos a <a href="mailto:soportesistemaapr@gmail.com">soportesistemaapr@gmail.com</a></p>
                </div>
            </div>
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

    .current-plan-banner {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 24px 32px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 32px;
        box-shadow: var(--shadow-lg);
    }

    .banner-icon {
        font-size: 3rem;
        opacity: 0.9;
    }

    .banner-content {
        flex: 1;
    }

    .banner-content h3 {
        margin: 0 0 8px 0;
        font-size: 1.5rem;
        font-weight: 400;
    }

    .banner-content h3 strong {
        font-weight: 700;
    }

    .banner-content .price {
        margin: 0;
        font-size: 1.25rem;
        opacity: 0.95;
        font-weight: 500;
    }

    .status-badge {
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #d1fae5;
        border: 2px solid rgba(16, 185, 129, 0.4);
    }

    .badge-info {
        background: rgba(59, 130, 246, 0.2);
        color: #dbeafe;
        border: 2px solid rgba(59, 130, 246, 0.4);
    }

    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .plan-card {
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 28px;
        position: relative;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .plan-card:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
    }

    .plan-card.featured-plan {
        border-color: var(--primary);
        box-shadow: var(--shadow-md);
    }

    .plan-card.current-plan {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-color: var(--primary);
    }

    .featured-badge {
        position: absolute;
        top: -14px;
        right: 20px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .current-plan-badge {
        position: absolute;
        top: -14px;
        left: 20px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .plan-header {
        text-align: center;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .plan-name {
        font-size: 1.75rem;
        margin: 0 0 16px 0;
        color: var(--dark);
        text-transform: capitalize;
        font-weight: 700;
    }

    .plan-price {
        display: flex;
        align-items: baseline;
        justify-content: center;
        gap: 4px;
    }

    .plan-price .currency {
        font-size: 1.25rem;
        color: var(--gray-600);
        font-weight: 600;
    }

    .plan-price .amount {
        font-size: 2.75rem;
        font-weight: 800;
        color: var(--primary);
        line-height: 1;
    }

    .plan-price .period {
        font-size: 1rem;
        color: var(--gray-600);
        font-weight: 500;
    }

    .plan-features {
        margin-bottom: 24px;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--gray-100);
    }

    .feature-item:last-child {
        border-bottom: none;
    }

    .feature-item i {
        font-size: 1.1rem;
        margin-top: 2px;
        color: var(--primary);
        min-width: 20px;
    }

    .feature-item span {
        flex: 1;
        color: var(--gray-700);
        font-size: 0.95rem;
        font-weight: 500;
    }

    .plan-footer {
        padding-top: 20px;
        border-top: 2px solid var(--gray-200);
    }

    .plan-footer .btn {
        width: 100%;
        padding: 14px 24px;
        font-size: 1rem;
        font-weight: 700;
        justify-content: center;
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
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
        color: var(--primary);
        border: 2px solid var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }

    .btn-current {
        background: var(--gray-300);
        color: var(--gray-600);
        cursor: not-allowed;
    }

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-body {
        padding: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
    }

    .info-item {
        display: flex;
        gap: 16px;
        padding: 16px;
        background: var(--gray-50);
        border-radius: var(--radius);
        border: 1px solid var(--gray-200);
    }

    .info-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .info-icon.upgrade {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .info-icon.downgrade {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .info-icon.payment {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .info-icon.support {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }

    .info-content h4 {
        margin: 0 0 8px 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--dark);
    }

    .info-content p {
        margin: 0;
        font-size: 0.9rem;
        color: var(--gray-600);
        line-height: 1.5;
    }

    .info-content a {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
    }

    .info-content a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .plans-grid {
            grid-template-columns: 1fr;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .current-plan-banner {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .banner-icon {
            font-size: 2rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    function confirmarUpgrade(planNombre, precio) {
        const precioFormateado = new Intl.NumberFormat('es-CL', {
            style: 'currency',
            currency: 'CLP'
        }).format(precio);

        return confirm(`¿Estás seguro de que deseas mejorar al plan ${planNombre}?\n\nNuevo precio: ${precioFormateado}/mes\n\nSerás redirigido a Flow para procesar el pago de la diferencia prorrateada.\n\nEl cambio se aplicará inmediatamente después del pago.`);
    }

    function confirmarDowngrade(planNombre, precio) {
        const precioFormateado = new Intl.NumberFormat('es-CL', {
            style: 'currency',
            currency: 'CLP'
        }).format(precio);

        return confirm(`¿Estás seguro de que deseas cambiar al plan ${planNombre}?\n\nNuevo precio: ${precioFormateado}/mes\n\nEl cambio se aplicará al final del período de facturación actual.`);
    }
</script>
@endsection
