@extends('layouts.app')

@section('title', 'Usuarios - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-users-cog"></i>
        Gestión de Usuarios
    </h2>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Usuario
    </a>
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
            <form method="GET" action="{{ route('usuarios.index') }}" class="filter-form">
                <div class="filter-group">
                    <label for="rol">Rol</label>
                    <select name="rol" id="rol" class="form-select">
                        <option value="">Todos los roles</option>
                        <option value="admin" {{ request('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="tesorero" {{ request('rol') == 'tesorero' ? 'selected' : '' }}>Tesorero</option>
                        <option value="operador" {{ request('rol') == 'operador' ? 'selected' : '' }}>Operador</option>
                        <option value="lecturista" {{ request('rol') == 'lecturista' ? 'selected' : '' }}>Lecturista</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td><strong>{{ $usuario->nombre_usuario }}</strong></td>
                        <td>{{ $usuario->nombre_completo }}</td>
                        <td>{{ $usuario->email ?: '-' }}</td>
                        <td>
                            @if($usuario->rol == 'admin')
                                <span class="badge badge-info">Administrador</span>
                            @elseif($usuario->rol == 'tesorero')
                                <span class="badge badge-success">Tesorero</span>
                            @elseif($usuario->rol == 'operador')
                                <span class="badge badge-info">Operador</span>
                            @elseif($usuario->rol == 'lecturista')
                                <span class="badge badge-warning">Lecturista</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($usuario->rol) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($usuario->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-danger">Inactivo</span>
                            @endif
                        </td>
                        <td>{{ $usuario->ultimo_acceso ? date('d/m/Y H:i', strtotime($usuario->ultimo_acceso)) : 'Nunca' }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('usuarios.show', $usuario->id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay usuarios registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $usuarios->links() }}
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

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }
</style>
@endsection
