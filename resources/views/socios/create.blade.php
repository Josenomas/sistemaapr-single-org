@extends('layouts.app')

@section('title', 'Nuevo Socio - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-user-plus"></i>
        Registrar Nuevo Socio
    </h2>
    <div class="header-actions">
        <button id="startTourBtn" class="btn btn-info" title="Iniciar tutorial">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
        <a href="{{ route('socios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Socio</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('socios.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <!-- RUT -->
                <div class="form-group col-md-4" data-intro="Ingresa el RUT del socio con puntos y guión. Ejemplo: 12.345.678-9. Este campo es obligatorio y debe ser único." data-step="1">
                    <label for="rut" class="form-label required">RUT</label>
                    <input type="text"
                           class="form-control @error('rut') is-invalid @enderror"
                           id="rut"
                           name="rut"
                           value="{{ old('rut') }}"
                           placeholder="12.345.678-9"
                           required>
                    @error('rut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nombre -->
                <div class="form-group col-md-4" data-intro="Nombre del socio. Campo obligatorio." data-step="2">
                    <label for="nombre" class="form-label required">Nombre</label>
                    <input type="text"
                           class="form-control @error('nombre') is-invalid @enderror"
                           id="nombre"
                           name="nombre"
                           value="{{ old('nombre') }}"
                           required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Apellido Paterno -->
                <div class="form-group col-md-4">
                    <label for="apellido_paterno" class="form-label required">Apellido Paterno</label>
                    <input type="text"
                           class="form-control @error('apellido_paterno') is-invalid @enderror"
                           id="apellido_paterno"
                           name="apellido_paterno"
                           value="{{ old('apellido_paterno') }}"
                           required>
                    @error('apellido_paterno')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Apellido Materno -->
                <div class="form-group col-md-4">
                    <label for="apellido_materno" class="form-label">Apellido Materno</label>
                    <input type="text"
                           class="form-control @error('apellido_materno') is-invalid @enderror"
                           id="apellido_materno"
                           name="apellido_materno"
                           value="{{ old('apellido_materno') }}">
                    @error('apellido_materno')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Dirección -->
                <div class="form-group col-md-8">
                    <label for="direccion" class="form-label required">Dirección</label>
                    <input type="text"
                           class="form-control @error('direccion') is-invalid @enderror"
                           id="direccion"
                           name="direccion"
                           value="{{ old('direccion') }}"
                           required>
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Sector -->
                <div class="form-group col-md-4">
                    <label for="sector" class="form-label">Sector</label>
                    <input type="text"
                           class="form-control @error('sector') is-invalid @enderror"
                           id="sector"
                           name="sector"
                           value="{{ old('sector') }}"
                           placeholder="Ej: Centro, Norte, Sur">
                    @error('sector')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div class="form-group col-md-4">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="tel"
                           class="form-control @error('telefono') is-invalid @enderror"
                           id="telefono"
                           name="telefono"
                           value="{{ old('telefono') }}"
                           placeholder="+56912345678">
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="correo@ejemplo.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Tipo de Cliente -->
                <div class="form-group col-md-4" data-intro="Selecciona el tipo de cliente. Esto determina la tarifa que se aplicará en las boletas: Residencial, Comercial o Industrial." data-step="3">
                    <label for="tipo_cliente" class="form-label required">Tipo de Cliente</label>
                    <select class="form-control @error('tipo_cliente') is-invalid @enderror"
                            id="tipo_cliente"
                            name="tipo_cliente"
                            required>
                        <option value="">Seleccione...</option>
                        <option value="residencial" {{ old('tipo_cliente') == 'residencial' ? 'selected' : '' }}>Residencial</option>
                        <option value="comercial" {{ old('tipo_cliente') == 'comercial' ? 'selected' : '' }}>Comercial</option>
                        <option value="industrial" {{ old('tipo_cliente') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                    </select>
                    @error('tipo_cliente')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Número de Medidor -->
                <div class="form-group col-md-4">
                    <label for="numero_medidor" class="form-label">Número de Medidor</label>
                    <input type="text"
                           class="form-control @error('numero_medidor') is-invalid @enderror"
                           id="numero_medidor"
                           name="numero_medidor"
                           value="{{ old('numero_medidor') }}"
                           placeholder="MED-001">
                    @error('numero_medidor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fecha de Ingreso -->
                <div class="form-group col-md-4">
                    <label for="fecha_ingreso" class="form-label required">Fecha de Ingreso</label>
                    <input type="date"
                           class="form-control @error('fecha_ingreso') is-invalid @enderror"
                           id="fecha_ingreso"
                           name="fecha_ingreso"
                           value="{{ old('fecha_ingreso', date('Y-m-d')) }}"
                           required>
                    @error('fecha_ingreso')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                          id="observaciones"
                          name="observaciones"
                          rows="3"
                          placeholder="Notas adicionales sobre el socio...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions" data-intro="Una vez completado el formulario, haz click en 'Guardar Socio' para registrar al nuevo socio. El sistema asignará automáticamente un número de socio único." data-step="4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Socio
                </button>
                <a href="{{ route('socios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el tour
        const intro = introJs();
        intro.setOptions({
            nextLabel: 'Siguiente',
            prevLabel: 'Anterior',
            doneLabel: 'Finalizar',
            skipLabel: 'Salir',
            showProgress: true,
            showBullets: false,
            exitOnOverlayClick: false,
            disableInteraction: true,
            tooltipClass: 'custom-tooltip'
        });

        // Botón para iniciar el tour
        document.getElementById('startTourBtn').addEventListener('click', function() {
            intro.start();
        });

        // Mostrar tour automáticamente solo la primera vez
        const tourShown = localStorage.getItem('sociosCreateTourShown');
        if (!tourShown) {
            setTimeout(function() {
                intro.start();
                localStorage.setItem('sociosCreateTourShown', 'true');
            }, 500);
        }
    });
</script>
@endsection

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
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

    .col-md-8 {
        grid-column: span 2;
    }

    .btn-info {
        background: #06b6d4;
        color: white;
    }

    /* Estilos personalizados para Intro.js */
    .custom-tooltip {
        max-width: 400px;
    }

    .introjs-tooltip {
        border-radius: 12px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
    }

    .introjs-button {
        border-radius: 6px !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        text-shadow: none !important;
    }

    .introjs-nextbutton {
        background: var(--primary) !important;
        border: none !important;
    }

    .introjs-prevbutton {
        background: var(--gray-500) !important;
        border: none !important;
    }

    .introjs-skipbutton {
        color: var(--gray-600) !important;
    }

    .introjs-donebutton {
        background: var(--success) !important;
        border: none !important;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-4,
        .col-md-8 {
            grid-column: span 1;
        }

        .header-actions {
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .header-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
    }
</style>
@endsection
