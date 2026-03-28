@extends('layouts.app')

@section('title', 'Noticias - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-newspaper"></i>
        Gestión de Noticias
    </h2>
    <div class="header-actions">
        <a href="{{ route('noticias.publicas', auth()->user()->organizacion->slug) }}" class="btn btn-info" target="_blank" title="Ver portal público">
            <i class="fas fa-eye"></i>
            Vista Pública
        </a>
        <a href="{{ route('noticias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nueva Noticia
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="filters-section">
            <form method="GET" action="{{ route('noticias.index') }}" class="filter-form">
                <div class="filter-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="publicada" {{ request('estado') == 'publicada' ? 'selected' : '' }}>Publicada</option>
                        <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="archivada" {{ request('estado') == 'archivada' ? 'selected' : '' }}>Archivada</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="categoria">Categoría</label>
                    <select name="categoria" id="categoria" class="form-select">
                        <option value="">Todas las categorías</option>
                        <option value="Aviso" {{ request('categoria') == 'Aviso' ? 'selected' : '' }}>Aviso</option>
                        <option value="Evento" {{ request('categoria') == 'Evento' ? 'selected' : '' }}>Evento</option>
                        <option value="Mantenimiento" {{ request('categoria') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="Corte" {{ request('categoria') == 'Corte' ? 'selected' : '' }}>Corte</option>
                        <option value="Reunión" {{ request('categoria') == 'Reunión' ? 'selected' : '' }}>Reunión</option>
                        <option value="Otro" {{ request('categoria') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('noticias.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="60"></th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Fecha Publicación</th>
                        <th>Vistas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($noticias as $noticia)
                    <tr>
                        <td>
                            @if($noticia->imagen_destacada)
                            <img src="{{ asset('storage/' . $noticia->imagen_destacada) }}"
                                 alt="{{ $noticia->titulo }}"
                                 class="img-thumbnail"
                                 style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                 style="width: 50px; height: 50px; border-radius: 4px;">
                                <i class="fas fa-image"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $noticia->titulo }}</strong>
                            @if($noticia->destacada)
                            <span class="badge badge-warning">
                                <i class="fas fa-star"></i> Destacada
                            </span>
                            @endif
                            @if($noticia->resumen)
                            <br><small class="text-muted">{{ Str::limit($noticia->resumen, 60) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($noticia->categoria)
                            <span class="badge badge-info">{{ $noticia->categoria }}</span>
                            @else
                            <span class="text-muted">Sin categoría</span>
                            @endif
                        </td>
                        <td>
                            @if($noticia->estado === 'publicada')
                            <span class="badge badge-success">Publicada</span>
                            @elseif($noticia->estado === 'borrador')
                            <span class="badge badge-secondary">Borrador</span>
                            @else
                            <span class="badge badge-danger">Archivada</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $noticia->fecha_publicacion ? $noticia->fecha_publicacion->format('d/m/Y') : '-' }}</small>
                        </td>
                        <td>
                            <i class="fas fa-eye text-muted"></i> {{ $noticia->vistas ?? 0 }}
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('noticias.show', $noticia->id) }}"
                                   class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('noticias.edit', $noticia->id) }}"
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('noticias.destroy', $noticia->id) }}"
                                      method="POST"
                                      style="display: inline;"
                                      onsubmit="return confirm('¿Está seguro de eliminar esta noticia?')">
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
                        <td colspan="7" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                <h5>No hay noticias registradas</h5>
                                <p class="text-muted">Comienza creando tu primera noticia</p>
                                <a href="{{ route('noticias.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Nueva Noticia
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $noticias->appends(request()->only(['estado', 'categoria']))->links() }}
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
        align-items: center;
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

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #dc2626;
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

    .filters-section {
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--gray-200);
    }

    .filter-form {
        display: flex;
        gap: 16px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 200px;
    }

    .filter-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
    }

    .form-select {
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table thead tr {
        background: var(--gray-100);
        border-bottom: 2px solid var(--gray-300);
    }

    .table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        white-space: nowrap;
    }

    .table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-200);
    }

    .table tbody tr:hover {
        background: var(--gray-50);
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

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-600);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-700);
    }

    .btn-outline {
        background: white;
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }

    .btn-outline:hover {
        background: var(--gray-50);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.75rem;
    }

    .btn-info {
        background: #06b6d4;
        color: white;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-danger {
        background: #dc2626;
        color: white;
    }

    .btn-group {
        display: flex;
        gap: 4px;
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

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--gray-500);
    }

    .empty-state {
        padding: 40px 20px;
    }

    .empty-state h5 {
        margin-top: 12px;
        margin-bottom: 8px;
        color: var(--gray-700);
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .header-actions {
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .header-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
    }
</style>
@endsection
