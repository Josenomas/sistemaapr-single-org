<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manifiesto de Movimiento - {{ $movimiento->numero_movimiento }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background: white;
        }

        .documento-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #1e293b;
            padding: 30px;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-icon {
            font-size: 48px;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 28px;
            color: #1e293b;
            margin-bottom: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .header .subtitle {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 15px;
        }

        .numero-movimiento {
            background: #2563eb;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            font-weight: 700;
            font-size: 18px;
        }

        .tipo-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .tipo-badge.entrada {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #065f46;
        }

        .tipo-badge.salida {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #92400e;
        }

        .tipo-badge.ajuste {
            background: #dbeafe;
            color: #1e40af;
            border: 2px solid #1e40af;
        }

        .info-section {
            margin: 30px 0;
        }

        .info-section h2 {
            font-size: 18px;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            padding: 12px;
            background: #f8fafc;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
        }

        .info-item label {
            display: block;
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-item value {
            display: block;
            font-size: 14px;
            color: #1e293b;
            font-weight: 600;
        }

        .info-item.full-width {
            grid-column: span 2;
        }

        .stock-box {
            background: #eff6ff;
            border: 2px solid #2563eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .stock-box h3 {
            font-size: 14px;
            color: #1e40af;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stock-flow {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            font-size: 24px;
            font-weight: 700;
        }

        .stock-flow .cantidad-anterior {
            color: #ef4444;
        }

        .stock-flow .flecha {
            color: #2563eb;
            font-size: 32px;
        }

        .stock-flow .cantidad-nueva {
            color: #10b981;
        }

        .stock-flow .unidad {
            font-size: 14px;
            color: #64748b;
            font-weight: 400;
        }

        .observaciones-box {
            background: #fef3c7;
            border: 2px dashed #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .observaciones-box h4 {
            font-size: 13px;
            color: #92400e;
            margin-bottom: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .observaciones-box p {
            font-size: 13px;
            color: #78350f;
            line-height: 1.6;
        }

        .firmas-section {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 3px solid #e2e8f0;
        }

        .firmas-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 80px;
        }

        .firma-box {
            text-align: center;
        }

        .firma-line {
            border-bottom: 2px solid #1e293b;
            margin-bottom: 10px;
            height: 60px;
        }

        .firma-label {
            font-size: 13px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .firma-nombre {
            font-size: 15px;
            color: #1e293b;
            font-weight: 700;
        }

        .firma-rut {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }

        .footer p {
            margin: 3px 0;
        }

        .footer .fecha-emision {
            font-weight: 700;
            color: #64748b;
            margin-top: 10px;
        }

        .sello-movimiento {
            position: absolute;
            top: 50px;
            right: 50px;
            transform: rotate(15deg);
            font-size: 18px;
            font-weight: 900;
            color: rgba(37, 99, 235, 0.15);
            border: 4px solid rgba(37, 99, 235, 0.15);
            padding: 15px 25px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        @media print {
            body {
                padding: 0;
            }

            .documento-container {
                border: none;
                max-width: 100%;
            }

            .sello-movimiento {
                color: rgba(37, 99, 235, 0.1);
                border-color: rgba(37, 99, 235, 0.1);
            }
        }
    </style>
</head>
<body>
    <div class="documento-container">
        <div class="sello-movimiento">
            {{ strtoupper($movimiento->tipo_movimiento) }}
        </div>

        <!-- Header -->
        <div class="header">
            <div class="header-icon">📦</div>
            <h1>Manifiesto de Movimiento de Inventario</h1>
            <div class="subtitle">Sistema de Agua Potable Rural</div>
            <div class="numero-movimiento">{{ $movimiento->numero_movimiento }}</div>
            <div class="tipo-badge {{ $movimiento->tipo_movimiento }}">
                {{ strtoupper($movimiento->tipo_movimiento) }}
            </div>
        </div>

        <!-- Información General -->
        <div class="info-section">
            <h2>📋 Información del Movimiento</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Fecha del Movimiento</label>
                    <value>{{ $movimiento->fecha_movimiento_formateada }}</value>
                </div>

                <div class="info-item">
                    <label>Tipo de Movimiento</label>
                    <value>{{ $movimiento->tipo_movimiento_texto }}</value>
                </div>

                <div class="info-item">
                    <label>Motivo</label>
                    <value>{{ $movimiento->motivo }}</value>
                </div>

                @if($movimiento->documento_referencia)
                <div class="info-item">
                    <label>Documento de Referencia</label>
                    <value>{{ $movimiento->documento_referencia }}</value>
                </div>
                @endif

                @if($movimiento->destino)
                <div class="info-item full-width">
                    <label>Destino / Lugar de Entrega</label>
                    <value>{{ $movimiento->destino }}</value>
                </div>
                @endif

                @if($movimiento->descripcion)
                <div class="info-item full-width">
                    <label>Descripción</label>
                    <value>{{ $movimiento->descripcion }}</value>
                </div>
                @endif
            </div>
        </div>

        <!-- Información del Producto -->
        <div class="info-section">
            <h2>📦 Información del Producto</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Código del Producto</label>
                    <value>{{ $movimiento->producto->codigo_producto }}</value>
                </div>

                <div class="info-item">
                    <label>Categoría</label>
                    <value>{{ $movimiento->producto->categoria_texto }}</value>
                </div>

                <div class="info-item full-width">
                    <label>Nombre del Producto</label>
                    <value>{{ $movimiento->producto->nombre }}</value>
                </div>

                <div class="info-item">
                    <label>Cantidad Movida</label>
                    <value>{{ $movimiento->cantidad_formateada }} {{ $movimiento->producto->unidad_medida }}</value>
                </div>

                <div class="info-item">
                    <label>Unidad de Medida</label>
                    <value>{{ $movimiento->producto->unidad_medida }}</value>
                </div>
            </div>
        </div>

        <!-- Stock -->
        <div class="stock-box">
            <h3>Control de Stock</h3>
            <div class="stock-flow">
                <div>
                    <div class="cantidad-anterior">{{ $movimiento->cantidad_anterior_formateada }}</div>
                    <div class="unidad">Stock Anterior</div>
                </div>
                <div class="flecha">→</div>
                <div>
                    <div class="cantidad-nueva">{{ $movimiento->cantidad_nueva_formateada }}</div>
                    <div class="unidad">Stock Nuevo</div>
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        @if($movimiento->observaciones)
        <div class="observaciones-box">
            <h4>⚠️ Observaciones</h4>
            <p>{{ $movimiento->observaciones }}</p>
        </div>
        @endif

        <!-- Firmas -->
        <div class="firmas-section">
            <h2 style="text-align: center; margin-bottom: 40px;">Firmas y Conformidad</h2>
            <div class="firmas-grid">
                <!-- Quien Entrega -->
                <div class="firma-box">
                    <div class="firma-line"></div>
                    <div class="firma-label">{{ $movimiento->tipo_movimiento === 'salida' ? 'Entrega' : 'Registra' }}</div>
                    <div class="firma-nombre">
                        {{ $movimiento->responsable ? $movimiento->responsable->nombre_completo : auth()->user()->nombre_usuario }}
                    </div>
                    @if($movimiento->responsable && $movimiento->responsable->rut)
                    <div class="firma-rut">RUT: {{ $movimiento->responsable->rut }}</div>
                    @endif
                </div>

                <!-- Quien Recibe -->
                <div class="firma-box">
                    <div class="firma-line"></div>
                    <div class="firma-label">{{ $movimiento->tipo_movimiento === 'salida' ? 'Recibe' : 'Autoriza' }}</div>
                    <div class="firma-nombre">
                        {{ $movimiento->destino ?? '___________________________' }}
                    </div>
                    <div class="firma-rut">RUT: ___________________________</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Sistema APR - Agua Potable Rural</strong></p>
            <p>Manifiesto de Movimiento de Inventario</p>
            <p class="fecha-emision">
                Fecha de Emisión: {{ now()->format('d/m/Y H:i') }}
            </p>
            <p style="margin-top: 15px; font-size: 10px;">
                Este documento es válido como comprobante de {{ $movimiento->tipo_movimiento }} de materiales
            </p>
        </div>
    </div>

    <script>
        // Auto-imprimir cuando se carga la página
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
