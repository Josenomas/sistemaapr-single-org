@extends('layouts.app')

@section('title', 'Dashboard DTE - Estadísticas')

@section('styles')
<style>
.stats-grid-dte {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.stat-card-dte {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 24px;
    transition: all 0.3s;
    border: 1px solid #e5e7eb;
    position: relative;
    overflow: hidden;
}

.stat-card-dte::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #7c3aed, #5b21b6);
}

.stat-card-dte:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.15);
    border-color: #7c3aed;
}

.stat-header-dte {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.stat-title-dte {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 600;
}

.stat-icon-dte {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.primary-bg-dte {
    background: linear-gradient(135deg, #7c3aed, #5b21b6);
}

.success-bg-dte {
    background: linear-gradient(135deg, #10b981, #059669);
}

.info-bg-dte {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.danger-bg-dte {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.stat-value-dte {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: #1f2937;
}

.stat-description-dte {
    font-size: 0.875rem;
    color: #6b7280;
}

.grid-charts {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 32px;
}

.chart-col-large,
.chart-col-small {
    min-width: 0;
}

@media (max-width: 992px) {
    .grid-charts {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .stats-grid-dte {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="header-left">
        <h2 class="page-title">
            <i class="fas fa-chart-line"></i>
            Dashboard de Facturación Electrónica
        </h2>
        @if($config)
            @if($config->ambiente === 'certificacion')
                <div class="ambiente-badge certificacion">
                    <i class="fas fa-flask"></i>
                    <span>Ambiente de Certificación</span>
                    <small>Documentos sin validez tributaria</small>
                </div>
            @else
                <div class="ambiente-badge produccion">
                    <i class="fas fa-shield-alt"></i>
                    <span>Ambiente de Producción</span>
                    <small>Documentos válidos ante el SII</small>
                </div>
            @endif
        @endif
    </div>
    <div class="header-actions">
        <a href="{{ route('dte.configuracion') }}" class="btn btn-secondary">
            <i class="fas fa-cog"></i>
            Configuración
        </a>
        <a href="{{ route('boletas.index') }}" class="btn btn-primary">
            <i class="fas fa-file-invoice"></i>
            Ver Boletas
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

<!-- Cards de Estadísticas -->
<div class="stats-grid-dte">
    <div class="stat-card-dte">
        <div class="stat-header-dte">
            <span class="stat-title-dte">DTEs Emitidos</span>
            <div class="stat-icon-dte primary-bg-dte">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
        <div class="stat-value-dte">{{ number_format($totalDTEsEmitidos, 0, ',', '.') }}</div>
        <div class="stat-description-dte">Total de documentos tributarios</div>
    </div>

    <div class="stat-card-dte" style="border-left-color: {{ $totalBoletasPendientes > 0 ? '#f59e0b' : '#10b981' }};">
        <div class="stat-header-dte">
            <span class="stat-title-dte">Pendientes de Emitir</span>
            <div class="stat-icon-dte" style="background: linear-gradient(135deg, {{ $totalBoletasPendientes > 0 ? '#f59e0b, #d97706' : '#10b981, #059669' }});">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-value-dte">{{ number_format($totalBoletasPendientes, 0, ',', '.') }}</div>
        <div class="stat-description-dte">Boletas sin DTE emitido</div>
    </div>

    <div class="stat-card-dte">
        <div class="stat-header-dte">
            <span class="stat-title-dte">Total Facturado</span>
            <div class="stat-icon-dte success-bg-dte">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <div class="stat-value-dte">${{ number_format($montoTotalFacturado, 0, ',', '.') }}</div>
        <div class="stat-description-dte">Monto total facturado electrónicamente</div>
    </div>

    <div class="stat-card-dte">
        <div class="stat-header-dte">
            <span class="stat-title-dte">Aceptados SII</span>
            <div class="stat-icon-dte info-bg-dte">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value-dte">{{ number_format($dtesPorEstado['aceptada'] ?? 0, 0, ',', '.') }}</div>
        <div class="stat-description-dte">Documentos aceptados por el SII</div>
    </div>

    <div class="stat-card-dte">
        <div class="stat-header-dte">
            <span class="stat-title-dte">LibreDTE</span>
            <div class="stat-icon-dte {{ $conexionLibreDTE ? 'success-bg-dte' : 'danger-bg-dte' }}">
                <i class="fas {{ $conexionLibreDTE ? 'fa-wifi' : 'fa-exclamation-triangle' }}"></i>
            </div>
        </div>
        <div class="stat-value-dte" style="font-size: 1.5rem;">{{ $conexionLibreDTE ? 'Conectado' : 'Desconectado' }}</div>
        <div class="stat-description-dte">Estado de conexión con LibreDTE</div>
    </div>
</div>

<!-- Gráficos -->
<div class="grid-charts">
    <!-- Gráfico de DTEs por Mes -->
    <div class="chart-col-large">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    DTEs Emitidos por Mes (Últimos 12 Meses)
                </h3>
            </div>
            <div class="card-body">
                <canvas id="chartDTEsPorMes" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- DTEs por Estado -->
    <div class="chart-col-small">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    DTEs por Estado
                </h3>
            </div>
            <div class="card-body">
                <canvas id="chartDTEsPorEstado" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Últimos DTEs Emitidos -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Últimos 10 DTEs Emitidos
        </h3>
    </div>
    <div class="card-body">
        @if($ultimosDTEs->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Tipo</th>
                        <th>Socio</th>
                        <th>Fecha Emisión</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimosDTEs as $dte)
                    <tr>
                        <td><strong>{{ $dte->folio_sii }}</strong></td>
                        <td>
                            @if($dte->tipo_dte == 39)
                                <span class="badge badge-primary">Boleta</span>
                            @elseif($dte->tipo_dte == 61)
                                <span class="badge badge-warning">Nota Crédito</span>
                            @else
                                <span class="badge badge-secondary">Tipo {{ $dte->tipo_dte }}</span>
                            @endif
                        </td>
                        <td>{{ $dte->socio->nombre_completo ?? 'N/A' }}</td>
                        <td>{{ $dte->fecha_emision_dte->format('d/m/Y H:i') }}</td>
                        <td>${{ number_format($dte->total, 0, ',', '.') }}</td>
                        <td>{!! $dte->estado_dte_badge !!}</td>
                        <td>
                            @if($dte->pdf_url || $dte->pdf_local_path)
                            <a href="{{ route('dte.descargar-pdf', $dte->id) }}"
                               class="btn btn-sm btn-primary"
                               title="Descargar PDF">
                                <i class="fas fa-download"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            No hay DTEs emitidos aún.
        </div>
        @endif
    </div>
</div>

<!-- Boletas Pendientes de Emitir DTE -->
@if($totalBoletasPendientes > 0)
<div class="card" style="margin-top: 32px;">
    <div class="card-header" style="background: linear-gradient(135deg, #7c3aed, #5b21b6); color: white; padding: 20px; border-radius: 8px 8px 0 0; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-file-invoice" style="font-size: 24px;"></i>
            <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Boletas Pendientes de Emitir DTE ({{ $totalBoletasPendientes }})</h3>
        </div>
        <div id="bulk-actions-dashboard" style="display: none; gap: 8px;">
            <span id="selected-count-dashboard" style="background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                <i class="fas fa-check-circle"></i> 0 seleccionadas
            </span>
            <button type="button" id="bulk-emit-btn-dashboard" class="btn" style="background: white; color: #7c3aed; border: none; font-weight: 600; padding: 8px 16px; border-radius: 6px;">
                <i class="fas fa-file-invoice-dollar"></i>
                Emitir DTEs
            </button>
            <button type="button" id="deselect-all-btn-dashboard" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid white; padding: 8px 16px; border-radius: 6px;">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all-dashboard" style="width: 18px; height: 18px; cursor: pointer;">
                        </th>
                        <th>N° Boleta</th>
                        <th>Socio</th>
                        <th>Mes</th>
                        <th>Fecha Emisión</th>
                        <th>Consumo</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boletasSinDTE as $boleta)
                    <tr>
                        <td>
                            <input type="checkbox" class="boleta-checkbox-dashboard" value="{{ $boleta->id }}" style="width: 18px; height: 18px; cursor: pointer;">
                        </td>
                        <td><strong>{{ $boleta->numero_boleta }}</strong></td>
                        <td>
                            <a href="{{ route('socios.show', $boleta->socio->id) }}">
                                {{ $boleta->socio->numero_socio }} - {{ $boleta->socio->nombre_completo }}
                            </a>
                        </td>
                        <td>{{ $boleta->mes_texto }}</td>
                        <td>{{ $boleta->fecha_emision_formateada }}</td>
                        <td>{{ $boleta->consumo_m3 }} m³</td>
                        <td><strong>${{ number_format($boleta->total, 0, ',', '.') }}</strong></td>
                        <td>{!! $boleta->estado_badge !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($totalBoletasPendientes > 20)
        <div style="margin-top: 16px; text-align: center;">
            <a href="{{ route('boletas.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i>
                Ver todas las {{ number_format($totalBoletasPendientes, 0, ',', '.') }} boletas pendientes
            </a>
        </div>
        @endif
    </div>
</div>
@else
<div class="card" style="margin-top: 32px;">
    <div class="card-body" style="text-align: center; padding: 40px;">
        <i class="fas fa-check-circle" style="font-size: 48px; color: #10b981; margin-bottom: 16px;"></i>
        <h3 style="color: #10b981; margin-bottom: 8px;">¡Excelente!</h3>
        <p style="color: #6b7280;">No hay boletas pendientes de emitir DTE.</p>
        <a href="{{ route('boletas.index') }}" class="btn btn-primary" style="margin-top: 16px;">
            <i class="fas fa-file-invoice"></i>
            Ver todas las boletas
        </a>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de DTEs por Mes
    const ctxMes = document.getElementById('chartDTEsPorMes');
    if (ctxMes) {
        const dtesPorMes = @json($dtesPorMes);
        console.log('DTEs por mes:', dtesPorMes);

        if (dtesPorMes && dtesPorMes.length > 0) {
            const meses = dtesPorMes.map(d => {
                const [año, mes] = d.mes.split('-');
                const fecha = new Date(año, mes - 1);
                return fecha.toLocaleDateString('es-CL', { month: 'short', year: 'numeric' });
            });
            const totales = dtesPorMes.map(d => d.total);

            new Chart(ctxMes, {
                type: 'bar',
                data: {
                    labels: meses,
                    datasets: [{
                        label: 'DTEs Emitidos',
                        data: totales,
                        backgroundColor: 'rgba(124, 58, 237, 0.6)',
                        borderColor: 'rgb(124, 58, 237)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'DTEs: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } else {
            // Mostrar mensaje cuando no hay datos
            ctxMes.parentElement.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No hay datos para mostrar en los últimos 12 meses.</div>';
        }
    }
}

    // Gráfico de DTEs por Estado
    const ctxEstado = document.getElementById('chartDTEsPorEstado');
    if (ctxEstado) {
        const dtesPorEstado = @json($dtesPorEstado);
        console.log('DTEs por estado:', dtesPorEstado);

        const estados = Object.keys(dtesPorEstado);
        const valores = Object.values(dtesPorEstado);

        if (estados.length > 0) {
            const coloresEstado = {
                'pendiente': '#fbbf24',
                'emitida': '#3b82f6',
                'aceptada': '#10b981',
                'rechazada': '#ef4444',
                'anulada': '#6b7280'
            };

            const colores = estados.map(e => coloresEstado[e] || '#9ca3af');

            new Chart(ctxEstado, {
                type: 'doughnut',
                data: {
                    labels: estados.map(e => e.charAt(0).toUpperCase() + e.slice(1)),
                    datasets: [{
                        data: valores,
                        backgroundColor: colores,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            ctxEstado.parentElement.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No hay datos para mostrar.</div>';
        }
    }

    // ========================================
    // EMISIÓN MASIVA DESDE DASHBOARD
    // ========================================

    const selectAllDashboard = document.getElementById('select-all-dashboard');
    const boletaCheckboxesDashboard = document.querySelectorAll('.boleta-checkbox-dashboard');
    const bulkActionsDashboard = document.getElementById('bulk-actions-dashboard');
    const selectedCountDashboard = document.getElementById('selected-count-dashboard');
    const bulkEmitBtnDashboard = document.getElementById('bulk-emit-btn-dashboard');
    const deselectAllBtnDashboard = document.getElementById('deselect-all-btn-dashboard');

    function updateBulkActionsDashboard() {
        const selectedCheckboxes = document.querySelectorAll('.boleta-checkbox-dashboard:checked');
        const count = selectedCheckboxes.length;

        if (selectedCountDashboard) {
            selectedCountDashboard.innerHTML = `<i class="fas fa-check-circle"></i> ${count} seleccionadas`;
        }

        if (bulkActionsDashboard) {
            bulkActionsDashboard.style.display = count > 0 ? 'flex' : 'none';
        }
    }

    // Seleccionar/deseleccionar todas
    if (selectAllDashboard) {
        selectAllDashboard.addEventListener('change', function() {
            boletaCheckboxesDashboard.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsDashboard();
        });
    }

    // Actualizar al seleccionar individual
    boletaCheckboxesDashboard.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionsDashboard();

            const allChecked = Array.from(boletaCheckboxesDashboard).every(cb => cb.checked);
            const someChecked = Array.from(boletaCheckboxesDashboard).some(cb => cb.checked);

            if (selectAllDashboard) {
                selectAllDashboard.checked = allChecked;
                selectAllDashboard.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // Deseleccionar todo
    if (deselectAllBtnDashboard) {
        deselectAllBtnDashboard.addEventListener('click', function() {
            boletaCheckboxesDashboard.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (selectAllDashboard) {
                selectAllDashboard.checked = false;
                selectAllDashboard.indeterminate = false;
            }
            updateBulkActionsDashboard();
        });
    }

    // Emisión masiva
    if (bulkEmitBtnDashboard) {
        bulkEmitBtnDashboard.addEventListener('click', async function() {
            const selectedCheckboxes = document.querySelectorAll('.boleta-checkbox-dashboard:checked');
            const boletaIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (boletaIds.length === 0) {
                alert('No hay boletas seleccionadas');
                return;
            }

            if (!confirm(`¿Está seguro de emitir ${boletaIds.length} DTEs?\n\nEste proceso puede tardar varios minutos.`)) {
                return;
            }

            bulkEmitBtnDashboard.disabled = true;
            bulkEmitBtnDashboard.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

            try {
                const response = await fetch('{{ route("dte.emitir-masivo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        boleta_ids: boletaIds
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(`✅ Proceso completado\n\n` +
                          `Total: ${data.total}\n` +
                          `Éxitos: ${data.exitosos}\n` +
                          `Errores: ${data.errores}`);

                    window.location.reload();
                } else {
                    alert('❌ Error: ' + (data.message || 'No se pudo completar la emisión masiva'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error al procesar la solicitud: ' + error.message);
            } finally {
                bulkEmitBtnDashboard.disabled = false;
                bulkEmitBtnDashboard.innerHTML = '<i class="fas fa-file-invoice-dollar"></i> Emitir DTEs';
            }
        });
    }
});
</script>

<style>
.header-left {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.ambiente-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    width: fit-content;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.ambiente-badge i {
    font-size: 1.1rem;
}

.ambiente-badge span {
    font-weight: 700;
}

.ambiente-badge small {
    opacity: 0.85;
    font-weight: 500;
    font-size: 0.75rem;
    margin-left: 4px;
}

.ambiente-badge.certificacion {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border: 2px solid #fbbf24;
}

.ambiente-badge.produccion {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: 2px solid #34d399;
}

@media (max-width: 768px) {
    .ambiente-badge {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
}
</style>
@endpush
