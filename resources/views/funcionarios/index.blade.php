@extends('layouts.app')

@section('title', 'Funcionarios - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-user-tie"></i>
        Gestión de Funcionarios
    </h2>
    <a href="{{ route('funcionarios.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Funcionario
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="filters-section">
            <form method="GET" action="{{ route('funcionarios.index') }}" class="filter-form">
                <div class="filter-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        <option value="licencia" {{ request('estado') == 'licencia' ? 'selected' : '' }}>Licencia</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="cargo">Cargo</label>
                    <select name="cargo" id="cargo" class="form-control">
                        <option value="">Todos los cargos</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo }}" {{ request('cargo') == $cargo ? 'selected' : '' }}>{{ $cargo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('funcionarios.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>RUT</th>
                        <th>Nombre Completo</th>
                        <th>Cargo</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Fecha Ingreso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($funcionarios as $funcionario)
                    <tr>
                        <td><strong>{{ $funcionario->rut }}</strong></td>
                        <td>{{ $funcionario->nombre_completo }}</td>
                        <td><span class="badge badge-info">{{ $funcionario->cargo }}</span></td>
                        <td>{{ $funcionario->telefono ?? '-' }}</td>
                        <td>{{ $funcionario->email ?? '-' }}</td>
                        <td>{{ $funcionario->fecha_ingreso->format('d/m/Y') }}</td>
                        <td>
                            @if($funcionario->estado === 'activo')
                                <span class="badge badge-success">Activo</span>
                            @elseif($funcionario->estado === 'licencia')
                                <span class="badge badge-warning">Licencia</span>
                            @else
                                <span class="badge badge-danger">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('funcionarios.show', $funcionario->id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('funcionarios.edit', $funcionario->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay funcionarios registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $funcionarios->appends(request()->only(['estado', 'cargo']))->links() }}
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

    .form-control {
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
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
