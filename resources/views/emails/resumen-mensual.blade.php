@extends('emails.layouts.base')

@section('title', 'Resumen Mensual')

@section('email-title')
    📊 Resumen del Mes
@endsection

@section('email-subtitle')
    {{ $mes }} {{ $anio }}
@endsection

@section('extra-styles')
<style>
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 15px;
    }

    .stat-number {
        font-size: 36px;
        font-weight: 700;
        margin: 10px 0;
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin: 20px 0;
    }

    .metric-card {
        background: #f9fafb;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        border: 2px solid #e5e7eb;
    }

    .metric-value {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 10px 0;
    }

    .metric-label {
        font-size: 13px;
        color: #6b7280;
        font-weight: 600;
    }

    .trend-up {
        color: #059669;
    }

    .trend-down {
        color: #dc2626;
    }
</style>
@endsection

@section('content')
    <div class="greeting">
        Hola, {{ $organizacion->nombre_apr }}
    </div>

    <div class="alert-box alert-info">
        <h2>📅 Resumen del Período</h2>
        <p>
            Te presentamos el resumen de actividad de tu organización durante <strong>{{ $mes }} {{ $anio }}</strong>.
        </p>
    </div>

    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 20px; text-align: center;">💰 Resumen Financiero</h3>

        <div class="stat-box">
            <div class="stat-label">Ingresos Totales del Mes</div>
            <div class="stat-number">${{ number_format($stats['ingresos_totales'], 0, ',', '.') }}</div>
            <div class="stat-label">CLP</div>
        </div>

        <div class="metric-grid">
            <div class="metric-card">
                <div class="metric-label">Boletas Emitidas</div>
                <div class="metric-value">{{ $stats['boletas_emitidas'] }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Pagos Recibidos</div>
                <div class="metric-value" style="color: #059669;">{{ $stats['pagos_recibidos'] }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Boletas Pendientes</div>
                <div class="metric-value" style="color: #f59e0b;">{{ $stats['boletas_pendientes'] }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Boletas Vencidas</div>
                <div class="metric-value" style="color: #dc2626;">{{ $stats['boletas_vencidas'] }}</div>
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 20px; text-align: center;">👥 Actividad de Socios</h3>

        <div class="metric-grid">
            <div class="metric-card">
                <div class="metric-label">Total Socios Activos</div>
                <div class="metric-value">{{ $stats['socios_activos'] }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Nuevos Socios</div>
                <div class="metric-value trend-up">+{{ $stats['nuevos_socios'] }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Lecturas Registradas</div>
                <div class="metric-value">{{ $stats['lecturas_registradas'] }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Consumo Promedio</div>
                <div class="metric-value">{{ $stats['consumo_promedio'] }} m³</div>
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 15px;">🔧 Servicio y Mantención</h3>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Incidentes Reportados:</span>
                <span class="info-value">{{ $stats['incidentes_reportados'] ?? 0 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Incidentes Resueltos:</span>
                <span class="info-value" style="color: #059669;">{{ $stats['incidentes_resueltos'] ?? 0 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cortes de Suministro:</span>
                <span class="info-value">{{ $stats['cortes_suministro'] ?? 0 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Trabajos Realizados:</span>
                <span class="info-value">{{ $stats['trabajos_realizados'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    @if(isset($stats['top_consumidores']) && count($stats['top_consumidores']) > 0)
    <div class="content-section">
        <h3 style="color: #1f2937; margin-bottom: 15px;">🏆 Top 5 Mayores Consumos</h3>
        <div style="background: #f9fafb; border-radius: 10px; padding: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                @foreach($stats['top_consumidores'] as $index => $consumidor)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 0; font-weight: 600; color: #6b7280;">{{ $index + 1 }}.</td>
                    <td style="padding: 12px 0; color: #1f2937;">{{ $consumidor['nombre'] }}</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 700; color: #3b82f6;">
                        {{ $consumidor['consumo'] }} m³
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    @endif

    <div class="divider"></div>

    <div class="alert-box alert-success">
        <h2>✨ Comparación con el mes anterior</h2>
        <div style="margin-top: 15px;">
            @if(isset($stats['comparacion']))
                <p style="margin-bottom: 8px;">
                    📈 Ingresos:
                    <strong class="{{ $stats['comparacion']['ingresos_cambio'] >= 0 ? 'trend-up' : 'trend-down' }}">
                        {{ $stats['comparacion']['ingresos_cambio'] >= 0 ? '+' : '' }}{{ $stats['comparacion']['ingresos_cambio'] }}%
                    </strong>
                </p>
                <p style="margin-bottom: 8px;">
                    👥 Socios Activos:
                    <strong class="{{ $stats['comparacion']['socios_cambio'] >= 0 ? 'trend-up' : 'trend-down' }}">
                        {{ $stats['comparacion']['socios_cambio'] >= 0 ? '+' : '' }}{{ $stats['comparacion']['socios_cambio'] }}%
                    </strong>
                </p>
                <p style="margin-bottom: 0;">
                    💧 Consumo Total:
                    <strong class="{{ $stats['comparacion']['consumo_cambio'] >= 0 ? 'trend-up' : 'trend-down' }}">
                        {{ $stats['comparacion']['consumo_cambio'] >= 0 ? '+' : '' }}{{ $stats['comparacion']['consumo_cambio'] }}%
                    </strong>
                </p>
            @else
                <p style="margin: 0;">Este es tu primer mes con datos registrados en el sistema.</p>
            @endif
        </div>
    </div>

    <div class="btn-center">
        <a href="{{ url('/reportes') }}" class="btn btn-primary">
            📊 Ver Reportes Completos
        </a>
    </div>

    <p class="text-muted text-center" style="margin-top: 30px;">
        Este resumen se genera automáticamente el primer día de cada mes con los datos del mes anterior.
    </p>
@endsection

@section('footer-extra')
    <p style="margin-top: 15px; font-size: 13px; color: #6b7280;">
        ¿Quieres dejar de recibir estos resúmenes? Puedes configurarlo en tu panel de notificaciones.
    </p>
@endsection
