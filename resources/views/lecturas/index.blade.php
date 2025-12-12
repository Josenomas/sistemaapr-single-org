@extends('layouts.app')

@section('title', 'Lecturas - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-tachometer-alt"></i>
        Gestión de Lecturas
    </h2>
    <div class="header-actions">
        <a href="{{ route('lecturas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nueva Lectura
        </a>
        <a href="{{ route('lecturas.masivo') }}" class="btn btn-success">
            <i class="fas fa-clipboard-list"></i>
            Registro Masivo
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="filters-section">
            <form method="GET" action="{{ route('lecturas.index') }}" class="filter-form">
                <div class="filter-group">
                    <label for="mes">Mes</label>
                    <input type="month" name="mes" id="mes" class="form-control" value="{{ request('mes') }}">
                </div>

                <div class="filter-group">
                    <label for="socio_id">Socio</label>
                    <select name="socio_id" id="socio_id" class="form-control">
                        <option value="">Todos los socios</option>
                        @foreach($socios as $socio)
                            <option value="{{ $socio->id }}" {{ request('socio_id') == $socio->id ? 'selected' : '' }}>
                                {{ $socio->numero_socio }} - {{ $socio->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('lecturas.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Socio</th>
                        <th>Nombre</th>
                        <th>Mes</th>
                        <th>Lect. Anterior</th>
                        <th>Lect. Actual</th>
                        <th>Consumo (m³)</th>
                        <th>Fecha Lectura</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lecturas as $lectura)
                    <tr>
                        <td><strong>{{ $lectura->socio->numero_socio }}</strong></td>
                        <td>{{ $lectura->socio->nombre_completo }}</td>
                        <td>
                            @php
                                $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                                          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                $fecha = explode('-', $lectura->mes);
                                echo $meses[(int)$fecha[1]] . ' ' . $fecha[0];
                            @endphp
                        </td>
                        <td>{{ number_format($lectura->lectura_anterior, 3, '.', ',') }} m³</td>
                        <td>{{ number_format($lectura->lectura_actual, 3, '.', ',') }} m³</td>
                        <td><span class="badge badge-info">{{ number_format($lectura->consumo, 3, '.', ',') }} m³</span></td>
                        <td>{{ date('d/m/Y', strtotime($lectura->fecha_lectura)) }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('lecturas.show', $lectura->id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$lectura->socio->boletas()->where('mes', $lectura->mes)->where('activo', 1)->exists())
                                <a href="{{ route('lecturas.edit', $lectura->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay lecturas registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $lecturas->links() }}
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

    .btn-success {
        background: #059669;
        color: white;
    }

    .btn-success:hover {
        background: #047857;
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
