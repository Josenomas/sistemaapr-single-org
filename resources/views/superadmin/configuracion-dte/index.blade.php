@extends('layouts.superadmin')

@section('title', 'Configuración DTE - Super Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-cog"></i> Configuración DTE por Organización</h2>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-building"></i> Organizaciones Activas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Organización</th>
                            <th>RUT</th>
                            <th>Proveedor DTE</th>
                            <th>Ambiente</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organizaciones as $org)
                        <tr>
                            <td>
                                <strong>{{ $org->nombre_apr }}</strong>
                            </td>
                            <td>
                                @if($org->configuracionDTE)
                                    {{ $org->configuracionDTE->rut_emisor }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($org->configuracionDTE)
                                    @if($org->configuracionDTE->proveedor_dte === 'libredte')
                                        <span class="badge bg-primary">LibreDTE</span>
                                    @elseif($org->configuracionDTE->proveedor_dte === 'simpleapi')
                                        <span class="badge bg-info">SimpleAPI</span>
                                    @elseif($org->configuracionDTE->proveedor_dte === 'simplefactura')
                                        <span class="badge bg-success">SimpleFactura</span>
                                    @endif
                                @else
                                    <span class="text-muted">Sin configurar</span>
                                @endif
                            </td>
                            <td>
                                @if($org->configuracionDTE)
                                    @if($org->configuracionDTE->ambiente === 'produccion')
                                        <span class="badge bg-success">Producción</span>
                                    @else
                                        <span class="badge bg-warning">Certificación</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($org->configuracionDTE && $org->configuracionDTE->activo)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Activo</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times"></i> Sin configurar</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('superadmin.configuracion-dte.editar', $org->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Configurar
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay organizaciones activas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle"></i> Información</h6>
            <ul class="mb-0">
                <li><strong>LibreDTE:</strong> Requiere hash de API según ambiente</li>
                <li><strong>SimpleAPI:</strong> Requiere certificado digital + token API</li>
                <li><strong>SimpleFactura:</strong> Requiere certificado digital + usuario + contraseña</li>
            </ul>
        </div>
    </div>
</div>
@endsection
