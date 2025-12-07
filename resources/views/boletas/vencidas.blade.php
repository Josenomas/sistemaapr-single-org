@extends('layouts.app')

@section('title', 'Boletas Vencidas - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exclamation-triangle"></i>
        Boletas Vencidas
    </h2>
    <a href="{{ route('boletas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<!-- Estadísticas -->
<div class="stats-grid">
    <div class="stat-card danger">
        <div class="stat-icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Total Vencidas</span>
            <span class="stat-value">{{ $boletas->count() }}</span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Monto Total</span>
            <span class="stat-value">${{ number_format($boletas->sum('total'), 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Socios Afectados</span>
            <span class="stat-value">{{ $boletas->unique('id_socio')->count() }}</span>
        </div>
    </div>
</div>

<!-- Tabla de Boletas Vencidas -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i>
            Listado de Boletas Vencidas
        </h3>
    </div>
    <div class="card-body">
        @if($boletas->isEmpty())
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No hay boletas vencidas</h3>
                <p>Todas las boletas están al día</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Boleta</th>
                            <th>Socio</th>
                            <th>Mes</th>
                            <th>Fecha Vencimiento</th>
                            <th>Días Atraso</th>
                            <th>Monto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($boletas as $boleta)
                        <tr>
                            <td><strong>{{ $boleta->numero_boleta }}</strong></td>
                            <td>
                                <div>{{ $boleta->socio->nombre_completo }}</div>
                                <small class="text-muted">{{ $boleta->socio->numero_socio }}</small>
                            </td>
                            <td>{{ $boleta->mes_texto }}</td>
                            <td>{{ $boleta->fecha_vencimiento_formateada }}</td>
                            <td>
                                <span class="badge badge-danger">
                                    <i class="fas fa-clock"></i>
                                    {{ $boleta->dias_atraso }} días
                                </span>
                            </td>
                            <td><strong>${{ number_format($boleta->total, 0, ',', '.') }}</strong></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('boletas.show', $boleta->id) }}"
                                       class="btn-action btn-primary"
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('boletas.imprimir', $boleta->id) }}"
                                       class="btn-action btn-secondary"
                                       target="_blank"
                                       title="Imprimir">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @if($boleta->socio->email)
                                    <form action="{{ route('boletas.enviar-email', $boleta->id) }}"
                                          method="POST"
                                          style="display: inline;">
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
                </table>
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
        color: #ef4444;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--white);
        padding: 24px;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 20px;
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

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
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

    .stat-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
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

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table thead {
        background: var(--gray-100);
    }

    .table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-200);
        white-space: nowrap;
    }

    .table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
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
        gap: 8px;
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

    .text-muted {
        color: var(--gray-500);
        font-size: 0.813rem;
    }

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
</style>
@endsection
