@extends('layouts.app')

@section('title', 'Mi Organización')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-building"></i>
        Mi Organización
    </h2>
    <div class="header-actions">
        <a href="{{ route('organizacion.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar Organización
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<!-- Información de la Organización -->
<div class="card mb-4">
    <div class="card-header">
        <h3><i class="fas fa-info-circle"></i> Información General</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th width="200">Nombre de la APR</th>
                    <td><strong>{{ $organizacion->nombre_apr }}</strong></td>
                </tr>
                <tr>
                    <th>RUT</th>
                    <td>{{ $organizacion->rut }}</td>
                </tr>
                <tr>
                    <th>Dirección</th>
                    <td>{{ $organizacion->direccion ?? 'No especificada' }}</td>
                </tr>
                <tr>
                    <th>Teléfono</th>
                    <td>{{ $organizacion->telefono ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <th>Email de Contacto</th>
                    <td>{{ $organizacion->email_contacto ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <th>Slug (URL)</th>
                    <td><code>{{ $organizacion->slug }}</code></td>
                </tr>
                <tr>
                    <th>Logo</th>
                    <td>
                        @if($organizacion->logo)
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <img src="{{ asset('storage/' . $organizacion->logo) }}"
                                     alt="Logo {{ $organizacion->nombre_apr }}"
                                     style="max-width: 200px; max-height: 80px; object-fit: contain; border: 1px solid var(--gray-300); padding: 8px; border-radius: var(--radius); background: white;">
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Configurado
                                </span>
                            </div>
                            <small class="text-muted" style="display: block; margin-top: 8px;">
                                <i class="fas fa-info-circle"></i> Este logo se muestra en el encabezado del sistema
                            </small>
                        @else
                            <span class="badge badge-info">
                                <i class="fas fa-image"></i> No configurado
                            </span>
                            <small class="text-muted" style="display: block; margin-top: 8px;">
                                <i class="fas fa-arrow-right"></i> Puedes subir un logo desde
                                <a href="{{ route('organizacion.edit') }}">Editar Organización</a>
                            </small>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<!-- Plan de Suscripción -->
<div class="card mb-4">
    <div class="card-header">
        <h3><i class="fas fa-crown"></i> Plan de Suscripción</h3>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 style="margin-bottom: 1rem;">
                    Plan {{ ucfirst($organizacion->suscripcion->nombre) }}
                    @if($organizacion->estado_suscripcion === 'activa')
                        <span class="badge badge-success">Activa</span>
                    @elseif($organizacion->estado_suscripcion === 'prueba')
                        <span class="badge badge-info">Período de Prueba ({{ $organizacion->dias_prueba_restantes }} días)</span>
                    @elseif($organizacion->estado_suscripcion === 'vencida')
                        <span class="badge badge-danger">Vencida</span>
                    @endif
                </h2>
                <p style="font-size: 1.25rem; color: var(--primary); margin: 0;">
                    <strong>${{ number_format($organizacion->suscripcion->precio_mensual, 0, ',', '.') }}</strong> / mes
                </p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('organizacion.upgrade') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-up"></i> Cambiar Plan
                </a>
            </div>
        </div>

        <!-- Tabla de Características -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Característica</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="fas fa-users"></i> Socios</td>
                        <td>
                            @if($organizacion->suscripcion->tieneSociosIlimitados())
                                <span class="badge badge-success">Ilimitados</span>
                            @else
                                Hasta {{ number_format($organizacion->suscripcion->max_socios) }} socios
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-user-shield"></i> Usuarios del Sistema</td>
                        <td>
                            @if($organizacion->suscripcion->tieneUsuariosIlimitados())
                                <span class="badge badge-success">Ilimitados</span>
                            @else
                                {{ $organizacion->suscripcion->max_usuarios }} {{ $organizacion->suscripcion->max_usuarios === 1 ? 'usuario' : 'usuarios' }}
                            @endif
                        </td>
                    </tr>
                    @if($organizacion->suscripcion->permite_dominio_personalizado)
                    <tr>
                        <td><i class="fas fa-globe"></i> Dominio Personalizado</td>
                        <td>
                            @if($organizacion->dominio_personalizado)
                                <a href="http://{{ $organizacion->dominio_personalizado }}" target="_blank">
                                    {{ $organizacion->dominio_personalizado }}
                                </a>
                            @else
                                <span class="text-muted">No configurado</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($organizacion->suscripcion->permite_modulo_noticias)
                    <tr>
                        <td><i class="fas fa-newspaper"></i> Módulo de Noticias</td>
                        <td><span class="badge badge-success">Habilitado</span></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($organizacion->suscripcion->features)
        <h5 style="margin-top: 1.5rem; margin-bottom: 1rem;">Módulos Incluidos:</h5>
        <div class="row">
            @foreach((is_array($organizacion->suscripcion->features) ? $organizacion->suscripcion->features : json_decode($organizacion->suscripcion->features, true)) as $feature)
                <div class="col-md-6 mb-2">
                    <i class="fas fa-check text-success"></i> {{ $feature }}
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Estadísticas de Uso -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar"></i> Uso de Socios</h3>
            </div>
            <div class="card-body text-center">
                <div style="font-size: 3rem; font-weight: bold; margin-bottom: 1rem;">
                    <span style="color: var(--primary);">{{ number_format($estadisticas['socios_totales']) }}</span>
                    <span style="color: var(--gray-300);">/</span>
                    <span style="color: var(--gray-500);">
                        {{ $organizacion->suscripcion->tieneSociosIlimitados() ? '∞' : number_format($estadisticas['socios_limite']) }}
                    </span>
                </div>

                @if(!$organizacion->suscripcion->tieneSociosIlimitados())
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar {{ $estadisticas['socios_porcentaje'] > 90 ? 'bg-danger' : ($estadisticas['socios_porcentaje'] > 70 ? 'bg-warning' : 'bg-success') }}"
                         role="progressbar"
                         style="width: {{ $estadisticas['socios_porcentaje'] }}%">
                        {{ $estadisticas['socios_porcentaje'] }}%
                    </div>
                </div>

                @if($estadisticas['socios_porcentaje'] > 90)
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    Estás cerca del límite de socios. Considera actualizar tu plan.
                </div>
                @endif
                @else
                <p class="text-muted">Socios ilimitados en tu plan</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar"></i> Uso de Usuarios</h3>
            </div>
            <div class="card-body text-center">
                <div style="font-size: 3rem; font-weight: bold; margin-bottom: 1rem;">
                    <span style="color: var(--primary);">{{ number_format($estadisticas['usuarios_totales']) }}</span>
                    <span style="color: var(--gray-300);">/</span>
                    <span style="color: var(--gray-500);">
                        {{ $organizacion->suscripcion->tieneUsuariosIlimitados() ? '∞' : number_format($estadisticas['usuarios_limite']) }}
                    </span>
                </div>

                @if(!$organizacion->suscripcion->tieneUsuariosIlimitados())
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar {{ $estadisticas['usuarios_porcentaje'] > 90 ? 'bg-danger' : ($estadisticas['usuarios_porcentaje'] > 70 ? 'bg-warning' : 'bg-success') }}"
                         role="progressbar"
                         style="width: {{ $estadisticas['usuarios_porcentaje'] }}%">
                        {{ $estadisticas['usuarios_porcentaje'] }}%
                    </div>
                </div>

                @if($estadisticas['usuarios_porcentaje'] > 90)
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    Estás cerca del límite de usuarios. Considera actualizar tu plan.
                </div>
                @endif
                @else
                <p class="text-muted">Usuarios ilimitados en tu plan</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Información de Facturación -->
@if($organizacion->fecha_inicio_suscripcion)
<div class="card mt-4">
    <div class="card-header">
        <h3><i class="fas fa-credit-card"></i> Información de Facturación</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th width="200">Inicio de Suscripción</th>
                    <td><strong>{{ \Carbon\Carbon::parse($organizacion->fecha_inicio_suscripcion)->format('d/m/Y') }}</strong></td>
                </tr>
                @if($organizacion->fecha_fin_suscripcion)
                <tr>
                    <th>Vencimiento</th>
                    <td><strong>{{ \Carbon\Carbon::parse($organizacion->fecha_fin_suscripcion)->format('d/m/Y') }}</strong></td>
                </tr>
                @endif
                @if($organizacion->proximo_pago)
                <tr>
                    <th>Próximo Pago</th>
                    <td><strong>{{ \Carbon\Carbon::parse($organizacion->proximo_pago)->format('d/m/Y') }}</strong></td>
                </tr>
                @endif
                @if($organizacion->metodo_pago)
                <tr>
                    <th>Método de Pago</th>
                    <td>{{ ucfirst($organizacion->metodo_pago) }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</div>
@endif
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
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-header h3 {
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

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        margin-bottom: 0;
    }

    .table-bordered {
        border: 1px solid var(--gray-200);
    }

    .table-bordered th,
    .table-bordered td {
        padding: 12px 16px;
        border: 1px solid var(--gray-200);
    }

    .table-bordered th {
        background: var(--gray-50);
        font-weight: 600;
        color: var(--gray-700);
    }

    .table-bordered td {
        color: var(--gray-700);
    }

    .table thead th {
        background: var(--gray-100);
        font-weight: 600;
        color: var(--dark);
        border-bottom: 2px solid var(--gray-300);
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 50px;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #3b82f6;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
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

    .btn-lg {
        padding: 14px 28px;
        font-size: 1rem;
    }

    .alert {
        padding: 12px 16px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -12px;
    }

    .col-md-4,
    .col-md-6,
    .col-md-8 {
        padding: 0 12px;
        width: 100%;
    }

    @media (min-width: 768px) {
        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .col-md-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
        }
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--gray-500);
    }

    .text-success {
        color: #10b981;
    }

    .mb-2 {
        margin-bottom: 8px;
    }

    .mb-3 {
        margin-bottom: 16px;
    }

    .mb-4 {
        margin-bottom: 24px;
    }

    .mt-3 {
        margin-top: 16px;
    }

    .mt-4 {
        margin-top: 24px;
    }

    .progress {
        display: flex;
        height: 1rem;
        overflow: hidden;
        background-color: var(--gray-200);
        border-radius: var(--radius);
    }

    .progress-bar {
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: white;
        text-align: center;
        white-space: nowrap;
        transition: width 0.6s ease;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .bg-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .bg-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .bg-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    code {
        background: var(--gray-100);
        padding: 4px 8px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        color: #e11d48;
    }

    @media (max-width: 767px) {
        .row {
            margin: 0;
        }

        .col-md-4,
        .col-md-6,
        .col-md-8 {
            padding: 0;
            margin-bottom: 20px;
        }

        .text-right {
            text-align: left;
        }
    }
</style>
@endsection
