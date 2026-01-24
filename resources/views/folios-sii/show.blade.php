@extends('layouts.app')

@section('title', 'Detalle Folio SII')

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
