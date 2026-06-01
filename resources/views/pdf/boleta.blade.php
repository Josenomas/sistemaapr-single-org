<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Boleta {{ $boleta->numero_boleta }}</title>
<style>
  @page {
    size: letter;
    margin: 10mm;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 11px; color: #1a1a1a; background: white; padding: 16px; width: 720px; }

  .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1a1a1a; padding-bottom: 8px; margin-bottom: 8px; }
  .header h1 { font-size: 13px; font-weight: 700; }
  .header p { font-size: 10px; color: #555; margin-top: 2px; }
  .folio-box { border: 2px solid #1a1a1a; border-radius: 4px; padding: 6px 12px; text-align: center; min-width: 185px; }
  .folio-label { font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.06em; color: #555; }
  .folio-num { font-size: 13px; font-weight: 700; }
  .folio-sub { font-size: 8px; color: #555; margin-top: 1px; }

  .meta { display: flex; gap: 6px; margin-bottom: 8px; }
  .meta-item { background: #f5f5f5; border-radius: 4px; padding: 5px 8px; flex: 1; }
  .meta-item .label { font-size: 8.5px; color: #555; text-transform: uppercase; letter-spacing: 0.04em; }
  .meta-item .value { font-size: 11px; font-weight: 600; }

  .two-col { display: flex; gap: 8px; margin-bottom: 8px; }
  .two-col > * { flex: 1; }

  .section { border: 0.5px solid #ddd; border-radius: 6px; padding: 7px 10px; }
  .section-title { font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #666; margin-bottom: 5px; border-bottom: 0.5px solid #eee; padding-bottom: 3px; }
  .row { display: flex; justify-content: space-between; padding: 2px 0; font-size: 10.5px; }
  .muted { color: #666; }
  .bold { font-weight: 600; }
  .total { font-size: 13px; font-weight: 700; border-top: 1.5px solid #1a1a1a; padding-top: 5px; margin-top: 4px; }

  .bar-label { display: flex; justify-content: space-between; font-size: 8.5px; color: #666; margin: 5px 0 2px; }
  .bar-track { height: 7px; background: #f0f0f0; border-radius: 3px; overflow: hidden; }
  .bar-fill { height: 100%; background: #1D9E75; border-radius: 3px; }

  .badge { border-radius: 4px; padding: 2px 7px; font-size: 10px; font-weight: 600; display: inline-block; background: #FAEEDA; color: #633806; }

  /* Gráfico SVG */
  .chart-wrap { border: 0.5px solid #ddd; border-radius: 6px; padding: 8px 10px; margin-bottom: 8px; }
  .chart-legend { display: flex; gap: 14px; margin-top: 6px; margin-left: 28px; }
  .legend-item { display: flex; align-items: center; gap: 4px; font-size: 8.5px; color: #666; }
  .legend-dot { width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0; }

  .pago-info { display: flex; gap: 8px; margin-bottom: 8px; }
  .pago-box { flex: 1; border: 0.5px solid #ddd; border-radius: 6px; padding: 6px 10px; }
  .pago-icon { font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #666; margin-bottom: 3px; }
  .pago-box p { font-size: 10px; line-height: 1.6; }

  .timbre-section { border: 1.5px dashed #aaa; border-radius: 6px; padding: 8px 12px; margin-bottom: 8px; display: flex; gap: 12px; align-items: center; }
  .timbre-qr { width: 68px; height: 68px; border: 0.5px solid #ccc; border-radius: 4px; flex-shrink: 0; }
  .t-title { font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #555; margin-bottom: 5px; }
  .t-row { display: flex; gap: 14px; flex-wrap: wrap; }
  .t-label { font-size: 8px; color: #666; }
  .t-val { font-size: 10px; font-weight: 600; font-family: 'Courier New', monospace; }
  .t-legal { font-size: 8px; color: #666; margin-top: 5px; }

  .footer { display: flex; justify-content: space-between; align-items: center; border-top: 0.5px solid #ddd; padding-top: 6px; font-size: 8.5px; color: #666; }
  .sii-badge { border: 0.5px solid #ccc; border-radius: 3px; padding: 2px 7px; font-size: 8.5px; }
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

    // Calcular consumo máximo histórico del socio para la barra
    $consumoMaximo = 18; // Valor por defecto
    if ($historialConsumo && $historialConsumo->count() > 0) {
        $consumosHistoricos = [];
        foreach($historialConsumo as $item) {
            $c = is_array($item) ? ($item['consumo_m3'] ?? $item['consumo'] ?? 0) : ($item->consumo_m3 ?? $item->consumo ?? 0);
            if ($c > 0) $consumosHistoricos[] = $c;
        }
        if (count($consumosHistoricos) > 0) {
            $consumoMaximo = max($consumosHistoricos);
            // Asegurar mínimo de 10 m³ para que la barra se vea proporcionada
            $consumoMaximo = max($consumoMaximo, 10);
        }
    }
    $porcentajeConsumo = min(100, ($boleta->consumo_m3 / $consumoMaximo) * 100);

    // Calcular tramos
    $tipoCliente = $boleta->socio->tipo_cliente ?? 'domestico';
    $datosConsumo = \App\Models\ConfiguracionTarifa::calcularMontoPorConsumo($tipoCliente, $boleta->consumo_m3);
    $tramosDetalle = $datosConsumo['tramos_detalle'] ?? [];

    // Calcular promedio
    $promedioConsumo = 0;
    if ($historialConsumo && $historialConsumo->count() > 0) {
        $consumos = [];
        foreach($historialConsumo as $item) {
            $c = is_array($item) ? ($item['consumo_m3'] ?? $item['consumo'] ?? 0) : ($item->consumo_m3 ?? $item->consumo ?? 0);
            if ($c > 0) $consumos[] = $c;
        }
        $promedioConsumo = count($consumos) > 0 ? array_sum($consumos) / count($consumos) : 0;
    }
@endphp

<!-- HEADER -->
<div class="header">
  <div>
    <h1>{{ strtoupper($organizacion->nombre_apr) }} — Boleta de Consumo Agua Potable Rural</h1>
    <p>RUT: {{ $organizacion->rut }} &nbsp;|&nbsp; {{ $organizacion->email_contacto ?? 'sistemaapr@gmail.com' }}</p>
  </div>
  <div class="folio-box">
    <div class="folio-label">Boleta Electrónica N°</div>
    <div class="folio-num">{{ $boleta->numero_boleta }}</div>
    @if(method_exists($boleta, 'tieneFolioSII') && $boleta->tieneFolioSII())
    <div class="folio-sub">Resolución SII N° 45 — 01/09/2003</div>
    @endif
  </div>
</div>

<!-- META -->
<div class="meta">
  <div class="meta-item"><div class="label">Período facturado</div><div class="value">{{ $boleta->mes_texto ?? \Carbon\Carbon::parse($boleta->mes)->locale('es')->isoFormat('MMMM YYYY') }}</div></div>
  <div class="meta-item"><div class="label">Fecha emisión</div><div class="value">{{ $boleta->fecha_emision_formateada ?? \Carbon\Carbon::parse($boleta->fecha_emision)->format('d/m/Y') }}</div></div>
  <div class="meta-item"><div class="label">Fecha vencimiento</div><div class="value">{{ $boleta->fecha_vencimiento_formateada ?? \Carbon\Carbon::parse($boleta->fecha_vencimiento)->format('d/m/Y') }}</div></div>
</div>

<!-- SOCIO + MEDIDOR -->
<div class="two-col">
  <div class="section">
    <div class="section-title">Datos del socio</div>
    <div class="row"><span class="muted">N° Socio</span><span>{{ $boleta->socio->numero_socio }}</span></div>
    <div class="row"><span class="muted">RUT</span><span>{{ $boleta->socio->rut }}</span></div>
    <div class="row"><span class="muted">Nombre</span><span style="text-align:right;max-width:150px">{{ $boleta->socio->nombre_completo }}</span></div>
    <div class="row"><span class="muted">Dirección</span><span>{{ $boleta->socio->direccion ?? 'No especificada' }}</span></div>
    <div class="row" style="margin-top:6px"><span class="muted">Estado</span><span><span class="badge">{{ ucfirst($boleta->estado) }}</span></span></div>
  </div>
  <div class="section">
    <div class="section-title">Lectura del medidor</div>
    @if($boleta->lectura)
    <div class="row"><span class="muted">Lectura anterior</span><span>{{ number_format($boleta->lectura->lectura_anterior, 2, ',', '.') }} m³</span></div>
    <div class="row"><span class="muted">Lectura actual</span><span>{{ number_format($boleta->lectura->lectura_actual, 2, ',', '.') }} m³</span></div>
    <div class="row bold"><span>Consumo período</span><span>{{ number_format($boleta->consumo_m3, 2, ',', '.') }} m³</span></div>
    @endif
    <div class="bar-label"><span>0 m³</span><span style="font-weight:600;color:#1a1a1a">{{ number_format($boleta->consumo_m3, 0) }} m³ actuales</span><span>{{ $consumoMaximo }} m³ máx.</span></div>
    <div class="bar-track"><div class="bar-fill" style="width:{{ $porcentajeConsumo }}%"></div></div>
  </div>
</div>

<!-- GRÁFICO BARRAS SVG -->
@if($historialConsumo && $historialConsumo->count() > 0)
<div class="chart-wrap">
  <div class="section-title" style="margin-bottom:8px">Historial de consumo — últimos {{ $historialConsumo->count() }} meses (m³)</div>
  <svg width="688" height="90" viewBox="0 0 688 90" xmlns="http://www.w3.org/2000/svg">
    @php
        $consumosArray = [];
        foreach($historialConsumo as $item) {
            $c = is_array($item) ? ($item['consumo_m3'] ?? $item['consumo'] ?? 0) : ($item->consumo_m3 ?? $item->consumo ?? 0);
            if ($c > 0) $consumosArray[] = $c;
        }
        $maxConsumo = count($consumosArray) > 0 ? max(max($consumosArray), 10) : 18;
        $barWidth = 60;
        $spacing = 12;
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
            $itemMes = is_array($item) ? $item['mes'] : $item->mes;
            $esActual = $itemMes == $boleta->mes;
            $fill = $esActual ? '#1D9E75' : '#9FE1CB';
            $stroke = $esActual ? '#0F6E56' : 'none';
            $strokeWidth = $esActual ? '1.5' : '0';
            $mesTexto = is_array($item) ? ($item['mes_texto'] ?? '') : ($item->mes_texto ?? '');
            $mesTexto = substr($mesTexto, 0, 3);
        @endphp

        <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ $barHeight }}" rx="2" fill="{{ $fill }}" stroke="{{ $stroke }}" stroke-width="{{ $strokeWidth }}"/>
        @if($consumo > 0)
        <text x="{{ $x + ($barWidth / 2) }}" y="{{ $y - 3 }}" font-size="8.5" font-weight="{{ $esActual ? 'bold' : 'normal' }}" fill="{{ $esActual ? '#0F6E56' : '#555' }}" text-anchor="middle">{{ number_format($consumo, 0) }}</text>
        @else
        <text x="{{ $x + ($barWidth / 2) }}" y="78" font-size="8.5" fill="#888" text-anchor="middle">0</text>
        @endif
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

  <div class="chart-legend">
    <div class="legend-item"><div class="legend-dot" style="background:#9FE1CB"></div>Meses anteriores</div>
    <div class="legend-item"><div class="legend-dot" style="background:#1D9E75"></div>Período actual</div>
    @if($promedioConsumo > 0)
    <div class="legend-item"><div class="legend-dot" style="background:#FAEEDA;border:1px solid #FAC775"></div>Promedio ({{ number_format($promedioConsumo, 1, ',', '.') }} m³)</div>
    @endif
  </div>
</div>
@endif

<!-- CARGOS + DEUDA -->
<div class="two-col">
  <div class="section">
    <div class="section-title">Detalle de cargos</div>
    @if(count($tramosDetalle) > 0)
      @foreach($tramosDetalle as $tramo)
      <div class="row"><span class="muted">Consumo agua ({{ number_format($tramo['m3_en_tramo'], 0) }} m³ × ${{ number_format($tramo['valor_unitario'], 0, ',', '.') }})</span><span>${{ number_format($tramo['subtotal'], 0, ',', '.') }}</span></div>
      @endforeach
    @else
    <div class="row"><span class="muted">Consumo agua ({{ number_format($boleta->consumo_m3, 0) }} m³)</span><span>{{ $boleta->cargo_consumo_formateado ?? '$' . number_format($boleta->cargo_consumo, 0, ',', '.') }}</span></div>
    @endif
    <div class="row"><span class="muted">Cargo fijo mensual</span><span>{{ $boleta->cargo_fijo_formateado ?? '$' . number_format($boleta->cargo_fijo, 0, ',', '.') }}</span></div>
    <div class="row"><span class="muted">Subtotal</span><span>${{ number_format($boleta->cargo_consumo + $boleta->cargo_fijo, 0, ',', '.') }}</span></div>
    <div class="row"><span class="muted">IVA (19%)</span><span>{{ ($boleta->socio->exento_iva ?? 0) ? 'Exento' : '$' . number_format(($boleta->cargo_consumo + $boleta->cargo_fijo) * 0.19, 0, ',', '.') }}</span></div>
    <div class="row total"><span>Total a pagar</span><span>${{ number_format($boleta->total, 0, ',', '.') }}</span></div>
  </div>
  <div class="section">
    <div class="section-title">Estado de cuenta</div>
    @if($mesesAdeudados > 0)
    <div style="margin-bottom:5px"><span class="badge">{{ $mesesAdeudados }} {{ $mesesAdeudados == 1 ? 'mes pendiente' : 'meses pendientes' }}</span></div>
    @foreach($boletasPendientes->take(8) as $boletaPendiente)
    <div class="row"><span class="muted">{{ $boletaPendiente->mes_texto ?? \Carbon\Carbon::parse($boletaPendiente->mes)->locale('es')->isoFormat('MMMM YYYY') }}</span><span>${{ number_format($boletaPendiente->total, 0, ',', '.') }}</span></div>
    @endforeach
    @if($boletasPendientes->count() > 8)
    <div class="row"><span class="muted" style="font-style:italic">... y {{ $boletasPendientes->count() - 8 }} más</span><span></span></div>
    @endif
    <div class="row bold" style="border-top:0.5px solid #eee;padding-top:4px;margin-top:3px"><span>Total adeudado</span><span>${{ number_format($totalAdeudado, 0, ',', '.') }}</span></div>
    @if($ultimoPago)
    <div class="row" style="margin-top:3px"><span class="muted">Último pago {{ \Carbon\Carbon::parse($ultimoPago->fecha_pago)->format('d/m/Y') }}</span><span>${{ number_format($ultimoPago->monto_pagado, 0, ',', '.') }} {{ ucfirst($ultimoPago->metodo_pago ?? 'efectivo') }}</span></div>
    @endif
    @else
    <div style="text-align:center;padding:20px 0;color:#666">
      <span class="badge">Al día</span>
      <p style="margin-top:8px;font-size:9px">No hay boletas pendientes</p>
    </div>
    @endif
  </div>
</div>

<!-- PAGO -->
<div class="pago-info">
  <div class="pago-box">
    <div class="pago-icon">Pago presencial</div>
    <p>{{ $organizacion->pago_presencial_lugar ?? 'Oficina APR' }}<br>{{ $organizacion->pago_presencial_dias ?? 'Lunes a Viernes' }} — {{ $organizacion->pago_presencial_horario ?? '09:00 a 17:00 hrs' }}</p>
  </div>
  <div class="pago-box">
    <div class="pago-icon">Transferencia bancaria</div>
    <p>
      @if($organizacion->banco && $organizacion->numero_cuenta)
        {{ $organizacion->banco }} — {{ $organizacion->tipo_cuenta ?? 'Cta. Cte.' }}: {{ $organizacion->numero_cuenta }}<br>
        @if($organizacion->titular_cuenta)
          Titular: {{ $organizacion->titular_cuenta }}<br>
        @endif
      @endif
      RUT: {{ $organizacion->rut }} &nbsp;|&nbsp; {{ $organizacion->email_contacto ?? 'sistemaapr@gmail.com' }}<br>Ref.: <strong>{{ $boleta->numero_boleta }}</strong>
    </p>
  </div>
</div>

<!-- TIMBRE SII -->
@if(method_exists($boleta, 'tieneFolioSII') && $boleta->tieneFolioSII())
<div class="timbre-section">
  @if($boleta->timbre_base64)
    {{-- Timbre REAL del SII en base64 --}}
    <img src="data:image/png;base64,{{ $boleta->timbre_base64 }}" class="timbre-qr" alt="Timbre SII" />
  @else
    {{-- QR placeholder (se elimina cuando se implemente SimpleAPI/LibreDTE) --}}
    <svg class="timbre-qr" width="68" height="68" viewBox="0 0 68 68" xmlns="http://www.w3.org/2000/svg">
      <rect width="68" height="68" fill="white" stroke="#ddd" stroke-width="0.5"/>
      <rect x="4" y="4" width="20" height="20" rx="1" fill="none" stroke="#222" stroke-width="1.8"/>
      <rect x="7" y="7" width="14" height="14" fill="#222"/>
      <rect x="44" y="4" width="20" height="20" rx="1" fill="none" stroke="#222" stroke-width="1.8"/>
      <rect x="47" y="7" width="14" height="14" fill="#222"/>
      <rect x="4" y="44" width="20" height="20" rx="1" fill="none" stroke="#222" stroke-width="1.8"/>
      <rect x="7" y="47" width="14" height="14" fill="#222"/>
      <rect x="28" y="28" width="12" height="12" fill="#222"/>
    </svg>
  @endif
  <div>
    <div class="t-title">Timbre Electrónico SII — Boleta Electrónica</div>
    <div class="t-row">
      <div><div class="t-label">RUT Emisor</div><div class="t-val">{{ $organizacion->rut }}</div></div>
      <div><div class="t-label">Tipo DTE</div><div class="t-val">39 — Boleta Electrónica</div></div>
      <div><div class="t-label">Folio</div><div class="t-val">{{ $boleta->folio_sii }}</div></div>
      <div><div class="t-label">Fecha emisión</div><div class="t-val">{{ \Carbon\Carbon::parse($boleta->fecha_emision_dte ?? $boleta->fecha_emision)->format('Y-m-d') }}</div></div>
      <div><div class="t-label">Monto</div><div class="t-val">${{ number_format($boleta->total, 0, ',', '.') }}</div></div>
    </div>
    <div class="t-legal">Resolución Ex. SII N° 45 del 01/09/2003 — Documento válido sin firma ni timbre físico. Verifique en www.sii.cl</div>
    @if($boleta->ted)
    <div class="t-legal" style="margin-top:3px;font-size:7px;color:#888;">TED: {{ substr($boleta->ted, 0, 60) }}...</div>
    @endif
  </div>
</div>
@endif

<!-- FOOTER -->
<div class="footer">
  <span>
    @if(count($tramosDetalle) > 0)
      @foreach($tramosDetalle as $index => $tramo)
        @if($index > 0) &nbsp;|&nbsp; @endif
        {{ $tramo['nombre'] }} ({{ $tramo['rango'] }}): {{ number_format($tramo['m3_en_tramo'], 2, ',', '.') }} m³ × ${{ number_format($tramo['valor_unitario'], 0, ',', '.') }} = ${{ number_format($tramo['subtotal'], 0, ',', '.') }}
      @endforeach
      &nbsp;|&nbsp;
    @endif
    Generado electrónicamente {{ now()->format('d/m/Y H:i:s') }}
  </span>
  <span class="sii-badge">SII — DTE Tipo 39</span>
</div>

</body>
</html>
