@extends('layouts.app')

@section('title', 'Nuevo Ticket - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-ticket-alt"></i>
        Crear Nuevo Ticket
    </h2>
    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Ticket</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf

            <!-- Información Básica -->
            <div class="form-section">
                <h4 class="section-title">Información Básica</h4>

                <div class="form-row">
                    <!-- Número de Ticket (Auto-generado) -->
                    <div class="form-group col-md-4">
                        <label for="numero_ticket" class="form-label">Número de Ticket</label>
                        <input type="text"
                               class="form-control"
                               value="{{ 'TKT-' . str_pad((App\Models\Ticket::count() + 1), 6, '0', STR_PAD_LEFT) }}"
                               disabled>
                        <small class="text-muted">Se generará automáticamente</small>
                    </div>

                    <!-- Fecha de Reporte -->
                    <div class="form-group col-md-4">
                        <label for="fecha_reporte" class="form-label required">Fecha de Reporte</label>
                        <input type="date"
                               class="form-control @error('fecha_reporte') is-invalid @enderror"
                               id="fecha_reporte"
                               name="fecha_reporte"
                               value="{{ old('fecha_reporte', date('Y-m-d')) }}"
                               required>
                        @error('fecha_reporte')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Socio -->
                    <div class="form-group col-md-4">
                        <label for="id_socio" class="form-label">Socio</label>
                        <select class="form-control @error('id_socio') is-invalid @enderror"
                                id="id_socio"
                                name="id_socio">
                            <option value="">Seleccione un socio...</option>
                            @foreach($socios as $socio)
                                <option value="{{ $socio->id }}" {{ old('id_socio') == $socio->id ? 'selected' : '' }}>
                                    {{ $socio->nombre_completo }} - {{ $socio->rut }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_socio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Opcional si no está registrado como socio</small>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Título -->
                    <div class="form-group col-md-12">
                        <label for="titulo" class="form-label required">Título</label>
                        <input type="text"
                               class="form-control @error('titulo') is-invalid @enderror"
                               id="titulo"
                               name="titulo"
                               value="{{ old('titulo') }}"
                               placeholder="Breve descripción del problema o solicitud"
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
                              placeholder="Describa detalladamente el problema, consulta o solicitud..."
                              required>{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Clasificación -->
            <div class="form-section">
                <h4 class="section-title">Clasificación</h4>

                <div class="form-row">
                    <!-- Tipo de Ticket -->
                    <div class="form-group col-md-4">
                        <label for="tipo_ticket" class="form-label required">Tipo de Ticket</label>
                        <select class="form-control @error('tipo_ticket') is-invalid @enderror"
                                id="tipo_ticket"
                                name="tipo_ticket"
                                required>
                            <option value="">Seleccione...</option>
                            <option value="consulta" {{ old('tipo_ticket') == 'consulta' ? 'selected' : '' }}>Consulta</option>
                            <option value="reclamo" {{ old('tipo_ticket') == 'reclamo' ? 'selected' : '' }}>Reclamo</option>
                            <option value="solicitud" {{ old('tipo_ticket') == 'solicitud' ? 'selected' : '' }}>Solicitud</option>
                            <option value="averia" {{ old('tipo_ticket') == 'averia' ? 'selected' : '' }}>Avería</option>
                            <option value="fuga" {{ old('tipo_ticket') == 'fuga' ? 'selected' : '' }}>Fuga</option>
                            <option value="corte" {{ old('tipo_ticket') == 'corte' ? 'selected' : '' }}>Corte</option>
                            <option value="reconexion" {{ old('tipo_ticket') == 'reconexion' ? 'selected' : '' }}>Reconexión</option>
                            <option value="lectura" {{ old('tipo_ticket') == 'lectura' ? 'selected' : '' }}>Lectura</option>
                            <option value="otro" {{ old('tipo_ticket') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('tipo_ticket')
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
                            <option value="">Seleccione...</option>
                            <option value="baja" {{ old('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ old('prioridad', 'media') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="urgente" {{ old('prioridad') == 'urgente' ? 'selected' : '' }}>Urgente</option>
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
                            <option value="abierto" {{ old('estado', 'abierto') == 'abierto' ? 'selected' : '' }}>Abierto</option>
                            <option value="en_proceso" {{ old('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="resuelto" {{ old('estado') == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                            <option value="cerrado" {{ old('estado') == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                            <option value="cancelado" {{ old('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Asignación -->
            <div class="form-section">
                <h4 class="section-title">Asignación</h4>

                <div class="form-row">
                    <!-- Asignado a -->
                    <div class="form-group col-md-6">
                        <label for="id_asignado" class="form-label">Asignado a</label>
                        <select class="form-control @error('id_asignado') is-invalid @enderror"
                                id="id_asignado"
                                name="id_asignado">
                            <option value="">Sin asignar</option>
                            @foreach($funcionarios as $funcionario)
                                <option value="{{ $funcionario->id }}" {{ old('id_asignado') == $funcionario->id ? 'selected' : '' }}>
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
                               value="{{ old('ubicacion') }}"
                               placeholder="Dirección o ubicación del problema">
                        @error('ubicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="form-section">
                <h4 class="section-title">Información de Contacto</h4>
                <p class="section-description">Complete esta información si el ticket no está asociado a un socio registrado</p>

                <div class="form-row">
                    <!-- Nombre de Contacto -->
                    <div class="form-group col-md-4">
                        <label for="contacto_nombre" class="form-label">Nombre de Contacto</label>
                        <input type="text"
                               class="form-control @error('contacto_nombre') is-invalid @enderror"
                               id="contacto_nombre"
                               name="contacto_nombre"
                               value="{{ old('contacto_nombre') }}"
                               placeholder="Nombre completo">
                        @error('contacto_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Teléfono de Contacto -->
                    <div class="form-group col-md-4">
                        <label for="contacto_telefono" class="form-label">Teléfono de Contacto</label>
                        <input type="tel"
                               class="form-control @error('contacto_telefono') is-invalid @enderror"
                               id="contacto_telefono"
                               name="contacto_telefono"
                               value="{{ old('contacto_telefono') }}"
                               placeholder="+56 9 1234 5678">
                        @error('contacto_telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email de Contacto -->
                    <div class="form-group col-md-4">
                        <label for="contacto_email" class="form-label">Email de Contacto</label>
                        <input type="email"
                               class="form-control @error('contacto_email') is-invalid @enderror"
                               id="contacto_email"
                               name="contacto_email"
                               value="{{ old('contacto_email') }}"
                               placeholder="contacto@ejemplo.com">
                        @error('contacto_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                          id="observaciones"
                          name="observaciones"
                          rows="3"
                          placeholder="Notas adicionales sobre el ticket...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Ticket
                </button>
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
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
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-description {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 16px;
        font-style: italic;
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

    .form-control:disabled {
        background: var(--gray-100);
        cursor: not-allowed;
    }

    .invalid-feedback {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 4px;
    }

    .text-muted {
        color: var(--gray-500);
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
