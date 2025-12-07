@extends('layouts.app')

@section('title', 'Editar Incidente - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exclamation-triangle"></i>
        Editar Incidente
    </h2>
    <div class="header-actions">
        <a href="{{ route('incidentes.show', $incidente->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('incidentes.update', $incidente->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="tipo" class="form-label required">Tipo de Incidente</label>
                    <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                        <option value="fuga" {{ $incidente->tipo == 'fuga' ? 'selected' : '' }}>Fuga de Agua</option>
                        <option value="corte" {{ $incidente->tipo == 'corte' ? 'selected' : '' }}>Corte de Suministro</option>
                        <option value="baja_presion" {{ $incidente->tipo == 'baja_presion' ? 'selected' : '' }}>Baja Presión</option>
                        <option value="contaminacion" {{ $incidente->tipo == 'contaminacion' ? 'selected' : '' }}>Contaminación</option>
                        <option value="otro" {{ $incidente->tipo == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="prioridad" class="form-label required">Prioridad</label>
                    <select class="form-control @error('prioridad') is-invalid @enderror" id="prioridad" name="prioridad" required>
                        <option value="baja" {{ $incidente->prioridad == 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ $incidente->prioridad == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ $incidente->prioridad == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="critica" {{ $incidente->prioridad == 'critica' ? 'selected' : '' }}>Crítica</option>
                    </select>
                    @error('prioridad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="ubicacion" class="form-label required">Ubicación</label>
                    <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" id="ubicacion" name="ubicacion" value="{{ $incidente->ubicacion }}" required>
                    @error('ubicacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="sector" class="form-label">Sector</label>
                    <input type="text" class="form-control" id="sector" name="sector" value="{{ $incidente->sector }}">
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label required">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="4" required>{{ $incidente->descripcion }}</textarea>
                @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="estado" class="form-label required">Estado</label>
                    <select class="form-control" id="estado" name="estado" required>
                        <option value="reportado" {{ $incidente->estado == 'reportado' ? 'selected' : '' }}>Reportado</option>
                        <option value="en_atencion" {{ $incidente->estado == 'en_atencion' ? 'selected' : '' }}>En Atención</option>
                        <option value="resuelto" {{ $incidente->estado == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                        <option value="cerrado" {{ $incidente->estado == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="id_usuario_asignado" class="form-label">Asignar a</label>
                    <select class="form-control" id="id_usuario_asignado" name="id_usuario_asignado">
                        <option value="">Sin asignar...</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ $incidente->id_usuario_asignado == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="solucion" class="form-label">Solución</label>
                <textarea class="form-control" id="solucion" name="solucion" rows="3">{{ $incidente->solucion }}</textarea>
            </div>

            <div class="form-group">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ $incidente->observaciones }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Cambios
                </button>
                <a href="{{ route('incidentes.show', $incidente->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 1.75rem; font-weight: 700; display: flex; align-items: center; gap: 12px; margin: 0; }
    .page-title i { color: #f59e0b; }
    .card { background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .card-body { padding: 32px; }
    .form-row { display: grid; grid-template-columns: repeat(12, 1fr); gap: 20px; margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; }
    .col-md-4 { grid-column: span 4; }
    .col-md-6 { grid-column: span 6; }
    .col-md-8 { grid-column: span 8; }
    .form-label { display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px; }
    .form-label.required::after { content: '*'; color: #ef4444; margin-left: 4px; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
    .form-control:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
    textarea.form-control { resize: vertical; font-family: inherit; }
    .form-actions { display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb; }
    .btn { padding: 10px 20px; border-radius: 6px; border: none; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; }
    .btn-secondary { background: #e5e7eb; color: #374151; }
    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
        .col-md-4, .col-md-6, .col-md-8 { grid-column: span 1; }
    }
</style>
@endsection
