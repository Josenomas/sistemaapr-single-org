@extends('layouts.app')

@section('title', 'Libro de Ventas Electrónico - DTE')

@section('styles')
<style>
.stats-grid-ventas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card-ventas {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
    border-left: 4px solid #7c3aed;
}

.stat-card-ventas h3 {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
    font-weight: 600;
}

.stat-card-ventas .value {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
}

.filter-section {
    background: white;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.filter-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 16px;
    align-items: end;
}

.form-group-inline label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #374151;
}

.form-group-inline input {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
}

.btn-filter {
    background: linear-gradient(135deg, #7c3aed, #5b21b6);
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.btn-download {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-download:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    color: white;
}

.table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    background: linear-gradient(135deg, #7c3aed, #5b21b6);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-header h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: #f9fafb;
}

thead th {
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    border-bottom: 2px solid #e5e7eb;
}

tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
    color: #374151;
}

tbody tr:hover {
    background: #f9fafb;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-39 {
    background: #dbeafe;
    color: #1e40af;
}

.badge-61 {
    background: #fef3c7;
    color: #92400e;
}
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-book"></i>
        Libro de Ventas Electrónico (IECV)
    </h1>
    <div class="header-actions">
        <a href="{{ route('dte.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver al Dashboard
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    {{ session('error') }}
</div>
@endif

<!-- Filtros de Fecha -->
<div class="filter-section">
    <form action="{{ route('dte.libro-ventas') }}" method="GET">
        <div class="filter-grid">
            <div class="form-group-inline">
                <label for="mes_inicio">Fecha Inicio</label>
                <input type="date" id="mes_inicio" name="mes_inicio" value="{{ $mesInicio }}" required>
            </div>
            <div class="form-group-inline">
                <label for="mes_fin">Fecha Fin</label>
                <input type="date" id="mes_fin" name="mes_fin" value="{{ $mesFin }}" required>
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-filter"></i>
                Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Estadísticas del Período -->
<div class="stats-grid-ventas">
    <div class="stat-card-ventas">
        <h3>Total DTEs</h3>
        <div class="value">{{ number_format($totalDTEs, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card-ventas" style="border-left-color: #10b981;">
        <h3>Monto Neto</h3>
        <div class="value">${{ number_format($montoNeto, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card-ventas" style="border-left-color: #f59e0b;">
        <h3>Monto IVA</h3>
        <div class="value">${{ number_format($montoIVA, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card-ventas" style="border-left-color: #3b82f6;">
        <h3>Monto Total</h3>
        <div class="value">${{ number_format($montoTotal, 0, ',', '.') }}</div>
    </div>
</div>

<!-- Tabla de DTEs -->
<div class="table-container">
    <div class="table-header">
        <h2>
            <i class="fas fa-file-invoice"></i>
            Detalle de Documentos ({{ $totalDTEs }})
        </h2>
        @if($totalDTEs > 0)
        <form action="{{ route('dte.descargar-libro-ventas') }}" method="POST" style="display: inline;">
            @csrf
            <input type="hidden" name="mes_inicio" value="{{ $mesInicio }}">
            <input type="hidden" name="mes_fin" value="{{ $mesFin }}">
            <button type="submit" class="btn-download">
                <i class="fas fa-download"></i>
                Descargar IECV (CSV)
            </button>
        </form>
        @endif
    </div>

    <div class="table-responsive">
        @if($totalDTEs > 0)
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>RUT Receptor</th>
                    <th>Razón Social</th>
                    <th style="text-align: right;">Neto</th>
                    <th style="text-align: right;">IVA</th>
                    <th style="text-align: right;">Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dtes as $dte)
                @php
                    $neto = round($dte->total / 1.19);
                    $iva = round($dte->total - $neto);
                @endphp
                <tr>
                    <td>
                        <span class="badge badge-{{ $dte->tipo_dte ?? 39 }}">
                            {{ $dte->tipo_dte == 39 ? 'Boleta (39)' : ($dte->tipo_dte == 61 ? 'NC (61)' : 'Tipo ' . $dte->tipo_dte) }}
                        </span>
                    </td>
                    <td><strong>{{ $dte->folio_sii }}</strong></td>
                    <td>{{ $dte->fecha_emision_dte->format('d/m/Y') }}</td>
                    <td>{{ $dte->socio->rut ?? '66666666-6' }}</td>
                    <td>{{ $dte->socio->nombre_completo ?? 'Cliente Final' }}</td>
                    <td style="text-align: right;">${{ number_format($neto, 0, ',', '.') }}</td>
                    <td style="text-align: right;">${{ number_format($iva, 0, ',', '.') }}</td>
                    <td style="text-align: right;"><strong>${{ number_format($dte->total, 0, ',', '.') }}</strong></td>
                    <td>{!! $dte->estado_dte_badge !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-inbox" style="font-size: 64px; color: #d1d5db; margin-bottom: 16px;"></i>
            <h3 style="color: #6b7280; margin-bottom: 8px;">No hay DTEs en este período</h3>
            <p style="color: #9ca3af;">Selecciona otro rango de fechas para ver documentos.</p>
        </div>
        @endif
    </div>
</div>

<!-- Información sobre IECV -->
<div class="card" style="margin-top: 32px; padding: 24px; background: linear-gradient(135deg, #ede9fe, #ddd6fe); border: none;">
    <h3 style="margin-bottom: 12px; color: #5b21b6;">
        <i class="fas fa-info-circle"></i>
        ¿Cómo usar el archivo IECV?
    </h3>
    <ol style="color: #6b21a8; line-height: 1.8;">
        <li>Descarga el archivo CSV haciendo clic en el botón "Descargar IECV"</li>
        <li>Ingresa al <a href="https://maullin.sii.cl" target="_blank" style="color: #7c3aed; font-weight: 600;">sitio web del SII</a></li>
        <li>Ve a <strong>Facturación Electrónica → Libro de Ventas</strong></li>
        <li>Sube el archivo CSV descargado</li>
        <li>Verifica que los datos sean correctos y confirma</li>
    </ol>
    <p style="margin-top: 16px; padding: 12px; background: rgba(255,255,255,0.7); border-radius: 6px; color: #5b21b6;">
        <i class="fas fa-lightbulb"></i>
        <strong>Nota:</strong> El archivo está en formato compatible con el SII (separado por punto y coma, codificación ISO-8859-1).
    </p>
</div>
@endsection
