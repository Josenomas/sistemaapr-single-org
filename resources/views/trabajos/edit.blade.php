@extends('layouts.app')

@section('title', 'Editar Trabajo - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-tools"></i>
        Editar Trabajo Realizado
    </h2>
    <a href="{{ route('trabajos.show', $trabajo->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Trabajo</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('trabajos.update', $trabajo->id) }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h4 class="section-title">Datos Básicos</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="titulo" class="required">Título del Trabajo</label>
                        <input type="text" id="titulo" name="titulo" class="form-control @error('titulo') error @enderror"
                               value="{{ old('titulo', $trabajo->titulo) }}" required>
                        @error('titulo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="descripcion" class="required">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') error @enderror"
                                  rows="4" required>{{ old('descripcion', $trabajo->descripcion) }}</textarea>
                        @error('descripcion')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo_trabajo" class="required">Tipo de Trabajo</label>
                        <select id="tipo_trabajo" name="tipo_trabajo" class="form-control @error('tipo_trabajo') error @enderror" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="mantenimiento" {{ old('tipo_trabajo', $trabajo->tipo_trabajo) == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="reparacion" {{ old('tipo_trabajo', $trabajo->tipo_trabajo) == 'reparacion' ? 'selected' : '' }}>Reparación</option>
                            <option value="instalacion" {{ old('tipo_trabajo', $trabajo->tipo_trabajo) == 'instalacion' ? 'selected' : '' }}>Instalación</option>
                            <option value="inspeccion" {{ old('tipo_trabajo', $trabajo->tipo_trabajo) == 'inspeccion' ? 'selected' : '' }}>Inspección</option>
                            <option value="otro" {{ old('tipo_trabajo', $trabajo->tipo_trabajo) == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('tipo_trabajo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" class="form-control @error('ubicacion') error @enderror"
                               value="{{ old('ubicacion', $trabajo->ubicacion) }}" placeholder="Ej: Estanque principal, Sector Norte">
                        @error('ubicacion')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Planificación</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_inicio" class="required">Fecha de Inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio"
                               class="form-control @error('fecha_inicio') error @enderror"
                               value="{{ old('fecha_inicio', $trabajo->fecha_inicio->format('Y-m-d')) }}" required>
                        @error('fecha_inicio')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="fecha_termino">Fecha de Término</label>
                        <input type="date" id="fecha_termino" name="fecha_termino"
                               class="form-control @error('fecha_termino') error @enderror"
                               value="{{ old('fecha_termino', $trabajo->fecha_termino ? $trabajo->fecha_termino->format('Y-m-d') : '') }}">
                        @error('fecha_termino')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="estado" class="required">Estado</label>
                        <select id="estado" name="estado" class="form-control @error('estado') error @enderror" required>
                            <option value="planificado" {{ old('estado', $trabajo->estado) == 'planificado' ? 'selected' : '' }}>Planificado</option>
                            <option value="en_proceso" {{ old('estado', $trabajo->estado) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="completado" {{ old('estado', $trabajo->estado) == 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="cancelado" {{ old('estado', $trabajo->estado) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('estado')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="prioridad" class="required">Prioridad</label>
                        <select id="prioridad" name="prioridad" class="form-control @error('prioridad') error @enderror" required>
                            <option value="baja" {{ old('prioridad', $trabajo->prioridad) == 'baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ old('prioridad', $trabajo->prioridad) == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('prioridad', $trabajo->prioridad) == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="urgente" {{ old('prioridad', $trabajo->prioridad) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        @error('prioridad')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="costo_estimado">Costo Estimado ($)</label>
                        <input type="number" id="costo_estimado" name="costo_estimado"
                               class="form-control @error('costo_estimado') error @enderror"
                               value="{{ old('costo_estimado', $trabajo->costo_estimado) }}" min="0" step="0.01">
                        @error('costo_estimado')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="costo_real">Costo Real ($)</label>
                        <input type="number" id="costo_real" name="costo_real"
                               class="form-control @error('costo_real') error @enderror"
                               value="{{ old('costo_real', $trabajo->costo_real) }}" min="0" step="0.01">
                        @error('costo_real')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Asignación</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="id_responsable">Funcionario Responsable</label>
                        <select id="id_responsable" name="id_responsable" class="form-control @error('id_responsable') error @enderror">
                            <option value="">Sin asignar</option>
                            @foreach($funcionarios as $funcionario)
                                <option value="{{ $funcionario->id }}" {{ old('id_responsable', $trabajo->id_responsable) == $funcionario->id ? 'selected' : '' }}>
                                    {{ $funcionario->nombre_completo }} - {{ $funcionario->cargo }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_responsable')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Detalles Adicionales</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="materiales_utilizados">Materiales Utilizados</label>
                        <textarea id="materiales_utilizados" name="materiales_utilizados" class="form-control @error('materiales_utilizados') error @enderror"
                                  rows="3">{{ old('materiales_utilizados', $trabajo->materiales_utilizados) }}</textarea>
                        @error('materiales_utilizados')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="observaciones">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control @error('observaciones') error @enderror"
                                  rows="3">{{ old('observaciones', $trabajo->observaciones) }}</textarea>
                        @error('observaciones')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Trabajo
                </button>
                <a href="{{ route('trabajos.show', $trabajo->id) }}" class="btn btn-secondary">
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
        color: var(--primary);
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 2px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-body {
        padding: 24px;
    }

    .form-section {
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--gray-200);
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 20px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--primary);
        display: inline-block;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
    }

    .form-group label.required::after {
        content: ' *';
        color: var(--danger);
    }

    .form-control {
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-control.error {
        border-color: var(--danger);
    }

    .error-message {
        font-size: 0.75rem;
        color: var(--danger);
        margin-top: 4px;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid var(--gray-200);
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-600);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-700);
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
