@extends('layouts.app')

@section('title', 'Detalle Socio - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-user"></i>
        Socio: {{ $socio->numero_socio }}
    </h2>
    <div class="btn-group">
        <a href="{{ route('socios.edit', $socio->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('socios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Información Personal -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-id-card"></i>
                    Información Personal
                </h3>
                <span class="badge badge-{{ $socio->estado === 'activo' ? 'success' : ($socio->estado === 'moroso' ? 'warning' : 'danger') }}">
                    {{ ucfirst($socio->estado) }}
                </span>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Número de Socio</label>
                        <value><strong>{{ $socio->numero_socio }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>RUT</label>
                        <value>{{ $socio->rut }}</value>
                    </div>

                    <div class="info-item">
                        <label>Nombre Completo</label>
                        <value>{{ $socio->nombre_completo }}</value>
                    </div>

                    <div class="info-item">
                        <label>Tipo de Cliente</label>
                        <value><span class="badge badge-info">{{ ucfirst($socio->tipo_cliente) }}</span></value>
                    </div>

                    <div class="info-item full-width">
                        <label>Dirección</label>
                        <value>{{ $socio->direccion }}</value>
                    </div>

                    <div class="info-item">
                        <label>Sector</label>
                        <value>{{ $socio->sector ?? 'No especificado' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Teléfono</label>
                        <value>{{ $socio->telefono ?? 'No registrado' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Email</label>
                        <value>{{ $socio->email ?? 'No registrado' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Número de Medidor</label>
                        <value>{{ $socio->numero_medidor ?? 'No asignado' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Fecha de Ingreso</label>
                        <value>{{ $socio->fecha_ingreso->format('d/m/Y') }}</value>
                    </div>

                    <div class="info-item">
                        <label>Registrado Hace</label>
                        <value>{{ $socio->fecha_creacion->diffForHumans() }}</value>
                    </div>

                    @if($socio->subsidio_porcentaje > 0 || $socio->descuento_monto > 0)
                    <div class="info-item full-width" style="background: #e3f2fd; padding: 12px; border-radius: 6px; border-left: 4px solid #1565c0;">
                        <label style="color: #1565c0; font-weight: bold;">
                            <i class="fas fa-hand-holding-usd"></i> Subsidios y Descuentos
                        </label>
                        <value>
                            @if($socio->subsidio_porcentaje > 0)
                                <span class="badge badge-info" style="font-size: 0.9rem; margin-right: 10px;">
                                    <i class="fas fa-percent"></i> Subsidio: {{ number_format($socio->subsidio_porcentaje, 2) }}%
                                </span>
                            @endif
                            @if($socio->descuento_monto > 0)
                                <span class="badge badge-success" style="font-size: 0.9rem; margin-right: 10px;">
                                    <i class="fas fa-dollar-sign"></i> Descuento: ${{ number_format($socio->descuento_monto, 0, ',', '.') }}
                                </span>
                            @endif
                            @if($socio->observaciones_subsidio)
                                <br><small style="color: #555; margin-top: 5px; display: block;">{{ $socio->observaciones_subsidio }}</small>
                            @endif
                        </value>
                    </div>
                    @endif

                    @if($socio->observaciones)
                    <div class="info-item full-width">
                        <label>Observaciones</label>
                        <value>{{ $socio->observaciones }}</value>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Lecturas Recientes -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Lecturas Recientes
                </h3>
            </div>
            <div class="card-body">
                @if($socio->lecturas->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Lectura Anterior</th>
                                    <th>Lectura Actual</th>
                                    <th>Consumo (m³)</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($socio->lecturas->take(5) as $lectura)
                                <tr>
                                    <td>{{ $lectura->mes }}</td>
                                    <td>{{ number_format($lectura->lectura_anterior, 2) }}</td>
                                    <td>{{ number_format($lectura->lectura_actual, 2) }}</td>
                                    <td><strong>{{ number_format($lectura->consumo_m3, 2) }}</strong></td>
                                    <td>{{ date('d/m/Y', strtotime($lectura->fecha_lectura)) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">No hay lecturas registradas</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Estadísticas y Acciones -->
    <div class="col-md-4">
        <!-- Estadísticas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    Resumen
                </h3>
            </div>
            <div class="card-body">
                <div class="stat-box">
                    <div class="stat-icon" style="background: #3b82f6;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $socio->boletas->count() }}</div>
                        <div class="stat-label">Boletas</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #10b981;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $socio->boletas->where('estado', 'pagada')->count() }}</div>
                        <div class="stat-label">Pagadas</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #f59e0b;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $socio->boletas->whereIn('estado', ['pendiente', 'vencida'])->count() }}</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #06b6d4;">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $socio->lecturas->count() }}</div>
                        <div class="stat-label">Lecturas</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt"></i>
                    Acciones Rápidas
                </h3>
            </div>
            <div class="card-body">
                <a href="{{ route('lecturas.create', ['socio_id' => $socio->id]) }}" class="action-btn">
                    <i class="fas fa-plus-circle"></i>
                    Registrar Lectura
                </a>
                <a href="{{ route('pagos.create', ['socio_id' => $socio->id]) }}" class="action-btn">
                    <i class="fas fa-dollar-sign"></i>
                    Registrar Pago
                </a>
                <a href="{{ route('socios.edit', $socio->id) }}" class="action-btn">
                    <i class="fas fa-edit"></i>
                    Editar Información
                </a>
                <form action="{{ route('socios.destroy', $socio->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Está seguro de eliminar este socio?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn danger">
                        <i class="fas fa-trash"></i>
                        Eliminar Socio
                    </button>
                </form>
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

    .row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    .col-md-8 {
        grid-column: 1;
    }

    .col-md-4 {
        grid-column: 2;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title i {
        color: var(--primary);
    }

    .card-body {
        padding: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-item.full-width {
        grid-column: span 2;
    }

    .info-item label {
        font-weight: 600;
        color: var(--gray-500);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .info-item value {
        color: var(--dark);
        font-size: 0.95rem;
    }

    .stat-box {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: var(--gray-50);
        border-radius: var(--radius);
        margin-bottom: 12px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .stat-info {
        flex: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        color: var(--dark);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        margin-bottom: 8px;
        cursor: pointer;
        width: 100%;
    }

    .action-btn:hover {
        background: var(--primary-light);
        border-color: var(--primary);
        color: var(--primary-dark);
    }

    .action-btn.danger:hover {
        background: #fee2e2;
        border-color: var(--danger);
        color: var(--danger);
    }

    .badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
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

    .btn-group {
        display: flex;
        gap: 8px;
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
    }

    .table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--gray-500);
    }

    .mt-4 {
        margin-top: 24px;
    }

    @media (max-width: 768px) {
        .row {
            grid-template-columns: 1fr;
        }

        .col-md-8,
        .col-md-4 {
            grid-column: 1;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .info-item.full-width {
            grid-column: span 1;
        }
    }
</style>
@endsection
