@extends('layouts.app')

@section('title', 'Editar Folio SII')

@section('styles')
<style>
.required::after {
    content: " *";
    color: #dc3545;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit"></i> Editar Folio #{{ $folio->id }}</h1>
        <a href="{{ route('folios-sii.show', $folio->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('folios-sii.update', $folio->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Solo se pueden modificar la fecha de vencimiento, estado y observaciones.
                    El rango de folios no puede ser modificado.
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Documento (No editable)</label>
                        <input type="text" class="form-control" value="{{ strtoupper(str_replace('_', ' ', $folio->tipo_documento)) }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Rango de Folios (No editable)</label>
                        <input type="text" class="form-control"
                               value="{{ number_format($folio->folio_desde, 0, ',', '.') }} - {{ number_format($folio->folio_hasta, 0, ',', '.') }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label required">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                               value="{{ old('fecha_vencimiento', $folio->fecha_vencimiento->format('Y-m-d')) }}" required>
                        @error('fecha_vencimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required">Estado</label>
                        <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                            <option value="activo" {{ old('estado', $folio->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="agotado" {{ old('estado', $folio->estado) == 'agotado' ? 'selected' : '' }}>Agotado</option>
                            <option value="vencido" {{ old('estado', $folio->estado) == 'vencido' ? 'selected' : '' }}>Vencido</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                              rows="3">{{ old('observaciones', $folio->observaciones) }}</textarea>
                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('folios-sii.show', $folio->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
