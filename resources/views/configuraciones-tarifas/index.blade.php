@extends('layouts.app')

@section('title', 'Configuraciones Tarifarias')

@section('styles')
<style>
    .tarifas-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .tarifas-title {
        font-size: 1.75rem;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        margin: 0;
    }

    .tarifas-title i {
        color: var(--primary);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px;
        border-left: 4px solid var(--primary);
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 8px;
        font-weight: 600;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
    }

    .filters-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        margin-bottom: 24px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--gray-700);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-group label i {
        color: var(--primary);
    }

    .filter-group select {
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .filter-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .filter-actions {
        display: flex;
        gap: 12px;
    }

    .tarifa-group {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 32px;
        overflow: hidden;
    }

    .tarifa-group-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-bottom: 2px solid var(--gray-300);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .tarifa-group-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .tarifa-subheader {
        padding: 16px 24px;
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .tarifa-subtitle {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        margin: 0;
    }

    .tarifas-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tarifas-table thead {
        background: var(--gray-100);
    }

    .tarifas-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--gray-700);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tarifas-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--gray-100);
    }

    .tarifas-table tbody tr:hover {
        background: var(--gray-50);
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-info {
        background: #cffafe;
        color: #0e7490;
    }

    .badge-success {
        background: var(--success-light);
        color: var(--success-dark);
    }

    .badge-danger {
        background: var(--danger-light);
        color: var(--danger-dark);
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: var(--radius);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .btn-icon:hover {
        transform: scale(1.1);
    }

    .btn-edit {
        background: var(--warning-light);
        color: var(--warning-dark);
    }

    .btn-delete {
        background: var(--danger-light);
        color: var(--danger-dark);
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--gray-500);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 16px;
        color: var(--gray-400);
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }
</style>
@endsection

@section('content')
<div class="tarifas-container">
    <div class="tarifas-header">
        <h2 class="tarifas-title">
            <i class="fas fa-sliders-h"></i>
            Configuraciones Tarifarias
        </h2>
        <a href="{{ route('configuraciones-tarifas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nueva Configuración
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total de Tramos</div>
            <div class="stat-value">{{ $estadisticas['total_tramos'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tipos de Cliente</div>
            <div class="stat-value">{{ $estadisticas['tipos_cliente'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Monto Mínimo</div>
            <div class="stat-value">${{ number_format($estadisticas['monto_minimo'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Monto Máximo</div>
            <div class="stat-value">${{ number_format($estadisticas['monto_maximo'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <form method="GET" action="{{ route('configuraciones-tarifas.index') }}" class="filters-form">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="tipo_cliente">
                        <i class="fas fa-user-tag"></i>
                        Tipo de Cliente
                    </label>
                    <select name="tipo_cliente" id="tipo_cliente" class="filter-select">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposCliente as $tipo)
                            <option value="{{ $tipo }}" {{ request('tipo_cliente') == $tipo ? 'selected' : '' }}>
                                {{ ucfirst($tipo) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="nombre_tarifa">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Nombre de Tarifa
                    </label>
                    <select name="nombre_tarifa" id="nombre_tarifa" class="filter-select">
                        <option value="">Todas las tarifas</option>
                        @foreach($nombresTarifas as $nombre)
                            <option value="{{ $nombre }}" {{ request('nombre_tarifa') == $nombre ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('configuraciones-tarifas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tarifas Agrupadas -->
    @if($tarifas->isEmpty())
        <div class="tarifa-group">
            <div class="empty-state">
                <i class="fas fa-sliders-h"></i>
                <p>No hay configuraciones tarifarias registradas.</p>
                <a href="{{ route('configuraciones-tarifas.create') }}" class="btn btn-primary" style="margin-top: 16px;">
                    <i class="fas fa-plus"></i>
                    Crear Primera Configuración
                </a>
            </div>
        </div>
    @else
        @foreach($tarifasAgrupadas as $tipoCliente => $tarifasPorNombre)
            <div class="tarifa-group">
                <div class="tarifa-group-header">
                    <h3 class="tarifa-group-title">
                        <i class="fas fa-{{ $tipoCliente == 'residencial' ? 'home' : ($tipoCliente == 'comercial' ? 'store' : 'industry') }}"></i>
                        {{ ucfirst($tipoCliente) }}
                        <span class="badge badge-{{ $tipoCliente == 'residencial' ? 'primary' : ($tipoCliente == 'comercial' ? 'warning' : 'info') }}">
                            {{ $tarifasPorNombre->flatten()->count() }} tramos
                        </span>
                    </h3>
                </div>

                @foreach($tarifasPorNombre as $nombreTarifa => $tramos)
                    <div class="tarifa-subheader">
                        <h4 class="tarifa-subtitle">{{ $nombreTarifa }}</h4>
                    </div>

                    <table class="tarifas-table">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Nombre Tramo</th>
                                <th>Rango de Consumo</th>
                                <th>Monto Base</th>
                                <th>Cargo Fijo</th>
                                <th>IVA</th>
                                <th>Vigencia</th>
                                <th>Estado</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tramos as $tarifa)
                                <tr>
                                    <td><strong>{{ $tarifa->orden }}</strong></td>
                                    <td>{{ $tarifa->nombre }}</td>
                                    <td>
                                        <strong>{{ $tarifa->rango_descripcion }}</strong>
                                    </td>
                                    <td><strong>${{ number_format($tarifa->monto, 0, ',', '.') }}</strong></td>
                                    <td>${{ number_format($tarifa->cargo_fijo ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ number_format($tarifa->iva ?? 0, 1) }}%</td>
                                    <td>
                                        <small>
                                            {{ $tarifa->vigente_desde ? $tarifa->vigente_desde->format('d/m/Y') : '-' }}
                                            @if($tarifa->vigente_hasta)
                                                <br>→ {{ $tarifa->vigente_hasta->format('d/m/Y') }}
                                            @else
                                                <br>→ Indefinido
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        @if($tarifa->activo)
                                            @if($tarifa->es_vigente)
                                                <span class="badge badge-success">Vigente</span>
                                            @else
                                                <span class="badge badge-warning">Activo (No vigente)</span>
                                            @endif
                                        @else
                                            <span class="badge badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons" style="justify-content: center;">
                                            <a href="{{ route('configuraciones-tarifas.edit', $tarifa->id) }}"
                                               class="btn-icon btn-edit"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('configuraciones-tarifas.destroy', $tarifa->id) }}"
                                                  method="POST"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('¿Está seguro de eliminar esta configuración?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon btn-delete" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        @endforeach
    @endif
</div>
@endsection
