@extends('layouts.app')

@section('title', 'Folios SII - Sistema APR')

@section('styles')
<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1><i class="fas fa-file-invoice"></i> Gestión de Folios SII</h1>
        <a href="{{ route('folios-sii.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Folio
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $estadisticas['total_folios'] }}</div>
                <div class="stat-label">Total Folios</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $estadisticas['folios_activos'] }}</div>
                <div class="stat-label">Folios Activos</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="fas fa-list-ol"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($estadisticas['folios_disponibles'], 0, ',', '.') }}</div>
                <div class="stat-label">Documentos Disponibles</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $estadisticas['folios_vencidos'] }}</div>
                <div class="stat-label">Folios Vencidos</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('folios-sii.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tipo de Documento</label>
                    <select name="tipo_documento" class="form-select">
                        <option value="">Todos</option>
                        <option value="boleta" {{ request('tipo_documento') == 'boleta' ? 'selected' : '' }}>Boleta</option>
                        <option value="factura" {{ request('tipo_documento') == 'factura' ? 'selected' : '' }}>Factura</option>
                        <option value="nota_credito" {{ request('tipo_documento') == 'nota_credito' ? 'selected' : '' }}>Nota de Crédito</option>
                        <option value="nota_debito" {{ request('tipo_documento') == 'nota_debito' ? 'selected' : '' }}>Nota de Débito</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="agotado" {{ request('estado') == 'agotado' ? 'selected' : '' }}>Agotado</option>
                        <option value="vencido" {{ request('estado') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('folios-sii.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Folios -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo Documento</th>
                            <th>Rango de Folios</th>
                            <th>Folio Actual</th>
                            <th>Disponibles</th>
                            <th>% Uso</th>
                            <th>Fecha Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($folios as $folio)
                            <tr>
                                <td>{{ $folio->id }}</td>
                                <td>
                                    <span class="badge bg-info">{{ strtoupper(str_replace('_', ' ', $folio->tipo_documento)) }}</span>
                                </td>
                                <td>
                                    <strong>{{ number_format($folio->folio_desde, 0, ',', '.') }}</strong>
                                    <i class="fas fa-arrow-right text-muted"></i>
                                    <strong>{{ number_format($folio->folio_hasta, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ number_format($folio->folio_actual, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <strong class="text-primary">{{ number_format($folio->folios_disponibles, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar {{ $folio->porcentaje_uso > 80 ? 'bg-danger' : ($folio->porcentaje_uso > 50 ? 'bg-warning' : 'bg-success') }}"
                                             style="width: {{ $folio->porcentaje_uso }}%">
                                            {{ $folio->porcentaje_uso }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $folio->fecha_vencimiento->format('d/m/Y') }}
                                    @if($folio->fecha_vencimiento->diffInDays(now()) <= 30 && $folio->fecha_vencimiento > now())
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Próximo a vencer
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($folio->estado == 'activo')
                                        <span class="badge bg-success">Activo</span>
                                    @elseif($folio->estado == 'agotado')
                                        <span class="badge bg-warning">Agotado</span>
                                    @else
                                        <span class="badge bg-danger">Vencido</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('folios-sii.show', $folio->id) }}" class="btn btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('folios-sii.edit', $folio->id) }}" class="btn btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay folios registrados</p>
                                    <a href="{{ route('folios-sii.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Cargar primer folio
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($folios->hasPages())
                <div class="mt-3">
                    {{ $folios->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
