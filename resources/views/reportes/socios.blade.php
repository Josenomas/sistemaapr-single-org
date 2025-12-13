@extends('layouts.app')

@section('title', 'Reporte de Socios - Sistema APR')

@section('styles')
<style>
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .report-title {
        font-size: 2rem;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        margin: 0;
    }

    .report-title i {
        color: var(--primary);
        font-size: 1.75rem;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--white);
        color: var(--primary);
        border: 2px solid var(--primary);
        border-radius: var(--radius);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s;
    }

    .back-btn:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px;
        transition: all 0.3s;
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .stat-card.primary::before {
        background: linear-gradient(180deg, var(--primary), var(--primary-dark));
    }

    .stat-card.success::before {
        background: linear-gradient(180deg, var(--success), #059669);
    }

    .stat-card.danger::before {
        background: linear-gradient(180deg, var(--danger), #dc2626);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .stat-title {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 600;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .primary-bg {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .success-bg {
        background: linear-gradient(135deg, var(--success), #059669);
    }

    .danger-bg {
        background: linear-gradient(135deg, var(--danger), #dc2626);
    }

    .stat-value {
        font-size: 1.875rem;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--gray-900);
    }

    .stat-description {
        font-size: 0.8125rem;
        color: var(--gray-500);
    }

    .filters-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid var(--gray-200);
    }

    .filters-title {
        font-size: 1.125rem;
        color: var(--dark);
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filters-title i {
        color: var(--primary);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    .chart-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid var(--gray-200);
    }

    .chart-title {
        font-size: 1.125rem;
        color: var(--dark);
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-title i {
        color: var(--primary);
    }

    .table-container {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        border: 1px solid var(--gray-200);
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
    }

    .table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-300);
        white-space: nowrap;
    }

    .table td {
        padding: 12px 16px;
        font-size: 0.875rem;
        color: var(--gray-800);
        border-bottom: 1px solid var(--gray-200);
    }

    .table tbody tr {
        transition: background 0.2s;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="report-container">
    <div class="report-header">
        <h2 class="report-title">
            <i class="fas fa-users"></i>
            Reporte de Socios
        </h2>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('reportes.socios.descargar', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-pdf"></i>
                Descargar PDF
            </a>
            <a href="{{ route('reportes.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-header">
                <div class="stat-title">Total Socios</div>
                <div class="stat-icon primary-bg">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($estadisticas['total'], 0, ',', '.') }}</div>
            <div class="stat-description">Registrados en el sistema</div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div class="stat-title">Socios Activos</div>
                <div class="stat-icon success-bg">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($estadisticas['activos'], 0, ',', '.') }}</div>
            <div class="stat-description">{{ $estadisticas['total'] > 0 ? number_format(($estadisticas['activos']/$estadisticas['total'])*100, 1) : 0 }}% del total</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-header">
                <div class="stat-title">Socios Inactivos</div>
                <div class="stat-icon danger-bg">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($estadisticas['inactivos'], 0, ',', '.') }}</div>
            <div class="stat-description">{{ $estadisticas['total'] > 0 ? number_format(($estadisticas['inactivos']/$estadisticas['total'])*100, 1) : 0 }}% del total</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <h3 class="filters-title">
            <i class="fas fa-filter"></i>
            Filtros
        </h3>
        <form method="GET" action="{{ route('reportes.socios') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Sector</label>
                    <select name="sector" class="form-control">
                        <option value="">Todos</option>
                        @foreach($sectores as $sector)
                            <option value="{{ $sector }}" {{ request('sector') == $sector ? 'selected' : '' }}>
                                {{ $sector }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrar
                    </button>
                </div>

                <div class="form-group">
                    <a href="{{ route('reportes.socios') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Gráfico de Socios por Sector -->
    <div class="chart-card">
        <h4 class="chart-title">
            <i class="fas fa-chart-pie"></i>
            Distribución de Socios por Sector
        </h4>
        <div style="max-width: 500px; margin: 0 auto;">
            <canvas id="sectoresChart"></canvas>
        </div>
    </div>

    <!-- Tabla de Socios -->
    <div class="table-container">
        <h4 class="chart-title">
            <i class="fas fa-list"></i>
            Listado de Socios
        </h4>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>RUT</th>
                    <th>Sector</th>
                    <th>Medidor</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($socios as $socio)
                <tr>
                    <td>{{ $socio->id_socio }}</td>
                    <td>{{ $socio->nombre_completo }}</td>
                    <td>{{ $socio->rut }}</td>
                    <td>{{ $socio->sector }}</td>
                    <td>{{ $socio->numero_medidor }}</td>
                    <td>{{ $socio->telefono ?? '-' }}</td>
                    <td>
                        @if($socio->estado == 'activo')
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-danger">Inactivo</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--gray-500);">No hay socios disponibles</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Socios por Sector
    const sectoresCtx = document.getElementById('sectoresChart').getContext('2d');
    new Chart(sectoresCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($estadisticas['por_sector']->toArray())) !!},
            datasets: [{
                data: {!! json_encode(array_values($estadisticas['por_sector']->toArray())) !!},
                backgroundColor: [
                    'rgba(37, 99, 235, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(34, 197, 94, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
