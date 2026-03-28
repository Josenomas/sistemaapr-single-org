@extends('layouts.superadmin')

@section('title', 'Auditoría y Logs - Super Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-clipboard-list"></i>
        Auditoría y Logs del Sistema
    </h1>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-list"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total de Acciones</div>
            <div class="stat-value">{{ number_format($totalAcciones, 0, ',', '.') }}</div>
            <div class="stat-sublabel">Registros históricos</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Acciones Hoy</div>
            <div class="stat-value">{{ $accionesHoy }}</div>
            <div class="stat-sublabel">En las últimas 24h</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Usuarios Activos Hoy</div>
            <div class="stat-value">{{ $usuariosActivos }}</div>
            <div class="stat-sublabel">Usuarios únicos</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header">
        <h3><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="filter-form">
            <div class="filter-grid">
                <div class="filter-item">
                    <label>Organización:</label>
                    <select name="organizacion" class="form-select">
                        <option value="">Todas las organizaciones</option>
                        @foreach($organizaciones as $org)
                            <option value="{{ $org->id }}" {{ $idOrganizacion == $org->id ? 'selected' : '' }}>
                                {{ $org->nombre_apr }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label>Módulo:</label>
                    <select name="modulo" class="form-select">
                        <option value="">Todos los módulos</option>
                        @foreach($modulos as $mod)
                            <option value="{{ $mod }}" {{ $modulo == $mod ? 'selected' : '' }}>
                                {{ ucfirst($mod) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label>Acción:</label>
                    <select name="accion" class="form-select">
                        <option value="">Todas las acciones</option>
                        @foreach($acciones as $acc)
                            <option value="{{ $acc }}" {{ $accion == $acc ? 'selected' : '' }}>
                                {{ ucfirst($acc) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label>Desde:</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ $fechaDesde }}">
                </div>

                <div class="filter-item">
                    <label>Hasta:</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ $fechaHasta }}">
                </div>

                <div class="filter-item" style="display: flex; gap: 0.5rem; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    @if($idOrganizacion || $modulo || $accion || $fechaDesde || $fechaHasta)
                        <a href="{{ route('superadmin.auditoria') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Logs -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Usuario</th>
                        <th>Organización</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <div class="timestamp">
                                    <strong>{{ $log->created_at->format('d/m/Y') }}</strong>
                                    <small class="text-muted d-block">{{ $log->created_at->format('H:i:s') }}</small>
                                </div>
                            </td>
                            <td>
                                @if($log->usuario)
                                    <div>
                                        <strong>{{ $log->usuario->nombre }} {{ $log->usuario->apellido }}</strong>
                                        <small class="text-muted d-block">{{ $log->usuario->nombre_usuario }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Sistema</span>
                                @endif
                            </td>
                            <td>
                                @if($log->organizacion)
                                    <span class="badge bg-secondary">{{ $log->organizacion->nombre_apr }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="modulo-badge">
                                    <i class="fas {{ $log->icono }}"></i>
                                    {{ ucfirst($log->modulo) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->colorAccion }}">
                                    {{ ucfirst($log->accion) }}
                                </span>
                            </td>
                            <td>
                                <div class="descripcion-cell">
                                    {{ $log->descripcion }}
                                    @if($log->datos_anteriores || $log->datos_nuevos)
                                        <button class="btn-detalles" onclick="toggleDetalles({{ $log->id }})">
                                            <i class="fas fa-info-circle"></i> Ver detalles
                                        </button>
                                        <div id="detalles-{{ $log->id }}" class="detalles-box" style="display: none;">
                                            @if($log->datos_anteriores)
                                                <div class="datos-section">
                                                    <strong>Datos anteriores:</strong>
                                                    <pre>{{ json_encode($log->datos_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            @endif
                                            @if($log->datos_nuevos)
                                                <div class="datos-section">
                                                    <strong>Datos nuevos:</strong>
                                                    <pre>{{ json_encode($log->datos_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $log->ip_address }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No hay registros de auditoría</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($logs->hasPages())
            <div class="pagination-wrapper">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleDetalles(id) {
    const element = document.getElementById('detalles-' + id);
    element.style.display = element.style.display === 'none' ? 'block' : 'none';
}
</script>

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

    .filter-form {
        padding: 0;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .filter-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .filter-item label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-muted);
        margin: 0;
    }

    .form-select, .form-control {
        background: var(--dark-input);
        color: var(--text-light);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-select:focus, .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(124, 58, 237, 0.3);
    }

    .btn-secondary {
        background: var(--gray-700);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
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
        vertical-align: top;
    }

    .table tbody tr:hover {
        background: var(--dark-lighter);
    }

    .timestamp strong {
        color: var(--text-light);
    }

    .modulo-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        background: rgba(124, 58, 237, 0.1);
        border-radius: 0.375rem;
        font-weight: 500;
    }

    .descripcion-cell {
        max-width: 400px;
    }

    .btn-detalles {
        background: none;
        border: none;
        color: var(--primary);
        cursor: pointer;
        font-size: 0.8rem;
        padding: 0.25rem 0;
        margin-top: 0.5rem;
        display: block;
        transition: all 0.2s;
    }

    .btn-detalles:hover {
        text-decoration: underline;
    }

    .detalles-box {
        margin-top: 0.75rem;
        padding: 0.75rem;
        background: var(--dark-lighter);
        border-radius: 0.5rem;
        border: 1px solid var(--border);
    }

    .datos-section {
        margin-bottom: 0.75rem;
    }

    .datos-section:last-child {
        margin-bottom: 0;
    }

    .datos-section strong {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    .datos-section pre {
        background: var(--dark-card);
        padding: 0.75rem;
        border-radius: 0.375rem;
        overflow-x: auto;
        margin: 0;
        font-size: 0.8rem;
        color: var(--text-light);
        border: 1px solid var(--border);
    }

    .pagination-wrapper {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }

    .d-block {
        display: block;
    }

    @media (max-width: 768px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
