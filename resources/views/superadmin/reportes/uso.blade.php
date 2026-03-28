@extends('layouts.superadmin')

@section('title', 'Reporte de Uso - Super Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-chart-bar"></i>
        Reporte de Uso del Sistema
    </h1>
</div>

<!-- Estadísticas Generales -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Promedio de Socios</div>
            <div class="stat-value">{{ $promedioSociosPorOrg }}</div>
            <div class="stat-sublabel">Por organización</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Promedio de Usuarios</div>
            <div class="stat-value">{{ $promedioUsuariosPorOrg }}</div>
            <div class="stat-sublabel">Por organización</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Promedio de Boletas</div>
            <div class="stat-value">{{ $promedioBoletasPorOrg }}</div>
            <div class="stat-sublabel">Por organización</div>
        </div>
    </div>
</div>

<!-- Top 10 por Socios -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-trophy"></i> Top 10 Organizaciones por Socios</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Organización</th>
                        <th>Plan</th>
                        <th>Total Socios</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topPorSocios as $index => $org)
                    <tr>
                        <td><strong>#{{ $index + 1 }}</strong></td>
                        <td>{{ $org->nombre_apr }}</td>
                        <td><span class="badge badge-primary">{{ $org->suscripcion->nombre }}</span></td>
                        <td><strong>{{ $org->total_socios }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Top 10 por Usuarios -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-trophy"></i> Top 10 Organizaciones por Usuarios</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Organización</th>
                        <th>Plan</th>
                        <th>Total Usuarios</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topPorUsuarios as $index => $org)
                    <tr>
                        <td><strong>#{{ $index + 1 }}</strong></td>
                        <td>{{ $org->nombre_apr }}</td>
                        <td><span class="badge badge-primary">{{ $org->suscripcion->nombre }}</span></td>
                        <td><strong>{{ $org->total_usuarios }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Top 10 por Actividad -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-trophy"></i> Top 10 Organizaciones por Actividad (Boletas)</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Organización</th>
                        <th>Plan</th>
                        <th>Total Boletas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topPorActividad as $index => $org)
                    <tr>
                        <td><strong>#{{ $index + 1 }}</strong></td>
                        <td>{{ $org->nombre_apr }}</td>
                        <td><span class="badge badge-primary">{{ $org->suscripcion->nombre }}</span></td>
                        <td><strong>{{ $org->total_boletas }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Uso por Plan -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-layer-group"></i> Estadísticas por Plan</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Plan</th>
                        <th>Organizaciones</th>
                        <th>Total Socios</th>
                        <th>Total Usuarios</th>
                        <th>Promedio Socios/Org</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usoPorPlan as $plan)
                    <tr>
                        <td><strong>{{ $plan->nombre }}</strong></td>
                        <td>{{ $plan->total_org }}</td>
                        <td>{{ $plan->total_socios }}</td>
                        <td>{{ $plan->total_usuarios }}</td>
                        <td>{{ $plan->total_org > 0 ? round($plan->total_socios / $plan->total_org, 2) : 0 }}</td>
                    </tr>
                    @endforeach
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

    .stat-icon.bg-primary {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
    }

    .stat-icon.bg-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-icon.bg-info {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
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

    .card {
        margin-bottom: 2rem;
    }
</style>
@endsection
