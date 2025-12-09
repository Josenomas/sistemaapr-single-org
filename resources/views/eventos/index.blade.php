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
                                       class="btn btn-sm btn-warning"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('eventos.destroy', $evento->id) }}"
                                          method="POST"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Está seguro de eliminar este evento?')"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
    border-right: 1px solid var(--gray-200);
    white-space: nowrap;
}

.table th:last-child {
    border-right: none;
}

.table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--gray-200);
    border-right: 1px solid var(--gray-200);
    vertical-align: middle;
}

.table td:last-child {
    border-right: none;
}

.table tbody tr:hover {
    background: var(--gray-50);
}

.event-icon-mini {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
}

.bg-primary { background-color: #2563eb !important; }
.bg-success { background-color: #10b981 !important; }
.bg-warning { background-color: #f59e0b !important; }
.bg-danger { background-color: #ef4444 !important; }
.bg-info { background-color: #06b6d4 !important; }

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-primary {
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
}

.badge-success {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.badge-warning {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.badge-danger {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.badge-info {
    background: rgba(6, 182, 212, 0.1);
    color: #06b6d4;
}

.countdown-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.countdown-badge.urgent {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

.countdown-badge.soon {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.countdown-badge.normal {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.875rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.pagination-wrapper {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}
</style>
@endsection
