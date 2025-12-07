@extends('layouts.app')

@section('title', 'Editar Renovación de Medidor - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-sync-alt"></i>
        Editar Renovación de Medidor
    </h2>
    <a href="{{ route('renovaciones.show', $renovacion->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Renovación</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('renovaciones.update', $renovacion->id) }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h4 class="section-title">Datos del Socio</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="id_socio" class="required">Socio</label>
                        <select id="id_socio" name="id_socio" class="form-control @error('id_socio') error @enderror" required>
                            <option value="">Seleccione un socio</option>
                            @foreach($socios as $socio)
                                <option value="{{ $socio->id }}" {{ old('id_socio', $renovacion->id_socio) == $socio->id ? 'selected' : '' }}>
                                    {{ $socio->numero_socio }} - {{ $socio->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_socio')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Información del Medidor Anterior</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="medidor_anterior">Número de Medidor Anterior</label>
                        <input type="text" id="medidor_anterior" name="medidor_anterior"
                               class="form-control @error('medidor_anterior') error @enderror"
                               value="{{ old('medidor_anterior', $renovacion->medidor_anterior) }}">
                        @error('medidor_anterior')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="lectura_anterior">Lectura Anterior (m³)</label>
                        <input type="number" id="lectura_anterior" name="lectura_anterior"
                               class="form-control @error('lectura_anterior') error @enderror"
                               value="{{ old('lectura_anterior', $renovacion->lectura_anterior) }}" min="0" step="0.01">
                        @error('lectura_anterior')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Información del Medidor Nuevo</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="medidor_nuevo" class="required">Número de Medidor Nuevo</label>
                        <input type="text" id="medidor_nuevo" name="medidor_nuevo"
                               class="form-control @error('medidor_nuevo') error @enderror"
                               value="{{ old('medidor_nuevo', $renovacion->medidor_nuevo) }}" required>
                        @error('medidor_nuevo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="lectura_inicial" class="required">Lectura Inicial (m³)</label>
                        <input type="number" id="lectura_inicial" name="lectura_inicial"
                               class="form-control @error('lectura_inicial') error @enderror"
                               value="{{ old('lectura_inicial', $renovacion->lectura_inicial) }}" min="0" step="0.01" required>
                        @error('lectura_inicial')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Detalles de la Renovación</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_renovacion" class="required">Fecha de Renovación</label>
                        <input type="date" id="fecha_renovacion" name="fecha_renovacion"
                               class="form-control @error('fecha_renovacion') error @enderror"
                               value="{{ old('fecha_renovacion', $renovacion->fecha_renovacion->format('Y-m-d')) }}" required>
                        @error('fecha_renovacion')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="motivo" class="required">Motivo</label>
                        <select id="motivo" name="motivo" class="form-control @error('motivo') error @enderror" required>
                            <option value="">Seleccione un motivo</option>
                            <option value="deterioro" {{ old('motivo', $renovacion->motivo) == 'deterioro' ? 'selected' : '' }}>Deterioro</option>
                            <option value="falla" {{ old('motivo', $renovacion->motivo) == 'falla' ? 'selected' : '' }}>Falla</option>
                            <option value="actualizacion" {{ old('motivo', $renovacion->motivo) == 'actualizacion' ? 'selected' : '' }}>Actualización</option>
                            <option value="robo" {{ old('motivo', $renovacion->motivo) == 'robo' ? 'selected' : '' }}>Robo</option>
                            <option value="otro" {{ old('motivo', $renovacion->motivo) == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('motivo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="estado" class="required">Estado</label>
                        <select id="estado" name="estado" class="form-control @error('estado') error @enderror" required>
                            <option value="planificado" {{ old('estado', $renovacion->estado) == 'planificado' ? 'selected' : '' }}>Planificado</option>
                            <option value="ejecutado" {{ old('estado', $renovacion->estado) == 'ejecutado' ? 'selected' : '' }}>Ejecutado</option>
                            <option value="cancelado" {{ old('estado', $renovacion->estado) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('estado')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="costo_renovacion">Costo de Renovación ($)</label>
                        <input type="number" id="costo_renovacion" name="costo_renovacion"
                               class="form-control @error('costo_renovacion') error @enderror"
                               value="{{ old('costo_renovacion', $renovacion->costo_renovacion) }}" min="0" step="0.01">
                        @error('costo_renovacion')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Asignación de Técnico</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="id_tecnico">Técnico Responsable</label>
                        <select id="id_tecnico" name="id_tecnico" class="form-control @error('id_tecnico') error @enderror">
                            <option value="">Sin asignar</option>
                            @foreach($tecnicos as $tecnico)
                                <option value="{{ $tecnico->id }}" {{ old('id_tecnico', $renovacion->id_tecnico) == $tecnico->id ? 'selected' : '' }}>
                                    {{ $tecnico->nombre_completo }} - {{ $tecnico->cargo }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_tecnico')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Observaciones</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="observaciones">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control @error('observaciones') error @enderror"
                                  rows="4">{{ old('observaciones', $renovacion->observaciones) }}</textarea>
                        @error('observaciones')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Renovación
                </button>
                <a href="{{ route('renovaciones.show', $renovacion->id) }}" class="btn btn-secondary">
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
