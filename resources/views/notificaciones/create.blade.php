@extends('layouts.app')

@section('title', 'Crear Notificación - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Crear Nueva Notificación
    </h2>
    <a href="{{ route('notificaciones.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-bell"></i>
            Información de la Notificación
        </h3>
    </div>
    <div class="card-body">
        <form action="{{ route('notificaciones.store') }}" method="POST" id="formNotificacion">
            @csrf

            <div class="form-row">
                <!-- Título -->
                <div class="form-group col-md-6">
                    <label for="titulo" class="form-label required">Título</label>
                    <input type="text"
                           class="form-control @error('titulo') is-invalid @enderror"
                           id="titulo"
                           name="titulo"
                           value="{{ old('titulo') }}"
                           maxlength="200"
                           required>
                    @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tipo -->
                <div class="form-group col-md-6">
                    <label for="tipo" class="form-label required">Tipo</label>
                    <select class="form-control @error('tipo') is-invalid @enderror"
                            id="tipo"
                            name="tipo"
                            required>
                        <option value="">Seleccione</option>
                        <option value="informativa" {{ old('tipo') == 'informativa' ? 'selected' : '' }}>Informativa</option>
                        <option value="recordatorio" {{ old('tipo') == 'recordatorio' ? 'selected' : '' }}>Recordatorio</option>
                        <option value="urgente" {{ old('tipo') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                        <option value="corte" {{ old('tipo') == 'corte' ? 'selected' : '' }}>Corte de Servicio</option>
                        <option value="reunion" {{ old('tipo') == 'reunion' ? 'selected' : '' }}>Reunión</option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Mensaje -->
                <div class="form-group col-md-12">
                    <label for="mensaje" class="form-label required">Mensaje</label>
                    <textarea class="form-control @error('mensaje') is-invalid @enderror"
                              id="mensaje"
                              name="mensaje"
                              rows="5"
                              required>{{ old('mensaje') }}</textarea>
                    @error('mensaje')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Destinatario -->
                <div class="form-group col-md-6">
                    <label for="destinatario" class="form-label required">Destinatario</label>
                    <select class="form-control @error('destinatario') is-invalid @enderror"
                            id="destinatario"
                            name="destinatario"
                            required>
                        <option value="">Seleccione</option>
                        <option value="todos" {{ old('destinatario') == 'todos' ? 'selected' : '' }}>Todos los Socios</option>
                        <option value="socio" {{ old('destinatario') == 'socio' ? 'selected' : '' }}>Socio Específico</option>
                        <option value="sector" {{ old('destinatario') == 'sector' ? 'selected' : '' }}>Por Sector</option>
                    </select>
                    @error('destinatario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Estado -->
                <div class="form-group col-md-6">
                    <label for="estado" class="form-label required">Estado</label>
                    <select class="form-control @error('estado') is-invalid @enderror"
                            id="estado"
                            name="estado"
                            required>
                        <option value="">Seleccione</option>
                        <option value="borrador" {{ old('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="programada" {{ old('estado') == 'programada' ? 'selected' : '' }}>Programada</option>
                        <option value="enviada" {{ old('estado') == 'enviada' ? 'selected' : '' }}>Enviar Ahora</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Socio Específico (Hidden by default) -->
                <div class="form-group col-md-6" id="socio-field" style="display: none;">
                    <label for="id_socio" class="form-label">Socio</label>
                    <select class="form-control @error('id_socio') is-invalid @enderror"
                            id="id_socio"
                            name="id_socio">
                        <option value="">Seleccione un socio</option>
                        @foreach($socios as $socio)
                            <option value="{{ $socio->id }}" {{ old('id_socio') == $socio->id ? 'selected' : '' }}>
                                {{ $socio->numero_socio }} - {{ $socio->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_socio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sector (Hidden by default) -->
                <div class="form-group col-md-6" id="sector-field" style="display: none;">
                    <label for="sector" class="form-label">Sector</label>
                    <input type="text"
                           class="form-control @error('sector') is-invalid @enderror"
                           id="sector"
                           name="sector"
                           value="{{ old('sector') }}"
                           maxlength="100"
                           placeholder="Ej: Centro, Norte, Sur">
                    @error('sector')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fecha Programada -->
                <div class="form-group col-md-6">
                    <label for="fecha_programada" class="form-label">Fecha Programada</label>
                    <input type="datetime-local"
                           class="form-control @error('fecha_programada') is-invalid @enderror"
                           id="fecha_programada"
                           name="fecha_programada"
                           value="{{ old('fecha_programada') }}">
                    @error('fecha_programada')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-help">Dejar vacío para enviar inmediatamente</small>
                </div>
            </div>

            <div class="form-row">
                <!-- Canal -->
                <div class="form-group col-md-12">
                    <label class="form-label required">Canal de Envío</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox"
                                   name="canal[]"
                                   value="email"
                                   {{ is_array(old('canal')) && in_array('email', old('canal')) ? 'checked' : '' }}>
                            <span>Email</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox"
                                   name="canal[]"
                                   value="whatsapp"
                                   {{ is_array(old('canal')) && in_array('whatsapp', old('canal')) ? 'checked' : '' }}>
                            <span>WhatsApp</span>
                        </label>
                    </div>
                    @error('canal')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Observaciones -->
                <div class="form-group col-md-12">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control @error('observaciones') is-invalid @enderror"
                              id="observaciones"
                              name="observaciones"
                              rows="3">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Notificación
                </button>
                <a href="{{ route('notificaciones.index') }}" class="btn btn-secondary">
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
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title i {
        color: var(--primary);
    }

    .card-body {
        padding: 24px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .col-md-12 { grid-column: span 12; }
    .col-md-6 { grid-column: span 6; }
    .col-md-4 { grid-column: span 4; }
    .col-md-3 { grid-column: span 3; }

    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-label.required::after {
        content: ' *';
        color: #ef4444;
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
        background: var(--white);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .invalid-feedback.d-block {
        display: block;
    }

    .form-help {
        color: var(--gray-500);
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .checkbox-group {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        padding: 10px;
        background: var(--gray-50);
        border-radius: var(--radius);
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 0.875rem;
        color: var(--dark);
    }

    .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
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
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-12,
        .col-md-6,
        .col-md-4,
        .col-md-3 {
            grid-column: span 1;
        }

        .checkbox-group {
            flex-direction: column;
            gap: 12px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const destinatarioSelect = document.getElementById('destinatario');
    const socioField = document.getElementById('socio-field');
    const sectorField = document.getElementById('sector-field');
    const idSocioSelect = document.getElementById('id_socio');
    const sectorInput = document.getElementById('sector');

    // Function to show/hide fields based on destinatario selection
    function toggleDestinatarioFields() {
        const value = destinatarioSelect.value;

        // Hide all fields first
        socioField.style.display = 'none';
        sectorField.style.display = 'none';

        // Remove required attribute
        idSocioSelect.removeAttribute('required');
        sectorInput.removeAttribute('required');

        // Show appropriate field based on selection
        if (value === 'socio') {
            socioField.style.display = 'block';
            idSocioSelect.setAttribute('required', 'required');
        } else if (value === 'sector') {
            sectorField.style.display = 'block';
            sectorInput.setAttribute('required', 'required');
        }
    }

    // Listen for changes in destinatario select
    destinatarioSelect.addEventListener('change', toggleDestinatarioFields);

    // Initialize on page load
    toggleDestinatarioFields();
});
</script>
@endsection
