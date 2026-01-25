@extends('layouts.app')

@section('title', 'Detalle Folio SII')

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    h1 i {
        color: var(--primary);
    }

    h5 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    h5 i {
        color: var(--primary);
    }

    .mb-4 {
        margin-bottom: 24px;
    }

    .mt-3 {
        margin-top: 16px;
    }

    .d-flex {
        display: flex;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .align-items-center {
        align-items: center;
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

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-warning {
        background: var(--warning);
        color: white;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
    }

    .col-md-8 { grid-column: span 8; }
    .col-md-4 { grid-column: span 4; }

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

    .card-body {
        padding: 24px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 12px 0;
        text-align: left;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.875rem;
    }

    .table th {
        font-weight: 600;
        color: var(--gray-700);
    }

    .table-borderless th,
    .table-borderless td {
        border-bottom: none;
        padding: 8px 0;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .bg-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .bg-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .bg-success {
        background: #d1fae5;
        color: #065f46;
    }

    .bg-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .bg-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .text-muted {
        color: var(--gray-500);
    }

    .text-primary {
        color: var(--primary);
    }

    .progress {
        background: var(--gray-200);
        border-radius: 12px;
        overflow: hidden;
        display: flex;
    }

    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.75rem;
        transition: width 0.3s;
    }

    .alert {
        padding: 16px;
        border-radius: var(--radius);
        border: 1px solid;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-color: #6ee7b7;
    }

    .alert-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border-color: var(--gray-300);
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border-color: #bfdbfe;
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        background: var(--white);
        width: 100%;
        font-family: monospace;
    }

    .form-control[readonly] {
        background-color: var(--gray-100);
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .row {
            grid-template-columns: 1fr;
        }

        .col-md-8,
        .col-md-4 {
            grid-column: span 1;
        }

        .btn-group {
            flex-direction: column;
        }

        .progress {
            width: 100% !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-file-invoice"></i> Detalle del Folio #{{ $folio->id }}</h1>
        <div class="btn-group">
            <a href="{{ route('folios-sii.edit', $folio->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('folios-sii.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Información del Folio</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Tipo de Documento:</th>
                            <td><span class="badge bg-info">{{ strtoupper(str_replace('_', ' ', $folio->tipo_documento)) }}</span></td>
                        </tr>
                        <tr>
                            <th>Rango de Folios:</th>
                            <td>
                                <strong>{{ number_format($folio->folio_desde, 0, ',', '.') }}</strong>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <strong>{{ number_format($folio->folio_hasta, 0, ',', '.') }}</strong>
                                <span class="text-muted">({{ number_format($folio->folio_hasta - $folio->folio_desde + 1, 0, ',', '.') }} folios)</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Folio Actual:</th>
                            <td><span class="badge bg-secondary">{{ number_format($folio->folio_actual, 0, ',', '.') }}</span></td>
                        </tr>
                        <tr>
                            <th>Folios Disponibles:</th>
                            <td><strong class="text-primary">{{ number_format($folio->folios_disponibles, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Porcentaje de Uso:</th>
                            <td>
                                <div class="progress" style="height: 25px; width: 300px;">
                                    <div class="progress-bar {{ $folio->porcentaje_uso > 80 ? 'bg-danger' : ($folio->porcentaje_uso > 50 ? 'bg-warning' : 'bg-success') }}"
                                         style="width: {{ $folio->porcentaje_uso }}%">
                                        {{ $folio->porcentaje_uso }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha Autorización SII:</th>
                            <td>{{ $folio->fecha_autorizacion->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Fecha Vencimiento:</th>
                            <td>
                                {{ $folio->fecha_vencimiento->format('d/m/Y') }}
                                @if($folio->fecha_vencimiento < now())
                                    <span class="badge bg-danger"><i class="fas fa-exclamation-circle"></i> Vencido</span>
                                @elseif($folio->fecha_vencimiento->diffInDays(now()) <= 30)
                                    <span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> Vence en {{ $folio->fecha_vencimiento->diffInDays(now()) }} días</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($folio->estado == 'activo')
                                    <span class="badge bg-success">Activo</span>
                                @elseif($folio->estado == 'agotado')
                                    <span class="badge bg-warning">Agotado</span>
                                @else
                                    <span class="badge bg-danger">Vencido</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Usuario que Cargó:</th>
                            <td>{{ $folio->usuarioCarga->nombre ?? 'N/A' }} {{ $folio->usuarioCarga->apellido ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Carga:</th>
                            <td>{{ $folio->fecha_creacion->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($folio->observaciones)
                        <div class="alert alert-secondary mt-3">
                            <strong>Observaciones:</strong><br>
                            {{ $folio->observaciones }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @if($folio->caf_xml)
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-file-code"></i> CAF XML</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" rows="15" readonly>{{ $folio->caf_xml }}</textarea>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No se ha cargado el archivo CAF XML para este folio.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
