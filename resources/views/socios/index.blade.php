@extends('layouts.app')

@section('title', 'Socios - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-users"></i>
        Gestión de Socios
    </h2>
    <a href="{{ route('socios.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Socio
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Socio</th>
                        <th>RUT</th>
                        <th>Nombre Completo</th>
                        <th>Dirección</th>
                        <th>Sector</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($socios as $socio)
                    <tr>
                        <td><strong>{{ $socio->numero_socio }}</strong></td>
                        <td>{{ $socio->rut }}</td>
                        <td>{{ $socio->nombre_completo }}</td>
                        <td>{{ $socio->direccion }}</td>
                        <td><span class="badge badge-info">{{ $socio->sector ?? 'N/A' }}</span></td>
                        <td>{{ $socio->telefono ?? '-' }}</td>
                        <td>
                            @if($socio->estado === 'activo')
                                <span class="badge badge-success">Activo</span>
                            @elseif($socio->estado === 'moroso')
                                <span class="badge badge-warning">Moroso</span>
                            @elseif($socio->estado === 'suspendido')
                                <span class="badge badge-danger">Suspendido</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($socio->estado) }}</span>
                            @endif
                            @if($socio->exento_iva)
                                <span class="badge badge-success" style="margin-left: 4px;" title="Exento de IVA">
                                    <i class="fas fa-percent"></i> Sin IVA
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('socios.show', $socio->id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('socios.edit', $socio->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('socios.toggleExentoIva', $socio->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $socio->exento_iva ? 'btn-success' : 'btn-secondary' }}"
                                            title="{{ $socio->exento_iva ? 'Exento de IVA' : 'Aplicar Exención IVA' }}">
                                        <i class="fas fa-percent"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay socios registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $socios->links() }}
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

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-secondary {
        background: #6b7280;
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

    /* Ajustar tamaño de las flechas de paginación */
    .pagination-wrapper nav {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .pagination-wrapper nav a,
    .pagination-wrapper nav span {
        min-width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
    }

    .pagination-wrapper nav a svg,
    .pagination-wrapper nav span svg {
        width: 16px !important;
        height: 16px !important;
    }

    .pagination-wrapper nav a:hover {
        background: var(--gray-100);
    }

    .pagination-wrapper nav .active {
        background: var(--primary);
        color: white;
    }
</style>
@endsection
