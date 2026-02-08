@extends('layouts.app')

@section('title', 'Boletas Vencidas - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exclamation-triangle"></i>
        Boletas Vencidas - Gestión de Morosidad
    </h2>
    <a href="{{ route('boletas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<!-- Estadísticas Generales -->
<div class="stats-grid">
    <div class="stat-card danger">
        <div class="stat-icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Total Boletas Vencidas</span>
            <span class="stat-value">{{ $estadisticas['total_vencidas'] }}</span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Deuda Total</span>
            <span class="stat-value">${{ number_format($estadisticas['monto_total'], 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Socios Morosos</span>
            <span class="stat-value">{{ $estadisticas['socios_afectados'] }}</span>
        </div>
    </div>

    <div class="stat-card critical">
        <div class="stat-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Casos Críticos (≥4 meses)</span>
            <span class="stat-value">{{ $estadisticas['criticos'] }}</span>
        </div>
    </div>
</div>

<!-- Distribución de Riesgos -->
<div class="risk-distribution">
    <div class="risk-item critico">
        <div class="risk-icon"><i class="fas fa-ban"></i></div>
        <div class="risk-info">
            <div class="risk-count">{{ $estadisticas['criticos'] }}</div>
            <div class="risk-label">Crítico (≥4)</div>
        </div>
    </div>
    <div class="risk-item alto">
        <div class="risk-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="risk-info">
            <div class="risk-count">{{ $estadisticas['alto_riesgo'] }}</div>
            <div class="risk-label">Alto (3)</div>
        </div>
    </div>
    <div class="risk-item medio">
        <div class="risk-icon"><i class="fas fa-exclamation-circle"></i></div>
        <div class="risk-info">
            <div class="risk-count">{{ $estadisticas['medio_riesgo'] }}</div>
            <div class="risk-label">Medio (2)</div>
        </div>
    </div>
    <div class="risk-item bajo">
        <div class="risk-icon"><i class="fas fa-info-circle"></i></div>
        <div class="risk-info">
            <div class="risk-count">{{ $estadisticas['bajo_riesgo'] }}</div>
            <div class="risk-label">Bajo (1)</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card filters-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i>
            Filtrar por Socio
        </h3>
    </div>
    <div class="card-body">
        <form action="{{ route('boletas.vencidas') }}" method="GET" class="filter-form">
            <div class="form-group">
                <label for="id_socio">Seleccionar Socio:</label>
                <select name="id_socio" id="id_socio" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Todos los Socios Morosos --</option>
                    @foreach($socios as $socio)
                        <option value="{{ $socio->id }}" {{ request('id_socio') == $socio->id ? 'selected' : '' }}>
                            {{ $socio->numero_socio }} - {{ $socio->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if(request('id_socio'))
            <a href="{{ route('boletas.vencidas') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Limpiar Filtro
            </a>
            @endif
        </form>
    </div>
</div>

<!-- Tabla de Resumen por Socio con Recomendaciones -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clipboard-list"></i>
            Resumen por Socio y Acciones Recomendadas
        </h3>
    </div>
    <div class="card-body">
        @if($resumenPorSocio->isEmpty())
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No hay boletas vencidas</h3>
                <p>Todos los socios están al día con sus pagos</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table socios-table">
                    <thead>
                        <tr>
                            <th>Socio</th>
                            <th class="text-center">Boletas Vencidas</th>
                            <th class="text-center">Días Máx. Atraso</th>
                            <th>Deuda Total</th>
                            <th class="text-center">Nivel de Riesgo</th>
                            <th>Acción Recomendada</th>
                            <th class="text-center">Ver Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resumenPorSocio as $resumen)
                        <tr class="socio-row riesgo-{{ $resumen['nivel_riesgo'] }}"
                            onclick="toggleDetalle({{ $resumen['socio']->id }})"
                            style="cursor: pointer;">
                            <td>
                                <div class="socio-info">
                                    <div class="socio-nombre">{{ $resumen['socio']->nombre_completo }}</div>
                                    <div class="socio-codigo">{{ $resumen['socio']->numero_socio }}</div>
                                    @if($resumen['socio']->email)
                                        <div class="socio-email">
                                            <i class="fas fa-envelope"></i> {{ $resumen['socio']->email }}
                                        </div>
                                    @endif
                                    @if($resumen['socio']->telefono)
                                        <div class="socio-telefono">
                                            <i class="fas fa-phone"></i> {{ $resumen['socio']->telefono }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge-count riesgo-{{ $resumen['nivel_riesgo'] }}">
                                    {{ $resumen['cantidad_boletas'] }} {{ Str::plural('boleta', $resumen['cantidad_boletas']) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-danger">
                                    <i class="fas fa-clock"></i>
                                    {{ $resumen['dias_max_atraso'] }} días
                                </span>
                            </td>
                            <td>
                                <strong class="monto-deuda">${{ number_format($resumen['monto_total'], 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="nivel-riesgo-badge {{ $resumen['nivel_riesgo'] }}">
                                    @if($resumen['nivel_riesgo'] == 'critico')
                                        <i class="fas fa-ban"></i> CRÍTICO
                                    @elseif($resumen['nivel_riesgo'] == 'alto')
                                        <i class="fas fa-exclamation-triangle"></i> ALTO
                                    @elseif($resumen['nivel_riesgo'] == 'medio')
                                        <i class="fas fa-exclamation-circle"></i> MEDIO
                                    @else
                                        <i class="fas fa-info-circle"></i> BAJO
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="recomendacion">
                                    <div class="recomendacion-titulo {{ $resumen['nivel_riesgo'] }}">
                                        <i class="fas fa-clipboard-check"></i>
                                        {{ $resumen['recomendacion'] }}
                                    </div>
                                    <div class="recomendacion-detalle">
                                        {{ $resumen['accion_detalle'] }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-toggle" onclick="event.stopPropagation(); toggleDetalle({{ $resumen['socio']->id }})">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </td>
                        </tr>
                        <!-- Fila de detalle expandible -->
                        <tr class="detalle-row" id="detalle-{{ $resumen['socio']->id }}" style="display: none;">
                            <td colspan="7">
                                <div class="detalle-content">
                                    <h4>
                                        <i class="fas fa-file-invoice"></i>
                                        Boletas Vencidas de {{ $resumen['socio']->nombre_completo }}
                                    </h4>
                                    <table class="table-detalle">
                                        <thead>
                                            <tr>
                                                <th>N° Boleta</th>
                                                <th>Mes</th>
                                                <th>F. Vencimiento</th>
                                                <th>Días Atraso</th>
                                                <th>Monto</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($resumen['boletas'] as $boleta)
                                            <tr>
                                                <td><strong>{{ $boleta->numero_boleta }}</strong></td>
                                                <td>{{ $boleta->mes_texto }}</td>
                                                <td>{{ $boleta->fecha_vencimiento_formateada }}</td>
                                                <td>
                                                    <span class="badge badge-danger">
                                                        {{ $boleta->dias_atraso }} días
                                                    </span>
                                                </td>
                                                <td><strong>${{ number_format($boleta->total, 0, ',', '.') }}</strong></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{ route('boletas.show', $boleta->id) }}"
                                                           class="btn-action btn-primary"
                                                           title="Ver detalle"
                                                           onclick="event.stopPropagation()">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('boletas.imprimir', $boleta->id) }}"
                                                           class="btn-action btn-secondary"
                                                           target="_blank"
                                                           title="Imprimir"
                                                           onclick="event.stopPropagation()">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                        @if($boleta->socio->email)
                                                        <form action="{{ route('boletas.enviar-email', $boleta->id) }}"
                                                              method="POST"
                                                              style="display: inline;"
                                                              onclick="event.stopPropagation()">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="btn-action btn-info"
                                                                    title="Enviar por email"
                                                                    onclick="return confirm('¿Enviar boleta a {{ $boleta->socio->email }}?')">
                                                                <i class="fas fa-envelope"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr style="background: #f8fafc; font-weight: 600;">
                                                <td colspan="4" style="text-align: right;">TOTAL:</td>
                                                <td colspan="2"><strong>${{ number_format($resumen['monto_total'], 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Leyenda de Niveles de Riesgo -->
<div class="card leyenda-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle"></i>
            Leyenda de Niveles de Riesgo y Acciones
        </h3>
    </div>
    <div class="card-body">
        <div class="leyenda-grid">
            <div class="leyenda-item">
                <div class="leyenda-badge critico">
                    <i class="fas fa-ban"></i> CRÍTICO
                </div>
                <div class="leyenda-texto">
                    <strong>4 o más meses vencidos</strong><br>
                    Acción: Corte de suministro inmediato por acumulación de deuda.
                </div>
            </div>
            <div class="leyenda-item">
                <div class="leyenda-badge alto">
                    <i class="fas fa-exclamation-triangle"></i> ALTO
                </div>
                <div class="leyenda-texto">
                    <strong>3 meses vencidos</strong><br>
                    Acción: Enviar notificación certificada de corte en 15 días.
                </div>
            </div>
            <div class="leyenda-item">
                <div class="leyenda-badge medio">
                    <i class="fas fa-exclamation-circle"></i> MEDIO
                </div>
                <div class="leyenda-texto">
                    <strong>2 meses vencidos</strong><br>
                    Acción: Enviar carta formal solicitando regularización en 30 días.
                </div>
            </div>
            <div class="leyenda-item">
                <div class="leyenda-badge bajo">
                    <i class="fas fa-info-circle"></i> BAJO
                </div>
                <div class="leyenda-texto">
                    <strong>1 mes vencido</strong><br>
                    Acción: Enviar recordatorio de pago por email o llamada.
                </div>
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
        color: #ef4444;
    }

    /* Estadísticas */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--white);
        padding: 20px;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 16px;
        border-left: 4px solid;
    }

    .stat-card.danger {
        border-left-color: #ef4444;
    }

    .stat-card.warning {
        border-left-color: #f59e0b;
    }

    .stat-card.info {
        border-left-color: #3b82f6;
    }

    .stat-card.critical {
        border-left-color: #dc2626;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-card.danger .stat-icon {
        background: #fee2e2;
        color: #ef4444;
    }

    .stat-card.warning .stat-icon {
        background: #fef3c7;
        color: #f59e0b;
    }

    .stat-card.info .stat-icon {
        background: #dbeafe;
        color: #3b82f6;
    }

    .stat-card.critical .stat-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stat-label {
        font-size: 0.813rem;
        color: var(--gray-600);
        font-weight: 500;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
    }

    /* Distribución de Riesgos */
    .risk-distribution {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
        padding: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: var(--radius);
    }

    .risk-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: white;
        border-radius: var(--radius);
        border-left: 4px solid;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .risk-item.critico {
        border-left-color: #dc2626;
    }

    .risk-item.alto {
        border-left-color: #f97316;
    }

    .risk-item.medio {
        border-left-color: #f59e0b;
    }

    .risk-item.bajo {
        border-left-color: #3b82f6;
    }

    .risk-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .risk-item.critico .risk-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .risk-item.alto .risk-icon {
        background: #ffedd5;
        color: #f97316;
    }

    .risk-item.medio .risk-icon {
        background: #fef3c7;
        color: #f59e0b;
    }

    .risk-item.bajo .risk-icon {
        background: #dbeafe;
        color: #3b82f6;
    }

    .risk-info {
        display: flex;
        flex-direction: column;
    }

    .risk-count {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
    }

    .risk-label {
        font-size: 0.75rem;
        color: var(--gray-600);
        font-weight: 600;
    }

    /* Filtros */
    .filters-card {
        margin-bottom: 24px;
    }

    .filter-form {
        display: flex;
        gap: 16px;
        align-items: flex-end;
    }

    .form-group {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-weight: 600;
        color: var(--gray-700);
        font-size: 0.875rem;
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Tarjetas */
    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
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

    /* Tabla de Socios */
    .table-responsive {
        overflow-x: auto;
    }

    .socios-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .socios-table thead {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: white;
    }

    .socios-table th {
        padding: 14px 12px;
        text-align: left;
        font-weight: 700;
        font-size: 0.813rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .socios-table th.text-center {
        text-align: center;
    }

    .socio-row {
        border-bottom: 2px solid var(--gray-200);
        transition: all 0.2s;
    }

    .socio-row:hover {
        background: #f8fafc;
    }

    .socio-row.riesgo-critico {
        background: #fef2f2;
    }

    .socio-row.riesgo-alto {
        background: #fff7ed;
    }

    .socio-row.riesgo-medio {
        background: #fffbeb;
    }

    .socio-row.riesgo-bajo {
        background: #eff6ff;
    }

    .socios-table td {
        padding: 16px 12px;
        vertical-align: top;
    }

    .socio-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .socio-nombre {
        font-weight: 700;
        color: var(--dark);
        font-size: 0.938rem;
    }

    .socio-codigo {
        font-size: 0.75rem;
        color: var(--gray-600);
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }

    .socio-email,
    .socio-telefono {
        font-size: 0.75rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .badge-count {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .badge-count.riesgo-critico {
        background: #dc2626;
        color: white;
    }

    .badge-count.riesgo-alto {
        background: #f97316;
        color: white;
    }

    .badge-count.riesgo-medio {
        background: #f59e0b;
        color: white;
    }

    .badge-count.riesgo-bajo {
        background: #3b82f6;
        color: white;
    }

    .monto-deuda {
        font-size: 1.125rem;
        color: #dc2626;
    }

    .nivel-riesgo-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.813rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .nivel-riesgo-badge.critico {
        background: #dc2626;
        color: white;
    }

    .nivel-riesgo-badge.alto {
        background: #f97316;
        color: white;
    }

    .nivel-riesgo-badge.medio {
        background: #f59e0b;
        color: white;
    }

    .nivel-riesgo-badge.bajo {
        background: #3b82f6;
        color: white;
    }

    .recomendacion {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .recomendacion-titulo {
        font-weight: 700;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .recomendacion-titulo.critico {
        color: #dc2626;
    }

    .recomendacion-titulo.alto {
        color: #f97316;
    }

    .recomendacion-titulo.medio {
        color: #f59e0b;
    }

    .recomendacion-titulo.bajo {
        color: #3b82f6;
    }

    .recomendacion-detalle {
        font-size: 0.75rem;
        color: var(--gray-600);
        line-height: 1.5;
    }

    .btn-toggle {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        border: 1px solid var(--gray-300);
        background: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        color: var(--gray-600);
    }

    .btn-toggle:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    /* Fila detalle */
    .detalle-row td {
        padding: 0 !important;
        background: #f8fafc;
    }

    .detalle-content {
        padding: 24px;
        border-top: 2px solid var(--primary);
    }

    .detalle-content h4 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .table-detalle {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.813rem;
    }

    .table-detalle thead {
        background: var(--gray-200);
    }

    .table-detalle th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
    }

    .table-detalle td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--gray-200);
    }

    .table-detalle tbody tr:hover {
        background: white;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.875rem;
        text-decoration: none;
    }

    .btn-action.btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-action.btn-primary:hover {
        background: #2563eb;
    }

    .btn-action.btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-action.btn-secondary:hover {
        background: var(--gray-300);
    }

    .btn-action.btn-info {
        background: #0ea5e9;
        color: white;
    }

    .btn-action.btn-info:hover {
        background: #0284c7;
    }

    /* Leyenda */
    .leyenda-card {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    .leyenda-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .leyenda-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 16px;
        background: white;
        border-radius: var(--radius);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .leyenda-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        width: fit-content;
    }

    .leyenda-badge.critico {
        background: #dc2626;
        color: white;
    }

    .leyenda-badge.alto {
        background: #f97316;
        color: white;
    }

    .leyenda-badge.medio {
        background: #f59e0b;
        color: white;
    }

    .leyenda-badge.bajo {
        background: #3b82f6;
        color: white;
    }

    .leyenda-texto {
        font-size: 0.813rem;
        color: var(--gray-700);
        line-height: 1.5;
    }

    .leyenda-texto strong {
        color: var(--dark);
    }

    /* Botones */
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

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray-500);
    }

    .empty-state i {
        font-size: 4rem;
        color: #10b981;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        color: var(--dark);
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 1rem;
        color: var(--gray-600);
    }

    .text-center {
        text-align: center;
    }

    @media (max-width: 1200px) {
        .risk-distribution {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .risk-distribution {
            grid-template-columns: 1fr;
        }

        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .leyenda-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    function toggleDetalle(socioId) {
        const detalleRow = document.getElementById('detalle-' + socioId);
        const button = event.currentTarget.querySelector('.btn-toggle i');

        if (detalleRow.style.display === 'none') {
            detalleRow.style.display = 'table-row';
            if (button) button.classList.replace('fa-chevron-down', 'fa-chevron-up');
        } else {
            detalleRow.style.display = 'none';
            if (button) button.classList.replace('fa-chevron-up', 'fa-chevron-down');
        }
    }
</script>
@endsection
