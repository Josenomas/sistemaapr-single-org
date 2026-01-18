@extends('layouts.app')

@section('title', 'Notificaciones - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-bell"></i>
        Gestión de Notificaciones
    </h2>
    <a href="{{ route('notificaciones.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nueva Notificación
    </a>
</div>

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-bell"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Notificaciones</div>
            <div class="stat-value">{{ number_format($estadisticas['total_notificaciones']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Enviadas Hoy</div>
            <div class="stat-value">{{ number_format($estadisticas['enviadas_hoy']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Programadas</div>
            <div class="stat-value">{{ number_format($estadisticas['programadas']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Destinatarios</div>
            <div class="stat-value">{{ number_format($estadisticas['total_destinatarios']) }}</div>
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
        <form method="GET" action="{{ route('notificaciones.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search">Buscar:</label>
                    <input type="text"
                           id="search"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Título, mensaje...">
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <select id="tipo" name="tipo" class="form-control">
                        <option value="">Todos</option>
                        <option value="sms" {{ request('tipo') == 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="email" {{ request('tipo') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="whatsapp" {{ request('tipo') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="programada" {{ request('estado') == 'programada' ? 'selected' : '' }}>Programada</option>
                        <option value="enviada" {{ request('estado') == 'enviada' ? 'selected' : '' }}>Enviada</option>
                        <option value="fallida" {{ request('estado') == 'fallida' ? 'selected' : '' }}>Fallida</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="destinatario">Destinatario:</label>
                    <select id="destinatario" name="destinatario" class="form-control">
                        <option value="">Todos</option>
                        <option value="todos" {{ request('destinatario') == 'todos' ? 'selected' : '' }}>Todos los socios</option>
                        <option value="activos" {{ request('destinatario') == 'activos' ? 'selected' : '' }}>Socios activos</option>
                        <option value="morosos" {{ request('destinatario') == 'morosos' ? 'selected' : '' }}>Socios morosos</option>
                        <option value="individual" {{ request('destinatario') == 'individual' ? 'selected' : '' }}>Individual</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_desde">Desde:</label>
                    <input type="date"
                           id="fecha_desde"
                           name="fecha_desde"
                           class="form-control"
                           value="{{ request('fecha_desde') }}">
                </div>

                <div class="form-group">
                    <label for="fecha_hasta">Hasta:</label>
                    <input type="date"
                           id="fecha_hasta"
                           name="fecha_hasta"
                           class="form-control"
                           value="{{ request('fecha_hasta') }}">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('notificaciones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Notificaciones -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Destinatario</th>
                        <th>Estado</th>
                        <th>Fecha Programada</th>
                        <th>Enviados/Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notificaciones as $notificacion)
                        <tr>
                            <td><strong>{{ $notificacion->titulo }}</strong></td>
                            <td>{!! $notificacion->tipo_badge !!}</td>
                            <td>{{ $notificacion->destinatario_texto }}</td>
                            <td>{!! $notificacion->estado_badge !!}</td>
                            <td>{{ $notificacion->fecha_programada_formateada ?? '-' }}</td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-text">
                                        {{ $notificacion->total_enviados }}/{{ $notificacion->total_destinatarios }}
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" style="width: {{ $notificacion->porcentaje_enviados }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('notificaciones.show', $notificacion->id) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('notificaciones.edit', $notificacion->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('notificaciones.destroy', $notificacion->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('¿Está seguro de eliminar esta notificación?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron notificaciones.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $notificaciones->appends(request()->only(['search', 'tipo', 'estado', 'destinatario', 'fecha_desde', 'fecha_hasta']))->links() }}
        </div>
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

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--white);
    }

    .stat-icon.bg-primary { background: var(--primary); }
    .stat-icon.bg-success { background: var(--success); }
    .stat-icon.bg-info { background: var(--info); }
    .stat-icon.bg-warning { background: var(--warning); }

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
        color: var(--dark);
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

    .filter-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-title i {
        color: var(--primary);
    }

    .filter-form .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
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
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
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
        background: var(--gray-500);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.75rem;
    }

    .btn-info {
        background: #06b6d4;
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

    .btn-group {
        display: flex;
        gap: 4px;
    }

    .mb-3 {
        margin-bottom: 24px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table thead tr {
        background: var(--gray-100);
        border-bottom: 2px solid var(--gray-300);
    }

    .table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        white-space: nowrap;
    }

    .table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-200);
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .text-center {
        text-align: center;
    }

    .progress-container {
        min-width: 120px;
    }

    .progress-text {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 4px;
    }

    .progress-bar-container {
        width: 100%;
        height: 8px;
        background: var(--gray-200);
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: var(--success);
        transition: width 0.3s ease;
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }
</style>
@endsection
