@extends('layouts.app')

@section('title', 'Pagos - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-money-bill-wave"></i>
        Gestión de Pagos
    </h2>
    <div class="btn-group">
        <button id="startTourBtn" class="btn btn-info" title="Iniciar tutorial">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
        <a href="{{ route('pagos.create') }}" class="btn btn-primary" data-intro="Registra un pago manualmente. Puedes buscar la boleta por número o seleccionar el socio para ver sus boletas pendientes." data-step="1">
            <i class="fas fa-plus"></i>
            Registrar Pago
        </a>
        <a href="{{ route('pagos.reporteCaja') }}" class="btn btn-secondary" data-intro="Genera un reporte de caja con todos los pagos recibidos en un período de tiempo. Útil para cuadrar la caja diaria." data-step="2">
            <i class="fas fa-cash-register"></i>
            Reporte de Caja
        </a>
    </div>
</div>

<!-- Estadísticas -->
<div class="stats-row" data-intro="Panel de estadísticas financieras: Total de pagos, recaudación del día, del mes actual y desgloses por método de pago (Efectivo, Transferencia, Webpay)." data-step="3">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Pagos</div>
            <div class="stat-value">{{ number_format($estadisticas['total_pagos']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pagos Hoy</div>
            <div class="stat-value">{{ number_format($estadisticas['pagos_hoy']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-coins"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Recaudado</div>
            <div class="stat-value">${{ number_format($estadisticas['total_recaudado'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card highlight">
        <div class="stat-icon bg-warning">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Recaudado Hoy</div>
            <div class="stat-value">${{ number_format($estadisticas['recaudado_hoy'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-purple">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Mes Actual</div>
            <div class="stat-value">${{ number_format($estadisticas['recaudado_mes'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-green">
            <i class="fas fa-money-bill"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Efectivo Total</div>
            <div class="stat-value">${{ number_format($estadisticas['efectivo'], 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <h3 class="filter-title">
            <i class="fas fa-filter"></i>
            Filtros de Búsqueda
        </h3>
        <form method="GET" action="{{ route('pagos.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search">Buscar:</label>
                    <input type="text"
                           id="search"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Recibo, socio, comprobante...">
                </div>

                <div class="form-group">
                    <label for="fecha_desde">Desde:</label>
                    <input type="date"
                           id="fecha_desde"
                           name="fecha_desde"
                           class="form-control"
                           value="{{ request('fecha_desde') }}">
                </div>

                <div class="form-group">
                    <label for="fecha_hasta">Hasta:</label>
                    <input type="date"
                           id="fecha_hasta"
                           name="fecha_hasta"
                           class="form-control"
                           value="{{ request('fecha_hasta') }}">
                </div>

                <div class="form-group">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select id="metodo_pago" name="metodo_pago" class="form-control">
                        <option value="">Todos</option>
                        <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="transferencia" {{ request('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="cheque" {{ request('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="debito" {{ request('metodo_pago') == 'debito' ? 'selected' : '' }}>Débito</option>
                        <option value="credito" {{ request('metodo_pago') == 'credito' ? 'selected' : '' }}>Crédito</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="socio_id">Socio:</label>
                    <select id="socio_id" name="socio_id" class="form-control">
                        <option value="">Todos los socios</option>
                        @foreach($socios as $socio)
                            <option value="{{ $socio->id }}" {{ request('socio_id') == $socio->id ? 'selected' : '' }}>
                                {{ $socio->numero_socio }} - {{ $socio->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Pagos -->
<div class="card" data-intro="Listado de todos los pagos registrados. Cada pago tiene un número de recibo único generado automáticamente." data-step="4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Recibo</th>
                        <th>Fecha</th>
                        <th>Socio</th>
                        <th>Boleta</th>
                        <th>Monto</th>
                        <th data-intro="Métodos de pago disponibles: Efectivo, Transferencia bancaria o Webpay (pago online)." data-step="5">Método</th>
                        <th>Comprobante</th>
                        <th data-intro="Ver detalles del pago, Descargar recibo en PDF o Anular el pago (solo si es necesario corregir un error)." data-step="6">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $pago)
                        <tr>
                            <td><strong>{{ $pago->numero_recibo }}</strong></td>
                            <td>{{ $pago->fecha_pago_formateada }}</td>
                            <td>
                                <a href="{{ route('socios.show', $pago->socio->id) }}" class="link">
                                    {{ $pago->socio->numero_socio }} - {{ $pago->socio->nombre_completo }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('boletas.show', $pago->boleta->id) }}" class="link">
                                    {{ $pago->boleta->numero_boleta }}
                                </a>
                            </td>
                            <td><strong>{{ $pago->monto_pagado_formateado }}</strong></td>
                            <td>{!! $pago->metodo_pago_badge !!}</td>
                            <td>{{ $pago->numero_comprobante ?? '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('pagos.show', $pago->id) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pagos.edit', $pago->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pagos.imprimir', $pago->id) }}" class="btn btn-sm btn-success" title="Imprimir" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <form action="{{ route('pagos.destroy', $pago->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('¿Está seguro de eliminar este pago?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No se encontraron pagos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $pagos->appends(request()->only(['search', 'fecha_desde', 'fecha_hasta', 'metodo_pago', 'socio_id']))->links() }}
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
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-title {
        font-size: 1.875rem;
        color: var(--dark);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .page-title i {
        color: var(--primary);
    }

    .btn-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.875rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-secondary {
        background: var(--gray-100);
        color: var(--dark);
    }

    .btn-secondary:hover {
        background: var(--gray-200);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.75rem;
    }

    .btn-info {
        background: var(--info);
        color: white;
    }

    .btn-warning {
        background: var(--warning);
        color: white;
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-card.highlight {
        border-left-color: var(--warning);
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: var(--white);
        flex-shrink: 0;
    }

    .stat-icon.bg-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }
    .stat-icon.bg-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .stat-icon.bg-info {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }
    .stat-icon.bg-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    .stat-icon.bg-purple {
        background: linear-gradient(135deg, #9333ea, #7c3aed);
    }
    .stat-icon.bg-green {
        background: linear-gradient(135deg, #16a34a, #15803d);
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 6px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .card-body {
        padding: 24px;
    }

    .filter-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--gray-200);
    }

    .filter-title i {
        color: var(--primary);
        font-size: 1.25rem;
    }

    .filter-form .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--dark);
        margin-bottom: 6px;
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 12px;
        padding-top: 12px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table thead {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .table th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--gray-100);
        vertical-align: middle;
    }

    .table tbody tr {
        transition: background 0.2s;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    .text-center {
        text-align: center;
        padding: 40px 20px;
        color: var(--gray-500);
        font-style: italic;
    }

    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    .mb-3 {
        margin-bottom: 24px;
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
        const tourShown = localStorage.getItem('pagosTourShown');
        if (!tourShown) {
            setTimeout(function() {
                intro.start();
                localStorage.setItem('pagosTourShown', 'true');
            }, 500);
        }
    });
</script>
@endsection
