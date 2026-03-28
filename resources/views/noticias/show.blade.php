@extends('layouts.app')

@section('title', $noticia->titulo . ' - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-newspaper"></i>
        Detalle de Noticia
    </h2>
    <div class="header-actions">
        <a href="{{ route('noticias.edit', $noticia->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('noticias.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="content-grid">
    <!-- Información General -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i>
                Información General
            </h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Título</label>
                    <p><strong>{{ $noticia->titulo }}</strong></p>
                </div>

                <div class="info-item">
                    <label>Categoría</label>
                    <p>
                        @if($noticia->categoria)
                            <span class="badge badge-info">{{ $noticia->categoria }}</span>
                        @else
                            <span class="text-muted">Sin categoría</span>
                        @endif
                    </p>
                </div>

                <div class="info-item">
                    <label>Estado</label>
                    <p>
                        @if($noticia->estado === 'publicada')
                            <span class="badge badge-success">Publicada</span>
                        @elseif($noticia->estado === 'borrador')
                            <span class="badge badge-secondary">Borrador</span>
                        @else
                            <span class="badge badge-danger">Archivada</span>
                        @endif
                    </p>
                </div>

                <div class="info-item">
                    <label>Destacada</label>
                    <p>
                        @if($noticia->destacada)
                            <span class="badge badge-warning">
                                <i class="fas fa-star"></i> Sí
                            </span>
                        @else
                            <span class="text-muted">No</span>
                        @endif
                    </p>
                </div>

                <div class="info-item">
                    <label>Fecha de Publicación</label>
                    <p>{{ $noticia->fecha_publicacion ? $noticia->fecha_publicacion->format('d/m/Y') : '-' }}</p>
                </div>

                <div class="info-item">
                    <label>Vistas</label>
                    <p><i class="fas fa-eye text-muted"></i> {{ $noticia->vistas ?? 0 }}</p>
                </div>

                @if($noticia->creador)
                <div class="info-item">
                    <label>Creado por</label>
                    <p>{{ $noticia->creador->nombre_completo }}</p>
                </div>
                @endif

                <div class="info-item">
                    <label>Fecha de Creación</label>
                    <p>{{ $noticia->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div class="info-item">
                    <label>Última Actualización</label>
                    <p>{{ $noticia->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($noticia->imagen_destacada)
    <!-- Imagen Destacada -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-image"></i>
                Imagen Destacada
            </h3>
        </div>
        <div class="card-body">
            <div class="featured-image-container">
                <img src="{{ asset('storage/' . $noticia->imagen_destacada) }}"
                     alt="{{ $noticia->titulo }}"
                     class="featured-image">
            </div>
        </div>
    </div>
    @endif

    @if($noticia->resumen)
    <!-- Resumen -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-align-left"></i>
                Resumen
            </h3>
        </div>
        <div class="card-body">
            <p class="resumen-text">{{ $noticia->resumen }}</p>
        </div>
    </div>
    @endif

    <!-- Contenido -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-alt"></i>
                Contenido
            </h3>
        </div>
        <div class="card-body">
            <div class="contenido-text">
                {{ $noticia->contenido }}
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

    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #059669;
    }

    .content-grid {
        display: grid;
        gap: 24px;
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
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: var(--primary);
        font-size: 1.1rem;
    }

    .card-body {
        padding: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .info-item label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-item p {
        font-size: 1rem;
        color: var(--dark);
        margin: 0;
    }

    .featured-image-container {
        display: flex;
        justify-content: center;
        padding: 20px;
        background: var(--gray-50);
        border-radius: var(--radius);
    }

    .featured-image {
        max-width: 100%;
        max-height: 500px;
        border-radius: var(--radius);
        box-shadow: var(--shadow-md);
        object-fit: contain;
    }

    .resumen-text {
        font-size: 1.1rem;
        line-height: 1.6;
        color: var(--gray-700);
        margin: 0;
        padding: 16px;
        background: var(--gray-50);
        border-radius: var(--radius);
        border-left: 4px solid var(--primary);
    }

    .contenido-text {
        font-size: 1rem;
        line-height: 1.8;
        color: var(--dark);
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .text-muted {
        color: var(--gray-500);
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

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
        background: #d97706;
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

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
        }

        .header-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .featured-image {
            max-height: 300px;
        }
    }
</style>
@endsection
