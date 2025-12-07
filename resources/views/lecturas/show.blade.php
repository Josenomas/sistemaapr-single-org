@extends('layouts.app')

@section('title', 'Detalle Lectura - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-tachometer-alt"></i>
        Detalle de Lectura
    </h2>
    <div class="btn-group">
        @if(!$lectura->socio->boletas()->where('mes', $lectura->mes)->where('activo', 1)->exists())
        <a href="{{ route('lecturas.edit', $lectura->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        @endif
        <a href="{{ route('lecturas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Información de la Lectura -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información de la Lectura
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>ID Lectura</label>
                        <value><strong>{{ $lectura->id }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Mes/Año</label>
                        <value>
                            @php
                                $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                                          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                $fecha = explode('-', $lectura->mes);
                                echo $meses[(int)$fecha[1]] . ' ' . $fecha[0];
                            @endphp
                        </value>
                    </div>

                    <div class="info-item">
                        <label>Fecha de Lectura</label>
                        <value>{{ date('d/m/Y', strtotime($lectura->fecha_lectura)) }}</value>
                    </div>

                    <div class="info-item">
                        <label>Lectura Anterior</label>
                        <value>{{ number_format($lectura->lectura_anterior, 2) }} m³</value>
                    </div>

                    <div class="info-item">
                        <label>Lectura Actual</label>
                        <value>{{ number_format($lectura->lectura_actual, 2) }} m³</value>
                    </div>

                    <div class="info-item">
                        <label>Consumo</label>
                        <value><strong style="color: #2563eb;">{{ number_format($lectura->consumo, 2) }} m³</strong></value>
                    </div>

                    @if($lectura->observaciones)
                    <div class="info-item full-width">
                        <label>Observaciones</label>
                        <value>{{ $lectura->observaciones }}</value>
                    </div>
                    @endif

                    <div class="info-item">
                        <label>Fecha de Registro</label>
                        <value>{{ date('d/m/Y H:i', strtotime($lectura->fecha_creacion)) }}</value>
                    </div>

                    @if($lectura->fecha_actualizacion)
                    <div class="info-item">
                        <label>Última Actualización</label>
                        <value>{{ date('d/m/Y H:i', strtotime($lectura->fecha_actualizacion)) }}</value>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Socio -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user"></i>
                    Información del Socio
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Número de Socio</label>
                        <value><strong>{{ $lectura->socio->numero_socio }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Nombre Completo</label>
                        <value>{{ $lectura->socio->nombre_completo }}</value>
                    </div>

                    <div class="info-item full-width">
                        <label>Dirección</label>
                        <value>{{ $lectura->socio->direccion }}</value>
                    </div>

                    <div class="info-item">
                        <label>Sector</label>
                        <value>{{ $lectura->socio->sector ?: 'No especificado' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Teléfono</label>
                        <value>{{ $lectura->socio->telefono ?: 'No registrado' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Estado</label>
                        <value>
                            @if($lectura->socio->estado == 'activo')
                                <span class="badge badge-success">Activo</span>
                            @elseif($lectura->socio->estado == 'moroso')
                                <span class="badge badge-warning">Moroso</span>
                            @elseif($lectura->socio->estado == 'suspendido')
                                <span class="badge badge-danger">Suspendido</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($lectura->socio->estado) }}</span>
                            @endif
                        </value>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('socios.show', $lectura->socio->id) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i>
                        Ver Socio Completo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Boleta Asociada y Acciones -->
    <div class="col-md-4">
        @if($lectura->socio->boletas()->where('mes', $lectura->mes)->where('activo', 1)->exists())
        <!-- Boleta Asociada -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice"></i>
                    Boleta Asociada
                </h3>
            </div>
            <div class="card-body">
                @php
                    $boleta = $lectura->socio->boletas()->where('mes', $lectura->mes)->where('activo', 1)->first();
                @endphp

                <div class="info-grid">
                    <div class="info-item">
                        <label>N° Boleta</label>
                        <value><strong>{{ $boleta->numero_boleta }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Monto Total</label>
                        <value style="font-weight: 600; color: #059669;">
                            ${{ number_format($boleta->monto_total, 0, ',', '.') }}
                        </value>
                    </div>

                    <div class="info-item">
                        <label>Estado</label>
                        <value>
                            @if($boleta->estado == 'pendiente')
                                <span class="badge badge-warning">Pendiente</span>
                            @elseif($boleta->estado == 'pagada')
                                <span class="badge badge-success">Pagada</span>
                            @elseif($boleta->estado == 'vencida')
                                <span class="badge badge-danger">Vencida</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($boleta->estado) }}</span>
                            @endif
                        </value>
                    </div>

                    <div class="info-item">
                        <label>Vencimiento</label>
                        <value>{{ date('d/m/Y', strtotime($boleta->fecha_vencimiento)) }}</value>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('boletas.show', $boleta->id) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-eye"></i>
                        Ver Boleta
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Acciones -->
        <div class="card {{ $lectura->socio->boletas()->where('mes', $lectura->mes)->where('activo', 1)->exists() ? 'mt-4' : '' }}">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs"></i>
                    Acciones
                </h3>
            </div>
            <div class="card-body">
                <div class="actions-list">
                    @if(!$lectura->socio->boletas()->where('mes', $lectura->mes)->where('activo', 1)->exists())
                    <a href="{{ route('lecturas.edit', $lectura->id) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i>
                        Editar Lectura
                    </a>

                    <a href="{{ route('boletas.generar', ['id_socio' => $lectura->id_socio, 'mes' => $lectura->mes]) }}" class="btn btn-success btn-block">
                        <i class="fas fa-file-invoice"></i>
                        Generar Boleta
                    </a>

                    <form action="{{ route('lecturas.destroy', $lectura->id) }}" method="POST"
                          onsubmit="return confirm('¿Está seguro que desea eliminar esta lectura?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i>
                            Eliminar Lectura
                        </button>
                    </form>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <p>Esta lectura tiene una boleta asociada y no puede ser modificada o eliminada.</p>
                    </div>
                    @endif
                </div>
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
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
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
        gap: 6px;
    }

    .info-item.full-width {
        grid-column: span 2;
    }

    .info-item label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-item value {
        font-size: 0.95rem;
        color: var(--gray-800);
    }

    .mt-3 {
        margin-top: 1rem;
    }

    .mt-4 {
        margin-top: 1.5rem;
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
        justify-content: center;
    }

    .btn-block {
        width: 100%;
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

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
        background: #d97706;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-danger {
        background: #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background: #b91c1c;
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

    .btn-info {
        background: #06b6d4;
        color: white;
    }

    .btn-info:hover {
        background: #0891b2;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-group {
        display: flex;
        gap: 8px;
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

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .actions-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .alert {
        padding: 16px;
        border-radius: var(--radius);
        display: flex;
        align-items: start;
        gap: 12px;
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #60a5fa;
    }

    .alert p {
        margin: 0;
        font-size: 0.875rem;
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
