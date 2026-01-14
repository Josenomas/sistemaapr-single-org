@extends('layouts.app')
@section('title', 'Detalle Activo Fijo')
@section('content')
<div class="page-header">
    <h2><i class="fas fa-box"></i> {{ $activo->nombre }}</h2>
    <div>
        <a href="{{ route('activos-fijos.edit', $activo->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
        <a href="{{ route('activos-fijos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3>Información del Activo</h3></div>
            <div class="card-body">
                <div class="info-grid">
                    <div><strong>Código:</strong> {{ $activo->codigo_activo }}</div>
                    <div><strong>Nombre:</strong> {{ $activo->nombre }}</div>
                    <div><strong>Categoría:</strong> {{ $activo->categoria_nombre }}</div>
                    <div><strong>Estado:</strong> {!! $activo->estado_badge !!}</div>
                    <div><strong>Marca:</strong> {{ $activo->marca ?? 'N/A' }}</div>
                    <div><strong>Modelo:</strong> {{ $activo->modelo ?? 'N/A' }}</div>
                    <div><strong>Número de Serie:</strong> {{ $activo->numero_serie ?? 'N/A' }}</div>
                    <div><strong>Ubicación:</strong> {{ $activo->ubicacion ?? 'N/A' }}</div>
                    <div><strong>Fecha Adquisición:</strong> {{ $activo->fecha_adquisicion_formateada }}</div>
                    <div><strong>Valor Adquisición:</strong> {{ $activo->valor_adquisicion_formateado }}</div>
                    <div><strong>Valor Actual:</strong> {{ $activo->valor_actual_formateado }}</div>
                    <div><strong>Proveedor:</strong> {{ $activo->proveedor ?? 'N/A' }}</div>
                    @if($activo->responsable)
                    <div><strong>Responsable:</strong> {{ $activo->responsable->nombre_completo }}</div>
                    @endif
                    @if($activo->vida_util_anos)
                    <div><strong>Vida Útil:</strong> {{ $activo->vida_util_anos }} años</div>
                    @endif
                    @if($activo->descripcion)
                    <div class="col-span-2"><strong>Descripción:</strong><br>{{ $activo->descripcion }}</div>
                    @endif
                    @if($activo->observaciones)
                    <div class="col-span-2"><strong>Observaciones:</strong><br>{{ $activo->observaciones }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3>Información Adicional</h3></div>
            <div class="card-body">
                <p><strong>Depreciación:</strong> ${{ number_format($activo->depreciacion, 0, ',', '.') }}</p>
                <p><strong>Valor Depreciado:</strong> ${{ number_format($activo->valor_depreciado, 0, ',', '.') }}</p>
                @if($activo->fecha_ultimo_mantenimiento)
                <p><strong>Último Mantenimiento:</strong> {{ $activo->fecha_ultimo_mantenimiento_formateada }}</p>
                @endif
                @if($activo->proxima_revision)
                <p><strong>Próxima Revisión:</strong> {{ $activo->proxima_revision_formateada }}</p>
                @endif
                <hr>
                <p><small><strong>Registrado:</strong> {{ $activo->fecha_creacion->format('d/m/Y H:i') }}</small></p>
                <p><small><strong>Actualizado:</strong> {{ $activo->fecha_actualizacion->format('d/m/Y H:i') }}</small></p>
            </div>
        </div>
    </div>
</div>
@endsection
