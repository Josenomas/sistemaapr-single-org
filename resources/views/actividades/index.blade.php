@extends('layouts.app')

@section('title', 'Actividad Reciente - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-history"></i>
        Actividad Reciente
    </h2>
    <p class="page-subtitle">Registro completo de todas las acciones realizadas en el sistema</p>
</div>

@if($actividades->isEmpty())
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>No hay actividad registrada</h3>
        <p>Cuando se realicen acciones en el sistema, aparecerán aquí.</p>
    </div>
@else
    <!-- Info de paginación -->
    <div class="results-info">
        <p class="text-muted">
            <i class="fas fa-info-circle"></i>
            Mostrando {{ $actividades->firstItem() }} - {{ $actividades->lastItem() }} de {{ $actividades->total() }} registros
        </p>
    </div>

    <!-- Lista de actividades -->
    <div class="activity-timeline">
        @foreach($actividades as $item)
            <div class="activity-card">
                <div class="activity-badge">
                    @if(str_contains(strtoupper($item->modulo), 'PAGO'))
                        <span class="badge-icon bg-success">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                    @elseif(str_contains(strtoupper($item->modulo), 'BOLETA'))
                        <span class="badge-icon bg-primary">
                            <i class="fas fa-file-invoice"></i>
                        </span>
                    @elseif(str_contains(strtoupper($item->modulo), 'SOCIO'))
                        <span class="badge-icon bg-info">
                            <i class="fas fa-users"></i>
                        </span>
                    @elseif(str_contains(strtoupper($item->modulo), 'LECTURA'))
                        <span class="badge-icon bg-warning">
                            <i class="fas fa-tachometer-alt"></i>
                        </span>
                    @elseif(str_contains(strtoupper($item->modulo), 'INCIDENTE'))
                        <span class="badge-icon bg-danger">
                            <i class="fas fa-exclamation-circle"></i>
                        </span>
                    @elseif(str_contains(strtoupper($item->modulo), 'USUARIO'))
                        <span class="badge-icon bg-secondary">
                            <i class="fas fa-user"></i>
                        </span>
                    @else
                        <span class="badge-icon bg-secondary">
                            <i class="fas fa-cog"></i>
                        </span>
                    @endif
                </div>

                <div class="activity-details">
                    <div class="activity-header">
                        <span class="module-badge module-{{ strtolower($item->modulo) }}">
                            {{ strtoupper($item->modulo) }}
                        </span>
                        <span class="activity-time">
                            <i class="fas fa-clock"></i>
                            {{ \Carbon\Carbon::parse($item->fecha_creacion)->diffForHumans() }}
                        </span>
                    </div>

                    <p class="activity-description">{{ $item->descripcion }}</p>

                    <div class="activity-footer">
                        <span class="activity-user">
                            <i class="fas fa-user-circle"></i>
                            {{ $item->nombre }} {{ $item->apellido }}
                        </span>
                        <span class="activity-date">
                            {{ \Carbon\Carbon::parse($item->fecha_creacion)->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Paginación -->
    <div class="pagination-wrapper">
        {{ $actividades->links() }}
    </div>
@endif

<style>
    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
    }

    .page-subtitle {
        color: var(--gray-600);
        font-size: 0.95rem;
        margin: 0;
    }

    .results-info {
        margin-bottom: 1.5rem;
        padding: 0.75rem 1rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .results-info p {
        margin: 0;
        font-size: 0.9rem;
    }

    .activity-timeline {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .activity-card {
        display: flex;
        gap: 1.25rem;
        padding: 1.5rem;
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .activity-card:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
        transform: translateY(-2px);
    }

    .activity-badge {
        flex-shrink: 0;
    }

    .badge-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .bg-secondary { background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); }

    .activity-details {
        flex: 1;
        min-width: 0;
    }

    .activity-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        gap: 1rem;
    }

    .module-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .module-pagos { background: #d1fae5; color: #065f46; }
    .module-boletas { background: #dbeafe; color: #1e40af; }
    .module-socios { background: #cffafe; color: #155e75; }
    .module-lecturas { background: #fef3c7; color: #92400e; }
    .module-incidentes { background: #fee2e2; color: #991b1b; }
    .module-usuarios { background: #e5e7eb; color: #374151; }

    .activity-time {
        font-size: 0.85rem;
        color: var(--gray-500);
        white-space: nowrap;
    }

    .activity-description {
        margin: 0 0 1rem 0;
        font-size: 1rem;
        font-weight: 500;
        color: var(--gray-900);
        line-height: 1.5;
    }

    .activity-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #f3f4f6;
    }

    .activity-user,
    .activity-date {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .activity-user i,
    .activity-date i,
    .activity-time i {
        margin-right: 0.375rem;
        opacity: 0.7;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        border: 2px dashed #e5e7eb;
    }

    .empty-state i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--gray-500);
        margin: 0;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .activity-card {
            flex-direction: column;
            padding: 1.25rem;
        }

        .activity-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .activity-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .badge-icon {
            width: 40px;
            height: 40px;
            font-size: 1.1rem;
        }
    }
</style>
@endsection
