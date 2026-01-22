@extends('layouts.app')

@section('title', 'Detalle Rendicion Mensual - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Rendicion {{ $rendicion->codigo_rendicion }}
    </h2>
    <div class="header-actions">
        @if($rendicion->estado == 'abierto')
            <a href="{{ route('rendiciones-mensuales.edit', $rendicion->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        @endif
        <a href="{{ route('rendiciones-mensuales.exportar-pdf', $rendicion->id) }}" class="btn btn-success">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="{{ route('rendiciones-mensuales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

<!-- KPIs Principales -->
<div class="kpi-grid">
    <div class="kpi-card bg-blue">
        <div class="kpi-icon"><i class="fas fa-wallet"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Saldo Anterior</div>
            <div class="kpi-value">{{ $rendicion->saldo_anterior_formateado }}</div>
        </div>
    </div>

    <div class="kpi-card bg-green">
        <div class="kpi-icon"><i class="fas fa-arrow-down"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Total Ingresos</div>
            <div class="kpi-value">{{ $rendicion->total_ingresos_formateado }}</div>
        </div>
    </div>

    <div class="kpi-card bg-red">
        <div class="kpi-icon"><i class="fas fa-arrow-up"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Total Egresos</div>
            <div class="kpi-value">{{ $rendicion->total_egresos_formateado }}</div>
        </div>
    </div>

    <div class="kpi-card {{ $rendicion->es_deficit ? 'bg-danger' : 'bg-primary' }}">
        <div class="kpi-icon"><i class="fas fa-calculator"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Saldo Final</div>
            <div class="kpi-value">{{ $rendicion->saldo_final_formateado }}</div>
        </div>
    </div>
</div>

<!-- Info General -->
<div class="card mb-3">
    <div class="card-header"><h3><i class="fas fa-info-circle"></i> Informacion General</h3></div>
    <div class="card-body">
        <div class="info-grid">
            <div><strong>Periodo:</strong> {{ $rendicion->periodo_texto }}</div>
            <div><strong>Estado:</strong> {!! $rendicion->estado_badge !!}</div>
            <div><strong>Creado:</strong> {{ $rendicion->fecha_creacion_formateada }}</div>
            @if($rendicion->responsable)
                <div><strong>Responsable:</strong> {{ $rendicion->responsable->name }}</div>
            @endif
            @if($rendicion->estado == 'cerrado')
                <div><strong>Cerrado:</strong> {{ $rendicion->fecha_cierre_formateada }}</div>
                @if($rendicion->usuarioCierre)
                    <div><strong>Cerrado por:</strong> {{ $rendicion->usuarioCierre->name }}</div>
                @endif
            @endif
        </div>
    </div>
</div>

<div class="row-grid">
    <!-- Desglose Ingresos -->
    <div class="card">
        <div class="card-header bg-success-light"><h3><i class="fas fa-arrow-down"></i> Desglose de Ingresos</h3></div>
        <div class="card-body">
            <table class="detail-table">
                <tr>
                    <td>Consumo de Agua</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->ingresos_consumo_agua, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_ingresos['consumo_agua'] }}%</td>
                </tr>
                <tr>
                    <td>Subsidios</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->ingresos_subsidios, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_ingresos['subsidios'] }}%</td>
                </tr>
                <tr>
                    <td>Aportes de Socios</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->ingresos_aportes_socios, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_ingresos['aportes'] }}%</td>
                </tr>
                <tr>
                    <td>Multas</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->ingresos_multas, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_ingresos['multas'] }}%</td>
                </tr>
                <tr>
                    <td>Incorporaciones</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->ingresos_incorporaciones, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_ingresos['incorporaciones'] }}%</td>
                </tr>
                <tr>
                    <td>Otros Ingresos</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->ingresos_otros, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_ingresos['otros'] }}%</td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="text-right text-success"><strong>{{ $rendicion->total_ingresos_formateado }}</strong></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Desglose Egresos -->
    <div class="card">
        <div class="card-header bg-danger-light"><h3><i class="fas fa-arrow-up"></i> Desglose de Egresos</h3></div>
        <div class="card-body">
            <table class="detail-table">
                <tr>
                    <td>Energia Electrica</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->egresos_energia_electrica, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_egresos['energia'] }}%</td>
                </tr>
                <tr>
                    <td>Productos Quimicos</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->egresos_productos_quimicos, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_egresos['quimicos'] }}%</td>
                </tr>
                <tr>
                    <td>Reparaciones</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->egresos_reparaciones, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_egresos['reparaciones'] }}%</td>
                </tr>
                <tr>
                    <td>Remuneraciones</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->egresos_remuneraciones, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_egresos['remuneraciones'] }}%</td>
                </tr>
                <tr>
                    <td>Gastos Administrativos</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->egresos_gastos_administrativos, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_egresos['administrativos'] }}%</td>
                </tr>
                <tr>
                    <td>Otros Egresos</td>
                    <td class="text-right"><strong>${{ number_format($rendicion->egresos_otros, 0, ',', '.') }}</strong></td>
                    <td class="text-muted">{{ $rendicion->porcentaje_egresos['otros'] }}%</td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="text-right text-danger"><strong>{{ $rendicion->total_egresos_formateado }}</strong></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</div>

@if($rendicion->observaciones)
<div class="card mb-3">
    <div class="card-header"><h3><i class="fas fa-comment"></i> Observaciones</h3></div>
    <div class="card-body">{{ $rendicion->observaciones }}</div>
</div>
@endif

