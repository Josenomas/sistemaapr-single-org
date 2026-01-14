@extends('layouts.app')

@section('title', 'Reporte de Recaudación - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Reporte de Recaudación
    </h2>
    <div class="btn-group">
        <a href="{{ route('historial-pagos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i>
            Imprimir
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3 no-print">
    <div class="card-body">
        <h3 class="filter-title">
            <i class="fas fa-calendar-alt"></i>
            Seleccionar Período
        </h3>
        <form method="GET" action="{{ route('historial-pagos.reporte-recaudacion') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha Inicio:</label>
                    <input type="date"
                           id="fecha_inicio"
                           name="fecha_inicio"
                           class="form-control"
                           value="{{ $fechaInicio }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="fecha_fin">Fecha Fin:</label>
                    <input type="date"
                           id="fecha_fin"
                           name="fecha_fin"
                           class="form-control"
                           value="{{ $fechaFin }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="periodo">Agrupar por:</label>
                    <select id="periodo" name="periodo" class="form-control">
                        <option value="dia" {{ $periodo == 'dia' ? 'selected' : '' }}>Día</option>
                        <option value="mes" {{ $periodo == 'mes' ? 'selected' : '' }}>Mes</option>
                        <option value="trimestre" {{ $periodo == 'trimestre' ? 'selected' : '' }}>Trimestre</option>
                        <option value="año" {{ $periodo == 'año' ? 'selected' : '' }}>Año</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Generar Reporte
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Resumen General -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Recaudado</div>
            <div class="stat-value">${{ number_format($estadisticas['total_recaudado'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-receipt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Pagos</div>
            <div class="stat-value">{{ number_format($estadisticas['total_pagos']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Promedio por Pago</div>
            <div class="stat-value">${{ number_format($estadisticas['promedio_pago'], 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<!-- Recaudación por Método de Pago -->
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-credit-card"></i>
            Recaudación por Método de Pago
        </h3>
    </div>
    <div class="card-body">
        <div class="metodos-grid">
            <div class="metodo-item">
                <div class="metodo-icon efectivo">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="metodo-info">
                    <div class="metodo-label">Efectivo</div>
                    <div class="metodo-monto">${{ number_format($estadisticas['por_metodo']['efectivo'], 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="metodo-item">
                <div class="metodo-icon transferencia">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="metodo-info">
                    <div class="metodo-label">Transferencia</div>
                    <div class="metodo-monto">${{ number_format($estadisticas['por_metodo']['transferencia'], 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="metodo-item">
                <div class="metodo-icon cheque">
                    <i class="fas fa-money-check"></i>
                </div>
                <div class="metodo-info">
                    <div class="metodo-label">Cheque</div>
                    <div class="metodo-monto">${{ number_format($estadisticas['por_metodo']['cheque'], 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="metodo-item">
                <div class="metodo-icon credito">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="metodo-info">
                    <div class="metodo-label">Crédito/Débito</div>
                    <div class="metodo-monto">${{ number_format($estadisticas['por_metodo']['credito'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detalle por Período -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-table"></i>
            Detalle por {{ ucfirst($periodo) }}
        </h3>
    </div>
    <div class="card-body">
        @if($recaudacionPorPeriodo->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                No hay pagos registrados en el período seleccionado.
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Período</th>
                        <th>Cantidad Pagos</th>
                        <th>Monto Total</th>
                        <th>Promedio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recaudacionPorPeriodo as $periodoKey => $pagosPeriodo)
                        <tr>
                            <td>
                                <strong>{{ $periodoKey }}</strong>
                            </td>
                            <td>{{ $pagosPeriodo->count() }}</td>
                            <td class="text-success">
                                <strong>${{ number_format($pagosPeriodo->sum('monto_pagado'), 0, ',', '.') }}</strong>
                            </td>
                            <td>${{ number_format($pagosPeriodo->avg('monto_pagado'), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-total">
                        <td><strong>TOTAL</strong></td>
                        <td><strong>{{ number_format($estadisticas['total_pagos']) }}</strong></td>
                        <td><strong>${{ number_format($estadisticas['total_recaudado'], 0, ',', '.') }}</strong></td>
                        <td><strong>${{ number_format($estadisticas['promedio_pago'], 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
</div>

<!-- Información adicional para impresión -->
<div class="print-footer">
    <p><strong>Período:</strong> {{ date('d/m/Y', strtotime($fechaInicio)) }} - {{ date('d/m/Y', strtotime($fechaFin)) }}</p>
    <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    <p><strong>Usuario:</strong> {{ auth()->user()->nombre_completo }}</p>
</div>
@endsection

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--gray-200);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: var(--primary);
    }

    .btn-group {
        display: flex;
        gap: 12px;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-icon.bg-success { background: var(--success); }
    .stat-icon.bg-primary { background: var(--primary); }
    .stat-icon.bg-info { background: var(--info); }
    .stat-icon.bg-warning { background: var(--warning); }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-800);
    }

    .card {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow);
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
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 24px;
    }

    .filter-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-form .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 6px;
        font-size: 0.875rem;
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .metodos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .metodo-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--gray-50);
        border-radius: 8px;
        border: 1px solid var(--gray-200);
    }

    .metodo-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .metodo-icon.efectivo { background: #10b981; }
    .metodo-icon.transferencia { background: #3b82f6; }
    .metodo-icon.cheque { background: #f59e0b; }
    .metodo-icon.credito { background: #8b5cf6; }

    .metodo-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 4px;
    }

    .metodo-monto {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-800);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead th {
        background: var(--gray-100);
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-300);
    }

    .table tbody td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .table-total {
        background: var(--gray-100);
        font-weight: 700;
    }

    .table-total td {
        padding: 16px 12px !important;
        border-top: 2px solid var(--gray-300);
    }

    .text-success {
        color: var(--success);
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: var(--gray-500);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
    }

    .print-footer {
        display: none;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 2px solid var(--gray-300);
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    @media print {
        .no-print,
        .btn-group,
        .page-header .btn-group {
            display: none !important;
        }

        .print-footer {
            display: block;
        }

        .page-header {
            border-bottom: 2px solid #000;
        }

        body {
            background: white;
        }

        .card {
            box-shadow: none;
            border: 1px solid #ddd;
            page-break-inside: avoid;
        }
    }
</style>
@endsection
