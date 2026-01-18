@extends('layouts.app')

@section('title', 'Trabajos Realizados - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-tools"></i>
        Trabajos Realizados
    </h2>
    <a href="{{ route('trabajos.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Registrar Trabajo
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filtros de Búsqueda</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('trabajos.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_trabajo">Tipo de Trabajo</label>
                    <select name="tipo_trabajo" id="tipo_trabajo" class="form-control">
                        <option value="">Todos los tipos</option>
                        <option value="mantenimiento" {{ request('tipo_trabajo') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="reparacion" {{ request('tipo_trabajo') == 'reparacion' ? 'selected' : '' }}>Reparación</option>
                        <option value="instalacion" {{ request('tipo_trabajo') == 'instalacion' ? 'selected' : '' }}>Instalación</option>
                        <option value="inspeccion" {{ request('tipo_trabajo') == 'inspeccion' ? 'selected' : '' }}>Inspección</option>
                        <option value="otro" {{ request('tipo_trabajo') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="planificado" {{ request('estado') == 'planificado' ? 'selected' : '' }}>Planificado</option>
                        <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="prioridad">Prioridad</label>
                    <select name="prioridad" id="prioridad" class="form-control">
                        <option value="">Todas las prioridades</option>
                        <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="urgente" {{ request('prioridad') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="id_responsable">Responsable</label>
                    <select name="id_responsable" id="id_responsable" class="form-control">
                        <option value="">Todos los responsables</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ request('id_responsable') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_desde">Fecha Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control"
                           value="{{ request('fecha_desde') }}">
                </div>

                <div class="form-group">
                    <label for="fecha_hasta">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control"
                           value="{{ request('fecha_hasta') }}">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Buscar
                </button>
                <a href="{{ route('trabajos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Trabajos</h3>
        <div class="card-stats">
            Total: <strong>{{ $trabajos->total() }}</strong> registros
        </div>
    </div>
    <div class="card-body">
        @if($trabajos->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Fecha Inicio</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th>Responsable</th>
                            <th>Costo Estimado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trabajos as $trabajo)
                            <tr>
                                <td><strong>{{ $trabajo->titulo }}</strong></td>
                                <td>
                                    @if($trabajo->tipo_trabajo === 'mantenimiento')
                                        <span class="badge badge-info">Mantenimiento</span>
                                    @elseif($trabajo->tipo_trabajo === 'reparacion')
                                        <span class="badge badge-warning">Reparación</span>
                                    @elseif($trabajo->tipo_trabajo === 'instalacion')
                                        <span class="badge badge-success">Instalación</span>
                                    @elseif($trabajo->tipo_trabajo === 'inspeccion')
                                        <span class="badge badge-secondary">Inspección</span>
                                    @else
                                        <span class="badge badge-secondary">Otro</span>
                                    @endif
                                </td>
                                <td>{{ $trabajo->fecha_inicio->format('d/m/Y') }}</td>
                                <td>
                                    @if($trabajo->estado === 'planificado')
                                        <span class="badge badge-info">Planificado</span>
                                    @elseif($trabajo->estado === 'en_proceso')
                                        <span class="badge badge-warning">En Proceso</span>
                                    @elseif($trabajo->estado === 'completado')
                                        <span class="badge badge-success">Completado</span>
                                    @else
                                        <span class="badge badge-secondary">Cancelado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($trabajo->prioridad === 'baja')
                                        <span class="badge badge-info">Baja</span>
                                    @elseif($trabajo->prioridad === 'media')
                                        <span class="badge badge-warning">Media</span>
                                    @elseif($trabajo->prioridad === 'alta')
                                        <span class="badge badge-danger">Alta</span>
                                    @else
                                        <span class="badge badge-danger pulse">Urgente</span>
                                    @endif
                                </td>
                                <td>{{ $trabajo->responsable ? $trabajo->responsable->nombre_completo : '-' }}</td>
                                <td>{{ $trabajo->costo_estimado_formateado }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('trabajos.show', $trabajo->id) }}" class="btn-icon" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('trabajos.edit', $trabajo->id) }}" class="btn-icon" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('trabajos.destroy', $trabajo->id) }}"
                                              onsubmit="return confirm('¿Está seguro de eliminar este trabajo?')" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $trabajos->appends(request()->only(['tipo_trabajo', 'estado', 'prioridad', 'id_responsable', 'fecha_desde', 'fecha_hasta']))->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-tools"></i>
                <p>No se encontraron trabajos realizados</p>
                <a href="{{ route('trabajos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Registrar Primer Trabajo
                </a>
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

    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #059669;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 2px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-stats {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .card-body {
        padding: 24px;
    }

    .filter-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
    }

    .form-control {
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-actions {
        display: flex;
        gap: 12px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.875rem;
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

    .btn-secondary {
        background: var(--gray-600);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-700);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background: var(--gray-50);
        padding: 12px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--gray-600);
        border-bottom: 2px solid var(--gray-200);
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.875rem;
        color: var(--gray-700);
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-secondary {
        background: #e5e7eb;
        color: #4b5563;
    }

    .badge.pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .7;
        }
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        background: var(--gray-100);
        color: var(--gray-600);
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .btn-icon:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--gray-300);
        margin-bottom: 16px;
    }

    .empty-state p {
        color: var(--gray-500);
        margin-bottom: 20px;
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
    }
</style>
@endsection