<!-- Acciones -->
<div class="card">
    <div class="card-header"><h3><i class="fas fa-cog"></i> Acciones</h3></div>
    <div class="card-body">
        @if($rendicion->estado == 'abierto')
            <button type="button" class="btn btn-success" onclick="abrirModalCerrar()">
                <i class="fas fa-lock"></i> Cerrar Mes
            </button>
            <p class="text-muted mt-2">Al cerrar el mes, no se podra editar esta rendicion.</p>
        @else
            <form action="{{ route('rendiciones-mensuales.reabrir-mes', $rendicion->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Esta seguro de reabrir este mes?');">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-unlock"></i> Reabrir Mes
                </button>
            </form>
            @if($rendicion->notas_cierre)
                <div class="alert alert-info mt-3">
                    <strong>Notas de Cierre:</strong> {{ $rendicion->notas_cierre }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Modal Cerrar Mes -->
<div id="modalCerrar" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-lock"></i> Cerrar Mes</h3>
            <button type="button" class="close-modal" onclick="cerrarModal()">&times;</button>
        </div>
        <form action="{{ route('rendiciones-mensuales.cerrar-mes', $rendicion->id) }}" method="POST">
            @csrf
            <div class="modal-body">
                <p>Esta seguro de cerrar la rendicion de <strong>{{ $rendicion->periodo_texto }}</strong>?</p>
                <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Una vez cerrada, no se podra editar.</p>
                <div class="form-group">
                    <label for="notas_cierre">Notas de Cierre (opcional)</label>
                    <textarea name="notas_cierre" id="notas_cierre" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-lock"></i> Confirmar Cierre</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.page-title{font-size:1.75rem;font-weight:700;color:var(--dark);display:flex;align-items:center;gap:12px;margin:0}
.page-title i{color:var(--primary)}
.header-actions{display:flex;gap:8px;flex-wrap:wrap}
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:24px}
.kpi-card{background:white;border-radius:var(--radius);box-shadow:var(--shadow);padding:20px;display:flex;align-items:center;gap:16px}
.kpi-icon{width:64px;height:64px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:28px;color:white}
.bg-blue .kpi-icon{background:linear-gradient(135deg,#3b82f6,#2563eb)}
.bg-green .kpi-icon{background:linear-gradient(135deg,#10b981,#059669)}
.bg-red .kpi-icon{background:linear-gradient(135deg,#ef4444,#dc2626)}
.bg-primary .kpi-icon{background:linear-gradient(135deg,var(--primary),var(--primary-dark))}
.bg-danger .kpi-icon{background:linear-gradient(135deg,#dc2626,#991b1b)}
.kpi-label{font-size:0.875rem;color:var(--gray-600);margin-bottom:4px}
.kpi-value{font-size:1.5rem;font-weight:700;color:var(--dark)}
.card{background:white;border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--gray-200)}
.card-header{padding:16px 24px;border-bottom:1px solid var(--gray-200);background:var(--gray-50)}
.card-header h3{margin:0;font-size:1.125rem;font-weight:600;display:flex;align-items:center;gap:8px}
.card-body{padding:24px}
.mb-3{margin-bottom:20px}
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px}
.row-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:20px;margin-bottom:20px}
.bg-success-light{background:#d1fae5!important}
.bg-danger-light{background:#fee2e2!important}
.detail-table{width:100%;border-collapse:collapse}
.detail-table td{padding:12px 8px;border-bottom:1px solid var(--gray-200)}
.detail-table .total-row{border-top:2px solid var(--dark);font-size:1.125rem}
.text-right{text-align:right}
.text-muted{color:var(--gray-500);font-size:0.875rem}
.text-success{color:#059669}
.text-danger{color:#dc2626}
.text-warning{color:#d97706}
.btn{padding:10px 20px;border-radius:var(--radius);border:none;font-weight:600;font-size:0.95rem;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:8px;text-decoration:none}
.btn-primary{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white}
.btn-success{background:linear-gradient(135deg,#10b981,#059669);color:white}
.btn-warning{background:linear-gradient(135deg,#f59e0b,#d97706);color:white}
.btn-secondary{background:var(--gray-200);color:var(--gray-700)}
.btn:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
.alert{padding:16px 20px;border-radius:var(--radius);margin-bottom:24px;display:flex;align-items:center;gap:12px;font-weight:500}
.alert-success{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
.alert-danger{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
.alert-info{background:#dbeafe;color:#1e40af;border:1px solid #93c5fd}
.badge{padding:4px 12px;border-radius:12px;font-size:.75rem;font-weight:600;text-transform:uppercase;display:inline-block}
.badge-info{background:#cffafe;color:#155e75}
.badge-success{background:#d1fae5;color:#065f46}
.modal{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center}
.modal-content{background:white;border-radius:var(--radius);max-width:500px;width:90%;box-shadow:var(--shadow-lg)}
.modal-header{padding:20px 24px;border-bottom:1px solid var(--gray-200);display:flex;justify-content:space-between;align-items:center}
.modal-header h3{margin:0;display:flex;align-items:center;gap:8px}
.close-modal{background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--gray-600)}
.modal-body{padding:24px}
.modal-footer{padding:16px 24px;border-top:1px solid var(--gray-200);display:flex;gap:8px;justify-content:flex-end}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-weight:600;margin-bottom:8px;font-size:0.875rem}
.form-control{width:100%;padding:10px 14px;border:1px solid var(--gray-300);border-radius:var(--radius);font-size:0.875rem}
.mt-2{margin-top:8px}
.mt-3{margin-top:16px}
@media (max-width:768px){.row-grid{grid-template-columns:1fr}.header-actions{width:100%}}
</style>
@endsection

@section('scripts')
<script>
function abrirModalCerrar(){document.getElementById('modalCerrar').style.display='flex'}
function cerrarModal(){document.getElementById('modalCerrar').style.display='none'}
</script>
@endsection
