<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conoce tu Boleta - Sistema APR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px 20px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header p {
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

        .boleta-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            position: relative;
        }

        /* Estilos copiados de pdf_new.blade.php */
        .boleta-content {
            width: 21cm;
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 0.8cm;
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
        }

        .info-section {
            position: relative;
            transition: all 0.3s;
            cursor: help;
        }

        .info-section:hover {
            background: rgba(103, 126, 234, 0.05);
            box-shadow: 0 0 0 2px rgba(103, 126, 234, 0.3);
            z-index: 10;
        }

        .tooltip {
            position: absolute;
            background: #1f2937;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            z-index: 1000;
            width: 280px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
            line-height: 1.5;
            top: 50%;
            left: calc(100% + 15px);
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

        /* Estilos de la boleta PDF */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #5e0a85;
        }

        .logo-section h1 {
            font-size: 18pt;
            font-weight: bold;
            color: #5e0a85;
            margin: 0 0 2px 0;
        }

        .logo-section .subtitle {
            font-size: 8pt;
            color: #666;
        }

        .numero-boleta {
            text-align: right;
        }

        .numero-boleta .label {
            font-size: 7pt;
            color: #666;
            text-transform: uppercase;
        }

        .numero-boleta .numero {
            font-size: 14pt;
            font-weight: bold;
            color: #5e0a85;
            border: 2px solid #5e0a85;
            padding: 4px 12px;
            display: inline-block;
            margin-top: 2px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .info-box {
            background: #f5f5f5;
            padding: 8px;
            border-left: 3px solid #5e0a85;
        }

        .info-box .label {
            font-size: 7pt;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .info-box .value {
            font-size: 10pt;
            font-weight: bold;
            color: #000;
        }

        .consumo-bar {
            background: #f8f8f8;
            border: 1px solid #ddd;
            padding: 8px;
            margin: 12px 0;
            border-radius: 4px;
        }

        .bar-container {
            height: 30px;
            background: #e8e8e8;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            transition: width 0.3s;
        }

        .bar-label {
            display: flex;
            justify-content: space-between;
            font-size: 7pt;
            margin-top: 4px;
            color: #666;
        }

        .chart-container {
            background: #f8f8f8;
            border: 1px solid #ddd;
            padding: 12px;
            margin: 12px 0;
            height: 120px;
        }

        .detalle-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 8pt;
        }

        .detalle-table th,
        .detalle-table td {
            padding: 6px 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .detalle-table th {
            background: #f5f5f5;
            font-weight: bold;
            font-size: 7pt;
            text-transform: uppercase;
            color: #666;
        }

        .total-section {
            background: #5e0a85;
            color: white;
            padding: 12px;
            text-align: center;
            margin: 12px 0;
            border-radius: 4px;
        }

        .total-section .label {
            font-size: 8pt;
            margin-bottom: 4px;
        }

        .total-section .amount {
            font-size: 20pt;
            font-weight: bold;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 12px 0;
        }

        .payment-box {
            border: 1px solid #ddd;
            padding: 10px;
            background: #f8f8f8;
            border-radius: 4px;
        }

        .payment-box h4 {
            font-size: 9pt;
            color: #5e0a85;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .payment-box p {
            font-size: 7pt;
            color: #666;
            margin: 2px 0;
        }

        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 7pt;
            color: #666;
            text-align: center;
        }

        .legend {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 20px 20px 20px;
        }

        .legend h3 {
            color: #92400e;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .legend p {
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
            }

            .tooltip::before {
                display: none;
            }

            .info-grid {
                grid-template-columns: 1fr 1fr;
            }

            .payment-methods {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('landing') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Volver al Inicio
        </a>

        <div class="header">
            <h1>💧 Conoce tu Boleta APR</h1>
            <p>Pasa el cursor sobre cada sección para conocer su significado</p>
        </div>

        <div class="boleta-wrapper">
            <div class="boleta-content">
                <!-- Header -->
                <div class="header-section info-section">
                    <div class="tooltip">
                        <strong>Encabezado:</strong> Contiene el nombre de tu APR y el número único de boleta para identificación y control.
                    </div>
                    <div class="logo-section">
                        <h1>APR El Valle</h1>
                        <div class="subtitle">AGUA POTABLE RURAL</div>
                        <div class="subtitle" style="margin-top: 2px;">RUT: 22.334.567-3 | Pasaje 1 #64</div>
                    </div>
                    <div class="numero-boleta">
                        <div class="label">Boleta Electrónica N°</div>
                        <div class="numero">BOL-00000001</div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="info-grid">
                    <div class="info-box info-section">
                        <div class="tooltip">
                            <strong>Período Facturado:</strong> Mes al que corresponde el consumo de agua cobrado.
                        </div>
                        <div class="label">Período Facturado</div>
                        <div class="value">Marzo 2026</div>
                    </div>
                    <div class="info-box info-section">
                        <div class="tooltip">
                            <strong>Fecha Emisión:</strong> Día en que se generó la boleta.
                        </div>
                        <div class="label">Fecha Emisión</div>
                        <div class="value">30/03/2026</div>
                    </div>
                    <div class="info-box info-section">
                        <div class="tooltip">
                            <strong>Fecha Vencimiento:</strong> Fecha límite para pagar sin recargos por mora.
                        </div>
                        <div class="label">Fecha Vencimiento</div>
                        <div class="value" style="color: #dc2626;">14/04/2026</div>
                    </div>
                    <div class="info-box info-section">
                        <div class="tooltip">
                            <strong>Estado:</strong> Indica si la boleta está pendiente de pago, pagada o vencida.
                        </div>
                        <div class="label">Estado</div>
                        <div class="value" style="color: #f59e0b;">Pendiente</div>
                    </div>
                </div>

                <!-- Datos del Socio -->
                <div class="info-box info-section" style="grid-column: 1 / -1; margin-bottom: 12px;">
                    <div class="tooltip">
                        <strong>Información del Socio:</strong> Tus datos personales como usuario del servicio de agua potable.
                    </div>
                    <div class="label">Información del Socio</div>
                    <div class="value" style="margin-top: 4px;">
                        N° Socio: SOC-0001 | RUT: 18762343-2 | Jose Roble dn<br>
                        <span style="font-weight: normal; font-size: 8pt; color: #666;">Dirección: Pasaje 1 #64 | Teléfono: +56918412133</span>
                    </div>
                </div>

                <!-- Lectura del Medidor -->
                <div class="info-box info-section" style="grid-column: 1 / -1; margin-bottom: 12px;">
                    <div class="tooltip">
                        <strong>Lectura del Medidor:</strong> Registro de las lecturas anterior y actual del medidor para calcular tu consumo real.
                    </div>
                    <div class="label">Lectura del Medidor</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-top: 6px;">
                        <div>
                            <div style="font-size: 7pt; color: #666;">Lectura anterior</div>
                            <div style="font-size: 11pt; font-weight: bold;">0,00 m³</div>
                        </div>
                        <div>
                            <div style="font-size: 7pt; color: #666;">Lectura actual</div>
                            <div style="font-size: 11pt; font-weight: bold;">0,00 m³</div>
                        </div>
                        <div>
                            <div style="font-size: 7pt; color: #666;">Consumo período</div>
                            <div style="font-size: 11pt; font-weight: bold; color: #10b981;">10,00 m³</div>
                        </div>
                    </div>
                </div>

                <!-- Barra de Consumo -->
                <div class="consumo-bar info-section">
                    <div class="tooltip">
                        <strong>Barra de Consumo:</strong> Visualización gráfica de tu consumo actual respecto al máximo histórico. Te ayuda a identificar si estás consumiendo más o menos de lo habitual.
                    </div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: 55%;"></div>
                    </div>
                    <div class="bar-label">
                        <span>0 m³</span>
                        <span style="font-weight: bold; color: #000;">10 m³ actuales</span>
                        <span>18 m³ máx.</span>
                    </div>
                </div>

                <!-- Historial de Consumo -->
                <div class="info-section">
                    <div class="tooltip">
                        <strong>Historial de Consumo:</strong> Gráfico de barras que muestra tu consumo de los últimos 12 meses. La barra negra representa el mes actual. Te permite identificar patrones y detectar fugas.
                    </div>
                    <div style="background: #f8f8f8; border: 1px solid #ddd; padding: 8px; margin: 12px 0;">
                        <div style="font-size: 8pt; font-weight: bold; color: #5e0a85; margin-bottom: 6px;">HISTORIAL DE CONSUMO — ÚLTIMOS 12 MESES (M³)</div>
                        <div class="chart-container">
                            <div style="text-align: center; padding-top: 40px; color: #999; font-size: 8pt;">
                                <i class="fas fa-chart-bar" style="font-size: 24px; margin-bottom: 8px;"></i><br>
                                Gráfico de consumo mensual
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalle de Consumo -->
                <div class="info-section">
                    <div class="tooltip">
                        <strong>Detalle de Consumo:</strong> Desglose de todos los cobros: consumo de agua, cargo fijo mensual, y otros cargos adicionales.
                    </div>
                    <div style="background: #f8f8f8; border: 1px solid #ddd; padding: 8px; margin: 12px 0;">
                        <div style="font-size: 8pt; font-weight: bold; color: #5e0a85; margin-bottom: 6px;">DETALLE DE CONSUMO Y CARGOS</div>
                        <table class="detalle-table">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th style="text-align: center;">Cantidad</th>
                                    <th style="text-align: center;">Unidad</th>
                                    <th style="text-align: right;">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="color: #666;">Consumo de agua</td>
                                    <td style="text-align: center;">10,00</td>
                                    <td style="text-align: center;">m³</td>
                                    <td style="text-align: right; font-weight: bold;">$870</td>
                                </tr>
                                <tr>
                                    <td style="color: #666;">Cargo fijo</td>
                                    <td style="text-align: center;">1</td>
                                    <td style="text-align: center;">mes</td>
                                    <td style="text-align: right; font-weight: bold;">$8.850</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total a Pagar -->
                <div class="total-section info-section">
                    <div class="tooltip">
                        <strong>Total a Pagar:</strong> Monto total que debes cancelar antes de la fecha de vencimiento para evitar recargos.
                    </div>
                    <div class="label">TOTAL A PAGAR</div>
                    <div class="amount">$11.563</div>
                </div>

                <!-- Estado de Cuenta -->
                <div class="info-section">
                    <div class="tooltip">
                        <strong>Estado de Cuenta:</strong> Resumen de tu situación financiera, incluyendo el último pago realizado y deudas pendientes de meses anteriores.
                    </div>
                    <div style="background: #f8f8f8; border: 1px solid #ddd; padding: 12px; margin: 12px 0;">
                        <div style="font-size: 8pt; font-weight: bold; color: #5e0a85; margin-bottom: 8px;">ESTADO DE CUENTA</div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 8pt;">
                            <div>
                                <div style="color: #666; margin-bottom: 2px;">Último pago</div>
                                <div style="font-weight: bold;">15/02/2026 — $10.500 — Efectivo</div>
                            </div>
                            <div>
                                <div style="color: #666; margin-bottom: 2px;">Deuda pendiente</div>
                                <div style="font-weight: bold; color: #10b981;">$0 (Sin deudas)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métodos de Pago -->
                <div class="payment-methods">
                    <div class="payment-box info-section">
                        <div class="tooltip">
                            <strong>Pago Presencial:</strong> Información sobre dónde y cuándo puedes pagar en efectivo directamente en la oficina del APR.
                        </div>
                        <h4><i class="fas fa-money-bill-wave" style="color: #10b981;"></i> Pago Presencial</h4>
                        <p><strong>Lugar:</strong> Oficina APR</p>
                        <p><strong>Días:</strong> Lunes a Viernes</p>
                        <p><strong>Horario:</strong> 09:00 a 17:00 hrs</p>
                    </div>
                    <div class="payment-box info-section">
                        <div class="tooltip">
                            <strong>Transferencia Bancaria:</strong> Datos bancarios para realizar transferencias electrónicas. Recuerda usar el número de boleta como referencia.
                        </div>
                        <h4><i class="fas fa-university" style="color: #3b82f6;"></i> Transferencia Bancaria</h4>
                        <p><strong>Banco:</strong> [Nombre del Banco]</p>
                        <p><strong>Cuenta:</strong> Cta. Cte. [N° cuenta]</p>
                        <p><strong>RUT:</strong> 22.334.567-3</p>
                        <p><strong>Ref.:</strong> BOL-00000001</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="footer">
                    <p>Boleta generada electrónicamente el 30/03/2026 15:47:00 | Sistema APR El Valle</p>
                    <p style="margin-top: 4px; font-size: 6pt;">Este documento es solo una guía educativa. No corresponde a una boleta real.</p>
                </div>
            </div>

            <!-- Leyenda -->
            <div class="legend">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    ¿Cómo usar esta guía?
                </h3>
                <p>
                    <strong>Pasa el cursor sobre cualquier sección</strong> de la boleta para ver una explicación detallada.
                    Cada elemento resaltado tiene información específica que te ayudará a entender mejor tu consumo y los cargos aplicados.
                    <br><br>
                    <strong>Esta es una representación exacta de cómo se ve tu boleta real.</strong> Los datos mostrados son solo ejemplos educativos.
                </p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('landing') }}" class="back-btn">
                <i class="fas fa-home"></i>
                Volver al Inicio
            </a>
        </div>
    </div>
</body>
</html>
