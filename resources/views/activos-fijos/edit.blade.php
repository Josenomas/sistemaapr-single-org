@extends('layouts.app')
@section('title', 'Editar Activo Fijo')
@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Editar Activo: {{ $activo->nombre }}</h2>
    <a href="{{ route('activos-fijos.show', $activo->id) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>
<div class="card">
    <div class="card-body">
        <form action="{{ route('activos-fijos.update', $activo->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Código Activo *</label>
                    <input type="text" name="codigo_activo" class="form-control" value="{{ old('codigo_activo', $activo->codigo_activo) }}" required>
                </div>
                <div class="form-group col-md-8">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $activo->nombre) }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Categoría *</label>
                    <select name="categoria" class="form-control" required>
                        <option value="mobiliario" {{ $activo->categoria == 'mobiliario' ? 'selected' : '' }}>Mobiliario</option>
                        <option value="equipos_computo" {{ $activo->categoria == 'equipos_computo' ? 'selected' : '' }}>Equipos de Cómputo</option>
                        <option value="equipos_oficina" {{ $activo->categoria == 'equipos_oficina' ? 'selected' : '' }}>Equipos de Oficina</option>
                        <option value="herramientas" {{ $activo->categoria == 'herramientas' ? 'selected' : '' }}>Herramientas</option>
                        <option value="vehiculos" {{ $activo->categoria == 'vehiculos' ? 'selected' : '' }}>Vehículos</option>
                        <option value="equipamiento_tecnico" {{ $activo->categoria == 'equipamiento_tecnico' ? 'selected' : '' }}>Equipamiento Técnico</option>
                        <option value="otros" {{ $activo->categoria == 'otros' ? 'selected' : '' }}>Otros</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Estado *</label>
                    <select name="estado" class="form-control" required>
                        <option value="excelente" {{ $activo->estado == 'excelente' ? 'selected' : '' }}>Excelente</option>
                        <option value="bueno" {{ $activo->estado == 'bueno' ? 'selected' : '' }}>Bueno</option>
                        <option value="regular" {{ $activo->estado == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="malo" {{ $activo->estado == 'malo' ? 'selected' : '' }}>Malo</option>
                        <option value="en_reparacion" {{ $activo->estado == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                        <option value="dado_de_baja" {{ $activo->estado == 'dado_de_baja' ? 'selected' : '' }}>Dado de Baja</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Marca</label>
                    <input type="text" name="marca" class="form-control" value="{{ old('marca', $activo->marca) }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ old('modelo', $activo->modelo) }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Número de Serie</label>
                    <input type="text" name="numero_serie" class="form-control" value="{{ old('numero_serie', $activo->numero_serie) }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Fecha Adquisición *</label>
                    <input type="date" name="fecha_adquisicion" class="form-control" value="{{ old('fecha_adquisicion', $activo->fecha_adquisicion->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Valor Adquisición *</label>
                    <input type="number" name="valor_adquisicion" class="form-control" value="{{ old('valor_adquisicion', $activo->valor_adquisicion) }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Valor Actual</label>
                    <input type="number" name="valor_actual" class="form-control" value="{{ old('valor_actual', $activo->valor_actual) }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Proveedor</label>
                    <input type="text" name="proveedor" class="form-control" value="{{ old('proveedor', $activo->proveedor) }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Ubicación</label>
                    <input type="text" name="ubicacion" class="form-control" value="{{ old('ubicacion', $activo->ubicacion) }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Responsable</label>
                    <select name="id_responsable" class="form-control">
                        <option value="">Sin asignar</option>
                        @foreach($responsables as $resp)
                            <option value="{{ $resp->id }}" {{ $activo->id_responsable == $resp->id ? 'selected' : '' }}>{{ $resp->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Vida Útil (años)</label>
                    <input type="number" name="vida_util_anos" class="form-control" value="{{ old('vida_util_anos', $activo->vida_util_anos) }}">
                </div>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $activo->descripcion) }}</textarea>
            </div>
            <div class="form-group">
                <label>Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $activo->observaciones) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
        </form>
    </div>
</div>
@endsection
