@extends('layouts.app')
@section('title', 'Nuevo Activo Fijo')
@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus"></i> Registrar Nuevo Activo</h2>
    <a href="{{ route('activos-fijos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>
<div class="card">
    <div class="card-body">
        <form action="{{ route('activos-fijos.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Código Activo *</label>
                    <input type="text" name="codigo_activo" class="form-control" value="{{ old('codigo_activo', $codigo) }}" required>
                </div>
                <div class="form-group col-md-8">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Categoría *</label>
                    <select name="categoria" class="form-control" required>
                        <option value="mobiliario">Mobiliario</option>
                        <option value="equipos_computo">Equipos de Cómputo</option>
                        <option value="equipos_oficina">Equipos de Oficina</option>
                        <option value="herramientas">Herramientas</option>
                        <option value="vehiculos">Vehículos</option>
                        <option value="equipamiento_tecnico">Equipamiento Técnico</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Estado *</label>
                    <select name="estado" class="form-control" required>
                        <option value="excelente">Excelente</option>
                        <option value="bueno" selected>Bueno</option>
                        <option value="regular">Regular</option>
                        <option value="malo">Malo</option>
                        <option value="en_reparacion">En Reparación</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Marca</label>
                    <input type="text" name="marca" class="form-control" value="{{ old('marca') }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ old('modelo') }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Número de Serie</label>
                    <input type="text" name="numero_serie" class="form-control" value="{{ old('numero_serie') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Fecha Adquisición *</label>
                    <input type="date" name="fecha_adquisicion" class="form-control" value="{{ old('fecha_adquisicion') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Valor Adquisición *</label>
                    <input type="number" name="valor_adquisicion" class="form-control" value="{{ old('valor_adquisicion') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Valor Actual</label>
                    <input type="number" name="valor_actual" class="form-control" value="{{ old('valor_actual') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Proveedor</label>
                    <input type="text" name="proveedor" class="form-control" value="{{ old('proveedor') }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Ubicación</label>
                    <input type="text" name="ubicacion" class="form-control" value="{{ old('ubicacion') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Responsable</label>
                    <select name="id_responsable" class="form-control">
                        <option value="">Sin asignar</option>
                        @foreach($responsables as $resp)
                            <option value="{{ $resp->id }}">{{ $resp->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Vida Útil (años)</label>
                    <input type="number" name="vida_util_anos" class="form-control" value="{{ old('vida_util_anos') }}">
                </div>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
            </div>
            <div class="form-group">
                <label>Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Activo</button>
        </form>
    </div>
</div>
@endsection
