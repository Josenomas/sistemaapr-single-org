@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1><i class="fas fa-history"></i> Actividad Reciente</h1>
        <p class="text-muted">Registro completo de todas las acciones realizadas en el sistema</p>
    </div>

    <div class="card">
        <div class="card-body">
            @if($actividades->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay actividad reciente registrada</p>
                </div>
            @else
                <div class="activity-list">
                    @foreach($actividades as $item)
                        <div class="activity-item">
                            <div class="activity-icon">
                                @if(str_contains(strtoupper($item->tipo), 'PAGO'))
                                    <span class="badge badge-success">💰 PAGOS</span>
                                @elseif(str_contains(strtoupper($item->tipo), 'BOLETA'))
                                    <span class="badge badge-primary">📄 BOLETAS</span>
                                @elseif(str_contains(strtoupper($item->tipo), 'SOCIO'))
                                    <span class="badge badge-info">👥 SOCIOS</span>
                                @elseif(str_contains(strtoupper($item->tipo), 'LECTURA'))
                                    <span class="badge badge-warning">📊 LECTURAS</span>
                                @elseif(str_contains(strtoupper($item->tipo), 'INCIDENTE'))
                                    <span class="badge badge-danger">🚨 INCIDENTES</span>
                                @elseif(str_contains(strtoupper($item->tipo), 'USUARIO'))
                                    <span class="badge badge-secondary">👤 USUARIOS</span>
                                @else
                                    <span class="badge badge-secondary">📋 {{ strtoupper($item->tipo) }}</span>
                                @endif
                            </div>

                            <div class="activity-content">
                                <p class="activity-description">{{ $item->descripcion }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> {{ $item->nombre }} {{ $item->apellido }}
                                    • <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($item->fecha_creacion)->diffForHumans() }}
                                    ({{ \Carbon\Carbon::parse($item->fecha_creacion)->format('d/m/Y H:i') }})
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $actividades->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 0.5rem;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .activity-item {
        display: flex;
        gap: 1rem;
        padding: 1.25rem;
        border-radius: 8px;
        background: #f8f9fa;
        border-left: 4px solid #6366f1;
        transition: all 0.2s;
    }

    .activity-item:hover {
        background: #e9ecef;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .activity-icon .badge {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-success { background: #10b981; color: white; }
    .badge-primary { background: #3b82f6; color: white; }
    .badge-info { background: #06b6d4; color: white; }
    .badge-warning { background: #f59e0b; color: white; }
    .badge-danger { background: #ef4444; color: white; }
    .badge-secondary { background: #6b7280; color: white; }

    .activity-content {
        flex: 1;
    }

    .activity-description {
        margin: 0 0 0.5rem 0;
        font-weight: 500;
        color: var(--gray-800);
        font-size: 0.95rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }

    .empty-state i {
        display: block;
        margin-bottom: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .activity-item {
            flex-direction: column;
            gap: 0.75rem;
        }

        .activity-icon {
            display: flex;
            justify-content: flex-start;
        }
    }
</style>
@endsection
