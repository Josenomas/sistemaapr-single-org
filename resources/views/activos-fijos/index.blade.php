@extends('layouts.app')

@section('title', 'Activos Fijos - Sistema APR')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-box"></i> Activos Fijos</h2>
    <a href="{{ route('activos-fijos.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Activo
    </a>
</div>

<div class="stats-row">
    <div class="stat-card">
        <i class="fas fa-box text-primary"></i>
        <div>
            <div class="stat-label">Total Activos</div>
            <div class="stat-value">{{ $estadisticas['total_activos'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <i class="fas fa-dollar-sign text-success"></i>
        <div>
            <div class="stat-label">Valor Total</div>
            <div class="stat-value">${{ number_format($estadisticas['valor_total'], 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET">
            <div class="form-row">
                <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}">
                <select name="categoria">
                    <option value="">Todas las categorías</option>
                    <option value="mobiliario">Mobiliario</option>
                    <option value="equipos_computo">Equipos de Cómputo</option>
                    <option value="equipos_oficina">Equipos de Oficina</option>
                    <option value="herramientas">Herramientas</option>
                    <option value="vehiculos">Vehículos</option>
                    <option value="equipamiento_tecnico">Equipamiento Técnico</option>
                    <option value="otros">Otros</option>
                </select>
                <select name="estado">
                    <option value="">Todos los estados</option>
                    <option value="excelente">Excelente</option>
                    <option value="bueno">Bueno</option>
                    <option value="regular">Regular</option>
                    <option value="malo">Malo</option>
                    <option value="en_reparacion">En Reparación</option>
                </select>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Valor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activos as $activo)
            <tr>
                <td>{{ $activo->codigo_activo }}</td>
                <td>{{ $activo->nombre }}</td>
                <td>{{ $activo->categoria_nombre }}</td>
                <td>{{ $activo->ubicacion ?? 'N/A' }}</td>
                <td>{!! $activo->estado_badge !!}</td>
                <td>{{ $activo->valor_adquisicion_formateado }}</td>
                <td>
                    <a href="{{ route('activos-fijos.show', $activo->id) }}" class="btn btn-sm btn-info">Ver</a>
                    <a href="{{ route('activos-fijos.edit', $activo->id) }}" class="btn btn-sm btn-warning">Editar</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No hay activos registrados</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $activos->links() }}
</div>
@endsection
