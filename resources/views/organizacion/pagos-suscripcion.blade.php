@extends('layouts.app')

@section('title', 'Historial de Pagos de Suscripción')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-credit-card"></i>
        Historial de Pagos de Suscripción
    </h2>
</div>

    @if($pagoPendiente)
        <div class="alert alert-warning border-start border-warning border-5 mb-4">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">⚠️ Tienes un pago pendiente</h5>
                    <p class="mb-2">
                        <strong>Monto:</strong> ${{ number_format($pagoPendiente->monto, 0, ',', '.') }} |
                        <strong>Vence:</strong> {{ $pagoPendiente->fecha_vencimiento->format('d/m/Y') }}
                        @if($pagoPendiente->fecha_vencimiento->isPast())
                            <span class="badge bg-danger">VENCIDO</span>
                        @elseif($pagoPendiente->fecha_vencimiento->diffInDays(now()) <= 3)
                            <span class="badge bg-warning text-dark">VENCE PRONTO</span>
                        @endif
                    </p>
                    <p class="mb-0">
                        <small>Período: {{ $pagoPendiente->periodo_inicio->format('d/m/Y') }} - {{ $pagoPendiente->periodo_fin->format('d/m/Y') }}</small>
                    </p>
                </div>
                <div>
                    <a href="{{ route('organizacion.upgrade') }}" class="btn btn-warning">
                        <i class="fas fa-credit-card"></i> Pagar Ahora
                    </a>
                </div>
            </div>
        </div>
    @endif

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card stat-success">
        <div class="stat-icon">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Pagado</div>
            <div class="stat-value">${{ number_format($totalPagado, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pagos Pendientes</div>
            <div class="stat-value">{{ $pagosPendientes }}</div>
        </div>
    </div>

    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Plan Actual</div>
            <div class="stat-value">{{ $organizacion->suscripcion->nombre }}</div>
            <div class="stat-sublabel">${{ number_format($organizacion->suscripcion->precio_mensual, 0, ',', '.') }}/mes</div>
        </div>
    </div>
</div>

<!-- Tabla de Pagos -->
<div class="card">
    <div class="card-body">
        @if($pagos->isEmpty())
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No hay pagos registrados aún</h3>
                <p>Los pagos mensuales se generan automáticamente cada mes.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                            <tr>
                                <th>ID</th>
                                <th>Plan</th>
                                <th>Monto</th>
                                <th>Período</th>
                                <th>Estado</th>
                                <th>Método</th>
                                <th>Fecha Pago</th>
                                <th>Vencimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pagos as $pago)
                                <tr>
                                    <td><code>#{{ $pago->id }}</code></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $pago->suscripcion->nombre }}</span>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($pago->monto, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $pago->periodo_inicio->format('d/m/Y') }}<br>
                                            {{ $pago->periodo_fin->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @php
                                            $estadoBadge = match($pago->estado) {
                                                'pagado' => 'bg-success',
                                                'pendiente' => 'bg-warning text-dark',
                                                'fallido' => 'bg-danger',
                                                'reembolsado' => 'bg-info',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $estadoBadge }}">
                                            {{ ucfirst($pago->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($pago->metodo_pago === 'flow')
                                            <i class="fas fa-credit-card text-primary"></i> Flow
                                        @elseif($pago->metodo_pago === 'manual')
                                            <i class="fas fa-money-bill text-success"></i> Manual
                                        @else
                                            <i class="fas fa-gift text-warning"></i> Cortesía
                                        @endif
                                    </td>
                                    <td>
                                        @if($pago->fecha_pago)
                                            <small>{{ $pago->fecha_pago->format('d/m/Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pago->fecha_vencimiento)
                                            <small class="{{ $pago->estaVencido() ? 'text-danger fw-bold' : '' }}">
                                                {{ $pago->fecha_vencimiento->format('d/m/Y') }}
                                            </small>
                                            @if($pago->estaVencido())
                                                <br><span class="badge bg-danger">Vencido</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>

            @if($pagos->hasPages())
                <div class="pagination-container">
                    {{ $pagos->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Información Adicional -->
<div class="info-cards">
    <div class="card">
        <div class="card-body">
            <h4><i class="fas fa-info-circle"></i> ¿Cómo funcionan los pagos?</h4>
            <ul>
                <li>Los pagos se generan automáticamente cada mes</li>
                <li>Tienes 5 días para realizar el pago desde el inicio del período</li>
                <li>Recibirás notificaciones 7, 3 y 1 día antes del vencimiento</li>
                <li>Si no pagas a tiempo, tu cuenta será suspendida automáticamente</li>
            </ul>

            <h4><i class="fas fa-credit-card"></i> Métodos de pago</h4>
            <ul>
                <li><strong>Flow:</strong> Tarjetas de crédito y débito (Webpay)</li>
                <li><strong>Manual:</strong> Transferencia o depósito (contacta al admin)</li>
            </ul>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4><i class="fas fa-question-circle"></i> ¿Necesitas ayuda?</h4>
            <p>Si tienes problemas con un pago o necesitas cambiar tu método de pago:</p>
            <ul>
                <li>Revisa que tu tarjeta tenga fondos disponibles</li>
                <li>Verifica que los datos sean correctos</li>
            </ul>

            <div class="action-buttons">
                <a href="{{ route('organizacion.upgrade') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-up"></i> Cambiar Plan
                </a>
                <a href="{{ route('organizacion.index') }}" class="btn btn-secondary">
                    <i class="fas fa-cog"></i> Configuración
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
    }

    .stat-success .stat-icon {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-warning .stat-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .stat-primary .stat-icon {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
    }

    .stat-sublabel {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin-top: 4px;
    }

    .info-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
    }

    .info-cards h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-cards h4 i {
        color: var(--primary);
    }

    .info-cards ul {
        margin-bottom: 20px;
        padding-left: 20px;
    }

    .info-cards li {
        margin-bottom: 8px;
        color: var(--gray-700);
        line-height: 1.6;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--gray-300);
        margin-bottom: 16px;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        color: var(--gray-700);
        margin-bottom: 8px;
    }

    .empty-state p {
        color: var(--gray-500);
    }

    @media (max-width: 768px) {
        .info-cards {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
