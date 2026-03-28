@extends('layouts.superadmin')

@section('title', 'Renovaciones y Vencimientos - Super Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-sync-alt"></i>
        Renovaciones y Vencimientos
    </h1>
</div>

<!-- Alertas -->
@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pendientes</div>
            <div class="stat-value">{{ $totalPendientes }}</div>
            <div class="stat-sublabel">Renovaciones por procesar</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Vencidas</div>
            <div class="stat-value">{{ $totalVencidas }}</div>
            <div class="stat-sublabel">Requieren atención</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-bell"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Próximas a Vencer</div>
            <div class="stat-value">{{ $totalProximasVencer }}</div>
            <div class="stat-sublabel">En los próximos 7 días</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Monto Total Pendiente</div>
            <div class="stat-value">${{ number_format($montoTotal, 0, ',', '.') }}</div>
            <div class="stat-sublabel">Ingresos esperados</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-item">
                    <label>Estado:</label>
                    <select name="estado" class="form-select" onchange="this.form.submit()">
                        <option value="todas" {{ $estado == 'todas' ? 'selected' : '' }}>Todas</option>
                        <option value="pendiente" {{ $estado == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="pagado" {{ $estado == 'pagado' ? 'selected' : '' }}>Pagadas</option>
                        <option value="fallido" {{ $estado == 'fallido' ? 'selected' : '' }}>Fallidas</option>
                        <option value="cancelado" {{ $estado == 'cancelado' ? 'selected' : '' }}>Canceladas</option>
                    </select>
                </div>

                @if($estado !== 'todas')
                    <div class="filter-item">
                        <a href="{{ route('superadmin.renovaciones') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar Filtros
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Renovaciones -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Organización</th>
                        <th>Plan</th>
                        <th>Fecha Vencimiento</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Notificaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($renovaciones as $renovacion)
                        @php
                            $diasRestantes = now()->diffInDays($renovacion->fecha_vencimiento, false);
                            $estaVencida = $diasRestantes < 0;
                            $claseVencimiento = '';
                            if ($estaVencida) {
                                $claseVencimiento = 'text-danger fw-bold';
                            } elseif ($diasRestantes <= 3) {
                                $claseVencimiento = 'text-warning fw-bold';
                            }

                            $estadoBadge = match($renovacion->estado) {
                                'pendiente' => 'bg-warning text-dark',
                                'procesando' => 'bg-info',
                                'pagado' => 'bg-success',
                                'fallido' => 'bg-danger',
                                'cancelado' => 'bg-secondary',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $renovacion->organizacion->nombre_apr }}</strong>
                                <br>
                                <small class="text-muted">{{ $renovacion->organizacion->slug }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $renovacion->organizacion->suscripcion->nombre }}
                                </span>
                            </td>
                            <td class="{{ $claseVencimiento }}">
                                {{ $renovacion->fecha_vencimiento->format('d/m/Y') }}
                                <br>
                                <small>
                                    @if($estaVencida)
                                        <span class="text-danger">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Vencida hace {{ abs($diasRestantes) }} días
                                        </span>
                                    @elseif($diasRestantes == 0)
                                        <span class="text-warning">
                                            <i class="fas fa-clock"></i>
                                            Vence hoy
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            En {{ $diasRestantes }} días
                                        </span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <strong>${{ number_format($renovacion->monto, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                <span class="badge {{ $estadoBadge }}">
                                    {{ ucfirst($renovacion->estado) }}
                                </span>
                                @if($renovacion->metodo_pago)
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-credit-card"></i>
                                        {{ ucfirst($renovacion->metodo_pago) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="notificaciones-status">
                                    @if($renovacion->notificado_7dias)
                                        <span class="notif-badge success" title="Notificado 7 días antes">
                                            <i class="fas fa-check"></i> 7d
                                        </span>
                                    @endif
                                    @if($renovacion->notificado_3dias)
                                        <span class="notif-badge success" title="Notificado 3 días antes">
                                            <i class="fas fa-check"></i> 3d
                                        </span>
                                    @endif
                                    @if($renovacion->notificado_1dia)
                                        <span class="notif-badge success" title="Notificado 1 día antes">
                                            <i class="fas fa-check"></i> 1d
                                        </span>
                                    @endif
                                    @if($renovacion->notificado_vencido)
                                        <span class="notif-badge danger" title="Notificado vencimiento">
                                            <i class="fas fa-exclamation"></i> Venc
                                        </span>
                                    @endif
                                    @if(!$renovacion->notificado_7dias && !$renovacion->notificado_3dias && !$renovacion->notificado_1dia)
                                        <small class="text-muted">Sin notificar</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($renovacion->estado === 'pendiente')
                                    <form method="POST" action="{{ route('superadmin.renovaciones.pagar', $renovacion->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Marcar como pagada">
                                            <i class="fas fa-check"></i> Marcar Pagada
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No hay renovaciones para mostrar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--dark-card);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.bg-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .stat-icon.bg-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .stat-icon.bg-info {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }

    .stat-icon.bg-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-light);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-sublabel {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .filter-form {
        padding: 0;
    }

    .filter-row {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 180px;
    }

    .filter-item label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-muted);
        margin: 0;
    }

    .form-select {
        background: var(--dark-input);
        color: var(--text-light);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }

    .btn-secondary {
        background: var(--gray-700);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
        transform: translateY(-1px);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table thead th {
        background: var(--dark-lighter);
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        text-align: left;
        border-bottom: 2px solid var(--border);
    }

    .table tbody td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        color: var(--text-light);
    }

    .table tbody tr:hover {
        background: var(--dark-lighter);
    }

    .notificaciones-status {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    .notif-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .notif-badge.success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .notif-badge.danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 0.375rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.95rem;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .d-inline {
        display: inline;
    }

    @media (max-width: 768px) {
        .filter-row {
            flex-direction: column;
        }

        .filter-item {
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
