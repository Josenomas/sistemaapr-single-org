@extends('layouts.app')

@section('title', 'Activos Fijos - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-box"></i>
        Activos Fijos
    </h2>
    <a href="{{ route('activos-fijos.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Activo
    </a>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-box fa-3x mr-3"></i>
                    <div>
                        <div class="small">Total Activos</div>
                        <div class="h3 mb-0">{{ $estadisticas['total_activos'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-dollar-sign fa-3x mr-3"></i>
                    <div>
                        <div class="small">Valor Total</div>
                        <div class="h3 mb-0">${{ number_format($estadisticas['valor_total'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chart-line fa-3x mr-3"></i>
                    <div>
                        <div class="small">Valor Actual</div>
                        <div class="h3 mb-0">${{ number_format($estadisticas['valor_actual'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Filtros de Búsqueda</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('activos-fijos.index') }}">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           placeholder="Código, nombre, marca..."
                           value="{{ request('search') }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select class="form-control" id="categoria" name="categoria">
                        <option value="">Todas las categorías</option>
                        <option value="mobiliario" {{ request('categoria') == 'mobiliario' ? 'selected' : '' }}>Mobiliario</option>
                        <option value="equipos_computo" {{ request('categoria') == 'equipos_computo' ? 'selected' : '' }}>Equipos de Cómputo</option>
                        <option value="equipos_oficina" {{ request('categoria') == 'equipos_oficina' ? 'selected' : '' }}>Equipos de Oficina</option>
                        <option value="herramientas" {{ request('categoria') == 'herramientas' ? 'selected' : '' }}>Herramientas</option>
                        <option value="vehiculos" {{ request('categoria') == 'vehiculos' ? 'selected' : '' }}>Vehículos</option>
                        <option value="equipamiento_tecnico" {{ request('categoria') == 'equipamiento_tecnico' ? 'selected' : '' }}>Equipamiento Técnico</option>
                        <option value="otros" {{ request('categoria') == 'otros' ? 'selected' : '' }}>Otros</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="excelente" {{ request('estado') == 'excelente' ? 'selected' : '' }}>Excelente</option>
                        <option value="bueno" {{ request('estado') == 'bueno' ? 'selected' : '' }}>Bueno</option>
                        <option value="regular" {{ request('estado') == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="malo" {{ request('estado') == 'malo' ? 'selected' : '' }}>Malo</option>
                        <option value="en_reparacion" {{ request('estado') == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                    </select>
                </div>
                <div class="form-group col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i>
                        Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Activos -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th>Valor Adquisición</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activos as $activo)
                    <tr>
                        <td><strong>{{ $activo->codigo_activo }}</strong></td>
                        <td>{{ $activo->nombre }}</td>
                        <td><span class="badge badge-info">{{ $activo->categoria_nombre }}</span></td>
                        <td>{{ $activo->ubicacion ?? 'N/A' }}</td>
                        <td>{!! $activo->estado_badge !!}</td>
                        <td>{{ $activo->valor_adquisicion_formateado }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('activos-fijos.show', $activo->id) }}"
                                   class="btn btn-sm btn-info"
                                   title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('activos-fijos.edit', $activo->id) }}"
                                   class="btn btn-sm btn-warning"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay activos registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Paginación -->
<div class="mt-3">
    {{ $activos->links() }}
</div>
@endsection
