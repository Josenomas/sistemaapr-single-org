@extends('layouts.app')

@section('title', 'Directiva - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-users-cog"></i>
        Gestión de Directiva
    </h2>
    <a href="{{ route('directiva.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Miembro
    </a>
</div>

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Miembros</div>
            <div class="stat-value">{{ number_format($estadisticas['total_miembros']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Activos</div>
            <div class="stat-value">{{ number_format($estadisticas['activos']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Períodos Registrados</div>
            <div class="stat-value">{{ number_format($estadisticas['total_periodos']) }}</div>
        </div>
    </div>

    <div class="stat-card highlight">
        <div class="stat-icon bg-warning">
            <i class="fas fa-crown"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Presidente Actual</div>
            <div class="stat-value">
                @if($estadisticas['presidente_actual'])
                    {{ $estadisticas['presidente_actual']->socio->nombre_completo }}
                @else
                    Sin asignar
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <h3 class="filter-title">
            <i class="fas fa-filter"></i>
            Filtros de Búsqueda
        </h3>
        <form method="GET" action="{{ route('directiva.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search">Buscar:</label>
                    <input type="text"
                           id="search"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Nombre, RUT, período...">
                </div>

                <div class="form-group">
                    <label for="cargo">Cargo:</label>
                    <select id="cargo" name="cargo" class="form-control">
                        <option value="">Todos</option>
                        <option value="presidente" {{ request('cargo') == 'presidente' ? 'selected' : '' }}>Presidente</option>
                        <option value="vicepresidente" {{ request('cargo') == 'vicepresidente' ? 'selected' : '' }}>Vicepresidente</option>
                        <option value="secretario" {{ request('cargo') == 'secretario' ? 'selected' : '' }}>Secretario</option>
                        <option value="tesorero" {{ request('cargo') == 'tesorero' ? 'selected' : '' }}>Tesorero</option>
                        <option value="director" {{ request('cargo') == 'director' ? 'selected' : '' }}>Director</option>
                        <option value="vocal" {{ request('cargo') == 'vocal' ? 'selected' : '' }}>Vocal</option>
                        <option value="suplente" {{ request('cargo') == 'suplente' ? 'selected' : '' }}>Suplente</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="finalizado" {{ request('estado') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                        <option value="renunciado" {{ request('estado') == 'renunciado' ? 'selected' : '' }}>Renunciado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="periodo">Período:</label>
                    <select id="periodo" name="periodo" class="form-control">
                        <option value="">Todos</option>
                        @foreach($periodos as $p)
                            <option value="{{ $p }}" {{ request('periodo') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="socio">Socio:</label>
                    <select id="socio" name="socio" class="form-control">
                        <option value="">Todos</option>
                        @foreach($socios as $s)
                            <option value="{{ $s->id }}" {{ request('socio') == $s->id ? 'selected' : '' }}>
                                {{ $s->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('directiva.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Directiva -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Socio</th>
                        <th>RUT</th>
                        <th>Cargo</th>
                        <th>Período</th>
                        <th>Estado</th>
                        <th>Fecha Inicio</th>
                        <th>Duración</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($directiva as $miembro)
                        <tr>
                            <td>
                                <strong>{{ $miembro->socio->nombre_completo }}</strong>
                            </td>
                            <td>{{ $miembro->socio->rut }}</td>
                            <td>{!! $miembro->cargo_badge !!}</td>
                            <td>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-calendar"></i>
                                    {{ $miembro->periodo }}
                                </span>
                            </td>
                            <td>{!! $miembro->estado_badge !!}</td>
                            <td>{{ $miembro->fecha_inicio->format('d/m/Y') }}</td>
                            <td>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i>
                                    {{ $miembro->duracion }}
                                </small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('directiva.show', $miembro->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('directiva.edit', $miembro->id) }}"
                                       class="btn btn-sm btn-warning"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('directiva.destroy', $miembro->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('¿Está seguro de eliminar este miembro de la directiva?');">
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
                            <td colspan="8" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <p>No hay miembros de directiva registrados</p>
                                    <a href="{{ route('directiva.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i>
                                        Registrar Primer Miembro
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($directiva->hasPages())
            <div class="pagination-wrapper">
                {{ $directiva->appends(request()->only(['search', 'cargo', 'estado', 'periodo', 'socio']))->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--gray-200);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: var(--primary);
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--white);
        border-radius: 8px;
        padding: 20px;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-card.highlight {
        border: 2px solid var(--warning);
        background: linear-gradient(135deg, #fff 0%, #fffbeb 100%);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.bg-primary { background: var(--primary); }
    .stat-icon.bg-success { background: var(--success); }
    .stat-icon.bg-info { background: var(--info); }
    .stat-icon.bg-warning { background: var(--warning); }
    .stat-icon.bg-danger { background: var(--danger); }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-800);
    }

    .card {
        background: var(--white);
        border-radius: 8px;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .card-body {
        padding: 24px;
    }

    .filter-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-form .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 6px;
        font-size: 0.875rem;
    }

    .form-control {
        padding: 8px 12px;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 12px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-secondary {
        background: var(--gray-500);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
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
        border-bottom: 2px solid var(--gray-200);
        font-size: 0.875rem;
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
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-primary { background: var(--primary-light); color: var(--primary-dark); }
    .badge-success { background: var(--success-light); color: var(--success-dark); }
    .badge-warning { background: var(--warning-light); color: var(--warning-dark); }
    .badge-danger { background: var(--danger-light); color: var(--danger-dark); }
    .badge-info { background: var(--info-light); color: var(--info-dark); }
    .badge-secondary { background: var(--gray-200); color: var(--gray-700); }
    .badge-light { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-300); }

    .action-buttons {
        display: flex;
        gap: 6px;
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 0.75rem;
    }

    .btn-info {
        background: var(--info);
        color: white;
    }

    .btn-info:hover {
        background: var(--info-dark);
    }

    .btn-warning {
        background: var(--warning);
        color: white;
    }

    .btn-warning:hover {
        background: var(--warning-dark);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background: var(--danger-dark);
    }

    .text-muted {
        color: var(--gray-500);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--gray-400);
        margin-bottom: 16px;
    }

    .empty-state p {
        color: var(--gray-600);
        margin-bottom: 16px;
    }

    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .stats-row {
            grid-template-columns: 1fr;
        }

        .filter-form .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
