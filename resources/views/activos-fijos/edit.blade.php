@extends('layouts.app')

@section('title', 'Editar Activo Fijo - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-edit"></i>
        Editar Activo: {{ $activo->nombre }}
    </h2>
    <a href="{{ route('activos-fijos.show', $activo->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Activo</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('activos-fijos.update', $activo->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="codigo_activo" class="form-label required">Código Activo</label>
                    <input type="text"
                           class="form-control @error('codigo_activo') is-invalid @enderror"
                           id="codigo_activo"
                           name="codigo_activo"
                           value="{{ old('codigo_activo', $activo->codigo_activo) }}"
                           readonly
                           required>
                    @error('codigo_activo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-8">
                    <label for="nombre" class="form-label required">Nombre</label>
                    <input type="text"
                           class="form-control @error('nombre') is-invalid @enderror"
                           id="nombre"
                           name="nombre"
                           value="{{ old('nombre', $activo->nombre) }}"
                           required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="categoria" class="form-label required">Categoría</label>
                    <select class="form-control @error('categoria') is-invalid @enderror"
                            id="categoria"
                            name="categoria"
                            required>
                        <option value="mobiliario" {{ old('categoria', $activo->categoria) == 'mobiliario' ? 'selected' : '' }}>Mobiliario</option>
                        <option value="equipos_computo" {{ old('categoria', $activo->categoria) == 'equipos_computo' ? 'selected' : '' }}>Equipos de Cómputo</option>
                        <option value="equipos_oficina" {{ old('categoria', $activo->categoria) == 'equipos_oficina' ? 'selected' : '' }}>Equipos de Oficina</option>
                        <option value="herramientas" {{ old('categoria', $activo->categoria) == 'herramientas' ? 'selected' : '' }}>Herramientas</option>
                        <option value="vehiculos" {{ old('categoria', $activo->categoria) == 'vehiculos' ? 'selected' : '' }}>Vehículos</option>
                        <option value="equipamiento_tecnico" {{ old('categoria', $activo->categoria) == 'equipamiento_tecnico' ? 'selected' : '' }}>Equipamiento Técnico</option>
                        <option value="otros" {{ old('categoria', $activo->categoria) == 'otros' ? 'selected' : '' }}>Otros</option>
                    </select>
                    @error('categoria')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="estado" class="form-label required">Estado</label>
                    <select class="form-control @error('estado') is-invalid @enderror"
                            id="estado"
                            name="estado"
                            required>
                        <option value="excelente" {{ old('estado', $activo->estado) == 'excelente' ? 'selected' : '' }}>Excelente</option>
                        <option value="bueno" {{ old('estado', $activo->estado) == 'bueno' ? 'selected' : '' }}>Bueno</option>
                        <option value="regular" {{ old('estado', $activo->estado) == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="malo" {{ old('estado', $activo->estado) == 'malo' ? 'selected' : '' }}>Malo</option>
                        <option value="en_reparacion" {{ old('estado', $activo->estado) == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                        <option value="dado_de_baja" {{ old('estado', $activo->estado) == 'dado_de_baja' ? 'selected' : '' }}>Dado de Baja</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text"
                           class="form-control @error('marca') is-invalid @enderror"
                           id="marca"
                           name="marca"
                           value="{{ old('marca', $activo->marca) }}">
                    @error('marca')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text"
                           class="form-control @error('modelo') is-invalid @enderror"
                           id="modelo"
                           name="modelo"
                           value="{{ old('modelo', $activo->modelo) }}">
                    @error('modelo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="numero_serie" class="form-label">Número de Serie</label>
                    <input type="text"
                           class="form-control @error('numero_serie') is-invalid @enderror"
                           id="numero_serie"
                           name="numero_serie"
                           value="{{ old('numero_serie', $activo->numero_serie) }}">
                    @error('numero_serie')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="fecha_adquisicion" class="form-label required">Fecha Adquisición</label>
                    <input type="date"
                           class="form-control @error('fecha_adquisicion') is-invalid @enderror"
                           id="fecha_adquisicion"
                           name="fecha_adquisicion"
                           value="{{ old('fecha_adquisicion', $activo->fecha_adquisicion->format('Y-m-d')) }}"
                           required>
                    @error('fecha_adquisicion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="valor_adquisicion" class="form-label required">Valor Adquisición</label>
                    <input type="number"
                           class="form-control @error('valor_adquisicion') is-invalid @enderror"
                           id="valor_adquisicion"
                           name="valor_adquisicion"
                           value="{{ old('valor_adquisicion', $activo->valor_adquisicion) }}"
                           step="0.01"
                           required>
                    @error('valor_adquisicion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="valor_actual" class="form-label">Valor Actual</label>
                    <input type="number"
                           class="form-control @error('valor_actual') is-invalid @enderror"
                           id="valor_actual"
                           name="valor_actual"
                           value="{{ old('valor_actual', $activo->valor_actual) }}"
                           step="0.01">
                    @error('valor_actual')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="proveedor" class="form-label">Proveedor</label>
                    <input type="text"
                           class="form-control @error('proveedor') is-invalid @enderror"
                           id="proveedor"
                           name="proveedor"
                           value="{{ old('proveedor', $activo->proveedor) }}">
                    @error('proveedor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text"
                           class="form-control @error('ubicacion') is-invalid @enderror"
                           id="ubicacion"
                           name="ubicacion"
                           value="{{ old('ubicacion', $activo->ubicacion) }}">
                    @error('ubicacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="id_responsable" class="form-label">Responsable</label>
                    <select class="form-control @error('id_responsable') is-invalid @enderror"
                            id="id_responsable"
                            name="id_responsable">
                        <option value="">Sin asignar</option>
                        @foreach($responsables as $resp)
                            <option value="{{ $resp->id }}" {{ old('id_responsable', $activo->id_responsable) == $resp->id ? 'selected' : '' }}>
                                {{ $resp->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_responsable')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="vida_util_anos" class="form-label">Vida Útil (años)</label>
                    <input type="number"
                           class="form-control @error('vida_util_anos') is-invalid @enderror"
                           id="vida_util_anos"
                           name="vida_util_anos"
                           value="{{ old('vida_util_anos', $activo->vida_util_anos) }}">
                    @error('vida_util_anos')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                          id="descripcion"
                          name="descripcion"
                          rows="3">{{ old('descripcion', $activo->descripcion) }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                          id="observaciones"
                          name="observaciones"
                          rows="3">{{ old('observaciones', $activo->observaciones) }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Cambios
                </button>
                <a href="{{ route('activos-fijos.show', $activo->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
