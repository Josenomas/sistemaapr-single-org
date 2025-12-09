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
            background: linear-gradient(135deg, #28023D 0%, #100119 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            padding: 40px 20px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3);
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
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
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            position: relative;
        }

        .info-section {
            position: relative;
            cursor: help;
            transition: all 0.3s;
        }

        .info-section:hover {
            background: rgba(59, 130, 246, 0.05);
            transform: scale(1.02);
        }

        .tooltip {
            position: absolute;
            background: #1f2937;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            z-index: 1000;
            width: 250px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .info-section:hover .tooltip {
            opacity: 1;
        }

        .tooltip::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 20px;
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid #1f2937;
        }

        /* Estilos de la boleta */
        .boleta-header {
            border: 3px solid #000;
            padding: 20px;
            margin-bottom: 20px;
        }

        .boleta-title {
            background: #000;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            margin: -20px -20px 15px -20px;
        }

        .boleta-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }

        .info-block {
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.08);
        }

        .info-block h3 {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .info-block .value {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1f2937;
        }

        .consumo-section {
            border: 2px solid #000;
            padding: 20px;
            margin: 20px 0;
            background: #f9fafb;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.1);
        }

        .consumo-title {
            background: #000;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            margin: -20px -20px 15px -20px;
        }

        .detalle-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .detalle-table th,
        .detalle-table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }

        .detalle-table th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .total-section {
            background: #000;
            color: white;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .total-section .label {
            font-size: 1rem;
            margin-bottom: 8px;
        }

        .total-section .amount {
            font-size: 2rem;
            font-weight: bold;
        }

        .instrucciones {
            background: #e0f2fe;
            border: 2px solid #0ea5e9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .instrucciones h3 {
            color: #0c4a6e;
            margin-bottom: 10px;
        }

        .instrucciones ul {
            list-style: none;
            padding: 0;
        }

        .instrucciones li {
            padding: 8px 0;
            padding-left: 24px;
            position: relative;
        }

        .instrucciones li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #0ea5e9;
            font-weight: bold;
        }

        .legend {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .legend h3 {
            color: #92400e;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend p {
            color: #78350f;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .boleta-info-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .boleta-wrapper {
                padding: 20px;
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
            <!-- Header de la boleta -->
            <div class="boleta-header info-section">
                <div class="tooltip">
                    <strong>Encabezado de la Boleta:</strong> Contiene los datos básicos de identificación del APR y el número de boleta único que identifica este documento.
                </div>
                <div class="boleta-title">
                    BOLETA DE CONSUMO - AGUA POTABLE RURAL
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="margin-bottom: 5px;">TU APR</h2>
                        <p style="color: #6b7280; font-size: 0.875rem;">AGUA POTABLE RURAL</p>
                        <p style="color: #6b7280; font-size: 0.75rem;">Dirección | Teléfono | Email</p>
                    </div>
                    <div style="border: 3px double #000; padding: 15px; text-align: center;">
                        <div style="font-size: 0.875rem; font-weight: bold;">BOLETA Nº</div>
                        <div style="font-size: 1.5rem; font-weight: bold;">12345</div>
                    </div>
                </div>
            </div>

            <!-- Fechas Importantes -->
            <div class="boleta-info-grid">
                <div class="info-block info-section">
                    <div class="tooltip">
                        <strong>Período Facturado:</strong> Mes al que corresponde el consumo de agua que se está cobrando en esta boleta.
                    </div>
                    <h3>📅 Período Facturado</h3>
                    <div class="value">Diciembre 2025</div>
                </div>
                <div class="info-block info-section">
                    <div class="tooltip">
                        <strong>Fecha de Vencimiento:</strong> Fecha límite para realizar el pago. Después de esta fecha se aplicarán recargos por mora.
                    </div>
                    <h3>⏰ Fecha Vencimiento</h3>
                    <div class="value" style="color: #dc2626;">13/12/2025</div>
                </div>
            </div>

            <!-- Datos del Cliente -->
            <div class="info-block info-section" style="margin: 20px 0;">
                <div class="tooltip">
                    <strong>Datos del Cliente:</strong> Información personal del socio que recibe el servicio de agua potable, incluyendo número de socio, RUT, nombre completo y dirección.
                </div>
                <h3>👤 Datos del Cliente</h3>
                <div style="margin-top: 10px;">
                    <p><strong>N° Socio:</strong> 001 | <strong>RUT:</strong> 12.345.678-9</p>
                    <p><strong>Nombre:</strong> Juan Pérez González</p>
                    <p><strong>Dirección:</strong> Calle Ejemplo #123</p>
                </div>
            </div>

            <!-- Historial de Consumo -->
            <div class="consumo-section info-section">
                <div class="tooltip">
                    <strong>Historial de Consumo:</strong> Gráfico que muestra tu consumo de agua de los últimos 12 meses. Te permite comparar y detectar variaciones inusuales en tu consumo.
                </div>
                <div class="consumo-title">
                    HISTORIAL DE CONSUMO (ÚLTIMOS 12 MESES)
                </div>
                <div style="text-align: center; padding: 20px;">
                    <p style="color: #6b7280;">Gráfico de barras mostrando consumo mensual</p>
                    <p style="color: #6b7280; font-size: 0.875rem; margin-top: 10px;">
                        * La barra negra indica el período actual
                    </p>
                </div>
            </div>

            <!-- Detalle de Consumo -->
            <div class="consumo-section info-section">
                <div class="tooltip">
                    <strong>Detalle de Consumo y Cargos:</strong> Desglose completo de los cobros: consumo de agua en metros cúbicos (m³), cargo fijo mensual, y otros cargos adicionales si los hubiera.
                </div>
                <div class="consumo-title">
                    DETALLE DE CONSUMO Y CARGOS
                </div>
                <table class="detalle-table">
                    <thead>
                        <tr>
                            <th>DESCRIPCIÓN</th>
                            <th>CANTIDAD</th>
                            <th>UNIDAD</th>
                            <th>MONTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CONSUMO DE AGUA POTABLE</td>
                            <td>10,00</td>
                            <td>m³</td>
                            <td><strong>$870</strong></td>
                        </tr>
                        <tr>
                            <td>CARGO FIJO MENSUAL</td>
                            <td>1</td>
                            <td>mes</td>
                            <td><strong>$8.850</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total a Pagar -->
            <div class="total-section info-section">
                <div class="tooltip">
                    <strong>Total a Pagar:</strong> Monto total que debes pagar antes de la fecha de vencimiento. Incluye consumo, cargo fijo y otros cargos, menos descuentos si los hubiera.
                </div>
                <div class="label">TOTAL A PAGAR</div>
                <div class="amount">$11.563</div>
            </div>

            <!-- Información de Último Pago -->
            <div class="boleta-info-grid">
                <div class="info-block info-section" style="background: #e8f5e9;">
                    <div class="tooltip">
                        <strong>Último Pago Registrado:</strong> Información del último pago que realizaste, incluyendo fecha, monto y método de pago utilizado.
                    </div>
                    <h3>💰 Último Pago</h3>
                    <div style="font-size: 0.875rem; margin-top: 8px;">
                        <p><strong>Fecha:</strong> 15/11/2025</p>
                        <p><strong>Monto:</strong> $11.000</p>
                        <p><strong>Método:</strong> Efectivo</p>
                    </div>
                </div>
                <div class="info-block info-section" style="background: #f1f8e9;">
                    <div class="tooltip">
                        <strong>Estado de Cuenta:</strong> Muestra si tienes deudas pendientes de meses anteriores o si tus pagos están al día.
                    </div>
                    <h3>✓ Estado de Cuenta</h3>
                    <div style="font-size: 0.875rem; margin-top: 8px; color: #2e7d32; font-weight: bold;">
                        ✓ No presenta deudas pendientes
                    </div>
                </div>
            </div>

            <!-- Instrucciones de Pago -->
            <div class="instrucciones info-section">
                <div class="tooltip">
                    <strong>Instrucciones de Pago:</strong> Información sobre cómo y dónde puedes realizar el pago de tu boleta, incluyendo horarios de atención y datos bancarios para transferencias.
                </div>
                <h3>📍 Instrucciones de Pago</h3>
                <ul>
                    <li>Pague antes de la fecha de vencimiento para evitar recargos por mora.</li>
                    <li>Conserve este documento como comprobante de pago.</li>
                    <li>Puede pagar en oficina presencial o mediante transferencia bancaria.</li>
                    <li>Ante cualquier consulta, comuníquese con nosotros.</li>
                </ul>
            </div>

            <!-- Leyenda -->
            <div class="legend">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    ¿Cómo usar esta guía?
                </h3>
                <p>
                    <strong>Pasa el cursor sobre cualquier sección</strong> de la boleta para ver una descripción detallada de lo que significa.
                    Cada elemento tiene información específica que te ayudará a entender mejor tu consumo y los cargos aplicados.
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
