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
    <h2 class="page-title">
        <i class="fas fa-chart-line"></i>
        Dashboard de Facturación Electrónica
    </h2>
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
});
</script>
@endpush
