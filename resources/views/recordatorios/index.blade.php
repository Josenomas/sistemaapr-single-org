@extends('layouts.app')

@section('title', 'Recordatorios - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-bell"></i>
        Gestión de Recordatorios
    </h2>
    <a href="{{ route('recordatorios.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Recordatorio
    </a>
</div>

<!-- Alertas -->
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Estadísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-bell"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['total_recordatorios'] }}</div>
            <div class="stat-label">Total Recordatorios</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['pendientes'] }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['hoy'] }}</div>
            <div class="stat-label">Para Hoy</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-secondary">
            <i class="fas fa-calendar-week"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['proximos_7_dias'] }}</div>
            <div class="stat-label">Próximos 7 Días</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['vencidos'] }}</div>
            <div class="stat-label">Vencidos</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['completados'] }}</div>
            <div class="stat-label">Completados</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('recordatorios.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Buscar por título o descripción..."
                           value="{{ request('search') }}">
                </div>

                <div class="form-group">
                    <select name="tipo_recordatorio" class="form-control">
                        <option value="">Todos los tipos</option>
                        <option value="reunion" {{ request('tipo_recordatorio') == 'reunion' ? 'selected' : '' }}>Reunión</option>
                        <option value="pago" {{ request('tipo_recordatorio') == 'pago' ? 'selected' : '' }}>Pago</option>
                        <option value="mantenimiento" {{ request('tipo_recordatorio') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="inspeccion" {{ request('tipo_recordatorio') == 'inspeccion' ? 'selected' : '' }}>Inspección</option>
                        <option value="vencimiento" {{ request('tipo_recordatorio') == 'vencimiento' ? 'selected' : '' }}>Vencimiento</option>
                        <option value="llamada" {{ request('tipo_recordatorio') == 'llamada' ? 'selected' : '' }}>Llamada</option>
                        <option value="tarea" {{ request('tipo_recordatorio') == 'tarea' ? 'selected' : '' }}>Tarea</option>
                        <option value="otro" {{ request('tipo_recordatorio') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="prioridad" class="form-control">
                        <option value="">Todas las prioridades</option>
                        <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="urgente" {{ request('prioridad') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        <option value="vencido" {{ request('estado') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="id_asignado" class="form-control">
                        <option value="">Todos los asignados</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ request('id_asignado') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>

                @if(request()->hasAny(['search', 'tipo_recordatorio', 'prioridad', 'estado', 'id_asignado']))
                    <a href="{{ route('recordatorios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Recordatorios -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Asignado</th>
                        <th>Días Restantes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recordatorios as $recordatorio)
                        <tr class="{{
                            $recordatorio->estado == 'vencido' || $recordatorio->estaVencido() ? 'row-danger' :
                            ($recordatorio->esHoy() ? 'row-warning' :
                            ($recordatorio->prioridad == 'urgente' ? 'row-warning' : ''))
                        }}">
                            <td>
                                <strong>{{ $recordatorio->fecha_recordatorio_formateada }}</strong>
                                @if($recordatorio->esHoy())
                                    <br><span class="badge badge-warning">HOY</span>
                                @endif
                            </td>
                            <td>{{ $recordatorio->hora_recordatorio_formateada }}</td>
                            <td>
                                <strong>{{ $recordatorio->titulo }}</strong>
                                @if($recordatorio->ubicacion)
                                    <br><small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> {{ $recordatorio->ubicacion }}
                                    </small>
                                @endif
                            </td>
                            <td>{!! $recordatorio->tipo_recordatorio_badge !!}</td>
                            <td>{!! $recordatorio->prioridad_badge !!}</td>
                            <td>{!! $recordatorio->estado_badge !!}</td>
                            <td>
                                @if($recordatorio->asignado)
                                    {{ $recordatorio->asignado->nombre_completo }}
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @if($recordatorio->estado == 'pendiente')
                                    <span class="{{ $recordatorio->dias_restantes_color }}">
                                        <strong>{{ $recordatorio->dias_restantes }}</strong>
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('recordatorios.show', $recordatorio->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('recordatorios.edit', $recordatorio->id) }}"
                                       class="btn btn-sm btn-warning"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('recordatorios.destroy', $recordatorio->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('¿Está seguro de eliminar este recordatorio?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="fas fa-inbox"></i>
                                <p>No se encontraron recordatorios</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($recordatorios->hasPages())
            <div class="pagination-wrapper">
                {{ $recordatorios->links() }}
            </div>
        @endif
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

    .mb-3 {
        margin-bottom: 20px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        border: 1px solid var(--gray-200);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    .stat-icon.bg-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
    .stat-icon.bg-success { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-icon.bg-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-icon.bg-info { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .stat-icon.bg-secondary { background: linear-gradient(135deg, #6b7280, #4b5563); }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-body {
        padding: 24px;
    }

    .filter-form .form-row {
        display: grid;
        grid-template-columns: 2fr repeat(4, 1fr) auto auto;
        gap: 12px;
        align-items: center;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: var(--gray-50);
    }

    .table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        font-size: 0.875rem;
        border-bottom: 2px solid var(--gray-200);
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.95rem;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .row-warning {
        background-color: #fef3c7 !important;
    }

    .row-danger {
        background-color: #fee2e2 !important;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn {
        padding: 10px 20px;
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

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.875rem;
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

    .btn-info {
        background: #3b82f6;
        color: white;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-block;
        white-space: nowrap;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-info {
        background: #cffafe;
        color: #155e75;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .badge-dark {
        background: var(--gray-700);
        color: var(--white);
    }

    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--gray-500);
    }

    .text-success {
        color: #059669;
    }

    .text-warning {
        color: #d97706;
    }

    .text-danger {
        color: #dc2626;
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .filter-form .form-row {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
