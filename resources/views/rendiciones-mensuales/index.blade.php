@extends('layouts.app')

@section('title', 'Rendicion Mensual - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Rendicion Mensual
    </h2>
    <div class="btn-group">
        <a href="{{ route('rendiciones-mensuales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nueva Rendicion
        </a>
    </div>
</div>

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

<!-- Estadisticas -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Rendiciones</div>
            <div class="stat-value">{{ $estadisticas['total_rendiciones'] }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Rendiciones Abiertas</div>
            <div class="stat-value">{{ $estadisticas['abiertas'] }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-lock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Rendiciones Cerradas</div>
            <div class="stat-value">{{ $estadisticas['cerradas'] }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Anio Actual</div>
            <div class="stat-value">{{ $estadisticas['anio_actual'] }}</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <h3 class="filter-title">
            <i class="fas fa-filter"></i>
            Filtros de Busqueda
        </h3>
        <form method="GET" action="{{ route('rendiciones-mensuales.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search">Buscar:</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Buscar por codigo o periodo..." value="{{ request('search') }}">
                </div>

                <div class="form-group">
                    <label for="anio">Anio:</label>
                    <select id="anio" name="anio" class="form-control">
                        <option value="">Todos los anios</option>
                        @foreach($aniosDisponibles as $anio)
                            <option value="{{ $anio }}" {{ request('anio') == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="mes">Mes:</label>
                    <select id="mes" name="mes" class="form-control">
                        <option value="">Todos los meses</option>
                        <option value="1" {{ request('mes') == '1' ? 'selected' : '' }}>Enero</option>
                        <option value="2" {{ request('mes') == '2' ? 'selected' : '' }}>Febrero</option>
                        <option value="3" {{ request('mes') == '3' ? 'selected' : '' }}>Marzo</option>
                        <option value="4" {{ request('mes') == '4' ? 'selected' : '' }}>Abril</option>
                        <option value="5" {{ request('mes') == '5' ? 'selected' : '' }}>Mayo</option>
                        <option value="6" {{ request('mes') == '6' ? 'selected' : '' }}>Junio</option>
                        <option value="7" {{ request('mes') == '7' ? 'selected' : '' }}>Julio</option>
                        <option value="8" {{ request('mes') == '8' ? 'selected' : '' }}>Agosto</option>
                        <option value="9" {{ request('mes') == '9' ? 'selected' : '' }}>Septiembre</option>
                        <option value="10" {{ request('mes') == '10' ? 'selected' : '' }}>Octubre</option>
                        <option value="11" {{ request('mes') == '11' ? 'selected' : '' }}>Noviembre</option>
                        <option value="12" {{ request('mes') == '12' ? 'selected' : '' }}>Diciembre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="abierto" {{ request('estado') == 'abierto' ? 'selected' : '' }}>Abierto</option>
                        <option value="cerrado" {{ request('estado') == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>

                @if(request()->hasAny(['search', 'anio', 'mes', 'estado']))
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <a href="{{ route('rendiciones-mensuales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Rendiciones -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Periodo</th>
                        <th>Saldo Anterior</th>
                        <th>Ingresos</th>
                        <th>Egresos</th>
                        <th>Saldo Final</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rendiciones as $rendicion)
                        <tr class="{{ $rendicion->es_deficit ? 'row-danger' : '' }}">
                            <td><strong>{{ $rendicion->codigo_rendicion }}</strong></td>
                            <td>{{ $rendicion->periodo_texto }}</td>
                            <td>{{ $rendicion->saldo_anterior_formateado }}</td>
                            <td class="text-success"><strong>{{ $rendicion->total_ingresos_formateado }}</strong></td>
                            <td class="text-danger"><strong>{{ $rendicion->total_egresos_formateado }}</strong></td>
                            <td class="{{ $rendicion->es_deficit ? 'text-danger' : 'text-success' }}">
                                <strong>{{ $rendicion->saldo_final_formateado }}</strong>
                                @if($rendicion->es_deficit)
                                    <i class="fas fa-exclamation-triangle text-danger" title="Deficit"></i>
                                @endif
                            </td>
                            <td>{!! $rendicion->estado_badge !!}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('rendiciones-mensuales.show', $rendicion->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($rendicion->estado == 'abierto')
                                        <a href="{{ route('rendiciones-mensuales.edit', $rendicion->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('rendiciones-mensuales.destroy', $rendicion->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Esta seguro de eliminar esta rendicion?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-inbox"></i>
                                <p>No se encontraron rendiciones mensuales</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rendiciones->hasPages())
            <div class="pagination-wrapper">
                {{ $rendiciones->appends(request()->only(['search', 'anio', 'mes', 'estado']))->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Estilos adicionales específicos si son necesarios */
    .filter-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-title i {
        color: var(--primary);
    }

    .filter-form .form-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto auto;
        gap: 16px;
        align-items: end;
    }

    .row-danger {
        background-color: #fee2e2 !important;
    }

    @media (max-width: 768px) {
        .filter-form .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
