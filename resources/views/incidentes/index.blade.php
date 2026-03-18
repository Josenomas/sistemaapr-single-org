@extends('layouts.app')

@section('title', 'Incidentes - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exclamation-triangle"></i>
        Gestión de Incidentes
    </h2>
    <div class="header-actions">
        <button id="startTourBtn" class="btn btn-info" title="Iniciar tutorial">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
        <a href="{{ route('incidentes.create') }}" class="btn btn-primary" data-intro="Reporta un nuevo incidente: fugas de agua, cortes de suministro, baja presión, contaminación u otros problemas del sistema." data-step="1">
            <i class="fas fa-plus"></i>
            Reportar Incidente
        </a>
    </div>
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
<div class="stats-grid" data-intro="Resumen de incidentes: Críticos (alta prioridad que requieren atención inmediata) y Activos (todos los incidentes aún no resueltos)." data-step="2">
    <div class="stat-card critical">
        <div class="stat-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $criticos }}</div>
            <div class="stat-label">Incidentes Críticos</div>
        </div>
    </div>

    <div class="stat-card active">
        <div class="stat-icon">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $activos }}</div>
            <div class="stat-label">Incidentes Activos</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card filters-card" data-intro="Filtra incidentes por tipo (fuga, corte, baja presión), prioridad (crítica, alta, media, baja) o estado (reportado, en proceso, resuelto)." data-step="3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i>
            Filtrar Incidentes
        </h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('incidentes.index') }}" class="filters-form">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-tag"></i>
                        Tipo
                    </label>
                    <select name="tipo" class="filter-select">
                        <option value="">Todos los tipos</option>
                        <option value="fuga" {{ request('tipo') == 'fuga' ? 'selected' : '' }}>Fuga de Agua</option>
                        <option value="corte" {{ request('tipo') == 'corte' ? 'selected' : '' }}>Corte de Suministro</option>
                        <option value="baja_presion" {{ request('tipo') == 'baja_presion' ? 'selected' : '' }}>Baja Presión</option>
                        <option value="contaminacion" {{ request('tipo') == 'contaminacion' ? 'selected' : '' }}>Contaminación</option>
                        <option value="otro" {{ request('tipo') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-info-circle"></i>
                        Estado
                    </label>
                    <select name="estado" class="filter-select">
                        <option value="">Todos los estados</option>
                        <option value="reportado" {{ request('estado') == 'reportado' ? 'selected' : '' }}>Reportado</option>
                        <option value="en_atencion" {{ request('estado') == 'en_atencion' ? 'selected' : '' }}>En Atención</option>
                        <option value="resuelto" {{ request('estado') == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                        <option value="cerrado" {{ request('estado') == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-exclamation-triangle"></i>
                        Prioridad
                    </label>
                    <select name="prioridad" class="filter-select">
                        <option value="">Todas las prioridades</option>
                        <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="critica" {{ request('prioridad') == 'critica' ? 'selected' : '' }}>Crítica</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('incidentes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Incidentes -->
<div class="card">
    <div class="card-body">
        @if($incidentes->count() > 0)
            <div class="table-responsive" data-intro="Listado de todos los incidentes reportados. Muestra fecha, tipo, ubicación, nivel de prioridad y estado actual." data-step="4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Ubicación</th>
                            <th data-intro="Niveles de prioridad: Crítica (requiere atención inmediata), Alta, Media, Baja." data-step="5">Prioridad</th>
                            <th data-intro="Estados: Reportado (recién ingresado), En Proceso (siendo atendido), Resuelto (completado)." data-step="6">Estado</th>
                            <th>Asignado</th>
                            <th data-intro="Ver detalles completos del incidente, Editar información o Cambiar estado del incidente." data-step="7">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidentes as $incidente)
                            <tr>
                                <td>{{ $incidente->fecha_reporte->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-tipo badge-{{ $incidente->tipo }}">
                                        {{ ucfirst(str_replace('_', ' ', $incidente->tipo)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="ubicacion-cell">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $incidente->ubicacion }}
                                        @if($incidente->sector)
                                            <small class="sector">({{ $incidente->sector }})</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-prioridad badge-{{ $incidente->prioridad }}">
                                        {{ ucfirst($incidente->prioridad) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-estado badge-{{ $incidente->estado }}">
                                        {{ ucfirst(str_replace('_', ' ', $incidente->estado)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($incidente->usuarioAsignado)
                                        <div class="asignado-info">
                                            <i class="fas fa-user"></i>
                                            {{ $incidente->usuarioAsignado->nombre_completo }}
                                        </div>
                                    @else
                                        <span class="text-muted">Sin asignar</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('incidentes.show', $incidente->id) }}"
                                           class="btn btn-sm btn-info"
                                           title="Ver Detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('incidentes.edit', $incidente->id) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="pagination-wrapper">
                {{ $incidentes->appends(request()->only(['tipo', 'estado', 'prioridad']))->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <p>No se encontraron incidentes</p>
                <a href="{{ route('incidentes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Reportar Primer Incidente
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
        color: #f59e0b;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        gap: 16px;
        align-items: center;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-card.critical .stat-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .stat-card.active .stat-icon {
        background: #dbeafe;
        color: #2563eb;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
        border-radius: 12px 12px 0 0;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: #2563eb;
    }

    .card-body {
        padding: 24px;
    }

    .filters-form {
        width: 100%;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 14px;
        color: var(--gray-700);
        margin-bottom: 8px;
    }

    .filter-label i {
        color: #2563eb;
        font-size: 13px;
    }

    .filter-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: white;
        color: var(--dark);
        transition: all 0.2s;
        cursor: pointer;
    }

    .filter-select:hover {
        border-color: #9ca3af;
    }

    .filter-select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead th {
        background: var(--gray-50);
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-200);
    }

    .table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--gray-200);
        font-size: 14px;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-tipo.badge-fuga { background: #fee2e2; color: #991b1b; }
    .badge-tipo.badge-corte { background: #fef3c7; color: #92400e; }
    .badge-tipo.badge-baja_presion { background: #dbeafe; color: #1e40af; }
    .badge-tipo.badge-contaminacion { background: #fee2e2; color: #7f1d1d; }
    .badge-tipo.badge-otro { background: #e5e7eb; color: #374151; }

    .badge-prioridad.badge-baja { background: #f0fdf4; color: #15803d; }
    .badge-prioridad.badge-media { background: #fef3c7; color: #92400e; }
    .badge-prioridad.badge-alta { background: #fed7aa; color: #9a3412; }
    .badge-prioridad.badge-critica { background: #fecaca; color: #991b1b; }

    .badge-estado.badge-reportado { background: #dbeafe; color: #1e40af; }
    .badge-estado.badge-en_atencion { background: #fef3c7; color: #92400e; }
    .badge-estado.badge-resuelto { background: #d1fae5; color: #065f46; }
    .badge-estado.badge-cerrado { background: #e5e7eb; color: #374151; }

    .ubicacion-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ubicacion-cell i {
        color: var(--gray-400);
    }

    .sector {
        color: var(--gray-500);
        font-size: 12px;
    }

    .asignado-info {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.813rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-info {
        background: #0ea5e9;
        color: white;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray-500);
    }

    .empty-state i {
        font-size: 64px;
        color: var(--gray-300);
        margin-bottom: 16px;
    }

    .empty-state p {
        font-size: 16px;
        margin-bottom: 20px;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
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

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .table-responsive {
            font-size: 12px;
        }
    }

    .btn-info {
        background: #06b6d4;
        color: white;
    }

    /* Estilos personalizados para Intro.js */
    .custom-tooltip {
        max-width: 400px;
    }

    .introjs-tooltip {
        border-radius: 12px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
    }

    .introjs-button {
        border-radius: 6px !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        text-shadow: none !important;
    }

    .introjs-nextbutton {
        background: var(--primary) !important;
        border: none !important;
    }

    .introjs-prevbutton {
        background: var(--gray-500) !important;
        border: none !important;
    }

    .introjs-skipbutton {
        color: var(--gray-600) !important;
    }

    .introjs-donebutton {
        background: var(--success) !important;
        border: none !important;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el tour
        const intro = introJs();
        intro.setOptions({
            nextLabel: 'Siguiente',
            prevLabel: 'Anterior',
            doneLabel: 'Finalizar',
            skipLabel: 'Salir',
            showProgress: true,
            showBullets: false,
            exitOnOverlayClick: false,
            disableInteraction: true,
            tooltipClass: 'custom-tooltip'
        });

        // Botón para iniciar el tour
        document.getElementById('startTourBtn').addEventListener('click', function() {
            intro.start();
        });

        // Mostrar tour automáticamente solo la primera vez
        const tourShown = localStorage.getItem('incidentesTourShown');
        if (!tourShown) {
            setTimeout(function() {
                intro.start();
                localStorage.setItem('incidentesTourShown', 'true');
            }, 500);
        }
    });
</script>
@endsection
