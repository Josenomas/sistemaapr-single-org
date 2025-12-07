@extends('layouts.app')

@section('title', 'Tickets - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-ticket-alt"></i>
        Gestión de Tickets
    </h2>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Ticket
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
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['total_tickets'] }}</div>
            <div class="stat-label">Total Tickets</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['abiertos'] }}</div>
            <div class="stat-label">Tickets Abiertos</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['cerrados'] }}</div>
            <div class="stat-label">Tickets Cerrados</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['urgentes'] }}</div>
            <div class="stat-label">Urgentes</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-user-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['sin_asignar'] }}</div>
            <div class="stat-label">Sin Asignar</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('tickets.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Buscar por número, título o socio..."
                           value="{{ request('search') }}">
                </div>

                <div class="form-group">
                    <select name="tipo_ticket" class="form-control">
                        <option value="">Todos los tipos</option>
                        <option value="consulta" {{ request('tipo_ticket') == 'consulta' ? 'selected' : '' }}>Consulta</option>
                        <option value="reclamo" {{ request('tipo_ticket') == 'reclamo' ? 'selected' : '' }}>Reclamo</option>
                        <option value="solicitud" {{ request('tipo_ticket') == 'solicitud' ? 'selected' : '' }}>Solicitud</option>
                        <option value="averia" {{ request('tipo_ticket') == 'averia' ? 'selected' : '' }}>Avería</option>
                        <option value="fuga" {{ request('tipo_ticket') == 'fuga' ? 'selected' : '' }}>Fuga</option>
                        <option value="corte" {{ request('tipo_ticket') == 'corte' ? 'selected' : '' }}>Corte</option>
                        <option value="reconexion" {{ request('tipo_ticket') == 'reconexion' ? 'selected' : '' }}>Reconexión</option>
                        <option value="lectura" {{ request('tipo_ticket') == 'lectura' ? 'selected' : '' }}>Lectura</option>
                        <option value="otro" {{ request('tipo_ticket') == 'otro' ? 'selected' : '' }}>Otro</option>
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
                        <option value="abierto" {{ request('estado') == 'abierto' ? 'selected' : '' }}>Abierto</option>
                        <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="resuelto" {{ request('estado') == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                        <option value="cerrado" {{ request('estado') == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="id_asignado" class="form-control">
                        <option value="">Todos los asignados</option>
                        <option value="sin_asignar" {{ request('id_asignado') == 'sin_asignar' ? 'selected' : '' }}>Sin asignar</option>
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

                @if(request()->hasAny(['search', 'tipo_ticket', 'prioridad', 'estado', 'id_asignado']))
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Tickets -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Fecha Reporte</th>
                        <th>Título</th>
                        <th>Socio</th>
                        <th>Tipo</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Asignado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr class="{{ $ticket->prioridad == 'urgente' ? 'row-danger' : ($ticket->prioridad == 'alta' ? 'row-warning' : '') }}">
                            <td><strong>{{ $ticket->numero_ticket }}</strong></td>
                            <td>{{ $ticket->fecha_reporte_formateada }}</td>
                            <td>
                                <strong>{{ $ticket->titulo }}</strong>
                                @if($ticket->ubicacion)
                                    <br><small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> {{ $ticket->ubicacion }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($ticket->socio)
                                    <a href="{{ route('socios.show', $ticket->socio->id) }}">
                                        {{ $ticket->socio->nombre_completo }}
                                    </a>
                                @else
                                    @if($ticket->contacto_nombre)
                                        {{ $ticket->contacto_nombre }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @endif
                            </td>
                            <td>{!! $ticket->tipo_ticket_badge !!}</td>
                            <td>{!! $ticket->prioridad_badge !!}</td>
                            <td>{!! $ticket->estado_badge !!}</td>
                            <td>
                                @if($ticket->asignado)
                                    {{ $ticket->asignado->nombre_completo }}
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('tickets.show', $ticket->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tickets.edit', $ticket->id) }}"
                                       class="btn btn-sm btn-warning"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tickets.destroy', $ticket->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('¿Está seguro de eliminar este ticket?');">
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
                                <p>No se encontraron tickets</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($tickets->hasPages())
            <div class="pagination-wrapper">
                {{ $tickets->links() }}
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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
