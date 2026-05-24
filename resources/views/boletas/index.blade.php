@extends('layouts.app')

@section('title', 'Boletas - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Gestión de Boletas
    </h2>
    <div class="header-actions">
        <button id="startTourBtn" class="btn btn-info" title="Iniciar tutorial">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
        <a href="{{ route('boletas.generar') }}" class="btn btn-success" data-intro="Genera boletas masivamente para todos los socios activos del mes seleccionado. El sistema calcula automáticamente los montos según las lecturas y tarifas configuradas." data-step="1">
            <i class="fas fa-plus-circle"></i>
            Generar Boletas
        </a>
        <a href="{{ route('boletas.create') }}" class="btn btn-primary" data-intro="Crea una boleta manual individual. Útil para casos especiales o ajustes específicos." data-step="2">
            <i class="fas fa-plus"></i>
            Nueva Boleta
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

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Estadísticas -->
<div class="stats-grid" data-intro="Panel de estadísticas que muestra un resumen del estado de todas las boletas: Total, Pendientes, Vencidas, Pagadas y el monto total." data-step="3">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['total_boletas'] }}</div>
            <div class="stat-label">Total Boletas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['pendientes'] }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['vencidas'] }}</div>
            <div class="stat-label">Vencidas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['pagadas'] }}</div>
            <div class="stat-label">Pagadas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">${{ number_format($estadisticas['total_pendiente'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendiente</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-secondary">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">${{ number_format($estadisticas['total_mes_actual'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Mes Actual</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('boletas.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Buscar por número o socio..."
                           value="{{ request('search') }}">
                </div>

                <div class="form-group">
                    <input type="month"
                           name="mes"
                           class="form-control"
                           value="{{ request('mes') }}">
                </div>

                <div class="form-group">
                    <select name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagada" {{ request('estado') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                        <option value="vencida" {{ request('estado') == 'vencida' ? 'selected' : '' }}>Vencida</option>
                        <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anulada</option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="id_socio" class="form-control">
                        <option value="">Todos los socios</option>
                        @foreach($socios as $socio)
                            <option value="{{ $socio->id }}" {{ request('id_socio') == $socio->id ? 'selected' : '' }}>
                                {{ $socio->numero_socio }} - {{ $socio->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>

                @if(request()->hasAny(['search', 'mes', 'estado', 'id_socio']))
                    <a href="{{ route('boletas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Boletas -->
<div class="card" data-intro="Listado de todas las boletas generadas. Puedes filtrar por mes, socio o estado para encontrar boletas específicas." data-step="4">
    <div class="card-body">
        <!-- Barra de Acciones Masivas -->
        <div id="bulk-actions-bar" style="display: none; background: linear-gradient(135deg, #7c3aed, #5b21b6); color: white; padding: 16px; border-radius: 8px; margin-bottom: 16px; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <strong style="font-size: 16px;">
                        <i class="fas fa-check-circle"></i>
                        <span id="selected-count">0</span> boletas seleccionadas
                    </strong>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" id="bulk-emit-btn" class="btn" style="background: white; color: #7c3aed; border: none; font-weight: 600;">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Emitir DTEs Masivamente
                    </button>
                    <button type="button" id="deselect-all-btn" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid white;">
                        <i class="fas fa-times"></i>
                        Deseleccionar Todo
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all-checkbox" style="width: 18px; height: 18px; cursor: pointer;">
                        </th>
                        <th>Número</th>
                        <th>Socio</th>
                        <th>Mes</th>
                        <th>Emisión</th>
                        <th>Vencimiento</th>
                        <th>Consumo</th>
                        <th>Total</th>
                        <th data-intro="Estados posibles: Pendiente (sin pagar), Pagada, Vencida (pasó la fecha de vencimiento sin pago)." data-step="5">Estado</th>
                        <th>DTE</th>
                        <th data-intro="Ver detalles, Descargar PDF, Enviar por email, Registrar pago o Anular boleta según corresponda." data-step="6">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boletas as $boleta)
                        <tr class="{{ $boleta->estado == 'vencida' ? 'row-danger' : '' }}" data-boleta-id="{{ $boleta->id }}">
                            <td>
                                @if(!$boleta->tieneDTE())
                                    <input type="checkbox" class="boleta-checkbox" value="{{ $boleta->id }}" style="width: 18px; height: 18px; cursor: pointer;">
                                @else
                                    <i class="fas fa-check text-success" title="Ya tiene DTE emitido"></i>
                                @endif
                            </td>
                            <td><strong>{{ $boleta->numero_boleta }}</strong></td>
                            <td>
                                <a href="{{ route('socios.show', $boleta->socio->id) }}">
                                    {{ $boleta->socio->numero_socio }} - {{ $boleta->socio->nombre_completo }}
                                </a>
                            </td>
                            <td>{{ $boleta->mes_texto }}</td>
                            <td>{{ $boleta->fecha_emision_formateada }}</td>
                            <td>
                                {{ $boleta->fecha_vencimiento_formateada }}
                                @if($boleta->dias_atraso > 0)
                                    <br><small class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $boleta->dias_atraso }} días
                                    </small>
                                @endif
                            </td>
                            <td>{{ $boleta->consumo_m3 }} m³</td>
                            <td><strong>{{ $boleta->total_formateado }}</strong></td>
                            <td>{!! $boleta->estado_badge !!}</td>
                            <td>
                                @if($boleta->tieneDTE())
                                    {!! $boleta->tipo_documento_badge !!}
                                    <br>{!! $boleta->estado_dte_badge !!}
                                    @if($boleta->folio_sii)
                                        <br><small class="text-muted">Folio: {{ $boleta->folio_sii }}</small>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">Sin DTE</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('boletas.show', $boleta->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($boleta->estado !== 'pagada')
                                    <a href="{{ route('boletas.edit', $boleta->id) }}"
                                       class="btn btn-sm btn-warning"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    <a href="{{ url('/pdf-boleta/' . $boleta->id) }}"
                                       class="btn btn-sm btn-secondary"
                                       title="Imprimir"
                                       target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <!-- Botones DTE -->
                                    @if(!$boleta->dteEmitido())
                                        <form action="{{ route('dte.boleta.emitir', $boleta->id) }}"
                                              method="POST"
                                              style="display: inline;"
                                              onsubmit="return confirm('¿Emitir boleta electrónica para este documento?');">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-success"
                                                    title="Emitir Boleta Electrónica">
                                                <i class="fas fa-file-invoice"></i>
                                            </button>
                                        </form>
                                    @else
                                        @if($boleta->pdf_url)
                                            <a href="{{ route('dte.boleta.pdf', $boleta->id) }}"
                                               class="btn btn-sm btn-primary"
                                               title="Descargar PDF Timbrado"
                                               target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @endif

                                        @if($boleta->estado_dte !== 'anulada' && in_array($boleta->estado_dte, ['emitida', 'aceptada']))
                                            <!-- Dropdown de acciones de DTE -->
                                            <div class="dropdown" style="display: inline-block;">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                        type="button"
                                                        id="dropdownDTE{{ $boleta->id }}"
                                                        data-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false"
                                                        title="Opciones DTE">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownDTE{{ $boleta->id }}">
                                                    @if($boleta->socio && $boleta->socio->email)
                                                        <form action="{{ route('dte.reenviar-email', $boleta->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item" onclick="return confirm('¿Reenviar DTE por email a {{ $boleta->socio->email }}?')">
                                                                <i class="fas fa-envelope text-primary"></i>
                                                                Reenviar Email
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                    @endif
                                                    <a class="dropdown-item" href="{{ route('dte.crear-nota-credito', $boleta->id) }}">
                                                        <i class="fas fa-minus-circle text-danger"></i>
                                                        Nota de Crédito
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('dte.crear-nota-debito', $boleta->id) }}">
                                                        <i class="fas fa-plus-circle text-warning"></i>
                                                        Nota de Débito
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    @if($boleta->estado !== 'pagada' && $boleta->pagos->count() == 0)
                                    <form action="{{ route('boletas.destroy', $boleta->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('¿Está seguro de eliminar esta boleta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">
                                <i class="fas fa-inbox"></i>
                                <p>No se encontraron boletas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($boletas->hasPages())
            <div class="pagination-wrapper">
                {{ $boletas->appends(request()->only(['search', 'mes', 'estado', 'id_socio']))->links() }}
            </div>
        @endif
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

    .mb-3 {
        margin-bottom: 20px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        border: 1px solid var(--gray-200);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    .stat-icon.bg-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
    .stat-icon.bg-success { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-icon.bg-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-icon.bg-info { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .stat-icon.bg-secondary { background: linear-gradient(135deg, #6b7280, #4b5563); }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-body {
        padding: 24px;
    }

    .filter-form .form-row {
        display: grid;
        grid-template-columns: 2fr repeat(3, 1fr) auto auto;
        gap: 12px;
        align-items: center;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: var(--gray-50);
    }

    .table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        font-size: 0.875rem;
        border-bottom: 2px solid var(--gray-200);
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.95rem;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .row-danger {
        background-color: #fee2e2 !important;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn {
        padding: 10px 20px;
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

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.875rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
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

    .btn-info {
        background: #3b82f6;
        color: white;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-block;
        white-space: nowrap;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
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

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--gray-500);
    }

    .text-danger {
        color: #991b1b;
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
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

        .filter-form .form-row {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
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
</style>
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
        const tourShown = localStorage.getItem('boletasTourShown');
        if (!tourShown) {
            setTimeout(function() {
                intro.start();
                localStorage.setItem('boletasTourShown', 'true');
            }, 500);
        }
    });

    // Modal de anulación DTE
    function mostrarModalAnular(boletaId, folio, monto) {
        Swal.fire({
            title: '⚠️ Anular Documento Tributario',
            html: `
                <div style="text-align: left; padding: 1rem;">
                    <p style="margin-bottom: 1rem;">
                        <strong>Folio SII:</strong> ${folio}<br>
                        <strong>Monto:</strong> $${new Intl.NumberFormat('es-CL').format(monto)}
                    </p>
                    <div style="background: #fff3cd; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <strong style="color: #856404;">ℹ️ Importante:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: #856404; font-size: 0.9rem;">
                            Se emitirá una <strong>Nota de Crédito Electrónica</strong> que anulará este documento ante el SII.
                            Esta acción es <strong>irreversible</strong>.
                        </p>
                    </div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Motivo de la anulación: <span style="color: #dc3545;">*</span>
                    </label>
                    <textarea id="motivoAnulacion"
                              class="swal2-textarea"
                              placeholder="Ej: Error en monto, Servicio no prestado, Anulación a solicitud del cliente..."
                              style="width: 100%; min-height: 100px; padding: 0.75rem; border: 2px solid #dee2e6; border-radius: 0.5rem;"
                              maxlength="500"></textarea>
                    <small style="color: #6c757d;">Máximo 500 caracteres</small>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-file-invoice"></i> Sí, emitir Nota de Crédito',
            cancelButtonText: 'Cancelar',
            width: '600px',
            preConfirm: () => {
                const motivo = document.getElementById('motivoAnulacion').value.trim();
                if (!motivo) {
                    Swal.showValidationMessage('Debe ingresar un motivo de anulación');
                    return false;
                }
                if (motivo.length < 10) {
                    Swal.showValidationMessage('El motivo debe tener al menos 10 caracteres');
                    return false;
                }
                return motivo;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario y enviarlo
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/dte/boleta/${boletaId}/anular`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const motivoInput = document.createElement('input');
                motivoInput.type = 'hidden';
                motivoInput.name = 'motivo';
                motivoInput.value = result.value;
                form.appendChild(motivoInput);

                document.body.appendChild(form);

                // Mostrar loader
                Swal.fire({
                    title: 'Emitiendo Nota de Crédito...',
                    html: 'Por favor espere mientras se procesa la anulación ante el SII',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                form.submit();
            }
        });
    }

    // ========================================
    // SELECCIÓN MASIVA DE BOLETAS
    // ========================================

    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const boletaCheckboxes = document.querySelectorAll('.boleta-checkbox');
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkEmitBtn = document.getElementById('bulk-emit-btn');
    const deselectAllBtn = document.getElementById('deselect-all-btn');

    function updateBulkActionsBar() {
        const selectedCheckboxes = document.querySelectorAll('.boleta-checkbox:checked');
        const count = selectedCheckboxes.length;

        selectedCountSpan.textContent = count;

        if (count > 0) {
            bulkActionsBar.style.display = 'block';
        } else {
            bulkActionsBar.style.display = 'none';
        }
    }

    // Seleccionar/deseleccionar todas
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            boletaCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsBar();
        });
    }

    // Actualizar contador al seleccionar individual
    boletaCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionsBar();

            // Actualizar estado del checkbox "seleccionar todo"
            const allChecked = Array.from(boletaCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(boletaCheckboxes).some(cb => cb.checked);

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // Deseleccionar todo
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            boletaCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            updateBulkActionsBar();
        });
    }

    // Emisión masiva de DTEs
    if (bulkEmitBtn) {
        bulkEmitBtn.addEventListener('click', async function() {
            const selectedCheckboxes = document.querySelectorAll('.boleta-checkbox:checked');
            const boletaIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (boletaIds.length === 0) {
                alert('No hay boletas seleccionadas');
                return;
            }

            if (!confirm(`¿Está seguro de emitir ${boletaIds.length} DTEs?\n\nEste proceso puede tardar varios minutos.`)) {
                return;
            }

            // Deshabilitar botón y mostrar loading
            bulkEmitBtn.disabled = true;
            bulkEmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

            try {
                const response = await fetch('{{ route("dte.emitir-masivo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        boleta_ids: boletaIds
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(`✅ Proceso iniciado exitosamente\n\n` +
                          `Total: ${data.total}\n` +
                          `Éxitos: ${data.exitosos}\n` +
                          `Errores: ${data.errores}\n\n` +
                          `${data.message || 'Los DTEs se están emitiendo. Recibirá notificaciones por email.'}`);

                    // Recargar página para ver los cambios
                    window.location.reload();
                } else {
                    alert('❌ Error: ' + (data.message || 'No se pudo iniciar la emisión masiva'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error al procesar la solicitud: ' + error.message);
            } finally {
                bulkEmitBtn.disabled = false;
                bulkEmitBtn.innerHTML = '<i class="fas fa-file-invoice-dollar"></i> Emitir DTEs Masivamente';
            }
        });
    }
</script>
@endsection
