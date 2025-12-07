@extends('layouts.app')

@section('title', 'Editar Recordatorio - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-edit"></i>
        Editar Recordatorio
    </h2>
    <a href="{{ route('recordatorios.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Recordatorio</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('recordatorios.update', $recordatorio->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Información Básica -->
            <div class="form-section">
                <h4 class="section-title">Información Básica</h4>

                <div class="form-row">
                    <!-- Título -->
                    <div class="form-group col-md-12">
                        <label for="titulo" class="form-label required">Título</label>
                        <input type="text"
                               class="form-control @error('titulo') is-invalid @enderror"
                               id="titulo"
                               name="titulo"
                               value="{{ old('titulo', $recordatorio->titulo) }}"
                               required>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div class="form-group">
                    <label for="descripcion" class="form-label required">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror"
                              id="descripcion"
                              name="descripcion"
                              rows="4"
                              required>{{ old('descripcion', $recordatorio->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Clasificación -->
            <div class="form-section">
                <h4 class="section-title">Clasificación</h4>

                <div class="form-row">
                    <!-- Tipo de Recordatorio -->
                    <div class="form-group col-md-4">
                        <label for="tipo_recordatorio" class="form-label required">Tipo de Recordatorio</label>
                        <select class="form-control @error('tipo_recordatorio') is-invalid @enderror"
                                id="tipo_recordatorio"
                                name="tipo_recordatorio"
                                required>
                            <option value="reunion" {{ old('tipo_recordatorio', $recordatorio->tipo_recordatorio) == 'reunion' ? 'selected' : '' }}>Reunión</option>
                            <option value="pago" {{ old('tipo_recordatorio', $recordatorio->tipo_recordatorio) == 'pago' ? 'selected' : '' }}>Pago</option>
                            <option value="mantenimiento" {{ old('tipo_recordatorio', $recordatorio->tipo_recordatorio) == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="inspeccion" {{ old('tipo_recordatorio', $recordatorio->tipo_recordatorio) == 'inspeccion' ? 'selected' : '' }}>Inspección</option>
                            <option value="vencimiento" {{ old('tipo_recordatorio', $recordatorio->tipo_recordatorio) == 'vencimiento' ? 'selected' : '' }}>Vencimiento</option>
                            <option value="llamada" {{ old('tipo_recordatorio', $recordatorio->tipo_recordatorio) == 'llamada' ? 'selected' : '' }}>Llamada</option>
                            <option value="tarea" {{ old('tipo_recordatorio', $recordatorio->tipo_recordatorio) == 'tarea' ? 'selected' : '' }}>Tarea</option>
                            <option value="otro" {{ old('tipo_recordatorio', $recordatorio->tipo_recordatorio) == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('tipo_recordatorio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Prioridad -->
                    <div class="form-group col-md-4">
                        <label for="prioridad" class="form-label required">Prioridad</label>
                        <select class="form-control @error('prioridad') is-invalid @enderror"
                                id="prioridad"
                                name="prioridad"
                                required>
                            <option value="baja" {{ old('prioridad', $recordatorio->prioridad) == 'baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ old('prioridad', $recordatorio->prioridad) == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('prioridad', $recordatorio->prioridad) == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="urgente" {{ old('prioridad', $recordatorio->prioridad) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        @error('prioridad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div class="form-group col-md-4">
                        <label for="estado" class="form-label required">Estado</label>
                        <select class="form-control @error('estado') is-invalid @enderror"
                                id="estado"
                                name="estado"
                                required>
                            <option value="pendiente" {{ old('estado', $recordatorio->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="completado" {{ old('estado', $recordatorio->estado) == 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="cancelado" {{ old('estado', $recordatorio->estado) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            <option value="vencido" {{ old('estado', $recordatorio->estado) == 'vencido' ? 'selected' : '' }}>Vencido</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Fechas y Horario -->
            <div class="form-section">
                <h4 class="section-title">Fechas y Horario</h4>

                <div class="form-row">
                    <!-- Fecha de Recordatorio -->
                    <div class="form-group col-md-4">
                        <label for="fecha_recordatorio" class="form-label required">Fecha de Recordatorio</label>
                        <input type="date"
                               class="form-control @error('fecha_recordatorio') is-invalid @enderror"
                               id="fecha_recordatorio"
                               name="fecha_recordatorio"
                               value="{{ old('fecha_recordatorio', $recordatorio->fecha_recordatorio?->format('Y-m-d')) }}"
                               required>
                        @error('fecha_recordatorio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hora -->
                    <div class="form-group col-md-4">
                        <label for="hora_recordatorio" class="form-label">Hora</label>
                        <input type="time"
                               class="form-control @error('hora_recordatorio') is-invalid @enderror"
                               id="hora_recordatorio"
                               name="hora_recordatorio"
                               value="{{ old('hora_recordatorio', $recordatorio->hora_recordatorio) }}">
                        @error('hora_recordatorio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fecha de Vencimiento -->
                    <div class="form-group col-md-4">
                        <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                        <input type="date"
                               class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                               id="fecha_vencimiento"
                               name="fecha_vencimiento"
                               value="{{ old('fecha_vencimiento', $recordatorio->fecha_vencimiento?->format('Y-m-d')) }}">
                        @error('fecha_vencimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Asignación y Ubicación -->
            <div class="form-section">
                <h4 class="section-title">Asignación y Ubicación</h4>

                <div class="form-row">
                    <!-- Asignado a -->
                    <div class="form-group col-md-6">
                        <label for="id_asignado" class="form-label">Asignado a</label>
                        <select class="form-control @error('id_asignado') is-invalid @enderror"
                                id="id_asignado"
                                name="id_asignado">
                            <option value="">Sin asignar</option>
                            @foreach($funcionarios as $funcionario)
                                <option value="{{ $funcionario->id }}" {{ old('id_asignado', $recordatorio->id_asignado) == $funcionario->id ? 'selected' : '' }}>
                                    {{ $funcionario->nombre_completo }} - {{ $funcionario->cargo }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_asignado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Ubicación -->
                    <div class="form-group col-md-6">
                        <label for="ubicacion" class="form-label">Ubicación</label>
                        <input type="text"
                               class="form-control @error('ubicacion') is-invalid @enderror"
                               id="ubicacion"
                               name="ubicacion"
                               value="{{ old('ubicacion', $recordatorio->ubicacion) }}">
                        @error('ubicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Notas -->
            <div class="form-group">
                <label for="notas" class="form-label">Notas</label>
                <textarea class="form-control @error('notas') is-invalid @enderror"
                          id="notas"
                          name="notas"
                          rows="3">{{ old('notas', $recordatorio->notas) }}</textarea>
                @error('notas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Recordatorio
                </button>
                <a href="{{ route('recordatorios.index') }}" class="btn btn-secondary">
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
        border-bottom: 1px solid var(--gray-200);
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
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 16px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-label.required::after {
        content: ' *';
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.95rem;
        transition: all 0.2s;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    .invalid-feedback {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
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
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    select.form-control {
        cursor: pointer;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .col-md-4 {
        grid-column: span 1;
    }

    .col-md-6 {
        grid-column: span 1;
    }

    .col-md-12 {
        grid-column: 1 / -1;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-4,
        .col-md-6,
        .col-md-12 {
            grid-column: span 1;
        }
    }
</style>
@endsection
