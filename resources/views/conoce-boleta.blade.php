<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Conoce tu Boleta - Sistema APR</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  /* ESTILOS BASE DEL PDF */
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, sans-serif;
    font-size: 14px;
    color: #1a1a1a;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px 20px;
  }

  .wrapper {
    max-width: 900px;
    margin: 0 auto;
  }

  .top-header {
    text-align: center;
    color: white;
    margin-bottom: 30px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 40px 20px;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
  }

  .top-header h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
  }

  .top-header p {
    font-size: 1.1rem;
    opacity: 0.95;
  }

  .back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 20px;
    transition: all 0.3s;
  }

  .back-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
  }

  .page-container {
    width: 100%;
    margin: 0 auto;
    padding: 35px 45px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  }

  /* TOOLTIPS EDUCATIVOS */
  .info-section {
    position: relative;
    cursor: help;
    transition: all 0.2s;
  }

  .info-section:hover {
    background: rgba(103, 126, 234, 0.06);
    outline: 2px solid rgba(103, 126, 234, 0.4);
    outline-offset: 2px;
    z-index: 100;
  }

  .tooltip {
    position: absolute;
    background: #1f2937;
    color: white;
    padding: 14px 18px;
    border-radius: 8px;
    font-size: 13px;
    z-index: 1000;
    width: 320px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
    line-height: 1.6;
    top: 50%;
    left: calc(100% + 20px);
    transform: translateY(-50%);
  }

  .info-section:hover .tooltip {
    opacity: 1;
  }

  .tooltip::before {
    content: '';
    position: absolute;
    top: 50%;
    left: -8px;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid #1f2937;
  }

  .tooltip strong {
    display: block;
    margin-bottom: 6px;
    color: #60a5fa;
  }

  /* ESTILOS DEL PDF ORIGINAL */
  .header { border-bottom: 2px solid #1a1a1a; padding-bottom: 14px; margin-bottom: 14px; }
  .header h1 { font-size: 19px; font-weight: 700; }
  .header p { font-size: 13px; color: #555; margin-top: 4px; }
  .folio-box { border: 2px solid #000; border-radius: 4px; padding: 12px 18px; text-align: center; min-width: 240px; max-width: 240px; }
  .folio-label { font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.06em; color: #555; }
  .folio-num { font-size: 19px; font-weight: 700; }

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

  .bar-label { display: flex; justify-content: space-between; font-size: 11.5px; color: #666; margin: 8px 0 4px; }
  .bar-track { height: 10px; background: #f0f0f0; border-radius: 3px; overflow: hidden; }
  .bar-fill { height: 100%; background: #1D9E75; border-radius: 3px; }

  .badge { border-radius: 4px; padding: 4px 10px; font-size: 13px; font-weight: 600; display: inline-block; background: #FAEEDA; color: #633806; }

  .chart-wrap { border: 1.5px solid #ddd; border-radius: 6px; padding: 14px 16px; margin-bottom: 14px; }
  .pago-box { flex: 1; border: 1.5px solid #ddd; border-radius: 6px; padding: 11px 15px; }
  .pago-icon { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #666; margin-bottom: 6px; }
  .pago-box p { font-size: 13px; line-height: 1.6; }

  .footer { display: flex; justify-content: space-between; align-items: center; border-top: 0.5px solid #ddd; padding-top: 10px; font-size: 11.5px; color: #666; margin-top: 20px; }
  .sii-badge { border: 0.5px solid #ccc; border-radius: 3px; padding: 4px 10px; font-size: 11.5px; }

  .legend-box {
    background: #fef3c7;
    border: 2px solid #f59e0b;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
  }

  .legend-box h3 {
    color: #92400e;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
  }

  .legend-box p {
    color: #78350f;
    line-height: 1.6;
    font-size: 13px;
  }

  @media (max-width: 768px) {
    .tooltip {
      position: fixed;
      top: auto !important;
      bottom: 20px;
      left: 20px !important;
      right: 20px;
      width: auto;
      transform: none;
      max-width: calc(100vw - 40px);
    }

    .tooltip::before {
      display: none;
    }

    .page-container {
      padding: 20px;
    }

    .top-header h1 {
      font-size: 1.8rem;
    }
  }
</style>
</head>
<body>
<div class="wrapper">

<a href="{{ route('landing') }}" class="back-btn">
  <i class="fas fa-arrow-left"></i>
  Volver al Inicio
</a>

<div class="top-header">
  <h1>💧 Conoce tu Boleta APR</h1>
  <p>Pasa el cursor sobre cada sección para conocer su significado</p>
</div>

<div class="page-container">

<!-- HEADER -->
<div class="header info-section">
  <div class="tooltip">
    <strong>Encabezado</strong>
    Contiene el nombre de tu APR, RUT, información de contacto y el número único de boleta electrónica para identificación y control.
  </div>
  <table style="width: 100%; border-collapse: collapse;">
    <tr>
      <td style="vertical-align: top; width: 65%;">
        <h1>APR PITRELAHUE — Boleta de Consumo Agua Potable Rural</h1>
        <p>RUT: 65.552.000-7 &nbsp;|&nbsp; sistemaapr@gmail.com &nbsp;|&nbsp; Captación, tratamiento y distribución de agua</p>
      </td>
      <td style="vertical-align: top; width: 35%; text-align: right;">
        <div class="folio-box" style="display: inline-block;">
          <div class="folio-label">RUT: 65.552.000-7</div>
          <div class="folio-label">BOLETA ELECTRÓNICA N°</div>
          <div class="folio-num">BOL-2026-03-0084</div>
        </div>
      </td>
    </tr>
  </table>
</div>

<!-- META -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
  <tr>
    <td style="width: 32%; vertical-align: top; padding: 0 6px 0 0;">
      <div class="meta-item info-section">
        <div class="tooltip">
          <strong>Período Facturado</strong>
          Mes al que corresponde el consumo de agua que se está cobrando en esta boleta.
        </div>
        <div class="label">Período facturado</div>
        <div class="value">Marzo 2026</div>
      </div>
    </td>
    <td style="width: 2%; vertical-align: top; padding: 0;"></td>
    <td style="width: 32%; vertical-align: top; padding: 0 3px;">
      <div class="meta-item info-section">
        <div class="tooltip">
          <strong>Fecha Emisión</strong>
          Día en que se generó la boleta. A partir de esta fecha comienza a contar el plazo de vencimiento.
        </div>
        <div class="label">Fecha emisión</div>
        <div class="value">31/03/2026</div>
      </div>
    </td>
    <td style="width: 2%; vertical-align: top; padding: 0;"></td>
    <td style="width: 32%; vertical-align: top; padding: 0 0 0 6px;">
      <div class="meta-item info-section">
        <div class="tooltip">
          <strong>Fecha Vencimiento</strong>
          Fecha límite para realizar el pago sin recargos por mora. Después de esta fecha se aplicarán intereses.
        </div>
        <div class="label">Fecha vencimiento</div>
        <div class="value">25/04/2026</div>
      </div>
    </td>
  </tr>
</table>

<!-- SOCIO + MEDIDOR -->
<table class="two-col"><tr>
  <td><div class="section info-section">
    <div class="tooltip">
      <strong>Datos del Socio</strong>
      Información personal del socio que recibe el servicio de agua potable, incluyendo número de socio, RUT, nombre completo, dirección y estado de la boleta.
    </div>
    <div class="section-title">Datos del socio</div>
    <table width="100%" style="border-collapse:collapse; font-size:13.5px;">
      <tr>
        <td style="color:#666; padding:4px 0; width:55%">N° Socio</td>
        <td style="text-align:right; padding:4px 0;">SOC-0054</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">RUT</td>
        <td style="text-align:right; padding:4px 0;">17.707.851-7</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Nombre</td>
        <td style="text-align:right; padding:4px 0;">Karina Ester Lincheo Raiman</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Dirección</td>
        <td style="text-align:right; padding:4px 0;">Pitrelahue</td>
      </tr>
      <tr>
        <td style="color:#666; padding:10px 0 4px 0;">Estado</td>
        <td style="text-align:right; padding:10px 0 4px 0;">
          <span class="badge">Pendiente</span>
        </td>
      </tr>
    </table>
  </div></td>
  <td><div class="section info-section">
    <div class="tooltip">
      <strong>Lectura del Medidor</strong>
      Registro de las lecturas anterior y actual del medidor de agua. La diferencia entre ambas determina el consumo del período. La barra verde muestra tu consumo actual respecto al máximo histórico.
    </div>
    <div class="section-title">Lectura del medidor</div>
    <table width="100%" style="border-collapse:collapse; font-size:13.5px;">
      <tr>
        <td style="color:#666; padding:4px 0; width:55%">Lectura anterior</td>
        <td style="text-align:right; padding:4px 0;">611,00 m³</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Lectura actual</td>
        <td style="text-align:right; padding:4px 0;">624,00 m³</td>
      </tr>
      <tr>
        <td style="font-weight:600; padding:4px 0;">Consumo período</td>
        <td style="text-align:right; font-weight:600; padding:4px 0;">13,00 m³</td>
      </tr>
    </table>
    <div class="bar-label"><span>0 m³</span><span style="font-weight:600;color:#1a1a1a">13 m³ actuales</span><span>18 m³ máx.</span></div>
    <div class="bar-track"><div class="bar-fill" style="width:72%"></div></div>
  </div></td>
</tr></table>

<!-- GRÁFICO HISTORIAL -->
<div class="chart-wrap info-section">
  <div class="tooltip">
    <strong>Historial de Consumo</strong>
    Gráfico de barras que muestra tu consumo de agua de los últimos meses. La barra verde oscuro con ★ representa el mes actual. La línea punteada naranja indica el promedio. Te ayuda a comparar y detectar variaciones inusuales.
  </div>
  <div class="section-title" style="margin-bottom:8px">Historial de consumo — últimos 3 meses (m³)</div>
  <div style="text-align: center;">
    <svg width="688" height="90" viewBox="0 0 688 90" xmlns="http://www.w3.org/2000/svg" style="display: inline-block;">
      <text x="20" y="8" font-size="8" fill="#888" text-anchor="end">18</text>
      <text x="20" y="30" font-size="8" fill="#888" text-anchor="end">14</text>
      <text x="20" y="52" font-size="8" fill="#888" text-anchor="end">9</text>
      <text x="20" y="74" font-size="8" fill="#888" text-anchor="end">5</text>

      <line x1="24" y1="4" x2="688" y2="4" stroke="#eee" stroke-width="0.5"/>
      <line x1="24" y1="26" x2="688" y2="26" stroke="#eee" stroke-width="0.5"/>
      <line x1="24" y1="48" x2="688" y2="48" stroke="#eee" stroke-width="0.5"/>
      <line x1="24" y1="70" x2="688" y2="70" stroke="#eee" stroke-width="0.5"/>
      <line x1="24" y1="80" x2="688" y2="80" stroke="#ccc" stroke-width="0.5"/>

      <!-- Ago -->
      <rect x="30" y="60" width="60" height="20" rx="2" fill="#9FE1CB"/>
      <text x="60" y="57" font-size="8.5" fill="#555" text-anchor="middle">9</text>
      <text x="60" y="89" font-size="8" fill="#888" text-anchor="middle">Ago</text>

      <!-- Nov -->
      <rect x="102" y="48" width="60" height="32" rx="2" fill="#9FE1CB"/>
      <text x="132" y="45" font-size="8.5" fill="#555" text-anchor="middle">11</text>
      <text x="132" y="89" font-size="8" fill="#888" text-anchor="middle">Nov</text>

      <!-- Dic -->
      <rect x="174" y="40" width="60" height="40" rx="2" fill="#9FE1CB"/>
      <text x="204" y="37" font-size="8.5" fill="#555" text-anchor="middle">14</text>
      <text x="204" y="89" font-size="8" fill="#888" text-anchor="middle">Dic</text>

      <!-- Ene -->
      <rect x="246" y="50" width="60" height="30" rx="2" fill="#9FE1CB"/>
      <text x="276" y="47" font-size="8.5" fill="#555" text-anchor="middle">11</text>
      <text x="276" y="89" font-size="8" fill="#888" text-anchor="middle">Ene</text>

      <!-- Feb -->
      <rect x="318" y="18" width="60" height="62" rx="2" fill="#9FE1CB"/>
      <text x="348" y="15" font-size="8.5" fill="#555" text-anchor="middle">14</text>
      <text x="348" y="89" font-size="8" fill="#888" text-anchor="middle">Feb</text>

      <!-- Abr -->
      <rect x="390" y="40" width="60" height="40" rx="2" fill="#9FE1CB"/>
      <text x="420" y="37" font-size="8.5" fill="#555" text-anchor="middle">14</text>
      <text x="420" y="89" font-size="8" fill="#888" text-anchor="middle">Abr</text>

      <!-- May -->
      <rect x="462" y="48" width="60" height="32" rx="2" fill="#9FE1CB"/>
      <text x="492" y="45" font-size="8.5" fill="#555" text-anchor="middle">12</text>
      <text x="492" y="89" font-size="8" fill="#888" text-anchor="middle">May</text>

      <!-- Mar (actual) -->
      <rect x="534" y="26" width="60" height="54" rx="2" fill="#1D9E75" stroke="#0F6E56" stroke-width="1.5"/>
      <text x="564" y="23" font-size="8.5" font-weight="bold" fill="#0F6E56" text-anchor="middle">13</text>
      <text x="564" y="89" font-size="8" font-weight="bold" fill="#1a1a1a" text-anchor="middle">Mar ★</text>

      <!-- Línea promedio -->
      <line x1="24" y1="45" x2="688" y2="45" stroke="#BA7517" stroke-width="1" stroke-dasharray="4,3"/>
      <text x="688" y="43" font-size="7.5" fill="#BA7517" text-anchor="end">prom. 13,0 m³</text>
    </svg>
  </div>

  <table style="width: auto; margin: 6px auto 0; border-collapse: separate; border-spacing: 14px 0;">
    <tr>
      <td style="vertical-align: middle; padding: 0;">
        <div style="display: inline-block; vertical-align: middle; width: 13px; height: 13px; background: #9FE1CB; border-radius: 2px; margin-right: 4px;"></div>
        <span style="display: inline-block; vertical-align: middle; font-size: 8.5px; color: #666;">Meses anteriores</span>
      </td>
      <td style="vertical-align: middle; padding: 0;">
        <div style="display: inline-block; vertical-align: middle; width: 13px; height: 13px; background: #1D9E75; border-radius: 2px; margin-right: 4px;"></div>
        <span style="display: inline-block; vertical-align: middle; font-size: 8.5px; color: #666;">Período actual</span>
      </td>
      <td style="vertical-align: middle; padding: 0;">
        <div style="display: inline-block; vertical-align: middle; width: 13px; height: 13px; background: #FAEEDA; border: 1px solid #FAC775; border-radius: 2px; margin-right: 4px;"></div>
        <span style="display: inline-block; vertical-align: middle; font-size: 8.5px; color: #666;">Promedio (13,0 m³)</span>
      </td>
    </tr>
  </table>
</div>

<!-- CARGOS + DEUDA -->
<table class="two-col"><tr>
  <td><div class="section info-section">
    <div class="tooltip">
      <strong>Detalle de Cargos</strong>
      Desglose completo de los cobros: consumo de agua en metros cúbicos (m³), cargo fijo mensual, subtotal, IVA (19% o exento) y total a pagar antes del vencimiento.
    </div>
    <div class="section-title">Detalle de cargos</div>
    <table width="100%" style="border-collapse:collapse; font-size:13.5px;">
      <tr>
        <td style="color:#666; padding:4px 0; width:55%">Consumo agua (13 m³)</td>
        <td style="text-align:right; padding:4px 0;">$11.310</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Cargo fijo mensual</td>
        <td style="text-align:right; padding:4px 0;">$8.850</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Subtotal</td>
        <td style="text-align:right; padding:4px 0;">$20.160</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">IVA (19%)</td>
        <td style="text-align:right; padding:4px 0;">Exento</td>
      </tr>
      <tr>
        <td style="font-size:16px; font-weight:700; border-top:1.5px solid #1a1a1a; padding-top:8px; padding-bottom:4px; margin-top:7px;">Total a pagar</td>
        <td style="text-align:right; font-size:16px; font-weight:700; border-top:1.5px solid #1a1a1a; padding-top:8px; padding-bottom:4px;">$20.160</td>
      </tr>
    </table>
  </div></td>
  <td><div class="section info-section">
    <div class="tooltip">
      <strong>Estado de Cuenta</strong>
      Resumen de tu situación financiera. Si tienes deudas pendientes, aquí aparecerán los meses adeudados y el total a pagar. Si no hay deudas, muestra el último pago realizado.
    </div>
    <div class="section-title">Estado de cuenta</div>
    <div style="margin-bottom:5px"><span class="badge">5 meses pendientes</span></div>
    <table width="100%" style="border-collapse:collapse; font-size:13.5px;">
      <tr>
        <td style="color:#666; padding:4px 0; width:55%">Agosto 2025</td>
        <td style="text-align:right; padding:4px 0;">$24.580</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Septiembre 2025</td>
        <td style="text-align:right; padding:4px 0;">$17.550</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Octubre 2025</td>
        <td style="text-align:right; padding:4px 0;">$18.420</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Noviembre 2025</td>
        <td style="text-align:right; padding:4px 0;">$21.030</td>
      </tr>
      <tr>
        <td style="color:#666; padding:4px 0;">Diciembre 2025</td>
        <td style="text-align:right; padding:4px 0;">$18.420</td>
      </tr>
      <tr>
        <td style="font-weight:600; border-top:0.5px solid #eee; padding-top:4px; padding-bottom:4px; margin-top:3px;">Total adeudado</td>
        <td style="text-align:right; font-weight:600; border-top:0.5px solid #eee; padding-top:4px; padding-bottom:4px;">$166.990</td>
      </tr>
    </table>
  </div></td>
</tr></table>

<!-- PAGO -->
<table style="width: 100%; margin-bottom: 8px; border-collapse: separate; border-spacing: 8px 0;">
  <tr>
    <td style="width: 50%; vertical-align: top;">
      <div class="pago-box info-section">
        <div class="tooltip">
          <strong>Pago Presencial</strong>
          Información sobre dónde y cuándo puedes realizar el pago en efectivo directamente en la oficina del APR. Verifica los días y horarios de atención.
        </div>
        <div class="pago-icon">Pago presencial</div>
        <p>Oficina APR — Sábado y Domingo<br>09:00 a 14:00 hrs</p>
      </div>
    </td>
    <td style="width: 50%; vertical-align: top;">
      <div class="pago-box info-section">
        <div class="tooltip">
          <strong>Transferencia Bancaria</strong>
          Datos bancarios para realizar transferencias electrónicas. Recuerda siempre usar el número de boleta como referencia para que tu pago sea identificado correctamente.
        </div>
        <div class="pago-icon">Transferencia bancaria</div>
        <p>
          Banco: [Nombre del Banco] &nbsp;|&nbsp; Cta. Cte.: [N° cuenta]<br>
          RUT: 65.552.000-7 &nbsp;|&nbsp; sistemaapr@gmail.com<br>
          Ref.: <strong>BOL-2026-03-0084</strong>
        </p>
      </div>
    </td>
  </tr>
</table>

<!-- FOOTER -->
<div class="footer">
  <span>Último pago: 13/01/2026 — $69.850 efectivo</span>
  <span class="sii-badge">SII — DTE Tipo 39</span>
</div>

<!-- LEYENDA EDUCATIVA -->
<div class="legend-box">
  <h3>
    <i class="fas fa-info-circle"></i>
    ¿Cómo usar esta guía?
  </h3>
  <p>
    <strong>Pasa el cursor sobre cualquier sección</strong> de la boleta para ver una explicación detallada.
    Cada elemento resaltado tiene información específica que te ayudará a entender mejor tu consumo y los cargos aplicados.
    <br><br>
    <strong>Esta es una representación exacta de cómo se ve tu boleta real.</strong> Los datos mostrados son solo ejemplos educativos basados en una boleta real del sistema.
  </p>
</div>

</div><!-- /page-container -->

<div style="text-align: center; margin-top: 30px;">
  <a href="{{ route('landing') }}" class="back-btn">
    <i class="fas fa-home"></i>
    Volver al Inicio
  </a>
</div>

</div><!-- /wrapper -->
</body>
</html>
