@extends('layouts.app')

@section('title', 'Eventos - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-calendar-alt"></i>
        Gestión de Eventos
    </h2>
    <div>
        <a href="{{ route('eventos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nuevo Evento
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Tipo</th>
                        <th>Próxima Fecha</th>
                        <th>Recurrencia</th>
                        <th>Días Restantes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventos as $evento)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="event-icon-mini bg-{{ $evento->color }}">
                                        <i class="fas {{ $evento->icono }}"></i>
                                    </div>
                                    <strong>{{ $evento->titulo }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $evento->color }}">{{ $evento->tipo }}</span>
                            </td>
                            <td>{{ $evento->proxima_fecha->format('d/m/Y') }}</td>
                            <td>
                                <small class="text-muted">{{ $evento->recurrencia_texto }}</small>
                            </td>
                            <td>
                                <span class="countdown-badge {{ $evento->countdown_class }}">
                                    {{ $evento->countdown_texto }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('eventos.edit', $evento->id) }}"
                                       class="btn-icon btn-icon-warning"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="fas fa-inbox"></i>
                                <p>No hay eventos registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($eventos->hasPages())
            <div class="pagination-wrapper">
                {{ $eventos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
/* Consistente con el resto del sistema */
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

.btn-primary {
    background: linear-gradient(135deg, var(--primary), #1e40af);
    color: white;
    padding: 12px 24px;
    border-radius: var(--radius);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
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

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

.table thead {
    background: var(--gray-100);
}

.table th {
    padding: 14px 16px;
    text-align: left;
    font-weight: 600;
    color: var(--gray-700);
    border-bottom: 2px solid var(--gray-300);
    border-right: 1px solid var(--gray-200);
    white-space: nowrap;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table th:last-child {
    border-right: none;
}

.table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--gray-200);
    border-right: 1px solid var(--gray-200);
    vertical-align: middle;
}

.table td:last-child {
    border-right: none;
}

.table tbody tr {
    transition: all 0.2s;
}

.table tbody tr:hover {
    background: var(--gray-50);
}

.event-icon-mini {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.bg-primary { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }
.bg-success { background: linear-gradient(135deg, #10b981, #059669) !important; }
.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706) !important; }
.bg-danger { background: linear-gradient(135deg, #ef4444, #dc2626) !important; }
.bg-info { background: linear-gradient(135deg, #06b6d4, #0891b2) !important; }

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-primary {
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
    border: 1px solid rgba(37, 99, 235, 0.2);
}

.badge-success {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.badge-warning {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.badge-danger {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.badge-info {
    background: rgba(6, 182, 212, 0.1);
    color: #06b6d4;
    border: 1px solid rgba(6, 182, 212, 0.2);
}

.countdown-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 700;
}

.countdown-badge.urgent {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.countdown-badge.soon {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.countdown-badge:not(.urgent):not(.soon) {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.btn-icon {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    border: none;
}

.btn-icon-warning {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.btn-icon-warning:hover {
    background: #f59e0b;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
}

.text-center {
    text-align: center;
}

.text-muted {
    color: var(--gray-500);
}

.pagination-wrapper {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}
</style>
@endsection
