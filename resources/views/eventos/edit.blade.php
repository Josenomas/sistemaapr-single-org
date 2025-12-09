@extends('layouts.app')

@section('title', 'Editar Evento - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-calendar-edit"></i>
        Editar Evento
    </h2>
    <a href="{{ route('eventos.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Evento</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('eventos.update', $evento->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="titulo" class="form-label required">Título del Evento</label>
                    <input type="text"
                           class="form-control @error('titulo') is-invalid @enderror"
                           id="titulo"
                           name="titulo"
                           value="{{ old('titulo', $evento->titulo) }}"
                           required>
                    @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="tipo" class="form-label required">Tipo de Evento</label>
                    <select name="tipo"
                            id="tipo"
                            class="form-control @error('tipo') is-invalid @enderror"
                            required>
                        <option value="">Seleccionar tipo...</option>
                        <option value="FACTURACIÓN" {{ old('tipo', $evento->tipo) == 'FACTURACIÓN' ? 'selected' : '' }}>Facturación</option>
                        <option value="OPERACIÓN" {{ old('tipo', $evento->tipo) == 'OPERACIÓN' ? 'selected' : '' }}>Operación</option>
                        <option value="COBRO" {{ old('tipo', $evento->tipo) == 'COBRO' ? 'selected' : '' }}>Cobro</option>
                        <option value="MANTENIMIENTO" {{ old('tipo', $evento->tipo) == 'MANTENIMIENTO' ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="OTRO" {{ old('tipo', $evento->tipo) == 'OTRO' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion"
                              id="descripcion"
                              class="form-control @error('descripcion') is-invalid @enderror"
                              rows="3">{{ old('descripcion', $evento->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="fecha_evento" class="form-label required">Fecha del Evento</label>
                    <input type="date"
                           class="form-control @error('fecha_evento') is-invalid @enderror"
                           id="fecha_evento"
                           name="fecha_evento"
                           value="{{ old('fecha_evento', $evento->fecha_evento->format('Y-m-d')) }}"
                           required>
                    @error('fecha_evento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="recurrencia" class="form-label required">Recurrencia</label>
                    <select name="recurrencia"
                            id="recurrencia"
                            class="form-control @error('recurrencia') is-invalid @enderror"
                            required>
                        <option value="ninguna" {{ old('recurrencia', $evento->recurrencia) == 'ninguna' ? 'selected' : '' }}>Evento único</option>
                        <option value="diaria" {{ old('recurrencia', $evento->recurrencia) == 'diaria' ? 'selected' : '' }}>Todos los días</option>
                        <option value="semanal" {{ old('recurrencia', $evento->recurrencia) == 'semanal' ? 'selected' : '' }}>Cada semana</option>
                        <option value="mensual" {{ old('recurrencia', $evento->recurrencia) == 'mensual' ? 'selected' : '' }}>Cada mes</option>
                        <option value="anual" {{ old('recurrencia', $evento->recurrencia) == 'anual' ? 'selected' : '' }}>Cada año</option>
                    </select>
                    @error('recurrencia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4" id="dia-recurrencia-group" style="display:{{ old('recurrencia', $evento->recurrencia) == 'mensual' ? 'block' : 'none' }};">
                    <label for="dia_recurrencia" class="form-label">Día del Mes</label>
                    <input type="number"
                           class="form-control @error('dia_recurrencia') is-invalid @enderror"
                           id="dia_recurrencia"
                           name="dia_recurrencia"
                           min="1"
                           max="31"
                           value="{{ old('dia_recurrencia', $evento->dia_recurrencia) }}">
                    <small class="form-text text-muted">Para eventos mensuales (ej: día 15)</small>
                    @error('dia_recurrencia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="icono" class="form-label required">Icono</label>
                    <select name="icono"
                            id="icono"
                            class="form-control @error('icono') is-invalid @enderror"
                            required>
                        <option value="fa-calendar-check" {{ old('icono', $evento->icono) == 'fa-calendar-check' ? 'selected' : '' }}>Calendario Check</option>
                        <option value="fa-file-invoice-dollar" {{ old('icono', $evento->icono) == 'fa-file-invoice-dollar' ? 'selected' : '' }}>Boletas</option>
                        <option value="fa-tint" {{ old('icono', $evento->icono) == 'fa-tint' ? 'selected' : '' }}>Agua</option>
                        <option value="fa-exclamation-triangle" {{ old('icono', $evento->icono) == 'fa-exclamation-triangle' ? 'selected' : '' }}>Advertencia</option>
                        <option value="fa-tools" {{ old('icono', $evento->icono) == 'fa-tools' ? 'selected' : '' }}>Herramientas</option>
                        <option value="fa-bell" {{ old('icono', $evento->icono) == 'fa-bell' ? 'selected' : '' }}>Campana</option>
                        <option value="fa-clipboard-list" {{ old('icono', $evento->icono) == 'fa-clipboard-list' ? 'selected' : '' }}>Lista</option>
                    </select>
                    @error('icono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="color" class="form-label required">Color</label>
                    <select name="color"
                            id="color"
                            class="form-control @error('color') is-invalid @enderror"
                            required>
                        <option value="primary" {{ old('color', $evento->color) == 'primary' ? 'selected' : '' }}>Azul (Primary)</option>
                        <option value="success" {{ old('color', $evento->color) == 'success' ? 'selected' : '' }}>Verde (Success)</option>
                        <option value="warning" {{ old('color', $evento->color) == 'warning' ? 'selected' : '' }}>Naranja (Warning)</option>
                        <option value="danger" {{ old('color', $evento->color) == 'danger' ? 'selected' : '' }}>Rojo (Danger)</option>
                        <option value="info" {{ old('color', $evento->color) == 'info' ? 'selected' : '' }}>Celeste (Info)</option>
                    </select>
                    @error('color')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="custom-control custom-checkbox" style="padding-top: 8px;">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="notificar"
                               name="notificar"
                               {{ old('notificar', $evento->notificar) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="notificar">
                            Activar notificaciones
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    Actualizar Evento
                </button>
                <a href="{{ route('eventos.index') }}" class="btn btn-secondary">
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

    .form-text {
        font-size: 0.8rem;
        color: var(--gray-500);
        margin-top: 4px;
    }

    .custom-control-input {
        margin-right: 8px;
    }

    .custom-control-label {
        font-size: 0.95rem;
        color: var(--gray-700);
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

    .btn-success {
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
    }

    .btn-success:hover {
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

@section('scripts')
<script>
document.getElementById('recurrencia').addEventListener('change', function() {
    const diaRecurrenciaGroup = document.getElementById('dia-recurrencia-group');
    if (this.value === 'mensual') {
        diaRecurrenciaGroup.style.display = 'block';
    } else {
        diaRecurrenciaGroup.style.display = 'none';
    }
});

// Mostrar el campo si ya está seleccionado mensual (en caso de errores de validación)
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('recurrencia').value === 'mensual') {
        document.getElementById('dia-recurrencia-group').style.display = 'block';
    }
});
</script>
@endsection
