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

<div class="card">
    <div class="card-body">
        <div class="filters-section">
            <form method="GET" action="{{ route('activos-fijos.index') }}" class="filter-form">
                <div class="filter-group">
                    <label for="search">Buscar</label>
                    <input type="text"
                           name="search"
                           id="search"
                           class="form-control"
                           placeholder="Código, nombre, marca..."
                           value="{{ request('search') }}">
                </div>

                <div class="filter-group">
                    <label for="categoria">Categoría</label>
                    <select name="categoria" id="categoria" class="form-control">
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

                <div class="filter-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="excelente" {{ request('estado') == 'excelente' ? 'selected' : '' }}>Excelente</option>
                        <option value="bueno" {{ request('estado') == 'bueno' ? 'selected' : '' }}>Bueno</option>
                        <option value="regular" {{ request('estado') == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="malo" {{ request('estado') == 'malo' ? 'selected' : '' }}>Malo</option>
                        <option value="en_reparacion" {{ request('estado') == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('activos-fijos.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

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

<div class="pagination-wrapper">
    {{ $activos->links() }}
</div>
@endsection
