@extends('layouts.app')

@section('title', 'Detalle Ticket - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-ticket-alt"></i>
        Detalle del Ticket
    </h2>
    <div class="header-actions">
        <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Alertas -->
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<!-- Información Principal -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Información del Ticket</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Número de Ticket:</span>
                <span class="detail-value"><strong>{{ $ticket->numero_ticket }}</strong></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Fecha de Reporte:</span>
                <span class="detail-value">{{ $ticket->fecha_reporte_formateada }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Tipo:</span>
                <span class="detail-value">{!! $ticket->tipo_ticket_badge !!}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Prioridad:</span>
                <span class="detail-value">{!! $ticket->prioridad_badge !!}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Estado:</span>
                <span class="detail-value">{!! $ticket->estado_badge !!}</span>
            </div>

            @if($ticket->ubicacion)
            <div class="detail-item">
                <span class="detail-label">Ubicación:</span>
                <span class="detail-value">
                    <i class="fas fa-map-marker-alt"></i> {{ $ticket->ubicacion }}
                </span>
            </div>
            @endif
        </div>

        <div class="detail-section">
            <h4 class="section-title">Título</h4>
            <p class="section-content"><strong>{{ $ticket->titulo }}</strong></p>
        </div>

        <div class="detail-section">
            <h4 class="section-title">Descripción</h4>
            <p class="section-content">{{ $ticket->descripcion }}</p>
        </div>
    </div>
</div>

<!-- Socio o Contacto -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            @if($ticket->socio)
                Información del Socio
            @else
                Información de Contacto
            @endif
        </h3>
    </div>
    <div class="card-body">
        @if($ticket->socio)
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Nombre:</span>
                    <span class="detail-value">
                        <a href="{{ route('socios.show', $ticket->socio->id) }}">
                            {{ $ticket->socio->nombre_completo }}
                        </a>
                    </span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">RUT:</span>
                    <span class="detail-value">{{ $ticket->socio->rut }}</span>
                </div>

                @if($ticket->socio->telefono)
                <div class="detail-item">
                    <span class="detail-label">Teléfono:</span>
                    <span class="detail-value">{{ $ticket->socio->telefono }}</span>
                </div>
                @endif

                @if($ticket->socio->email)
                <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $ticket->socio->email }}</span>
                </div>
                @endif
            </div>
        @else
            <div class="detail-grid">
                @if($ticket->contacto_nombre)
                <div class="detail-item">
                    <span class="detail-label">Nombre:</span>
                    <span class="detail-value">{{ $ticket->contacto_nombre }}</span>
                </div>
                @endif

                @if($ticket->contacto_telefono)
                <div class="detail-item">
                    <span class="detail-label">Teléfono:</span>
                    <span class="detail-value">{{ $ticket->contacto_telefono }}</span>
                </div>
                @endif

                @if($ticket->contacto_email)
                <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $ticket->contacto_email }}</span>
                </div>
                @endif

                @if(!$ticket->contacto_nombre && !$ticket->contacto_telefono && !$ticket->contacto_email)
                <p class="text-muted">No se registró información de contacto</p>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Asignación -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Asignación</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Asignado a:</span>
                <span class="detail-value">
                    @if($ticket->asignado)
                        {{ $ticket->asignado->nombre_completo }}
                        <br><small class="text-muted">{{ $ticket->asignado->cargo }}</small>
                    @else
                        <span class="text-muted">Sin asignar</span>
                    @endif
                </span>
            </div>

            @if($ticket->fecha_asignacion)
            <div class="detail-item">
                <span class="detail-label">Fecha de Asignación:</span>
                <span class="detail-value">{{ $ticket->fecha_asignacion_formateada }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Resolución -->
@if($ticket->solucion || $ticket->fecha_resolucion || $ticket->costo_reparacion)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Resolución</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            @if($ticket->fecha_resolucion)
            <div class="detail-item">
                <span class="detail-label">Fecha de Resolución:</span>
                <span class="detail-value">{{ $ticket->fecha_resolucion_formateada }}</span>
            </div>
            @endif

            @if($ticket->fecha_cierre)
            <div class="detail-item">
                <span class="detail-label">Fecha de Cierre:</span>
                <span class="detail-value">{{ $ticket->fecha_cierre_formateada }}</span>
            </div>
            @endif

            @if($ticket->costo_reparacion)
            <div class="detail-item">
                <span class="detail-label">Costo de Reparación:</span>
                <span class="detail-value"><strong>{{ $ticket->costo_reparacion_formateado }}</strong></span>
            </div>
            @endif

            @if($ticket->tiempo_respuesta)
            <div class="detail-item">
                <span class="detail-label">Tiempo de Respuesta:</span>
                <span class="detail-value">{{ $ticket->tiempo_respuesta_formateado }}</span>
            </div>
            @endif

            @if($ticket->tiempo_resolucion)
            <div class="detail-item">
                <span class="detail-label">Tiempo de Resolución:</span>
                <span class="detail-value">{{ $ticket->tiempo_resolucion_formateado }}</span>
            </div>
            @endif
        </div>

        @if($ticket->solucion)
        <div class="detail-section">
            <h4 class="section-title">Solución</h4>
            <p class="section-content">{{ $ticket->solucion }}</p>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Satisfacción y Cierre -->
@if($ticket->satisfaccion || $ticket->comentario_cierre)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Satisfacción y Cierre</h3>
    </div>
    <div class="card-body">
        @if($ticket->satisfaccion)
        <div class="detail-item">
            <span class="detail-label">Nivel de Satisfacción:</span>
            <span class="detail-value">{!! $ticket->satisfaccion_badge !!}</span>
        </div>
        @endif

        @if($ticket->comentario_cierre)
        <div class="detail-section">
            <h4 class="section-title">Comentario de Cierre</h4>
            <p class="section-content">{{ $ticket->comentario_cierre }}</p>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Observaciones -->
@if($ticket->observaciones)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Observaciones</h3>
    </div>
    <div class="card-body">
        <p class="section-content">{{ $ticket->observaciones }}</p>
    </div>
</div>
@endif

<!-- Respuestas y Comentarios -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Respuestas y Seguimiento</h3>
    </div>
    <div class="card-body">
        <!-- Lista de Respuestas -->
        @forelse($respuestas as $respuesta)
            <div class="response-item">
                <div class="response-header-info">
                    <div class="response-author-info">
                        <div class="avatar-circle">
                            {{ substr($respuesta->autor_nombre, 0, 1) }}
                        </div>
                        <div>
                            <div class="response-author-name">{{ $respuesta->autor_nombre }}</div>
                            <div class="response-meta">
                                <span class="response-role {{ $respuesta->tipo_autor }}">{{ ucfirst($respuesta->tipo_autor) }}</span>
                                <span class="response-date">{{ $respuesta->fecha_creacion_formateada }}</span>
                            </div>
                        </div>
                    </div>
                    @if(!$respuesta->visible_socio)
                        <span class="badge badge-warning" title="No visible para el socio">
                            <i class="fas fa-eye-slash"></i> Privado
                        </span>
                    @endif
                </div>
                <div class="response-message">{{ $respuesta->mensaje }}</div>
            </div>
        @empty
            <p class="text-muted" style="text-align: center; padding: 20px;">
                <i class="fas fa-comments"></i><br>
                Aún no hay respuestas en este ticket.
            </p>
        @endforelse

        <!-- Formulario para Agregar Respuesta -->
        <div class="response-form">
            <h4 class="form-title">Agregar Respuesta</h4>
            <form action="{{ route('tickets.agregar-respuesta', $ticket->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="mensaje">Mensaje:</label>
                    <textarea id="mensaje"
                              name="mensaje"
                              class="form-control-textarea"
                              rows="4"
                              placeholder="Escribe tu respuesta aquí..."
                              required></textarea>
                </div>

                <div class="form-group-checkbox">
                    <input type="checkbox"
                           id="visible_socio"
                           name="visible_socio"
                           value="1"
                           checked>
                    <label for="visible_socio">Visible para el socio (se enviará notificación por email)</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Enviar Respuesta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Información de Registro -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de Registro</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Fecha de Creación:</span>
                <span class="detail-value">{{ $ticket->fecha_creacion_formateada }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Última Actualización:</span>
                <span class="detail-value">{{ $ticket->fecha_actualizacion_formateada }}</span>
            </div>
        </div>
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

    .header-actions {
        display: flex;
        gap: 12px;
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

    .mb-4 {
        margin-bottom: 24px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .detail-value {
        font-size: 1rem;
        color: var(--dark);
    }

    .detail-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 12px;
    }

    .section-content {
        font-size: 0.95rem;
        color: var(--gray-700);
        line-height: 1.6;
        margin: 0;
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

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
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

    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-block;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-info {
        background: #cffafe;
        color: #155e75;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .badge-dark {
        background: var(--gray-700);
        color: var(--white);
    }

    .text-muted {
        color: var(--gray-500);
    }

    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    /* Estilos para Respuestas */
    .response-item {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 16px;
    }

    .response-header-info {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e2e8f0;
    }

    .response-author-info {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .avatar-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981, #059669);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 18px;
        flex-shrink: 0;
    }

    .response-author-name {
        font-weight: 600;
        color: var(--dark);
        font-size: 15px;
    }

    .response-meta {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-top: 4px;
    }

    .response-role {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 2px 8px;
        border-radius: 10px;
    }

    .response-role.funcionario {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .response-role.socio {
        background-color: #fef3c7;
        color: #92400e;
    }

    .response-role.sistema {
        background-color: #e5e7eb;
        color: #374151;
    }

    .response-date {
        font-size: 13px;
        color: var(--gray-500);
    }

    .response-message {
        color: #334155;
        font-size: 15px;
        line-height: 1.7;
        white-space: pre-wrap;
    }

    .response-form {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px solid #e2e8f0;
    }

    .form-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-control-textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
        transition: all 0.2s;
    }

    .form-control-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-group-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }

    .form-group-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .form-group-checkbox label {
        cursor: pointer;
        margin: 0;
        font-weight: 500;
        color: var(--gray-700);
        font-size: 14px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .response-header-info {
            flex-direction: column;
            gap: 12px;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
    }
</style>
@endsection
