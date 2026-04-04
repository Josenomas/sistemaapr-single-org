@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suscripcion.renovar') }}">Renovación</a></li>
                    <li class="breadcrumb-item active">Confirmar cambio de plan</li>
                </ol>
            </nav>

            <!-- Card de Confirmación -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Confirmar Cambio de Plan
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Resumen del cambio -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted">Plan Actual</small>
                                    <h5 class="mt-2 mb-0">{{ $planActual->nombre }}</h5>
                                    <h3 class="text-primary mb-0">${{ number_format($planActual->precio_mensual, 0, ',', '.') }}</h3>
                                    <small class="text-muted">por mes</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <small class="text-muted">Plan Nuevo</small>
                                    <h5 class="mt-2 mb-0 text-success">{{ $planNuevo->nombre }}</h5>
                                    <h3 class="text-success mb-0">${{ number_format($planNuevo->precio_mensual, 0, ',', '.') }}</h3>
                                    <small class="text-muted">por mes</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Características del nuevo plan -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            Características del Plan {{ $planNuevo->nombre }}
                        </h6>
                        <ul class="mb-0">
                            @if($planNuevo->socios_ilimitados)
                            <li><i class="fas fa-check text-success me-2"></i>Socios ilimitados</li>
                            @else
                            <li><i class="fas fa-check text-success me-2"></i>Hasta {{ $planNuevo->max_socios }} socios</li>
                            @endif

                            @if($planNuevo->usuarios_ilimitados)
                            <li><i class="fas fa-check text-success me-2"></i>Usuarios ilimitados</li>
                            @else
                            <li><i class="fas fa-check text-success me-2"></i>Hasta {{ $planNuevo->max_usuarios }} usuarios</li>
                            @endif

                            @if($planNuevo->permite_dominio_personalizado)
                            <li><i class="fas fa-check text-success me-2"></i>Dominio personalizado</li>
                            @endif
                        </ul>
                    </div>

                    <!-- Monto a pagar -->
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-2">Monto a Pagar</h5>
                            <h2 class="mb-0">${{ number_format($montoPagar, 0, ',', '.') }}</h2>
                            <small>
                                @if($organizacion->suscripcionVencida())
                                    Precio completo del plan (suscripción vencida)
                                @else
                                    Diferencia prorrateada por {{ $diasRestantes ?? 0 }} días restantes
                                @endif
                            </small>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    @if($tipo === 'upgrade')
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Upgrade de plan:</strong> El cambio se aplicará inmediatamente después de confirmar el pago.
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Cambio de plan:</strong> El cambio se aplicará al final de tu período de facturación actual.
                    </div>
                    @endif

                    <!-- Botones de acción -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('suscripcion.renovar') }}" class="btn btn-secondary btn-lg w-100">
                                <i class="fas fa-arrow-left me-2"></i>
                                Cancelar
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <form action="{{ route('organizacion.cambiar-plan', $planNuevo->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Confirmar y Pagar con Flow
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
