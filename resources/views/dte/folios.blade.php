@extends('layouts.app')

@section('title', 'Gestión de Folios DTE')

@section('styles')
<style>
.folios-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.folio-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 24px;
    border: 1px solid #e5e7eb;
    position: relative;
    overflow: hidden;
}

.folio-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #7c3aed, #5b21b6);
}

.folio-card.warning::before {
    background: linear-gradient(180deg, #f59e0b, #d97706);
}

.folio-card.danger::before {
    background: linear-gradient(180deg, #ef4444, #dc2626);
}

.folio-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.folio-title {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.folio-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.folio-icon.primary {
    background: linear-gradient(135deg, #7c3aed, #5b21b6);
    color: white;
}

.folio-icon.warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.folio-icon.danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.folio-value {
    font-size: 32px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 8px;
}

.folio-description {
    font-size: 14px;
    color: #6b7280;
}

.alert-folios {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-left: 4px solid #f59e0b;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-folios-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border-left: 4px solid #ef4444;
}

.alert-folios i {
    font-size: 24px;
    color: #f59e0b;
}

.alert-folios-danger i {
    color: #ef4444;
}

.alert-folios-content {
    flex: 1;
}

.alert-folios-title {
    font-weight: 600;
    color: #92400e;
    margin-bottom: 4px;
}

.alert-folios-danger .alert-folios-title {
    color: #7f1d1d;
}

.alert-folios-message {
    color: #78350f;
    font-size: 14px;
}

.alert-folios-danger .alert-folios-message {
    color: #991b1b;
}

.btn-solicitar-folios {
    background: linear-gradient(135deg, #7c3aed, #5b21b6);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-solicitar-folios:hover {
    background: linear-gradient(135deg, #6d28d9, #4c1d95);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.historial-table {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.historial-table-header {
    background: linear-gradient(135deg, #7c3aed, #5b21b6);
    color: white;
    padding: 16px 24px;
    font-weight: 600;
    font-size: 16px;
}

.table-responsive {
    overflow-x: auto;
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

.badge-estado {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-emitida {
    background: #dbeafe;
    color: #1e40af;
}

.badge-aceptada {
    background: #d1fae5;
    color: #065f46;
}

.badge-rechazada {
    background: #fee2e2;
    color: #991b1b;
}

.badge-anulada {
    background: #f3f4f6;
    color: #4b5563;
}
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-list-ol"></i>
        Gestión de Folios DTE
    </h1>
    <div class="header-actions">
        <a href="{{ route('dte.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver al Dashboard
        </a>
    </div>
</div>

@if($errorConexion)
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            <strong>Error de conexión con LibreDTE</strong>
            <p>No se pudo obtener información de folios disponibles. {{ $foliosData['error'] ?? 'Error desconocido' }}</p>
        </div>
    </div>
@endif

@if(!$errorConexion && $foliosData && $foliosData['alerta'])
    @if($foliosData['disponibles'] <= 10)
        <div class="alert-folios alert-folios-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="alert-folios-content">
                <div class="alert-folios-title">⚠️ Folios Críticos - Acción Urgente Requerida</div>
                <div class="alert-folios-message">
                    Solo quedan <strong>{{ $foliosData['disponibles'] }}</strong> folios disponibles. Debe solicitar nuevos folios en el SII de inmediato.
                </div>
            </div>
            <a href="https://maullin.sii.cl/cvc_cgi/dte/of_solicita_folios" target="_blank" class="btn-solicitar-folios">
                <i class="fas fa-external-link-alt"></i>
                Solicitar Folios SII
            </a>
        </div>
    @else
        <div class="alert-folios">
            <i class="fas fa-info-circle"></i>
            <div class="alert-folios-content">
                <div class="alert-folios-title">⚠️ Advertencia: Folios Bajos</div>
                <div class="alert-folios-message">
                    Quedan <strong>{{ $foliosData['disponibles'] }}</strong> folios disponibles. Se recomienda solicitar nuevos folios pronto.
                </div>
            </div>
            <a href="https://maullin.sii.cl/cvc_cgi/dte/of_solicita_folios" target="_blank" class="btn-solicitar-folios">
                <i class="fas fa-external-link-alt"></i>
                Solicitar Folios SII
            </a>
        </div>
    @endif
@endif

<div class="folios-grid">
    <!-- Card 1: Folios Disponibles -->
    <div class="folio-card {{ !$errorConexion && $foliosData && $foliosData['disponibles'] <= 10 ? 'danger' : (!$errorConexion && $foliosData && $foliosData['alerta'] ? 'warning' : '') }}">
        <div class="folio-header">
            <span class="folio-title">Folios Disponibles</span>
            <div class="folio-icon {{ !$errorConexion && $foliosData && $foliosData['disponibles'] <= 10 ? 'danger' : (!$errorConexion && $foliosData && $foliosData['alerta'] ? 'warning' : 'primary') }}">
                <i class="fas fa-list-ol"></i>
            </div>
        </div>
        <div class="folio-value">
            {{ !$errorConexion && $foliosData && $foliosData['disponibles'] !== null ? number_format($foliosData['disponibles'], 0, ',', '.') : 'N/A' }}
        </div>
        <div class="folio-description">En LibreDTE - Boleta Electrónica (39)</div>
    </div>

    <!-- Card 2: Próximo Folio -->
    <div class="folio-card">
        <div class="folio-header">
            <span class="folio-title">Próximo Folio</span>
            <div class="folio-icon primary">
                <i class="fas fa-arrow-right"></i>
            </div>
        </div>
        <div class="folio-value">
            {{ !$errorConexion && $foliosData && $foliosData['siguiente'] ? $foliosData['siguiente'] : 'N/A' }}
        </div>
        <div class="folio-description">Siguiente DTE a emitir</div>
    </div>

    <!-- Card 3: Folios Usados Total -->
    <div class="folio-card">
        <div class="folio-header">
            <span class="folio-title">Total Folios Usados</span>
            <div class="folio-icon primary">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="folio-value">{{ number_format($totalFoliosUsados, 0, ',', '.') }}</div>
        <div class="folio-description">DTEs emitidos históricamente</div>
    </div>

    <!-- Card 4: Folios Usados Este Mes -->
    <div class="folio-card">
        <div class="folio-header">
            <span class="folio-title">Folios Este Mes</span>
            <div class="folio-icon primary">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
        <div class="folio-value">{{ number_format($foliosUsadosEsteMes, 0, ',', '.') }}</div>
        <div class="folio-description">DTEs emitidos en {{ now()->format('F Y') }}</div>
    </div>
</div>

<!-- Historial de Folios -->
<div class="historial-table">
    <div class="historial-table-header">
        <i class="fas fa-history"></i>
        Historial de Folios (Últimos 30 DTEs Emitidos)
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Folio SII</th>
                    <th>Tipo DTE</th>
                    <th>Boleta N°</th>
                    <th>Socio</th>
                    <th>Monto</th>
                    <th>Fecha Emisión</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historialFolios as $boleta)
                    <tr>
                        <td><strong>{{ $boleta->folio_sii }}</strong></td>
                        <td>
                            @if($boleta->tipo_dte == 39)
                                <span class="badge badge-info">Boleta (39)</span>
                            @elseif($boleta->tipo_dte == 61)
                                <span class="badge badge-warning">NC (61)</span>
                            @else
                                <span class="badge badge-secondary">Tipo {{ $boleta->tipo_dte }}</span>
                            @endif
                        </td>
                        <td>{{ $boleta->numero_boleta }}</td>
                        <td>{{ $boleta->socio->nombre_completo ?? 'N/A' }}</td>
                        <td>${{ number_format($boleta->total, 0, ',', '.') }}</td>
                        <td>{{ $boleta->fecha_emision_dte ? $boleta->fecha_emision_dte->format('d/m/Y H:i') : 'N/A' }}</td>
                        <td>
                            @if($boleta->estado_dte == 'emitida')
                                <span class="badge-estado badge-emitida">Emitida</span>
                            @elseif($boleta->estado_dte == 'aceptada')
                                <span class="badge-estado badge-aceptada">Aceptada</span>
                            @elseif($boleta->estado_dte == 'rechazada')
                                <span class="badge-estado badge-rechazada">Rechazada</span>
                            @elseif($boleta->estado_dte == 'anulada')
                                <span class="badge-estado badge-anulada">Anulada</span>
                            @else
                                <span class="badge-estado">{{ ucfirst($boleta->estado_dte ?? 'Desconocido') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px; color: #6b7280;">
                            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                            No hay DTEs emitidos aún
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($ultimoFolioEmitido)
    <div class="card" style="margin-top: 24px; padding: 16px; background: #f9fafb; border: 1px solid #e5e7eb;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-info-circle" style="color: #7c3aed; font-size: 20px;"></i>
            <div>
                <strong>Último Folio Emitido:</strong> {{ $ultimoFolioEmitido->folio_sii }}
                ({{ $ultimoFolioEmitido->fecha_emision_dte->format('d/m/Y H:i') }})
            </div>
        </div>
    </div>
@endif

<div class="card" style="margin-top: 24px; padding: 24px; background: linear-gradient(135deg, #ede9fe, #ddd6fe); border: none;">
    <h3 style="margin-bottom: 12px; color: #5b21b6;">
        <i class="fas fa-question-circle"></i>
        ¿Cómo solicitar folios en el SII?
    </h3>
    <ol style="color: #6b21a8; line-height: 1.8;">
        <li>Ingresa al sitio web del SII con tu RUT y clave tributaria</li>
        <li>Ve a <strong>Facturación Electrónica → Solicitud de Folios</strong></li>
        <li>Selecciona <strong>Boleta Electrónica (Tipo 39)</strong></li>
        <li>Solicita la cantidad de folios necesarios (recomendado: 500-1000)</li>
        <li>Descarga el archivo <strong>.xml</strong> y súbelo a LibreDTE</li>
    </ol>
    <a href="https://maullin.sii.cl/cvc_cgi/dte/of_solicita_folios" target="_blank" class="btn-solicitar-folios" style="margin-top: 16px;">
        <i class="fas fa-external-link-alt"></i>
        Ir al SII para Solicitar Folios
    </a>
</div>
@endsection
