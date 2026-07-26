<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Boleta {{ $boleta->numero_boleta }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 14px; color: #1a1a1a; background: white; }
  .page-container { width: 100%; margin: 0 auto; padding: 35px 45px; }

  .header { border-bottom: 2px solid #1a1a1a; padding-bottom: 14px; margin-bottom: 14px; }
  .header h1 { font-size: 19px; font-weight: 700; }
  .header p { font-size: 13px; color: #555; margin-top: 4px; }
  .folio-box { border: 2px solid #000; border-radius: 4px; padding: 12px 18px; text-align: center; min-width: 240px; max-width: 240px; }
  .folio-label { font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.06em; color: #555; }
  .folio-num { font-size: 19px; font-weight: 700; }
  .folio-sub { font-size: 11px; color: #555; margin-top: 2px; }

  .meta { display: flex; gap: 9px; margin-bottom: 14px; }
  .meta-item { background: #f5f5f5; border: 1.5px solid #ddd; border-radius: 4px; padding: 9px 13px; flex: 1; margin: 0 5px; }
  .meta-item:first-child { margin-left: 0; }
  .meta-item:last-child { margin-right: 0; }
  .meta-item .label { font-size: 11.5px; color: #555; text-transform: uppercase; letter-spacing: 0.04em; }
  .meta-item .value { font-size: 14px; font-weight: 600; }

  .two-col { width: 100%; margin-bottom: 8px; border-collapse: collapse; }
  .two-col td { width: 50%; vertical-align: top; padding: 0 4px; }
  .two-col td:first-child { padding-left: 0; }
  .two-col td:last-child { padding-right: 0; }

  .section { border: 1.5px solid #ddd; border-radius: 6px; padding: 13px 16px; page-break-inside: avoid; }
  .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #666; margin-bottom: 8px; border-bottom: 0.5px solid #eee; padding-bottom: 6px; }
  .row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13.5px; }
  .muted { color: #666; }
  .bold { font-weight: 600; }
  .total { font-size: 16px; font-weight: 700; border-top: 1.5px solid #1a1a1a; padding-top: 8px; margin-top: 7px; }

  .bar-label { display: flex; justify-content: space-between; font-size: 11.5px; color: #666; margin: 8px 0 4px; }
  .bar-track { height: 10px; background: #f0f0f0; border-radius: 3px; overflow: hidden; }
  .bar-fill { height: 100%; background: #1D9E75; border-radius: 3px; }

  .badge { border-radius: 4px; padding: 4px 10px; font-size: 13px; font-weight: 600; display: inline-block; background: #FAEEDA; color: #633806; }

  /* Gráfico SVG */
  .chart-wrap { border: 1.5px solid #ddd; border-radius: 6px; padding: 14px 16px; margin-bottom: 14px; }
  .chart-legend { display: flex; gap: 20px; margin-top: 10px; margin-left: 34px; }
  .legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #666; }
  .legend-dot { width: 13px; height: 13px; border-radius: 2px; flex-shrink: 0; }

  .pago-info { display: flex; gap: 11px; margin-bottom: 14px; }
  .pago-box { flex: 1; border: 1.5px solid #ddd; border-radius: 6px; padding: 11px 15px; }
  .pago-icon { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #666; margin-bottom: 6px; }
  .pago-box p { font-size: 13px; line-height: 1.6; }

  .timbre-section { border: 2px dashed #999; border-radius: 6px; padding: 14px 18px; margin-bottom: 14px; display: flex; gap: 18px; align-items: center; }
  .t-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #555; margin-bottom: 8px; }
  .t-row { display: flex; gap: 20px; flex-wrap: wrap; }
  .t-label { font-size: 11px; color: #666; }
  .t-val { font-size: 13px; font-weight: 600; font-family: 'Courier New', monospace; }
  .t-legal { font-size: 11px; color: #666; margin-top: 8px; }

  .footer { display: flex; justify-content: space-between; align-items: center; border-top: 0.5px solid #ddd; padding-top: 10px; font-size: 11.5px; color: #666; }
  .sii-badge { border: 0.5px solid #ccc; border-radius: 3px; padding: 4px 10px; font-size: 11.5px; }
</style>
</head>
<body>
@php
    $organizacion = $boleta->socio->organizacion;

    // Variables por defecto
    $historialConsumo = $historialConsumo ?? collect();
    $ultimoPago = $ultimoPago ?? null;
    $boletasPendientes = $boletasPendientes ?? collect();
    $totalAdeudado = $totalAdeudado ?? 0;
    $mesesAdeudados = $mesesAdeudados ?? 0;

    // Calcular consumo máximo para la barra (basado en consumo actual con margen del 20%)
    $consumoMaximo = max(ceil($boleta->consumo_m3 * 1.2), 10);
    $porcentajeConsumo = min(100, ($boleta->consumo_m3 / $consumoMaximo) * 100);

    // Calcular promedio para el gráfico
    $promedioConsumo = $historialConsumo->count() > 0 ? $historialConsumo->avg('consumo') : 0;

    // Calcular valores correctos si están en 0 (por inconsistencias en BD)
    $montoConsumoReal = $boleta->monto_consumo > 0 ? $boleta->monto_consumo : $boleta->cargo_consumo;
    $subtotalReal = $boleta->subtotal > 0 ? $boleta->subtotal : ($boleta->cargo_consumo + $boleta->cargo_fijo + $boleta->otros_cargos);
@endphp
<div class="page-container">

<!-- HEADER -->
<div class="header">
  <table style="width: 100%; border-collapse: collapse;">
    <tr>
      <td style="vertical-align: top; width: 65%;">
        <h1>{{ strtoupper($organizacion->nombre_apr) }} — Boleta de Consumo Agua Potable Rural</h1>
        <p>RUT: {{ $organizacion->rut }} &nbsp;|&nbsp; {{ $organizacion->email_contacto ?? 'sistemaapr@gmail.com' }} &nbsp;|&nbsp; Captación, tratamiento y distribución de agua</p>
      </td>
      <td style="vertical-align: top; width: 35%; text-align: right;">
        <div class="folio-box" style="display: inline-block;">
          <div class="folio-label">RUT: {{ $organizacion->rut }}</div>
          <div class="folio-label">BOLETA ELECTRÓNICA N°</div>
          <div class="folio-num">{{ $boleta->numero_boleta }}</div>
        </div>
      </td>
    </tr>
  </table>
</div>

<!-- META -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
  <tr>
    <td style="width: 32%; vertical-align: top; padding: 0 6px 0 0;">
      <div class="meta-item">
        <div class="label">Período facturado</div>
        <div class="value">{{ $boleta->mes_texto ?? \Carbon\Carbon::parse($boleta->mes)->locale('es')->isoFormat('MMMM YYYY') }}</div>
      </div>
    </td>
    <td style="width: 2%; vertical-align: top; padding: 0;"></td>
    <td style="width: 32%; vertical-align: top; padding: 0 3px;">
      <div class="meta-item">
        <div class="label">Fecha emisión</div>
        <div class="value">{{ $boleta->fecha_emision_formateada ?? \Carbon\Carbon::parse($boleta->fecha_emision)->format('d/m/Y') }}</div>
      </div>
    </td>
    <td style="width: 2%; vertical-align: top; padding: 0;"></td>
    <td style="width: 32%; vertical-align: top; padding: 0 0 0 6px;">
      <div class="meta-item">
        <div class="label">Fecha vencimiento</div>
        <div class="value">{{ $boleta->fecha_vencimiento_formateada ?? \Carbon\Carbon::parse($boleta->fecha_vencimiento)->format('d/m/Y') }}</div>
      </div>
    </td>
  </tr>
</table>

<!-- SOCIO + MEDIDOR -->
<table class="two-col"><tr>
  <td><div class="section">
    <div class="section-title">Datos del socio</div>
    <table width="100%" style="border-collapse:collapse; font-size:13.5px;">
      <tr>
        <td style="color:#666; padding:4px 0; width:55%">N° Socio</td>
        <td style="text-align:right; padding:4px 0;">{{ $boleta->socio->numero_socio }}</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">RUT</td>
        <td style="text-align:right; padding:4px 0;">{{ $boleta->socio->rut }}</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Nombre</td>
        <td style="text-align:right; padding:4px 0;">{{ $boleta->socio->nombre }}</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Dirección</td>
        <td style="text-align:right; padding:4px 0;">{{ $boleta->socio->direccion }}</td>
      </tr>
      <tr>
        <td style="color:#666; padding:10px 0 4px 0;">Estado</td>
        <td style="text-align:right; padding:10px 0 4px 0;">
          @if($boleta->estado === 'pagada')
            <span class="badge badge-success">Pagada</span>
          @elseif($boleta->estado === 'vencida')
            <span class="badge badge-danger">Vencida</span>
          @else
            <span class="badge badge-warning">Pendiente</span>
          @endif
        </td>
      </tr>
    </table>
  </div></td>
  <td><div class="section">
    <div class="section-title">Lectura del medidor</div>
    <table width="100%" style="border-collapse:collapse; font-size:13.5px;">
      <tr>
        <td style="color:#666; padding:4px 0; width:55%">Lectura anterior</td>
        <td style="text-align:right; padding:4px 0;">{{ $boleta->lectura ? number_format($boleta->lectura->lectura_anterior, 2, ',', '.') : number_format($boleta->lectura_anterior ?? 0, 2, ',', '.') }} m³</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Lectura actual</td>
        <td style="text-align:right; padding:4px 0;">{{ $boleta->lectura ? number_format($boleta->lectura->lectura_actual, 2, ',', '.') : number_format($boleta->lectura_actual ?? 0, 2, ',', '.') }} m³</td>
      </tr>
      <tr>
        <td style="font-weight:600; padding:4px 0;">Consumo período</td>
        <td style="text-align:right; font-weight:600; padding:4px 0;">{{ number_format($boleta->consumo_m3, 2, ',', '.') }} m³</td>
      </tr>
    </table>
    <div class="bar-label"><span>0 m³</span><span style="font-weight:600;color:#1a1a1a">{{ number_format($boleta->consumo_m3, 0) }} m³ actuales</span><span>{{ $consumoMaximo }} m³ máx.</span></div>
    <div class="bar-track"><div class="bar-fill" style="width:{{ $porcentajeConsumo }}%"></div></div>
  </div></td>
</tr></table>

<!-- GRÁFICO HISTORIAL -->
@if($historialConsumo && $historialConsumo->count() > 0)
<div class="chart-wrap">
  <div class="section-title" style="margin-bottom:8px">Historial de consumo — últimos {{ $historialConsumo->count() }} meses (m³)</div>
  <div style="text-align: center;">
  <svg width="688" height="90" viewBox="0 0 688 90" xmlns="http://www.w3.org/2000/svg" style="display: inline-block;">
    @php
        $consumosArray = $historialConsumo->map(function($item) {
            return is_array($item) ? ($item['consumo_m3'] ?? $item['consumo'] ?? 0) : ($item->consumo_m3 ?? $item->consumo ?? 0);
        })->toArray();

        $maxConsumo = count($consumosArray) > 0 ? max(max($consumosArray), 10) : 18;
        $barWidth = 60;
        $spacing = 12;
        $totalWidth = ($historialConsumo->count() * ($barWidth + $spacing)) - $spacing;
        $startX = 30;
        $baseY = 80;
        $maxHeight = 76;
    @endphp

    <!-- Ejes Y -->
    <text x="20" y="8" font-size="8" fill="#888" text-anchor="end">{{ $maxConsumo }}</text>
    <text x="20" y="30" font-size="8" fill="#888" text-anchor="end">{{ round($maxConsumo * 0.75) }}</text>
    <text x="20" y="52" font-size="8" fill="#888" text-anchor="end">{{ round($maxConsumo * 0.5) }}</text>
    <text x="20" y="74" font-size="8" fill="#888" text-anchor="end">{{ round($maxConsumo * 0.25) }}</text>

    <!-- Líneas guía -->
    <line x1="24" y1="4" x2="688" y2="4" stroke="#eee" stroke-width="0.5"/>
    <line x1="24" y1="26" x2="688" y2="26" stroke="#eee" stroke-width="0.5"/>
    <line x1="24" y1="48" x2="688" y2="48" stroke="#eee" stroke-width="0.5"/>
    <line x1="24" y1="70" x2="688" y2="70" stroke="#eee" stroke-width="0.5"/>
    <line x1="24" y1="80" x2="688" y2="80" stroke="#ccc" stroke-width="0.5"/>

    <!-- Barras de consumo -->
    @foreach($historialConsumo as $index => $item)
        @php
            $x = $startX + ($index * ($barWidth + $spacing));
            $consumo = is_array($item) ? ($item['consumo_m3'] ?? $item['consumo'] ?? 0) : ($item->consumo_m3 ?? $item->consumo ?? 0);
            $barHeight = $consumo > 0 ? ($consumo / $maxConsumo) * $maxHeight : 0;
            $y = $baseY - $barHeight;
            $itemMes = is_array($item) ? ($item['mes'] ?? '') : ($item->mes ?? '');
            $esActual = $itemMes == $boleta->mes;
            $fill = $esActual ? '#1D9E75' : '#9FE1CB';
            $stroke = $esActual ? '#0F6E56' : 'none';
            $strokeWidth = $esActual ? '1.5' : '0';

            $mesTexto = is_array($item) ? ($item['mes_texto'] ?? '') : ($item->mes_texto ?? '');
            $mesTexto = substr($mesTexto, 0, 3);
        @endphp

        <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ $barHeight }}" rx="2" fill="{{ $fill }}" stroke="{{ $stroke }}" stroke-width="{{ $strokeWidth }}"/>
        <text x="{{ $x + ($barWidth / 2) }}" y="{{ $y - 3 }}" font-size="8.5" font-weight="{{ $esActual ? 'bold' : 'normal' }}" fill="{{ $esActual ? '#0F6E56' : '#555' }}" text-anchor="middle">{{ number_format($consumo, 0) }}</text>
        <text x="{{ $x + ($barWidth / 2) }}" y="89" font-size="8" font-weight="{{ $esActual ? 'bold' : 'normal' }}" fill="{{ $esActual ? '#1a1a1a' : '#888' }}" text-anchor="middle">{{ $mesTexto }}{{ $esActual ? ' ★' : '' }}</text>
    @endforeach

    <!-- Línea promedio -->
    @if($promedioConsumo > 0)
        @php
            $promedioY = $baseY - (($promedioConsumo / $maxConsumo) * $maxHeight);
        @endphp
        <line x1="24" y1="{{ $promedioY }}" x2="688" y2="{{ $promedioY }}" stroke="#BA7517" stroke-width="1" stroke-dasharray="4,3"/>
        <text x="688" y="{{ $promedioY - 2 }}" font-size="7.5" fill="#BA7517" text-anchor="end">prom. {{ number_format($promedioConsumo, 1, ',', '.') }} m³</text>
    @endif
  </svg>
  </div>

  <table style="width: auto; margin: 6px auto 0; border-collapse: separate; border-spacing: 14px 0;">
    <tr>
      <td style="vertical-align: middle; padding: 0;">
        <div class="legend-item">
          <div class="legend-dot" style="background:#9FE1CB; display: inline-block; vertical-align: middle; margin-right: 4px;"></div>
          <span style="display: inline-block; vertical-align: middle; font-size: 8.5px; color: #666;">Meses anteriores</span>
        </div>
      </td>
      <td style="vertical-align: middle; padding: 0;">
        <div class="legend-item">
          <div class="legend-dot" style="background:#1D9E75; display: inline-block; vertical-align: middle; margin-right: 4px;"></div>
          <span style="display: inline-block; vertical-align: middle; font-size: 8.5px; color: #666;">Período actual</span>
        </div>
      </td>
      @if($promedioConsumo > 0)
      <td style="vertical-align: middle; padding: 0;">
        <div class="legend-item">
          <div class="legend-dot" style="background:#FAEEDA; border:1px solid #FAC775; display: inline-block; vertical-align: middle; margin-right: 4px;"></div>
          <span style="display: inline-block; vertical-align: middle; font-size: 8.5px; color: #666;">Promedio ({{ number_format($promedioConsumo, 1, ',', '.') }} m³)</span>
        </div>
      </td>
      @endif
    </tr>
  </table>
</div>
@endif

<!-- CARGOS + DEUDA -->
<table class="two-col"><tr>
  <td><div class="section">
    <div class="section-title">Detalle de cargos</div>
    <table width="100%" style="border-collapse:collapse; font-size:13.5px;">
      @if($boleta->detalles && $boleta->detalles->count() > 0)
        @foreach($boleta->detalles as $detalle)
          <tr>
            <td style="color:#666; padding:4px 0; width:55%">{{ $detalle->concepto }}</td>
            <td style="text-align:right; padding:4px 0;">${{ number_format($detalle->monto, 0, ',', '.') }}</td>
          </tr>
        @endforeach
      @else
        <tr>
          <td style="color:#666; padding:4px 0; width:55%">Consumo agua ({{ number_format($boleta->consumo_m3, 0) }} m³)</td>
          <td style="text-align:right; padding:4px 0;">${{ number_format($montoConsumoReal, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td style="color:#666; padding:4px 0;">Cargo fijo mensual</td>
          <td style="text-align:right; padding:4px 0;">${{ number_format($boleta->cargo_fijo, 0, ',', '.') }}</td>
        </tr>
      @endif
      <tr>
        <td style="color:#666; padding:4px 0;">Subtotal</td>
        <td style="text-align:right; padding:4px 0;">${{ number_format($subtotalReal, 0, ',', '.') }}</td>
      </tr>
      @if($boleta->descuentos > 0)
      <tr>
        <td style="color:#10b981; padding:4px 0;">Descuentos</td>
        <td style="text-align:right; padding:4px 0; color:#10b981;">-${{ number_format($boleta->descuentos, 0, ',', '.') }}</td>
      </tr>
      @endif
      <tr>
        <td style="color:#666; padding:4px 0;">IVA (19%)</td>
        <td style="text-align:right; padding:4px 0;">{{ $boleta->iva > 0 ? '$'.number_format($boleta->iva, 0, ',', '.') : 'Exento' }}</td>
      </tr>
      <tr>
        <td style="font-size:16px; font-weight:700; border-top:1.5px solid #1a1a1a; padding-top:8px; padding-bottom:4px; margin-top:7px;">Total a pagar</td>
        <td style="text-align:right; font-size:16px; font-weight:700; border-top:1.5px solid #1a1a1a; padding-top:8px; padding-bottom:4px;">${{ number_format($boleta->total, 0, ',', '.') }}</td>
      </tr>
    </table>
  </div></td>
  <td><div class="section">
    <div class="section-title">Estado de cuenta</div>
    @if($mesesAdeudados > 0)
      <div style="margin-bottom:5px"><span class="badge">{{ $mesesAdeudados }} {{ $mesesAdeudados == 1 ? 'mes pendiente' : 'meses pendientes' }}</span></div>
      @php
        // Mostrar solo los últimos 6 meses
        $boletasMostrar = $boletasPendientes->take(-6);
        $mesesAnteriores = $mesesAdeudados - $boletasMostrar->count();
      @endphp
      <table width="100%" style="border-collapse:collapse; font-size:13.5px;">
        @foreach($boletasMostrar as $boletaPendiente)
          <tr>
            <td style="color:#666; padding:4px 0; width:55%">{{ $boletaPendiente->mes_texto ?? \Carbon\Carbon::parse($boletaPendiente->mes)->locale('es')->isoFormat('MMMM YYYY') }}</td>
            <td style="text-align:right; padding:4px 0;">${{ number_format($boletaPendiente->total, 0, ',', '.') }}</td>
          </tr>
        @endforeach
        @if($mesesAnteriores > 0)
          <tr>
            <td colspan="2" style="color:#888; padding:6px 0; font-size:12px; font-style:italic;">
              * Deuda total incluye {{ $mesesAnteriores }} {{ $mesesAnteriores == 1 ? 'mes anterior' : 'meses anteriores' }}
            </td>
          </tr>
        @endif
        @if($totalAdeudado > 0)
          <tr>
            <td style="font-weight:600; border-top:0.5px solid #eee; padding-top:4px; padding-bottom:4px; margin-top:3px;">Total adeudado</td>
            <td style="text-align:right; font-weight:600; border-top:0.5px solid #eee; padding-top:4px; padding-bottom:4px;">${{ number_format($totalAdeudado, 0, ',', '.') }}</td>
          </tr>
        @endif
      </table>
    @else
      <p style="color:#666;font-size:13px">Sin deudas pendientes</p>
      @if($ultimoPago)
        <table width="100%" style="border-collapse:collapse; font-size:13.5px; margin-top:6px;">
          <tr>
            <td style="color:#666; padding:4px 0; width:55%">Último pago</td>
            <td style="text-align:right; padding:4px 0;">{{ \Carbon\Carbon::parse($ultimoPago->fecha_pago)->format('d/m/Y') }}</td>
          </tr>
          <tr>
            <td style="color:#666; padding:4px 0;">Monto</td>
            <td style="text-align:right; padding:4px 0;">${{ number_format($ultimoPago->monto_pagado, 0, ',', '.') }}</td>
          </tr>
        </table>
      @endif
    @endif
  </div></td>
</tr></table>

<!-- PAGO -->
<table style="width: 100%; margin-bottom: 8px; border-collapse: separate; border-spacing: 8px 0;">
  <tr>
    <td style="width: 50%; vertical-align: top;">
      <div class="pago-box">
        <div class="pago-icon">Pago presencial</div>
        <p>{{ $organizacion->pago_presencial_lugar ?? 'Oficina APR' }} — {{ $organizacion->pago_presencial_dias ?? 'Lunes a Viernes' }}<br>{{ $organizacion->pago_presencial_horario ?? '09:00 a 17:00 hrs' }}</p>
      </div>
    </td>
    <td style="width: 50%; vertical-align: top;">
      <div class="pago-box">
        <div class="pago-icon">Transferencia bancaria</div>
        <p>
          @if($organizacion->banco && $organizacion->numero_cuenta)
            Banco: {{ $organizacion->banco }} &nbsp;|&nbsp; {{ $organizacion->tipo_cuenta ?? 'Cta. Cte.' }}: {{ $organizacion->numero_cuenta }}<br>
            @if($organizacion->titular_cuenta)
              Titular: {{ $organizacion->titular_cuenta }}<br>
            @endif
            RUT: {{ $organizacion->rut }} &nbsp;|&nbsp; {{ $organizacion->email_contacto ?? 'sistemaapr@gmail.com' }}<br>
          @else
            Banco: [Nombre del Banco] &nbsp;|&nbsp; Cta. Cte.: [N° cuenta]<br>
            RUT: {{ $organizacion->rut }} &nbsp;|&nbsp; {{ $organizacion->email_contacto ?? 'sistemaapr@gmail.com' }}<br>
          @endif
          Ref.: <strong>{{ $boleta->numero_boleta }}</strong>
        </p>
      </div>
    </td>
  </tr>
</table>

<!-- TIMBRE SII -->
@if($boleta->tieneFolioSII())
<div class="timbre-section">
  <table style="width: 100%; border-collapse: collapse;">
    <tr>
      <td style="width: 420px; vertical-align: middle; padding-right: 18px;">
        <svg width="400" height="160" viewBox="0 0 400 160" xmlns="http://www.w3.org/2000/svg" style="border: 1px solid #000;">
          <rect width="400" height="160" fill="white"/>
          <!-- Simulación de código de barras PDF417 -->
          <rect x="5" y="5" width="2" height="150" fill="#000"/><rect x="10" y="5" width="3" height="150" fill="#000"/>
          <rect x="16" y="5" width="2" height="150" fill="#000"/><rect x="21" y="5" width="4" height="150" fill="#000"/>
          <rect x="28" y="5" width="2" height="150" fill="#000"/><rect x="33" y="5" width="3" height="150" fill="#000"/>
          <rect x="39" y="5" width="4" height="150" fill="#000"/><rect x="46" y="5" width="2" height="150" fill="#000"/>
          <rect x="51" y="5" width="3" height="150" fill="#000"/><rect x="57" y="5" width="2" height="150" fill="#000"/>
          <rect x="62" y="5" width="4" height="150" fill="#000"/><rect x="69" y="5" width="2" height="150" fill="#000"/>
          <rect x="74" y="5" width="3" height="150" fill="#000"/><rect x="80" y="5" width="4" height="150" fill="#000"/>
          <rect x="87" y="5" width="2" height="150" fill="#000"/><rect x="92" y="5" width="3" height="150" fill="#000"/>
          <rect x="98" y="5" width="2" height="150" fill="#000"/><rect x="103" y="5" width="4" height="150" fill="#000"/>
          <rect x="110" y="5" width="2" height="150" fill="#000"/><rect x="115" y="5" width="3" height="150" fill="#000"/>
          <rect x="121" y="5" width="4" height="150" fill="#000"/><rect x="128" y="5" width="2" height="150" fill="#000"/>
          <rect x="133" y="5" width="3" height="150" fill="#000"/><rect x="139" y="5" width="2" height="150" fill="#000"/>
          <rect x="144" y="5" width="4" height="150" fill="#000"/><rect x="151" y="5" width="2" height="150" fill="#000"/>
          <rect x="156" y="5" width="3" height="150" fill="#000"/><rect x="162" y="5" width="4" height="150" fill="#000"/>
          <rect x="169" y="5" width="2" height="150" fill="#000"/><rect x="174" y="5" width="3" height="150" fill="#000"/>
          <rect x="180" y="5" width="2" height="150" fill="#000"/><rect x="185" y="5" width="4" height="150" fill="#000"/>
          <rect x="192" y="5" width="2" height="150" fill="#000"/><rect x="197" y="5" width="3" height="150" fill="#000"/>
          <rect x="203" y="5" width="4" height="150" fill="#000"/><rect x="210" y="5" width="2" height="150" fill="#000"/>
          <rect x="215" y="5" width="3" height="150" fill="#000"/><rect x="221" y="5" width="2" height="150" fill="#000"/>
          <rect x="226" y="5" width="4" height="150" fill="#000"/><rect x="233" y="5" width="2" height="150" fill="#000"/>
          <rect x="238" y="5" width="3" height="150" fill="#000"/><rect x="244" y="5" width="4" height="150" fill="#000"/>
          <rect x="251" y="5" width="2" height="150" fill="#000"/><rect x="256" y="5" width="3" height="150" fill="#000"/>
          <rect x="262" y="5" width="2" height="150" fill="#000"/><rect x="267" y="5" width="4" height="150" fill="#000"/>
          <rect x="274" y="5" width="2" height="150" fill="#000"/><rect x="279" y="5" width="3" height="150" fill="#000"/>
          <rect x="285" y="5" width="4" height="150" fill="#000"/><rect x="292" y="5" width="2" height="150" fill="#000"/>
          <rect x="297" y="5" width="3" height="150" fill="#000"/><rect x="303" y="5" width="2" height="150" fill="#000"/>
          <rect x="308" y="5" width="4" height="150" fill="#000"/><rect x="315" y="5" width="2" height="150" fill="#000"/>
          <rect x="320" y="5" width="3" height="150" fill="#000"/><rect x="326" y="5" width="4" height="150" fill="#000"/>
          <rect x="333" y="5" width="2" height="150" fill="#000"/><rect x="338" y="5" width="3" height="150" fill="#000"/>
          <rect x="344" y="5" width="2" height="150" fill="#000"/><rect x="349" y="5" width="4" height="150" fill="#000"/>
          <rect x="356" y="5" width="2" height="150" fill="#000"/><rect x="361" y="5" width="3" height="150" fill="#000"/>
          <rect x="367" y="5" width="4" height="150" fill="#000"/><rect x="374" y="5" width="2" height="150" fill="#000"/>
          <rect x="379" y="5" width="3" height="150" fill="#000"/><rect x="385" y="5" width="2" height="150" fill="#000"/>
          <rect x="390" y="5" width="5" height="150" fill="#000"/>
        </svg>
        <div style="text-align: center; margin-top: 5px; font-size: 11px; font-weight: 600;">Timbre Electrónico SII</div>
        <div style="text-align: center; font-size: 10px; color: #666;">Res.86 de 2005 Verifique documento: www.sii.cl</div>
      </td>
      <td style="vertical-align: middle;">
        <div class="t-title">Timbre Electrónico SII — Boleta Electrónica</div>
        <table style="width: 100%; border-collapse: separate; border-spacing: 14px 0; margin-bottom: 5px;">
          <tr>
            <td style="vertical-align: top; padding: 0;">
              <div class="t-label">RUT Emisor</div>
              <div class="t-val">{{ $organizacion->rut }}</div>
            </td>
            <td style="vertical-align: top; padding: 0;">
              <div class="t-label">Tipo DTE</div>
              <div class="t-val">39 — Boleta Electrónica</div>
            </td>
            <td style="vertical-align: top; padding: 0;">
              <div class="t-label">Folio</div>
              <div class="t-val">{{ $boleta->folio_sii ?? substr($boleta->numero_boleta, -4) }}</div>
            </td>
            <td style="vertical-align: top; padding: 0;">
              <div class="t-label">Fecha emisión</div>
              <div class="t-val">{{ \Carbon\Carbon::parse($boleta->fecha_emision)->format('Y-m-d') }}</div>
            </td>
            <td style="vertical-align: top; padding: 0;">
              <div class="t-label">Monto</div>
              <div class="t-val">${{ number_format($boleta->total, 0, ',', '.') }}</div>
            </td>
          </tr>
        </table>
        <div class="t-legal">Resolución Ex. SII N° 45 del 01/09/2003 — Documento válido sin firma ni timbre físico. Verifique en www.sii.cl</div>
      </td>
    </tr>
  </table>
</div>
@endif

<!-- FOOTER -->
<div class="footer">
  <span>
    @if($ultimoPago)
      Último pago: {{ \Carbon\Carbon::parse($ultimoPago->fecha_pago)->format('d/m/Y') }} — ${{ number_format($ultimoPago->monto_pagado, 0, ',', '.') }} — {{ ucfirst($ultimoPago->metodo_pago ?? 'efectivo') }}
    @else
      Generado electrónicamente {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
    @endif
  </span>
  @if($boleta->dte_emitido)
    <span class="sii-badge">SII — DTE Tipo 39</span>
  @endif
</div>

</div><!-- /page-container -->
</body>
</html>
