@extends('layouts.superadmin')

@section('title', 'Reporte Comparativo - Super Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-balance-scale"></i>
        Reporte Comparativo de Organizaciones
    </h1>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-item">
                    <label>Ordenar por:</label>
                    <select name="orden" class="form-select" onchange="this.form.submit()">
                        <option value="socios" {{ request('orden') == 'socios' ? 'selected' : '' }}>Socios</option>
                        <option value="usuarios" {{ request('orden') == 'usuarios' ? 'selected' : '' }}>Usuarios</option>
                        <option value="boletas" {{ request('orden') == 'boletas' ? 'selected' : '' }}>Boletas</option>
                        <option value="ingresos" {{ request('orden') == 'ingresos' ? 'selected' : '' }}>Ingresos</option>
                    </select>
                </div>

                <div class="filter-item">
                    <label>Plan:</label>
                    <select name="plan" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los planes</option>
                        @foreach($planes as $plan)
                            <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label>Estado:</label>
                    <select name="estado" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activas</option>
                        <option value="suspendida" {{ request('estado') == 'suspendida' ? 'selected' : '' }}>Suspendidas</option>
                        <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Canceladas</option>
                    </select>
                </div>

                @if(request()->hasAny(['orden', 'plan', 'estado']))
                    <div class="filter-item">
                        <a href="{{ route('superadmin.reportes.comparativo') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar Filtros
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla Comparativa -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Organización</th>
                        <th>Plan</th>
                        <th>Estado</th>
                        <th>Socios</th>
                        <th>Usuarios</th>
                        <th>Boletas</th>
                        <th>Ingresos/Mes</th>
                        <th>Uso %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($organizaciones as $index => $org)
                        @php
                            $porcentajeUso = 0;
                            if ($org->suscripcion->limite_socios > 0) {
                                $porcentajeUso = round(($org->total_socios / $org->suscripcion->limite_socios) * 100);
                            }

                            $estadoBadge = match($org->estado_suscripcion) {
                                'activa' => 'bg-success',
                                'suspendida' => 'bg-warning text-dark',
                                'cancelada' => 'bg-danger',
                                default => 'bg-secondary'
                            };

                            $usoClass = '';
                            if ($porcentajeUso >= 90) {
                                $usoClass = 'text-danger fw-bold';
                            } elseif ($porcentajeUso >= 70) {
                                $usoClass = 'text-warning fw-bold';
                            }
                        @endphp
                        <tr>
                            <td><strong>#{{ $index + 1 }}</strong></td>
                            <td>
                                <strong>{{ $org->nombre_apr }}</strong>
                                <br>
                                <small class="text-muted">{{ $org->slug }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $org->suscripcion->nombre }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $estadoBadge }}">
                                    {{ ucfirst($org->estado_suscripcion) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $org->total_socios }}</strong>
                                <small class="text-muted">/ {{ $org->suscripcion->limite_socios }}</small>
                            </td>
                            <td>
                                <strong>{{ $org->total_usuarios }}</strong>
                                <small class="text-muted">/ {{ $org->suscripcion->limite_usuarios }}</small>
                            </td>
                            <td>
                                <strong>{{ $org->total_boletas }}</strong>
                            </td>
                            <td>
                                <strong>${{ number_format($org->suscripcion->precio_mensual, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                <div class="progress-wrapper">
                                    <span class="{{ $usoClass }}">{{ $porcentajeUso }}%</span>
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill {{ $usoClass }}" style="width: {{ min($porcentajeUso, 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Resumen General -->
<div class="stats-grid mt-4">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Organizaciones</div>
            <div class="stat-value">{{ $organizaciones->count() }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Socios</div>
            <div class="stat-value">{{ $organizaciones->sum('total_socios') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Boletas</div>
            <div class="stat-value">{{ $organizaciones->sum('total_boletas') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Ingresos Totales/Mes</div>
            <div class="stat-value">${{ number_format($organizaciones->sum(fn($o) => $o->suscripcion->precio_mensual), 0, ',', '.') }}</div>
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

    .stat-icon.bg-primary {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
    }

    .stat-icon.bg-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-icon.bg-info {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }

    .stat-icon.bg-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
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
    }

    .card {
        margin-bottom: 2rem;
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

    .progress-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        min-width: 100px;
    }

    .progress-bar-custom {
        width: 100%;
        height: 6px;
        background: var(--dark-lighter);
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #059669);
        border-radius: 3px;
        transition: width 0.3s;
    }

    .progress-fill.text-warning {
        background: linear-gradient(90deg, #f59e0b, #d97706);
    }

    .progress-fill.text-danger {
        background: linear-gradient(90deg, #ef4444, #dc2626);
    }

    @media (max-width: 768px) {
        .filter-row {
            flex-direction: column;
        }

        .filter-item {
            width: 100%;
        }
    }
</style>
@endsection
