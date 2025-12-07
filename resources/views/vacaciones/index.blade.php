@extends('layouts.app')

@section('title', 'Vacaciones - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-umbrella-beach"></i>
        Gestión de Vacaciones
    </h2>
    <a href="{{ route('vacaciones.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nueva Vacación
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('vacaciones.index') }}" class="filters-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="funcionario">Funcionario</label>
                    <select name="funcionario" id="funcionario" class="form-control">
                        <option value="">Todos</option>
                        @foreach($funcionarios as $func)
                            <option value="{{ $func->id }}" {{ request('funcionario') == $func->id ? 'selected' : '' }}>
                                {{ $func->nombre }} {{ $func->apellido_paterno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="solicitada" {{ request('estado') == 'solicitada' ? 'selected' : '' }}>Solicitada</option>
                        <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                        <option value="en_curso" {{ request('estado') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                        <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo</label>
                    <select name="tipo" id="tipo" class="form-control">
                        <option value="">Todos</option>
                        <option value="legales" {{ request('tipo') == 'legales' ? 'selected' : '' }}>Legales</option>
                        <option value="progresivas" {{ request('tipo') == 'progresivas' ? 'selected' : '' }}>Progresivas</option>
                        <option value="administrativas" {{ request('tipo') == 'administrativas' ? 'selected' : '' }}>Administrativas</option>
                        <option value="sin_goce" {{ request('tipo') == 'sin_goce' ? 'selected' : '' }}>Sin Goce</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="periodo">Periodo</label>
                    <input type="text" name="periodo" id="periodo" class="form-control"
                           placeholder="Ej: 2024" value="{{ request('periodo') }}" maxlength="4">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="margin-top: 22px;">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Alertas -->
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Tabla -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Funcionario</th>
                        <th>Periodo</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Término</th>
                        <th>Días</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vacaciones as $vacacion)
                    <tr>
                        <td>{{ $vacacion->funcionario->nombre }} {{ $vacacion->funcionario->apellido_paterno }}</td>
                        <td><strong>{{ $vacacion->periodo }}</strong></td>
                        <td>{{ $vacacion->fecha_inicio_formateada }}</td>
                        <td>{{ $vacacion->fecha_termino_formateada }}</td>
                        <td><strong>{{ $vacacion->dias_habiles }}</strong> días</td>
                        <td>{!! $vacacion->tipo_badge !!}</td>
                        <td>{!! $vacacion->estado_badge !!}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('vacaciones.show', $vacacion->id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('vacaciones.edit', $vacacion->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay vacaciones registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $vacaciones->links() }}
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

    .mb-4 {
        margin-bottom: 24px;
    }

    .filters-form .form-row {
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
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
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

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-info {
        background: #cffafe;
        color: #155e75;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .badge-dark {
        background: var(--gray-700);
        color: var(--white);
    }

    .text-center {
        text-align: center;
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
</style>
@endsection
