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
            font-family: 'Segoe UI', 'Arial', sans-serif;
            padding: 20px;
            background: #f8fafc;
            color: #1e293b;
            font-size: 13px;
        }

        .documento-container {
            max-width: 850px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        /* Header Superior con Logo y Tipo */
        .header-top {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            padding: 15px 25px;
            color: white;
            position: relative;
        }

        .header-top-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-icon {
            font-size: 32px;
        }

        .logo-text h1 {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.3px;
            margin-bottom: 2px;
        }

        .logo-text p {
            font-size: 11px;
            opacity: 0.9;
            font-weight: 400;
        }

        .tipo-badge-header {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 6px 12px;
            border-radius: 6px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            text-align: center;
        }

        .tipo-badge-header .tipo-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.8;
            margin-bottom: 2px;
        }

        .tipo-badge-header .tipo-value {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Número de Movimiento */
        .numero-section {
            background: #0f172a;
            padding: 8px 20px;
            text-align: center;
            border-bottom: 3px solid #2563eb;
        }

        .numero-section .label {
            font-size: 9px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .numero-section .numero {
            font-size: 14px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 1px;
        }

        /* Contenido Principal */
        .contenido-principal {
            padding: 18px 25px;
        }

        /* Secciones de Información */
        .seccion {
            margin-bottom: 15px;
        }

        .seccion-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: linear-gradient(90deg, #f1f5f9 0%, #ffffff 100%);
            border-left: 3px solid #2563eb;
            border-radius: 4px;
            margin-bottom: 12px;
        }

        .seccion-header .icon {
            font-size: 16px;
        }

        .seccion-header h2 {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Grid de Información */
        .data-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .data-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 8px;
            transition: all 0.2s ease;
        }

        .data-item:hover {
            border-color: #2563eb;
            box-shadow: 0 1px 4px rgba(37, 99, 235, 0.1);
        }

        .data-item.full {
            grid-column: span 3;
        }

        .data-item .label {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
            display: block;
        }

        .data-item .data-value {
            font-size: 11px;
            font-weight: 600;
            color: #1e293b;
            word-wrap: break-word;
            display: block;
        }

        /* Destacado de Cantidad */
        .cantidad-destacada {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #2563eb;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin: 12px 0;
        }

        .cantidad-destacada .cantidad-value {
            font-size: 18px;
            font-weight: 900;
            color: #1e40af;
            margin-bottom: 3px;
        }

        .cantidad-destacada .cantidad-label {
            font-size: 9px;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        /* Control de Stock - Mejorado */
        .stock-control {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid #16a34a;
            border-radius: 6px;
            padding: 12px;
            margin: 12px 0;
        }

        .stock-control-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .stock-control-header h3 {
            font-size: 10px;
            font-weight: 800;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stock-flow {
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 8px;
        }

        .stock-item {
            flex: 1;
            text-align: center;
            background: white;
            border-radius: 6px;
            padding: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .stock-item .cantidad {
            font-size: 16px;
            font-weight: 900;
            margin-bottom: 4px;
        }

        .stock-item.anterior .cantidad {
            color: #dc2626;
        }

        .stock-item.nueva .cantidad {
            color: #16a34a;
        }

        .stock-item .stock-label {
            font-size: 8px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stock-arrow {
            font-size: 20px;
            color: #16a34a;
            font-weight: 700;
        }

        /* Tabla de Productos */
        .productos-tabla {
            margin: 10px 0;
            overflow-x: auto;
        }

        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .tabla-productos thead {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            color: white;
        }

        .tabla-productos th {
            padding: 8px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .tabla-productos tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .tabla-productos tbody tr:last-child {
            border-bottom: none;
        }

        .tabla-productos td {
            padding: 8px 8px;
            font-size: 11px;
            color: #1e293b;
        }

        .tabla-numero {
            font-weight: 700;
            color: #2563eb;
            text-align: center;
            width: 30px;
        }

        .tabla-codigo {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #64748b;
            font-size: 9px;
        }

        .tabla-producto {
            max-width: 200px;
        }

        .producto-nombre {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
            font-size: 10px;
        }

        .producto-categoria {
            font-size: 8px;
            color: #64748b;
            font-style: italic;
        }

        .tabla-cantidad {
            text-align: center;
        }

        .cantidad-badge {
            background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
            border: 1px solid #2563eb;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 800;
            color: #1e40af;
            font-size: 10px;
            display: inline-block;
        }

        .tabla-stock {
            text-align: center;
            font-weight: 700;
            font-size: 10px;
        }

        .tabla-stock.anterior {
            color: #dc2626;
        }

        .tabla-stock.nueva {
            color: #16a34a;
        }

        /* Resumen */
        .resumen-productos {
            background: #eff6ff;
            border: 1px solid #2563eb;
            border-radius: 3px;
            padding: 4px 8px;
            margin-top: 5px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .resumen-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .resumen-label {
            font-size: 8px;
            font-weight: 700;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .resumen-value {
            font-size: 10px;
            font-weight: 900;
            color: #1e40af;
        }

        /* Observaciones */
        .observaciones {
            background: #fffbeb;
            border: 1px solid #f59e0b;
            border-left-width: 3px;
            border-radius: 4px;
            padding: 10px 12px;
            margin: 12px 0;
        }

        .observaciones h4 {
            font-size: 10px;
            font-weight: 800;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .observaciones p {
            font-size: 10px;
            color: #78350f;
            line-height: 1.5;
        }

        /* Firmas */
        .firmas-seccion {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }

        .firmas-titulo {
            text-align: center;
            font-size: 8px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 8px;
        }

        .firmas-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .firma-box {
            text-align: center;
        }

        .firma-espacio {
            height: 30px;
            border-bottom: 1px solid #1e293b;
            margin-bottom: 5px;
        }

        .firma-rol {
            font-size: 8px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
        }

        .firma-nombre {
            font-size: 9px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .firma-rut {
            font-size: 8px;
            color: #64748b;
        }

        /* Footer */
        .footer {
            background: #f8fafc;
            padding: 8px 15px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            margin-top: 10px;
        }

        .footer-title {
            font-size: 9px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 3px;
        }

        .footer-subtitle {
            font-size: 8px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .footer-fecha {
            font-size: 8px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 4px;
        }

        .footer-validez {
            font-size: 7px;
            color: #94a3b8;
            font-style: italic;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            font-weight: 900;
            color: rgba(37, 99, 235, 0.03);
            text-transform: uppercase;
            pointer-events: none;
            z-index: 1;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .documento-container {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }

            .data-item:hover {
                border-color: #e2e8f0;
                box-shadow: none;
            }

            .watermark {
                color: rgba(37, 99, 235, 0.02);
            }
        }
    </style>
</head>
<body>
    <div class="documento-container">
        <!-- Watermark -->
        <div class="watermark">INVENTARIO</div>

        <!-- Header Superior -->
        <div class="header-top">
            <div class="header-top-content">
                <div class="logo-section">
                    <div class="logo-icon">📦</div>
                    <div class="logo-text">
                        <h1>MANIFIESTO DE MOVIMIENTO</h1>
                        <p>Sistema de Agua Potable Rural</p>
                    </div>
                </div>
                <div class="tipo-badge-header">
                    <div class="tipo-label">Tipo de Movimiento</div>
                    <div class="tipo-value">{{ strtoupper($movimiento->tipo_movimiento) }}</div>
                </div>
            </div>
        </div>

        <!-- Número de Movimiento -->
        <div class="numero-section">
            <div class="label">N° de Movimiento</div>
            <div class="numero">{{ $movimiento->numero_movimiento }}</div>
        </div>

        <!-- Contenido Principal -->
        <div class="contenido-principal">
            <!-- Información del Movimiento -->
            <div class="seccion">
                <div class="seccion-header">
                    <span class="icon">📋</span>
                    <h2>Información del Movimiento</h2>
                </div>
                <div class="data-grid">
                    <div class="data-item">
                        <span class="label">Fecha del Movimiento</span>
                        <span class="data-value">{{ $movimiento->fecha_movimiento_formateada }}</span>
                    </div>
                    <div class="data-item">
                        <span class="label">Tipo de Movimiento</span>
                        <span class="data-value">{{ $movimiento->tipo_movimiento_texto }}</span>
                    </div>
                    <div class="data-item">
                        <span class="label">Motivo</span>
                        <span class="data-value">{{ $movimiento->motivo }}</span>
                    </div>
                    @if($movimiento->documento_referencia)
                    <div class="data-item">
                        <span class="label">Documento de Referencia</span>
                        <span class="data-value">{{ $movimiento->documento_referencia }}</span>
                    </div>
                    @endif
                    @if($movimiento->destino)
                    <div class="data-item {{ !$movimiento->documento_referencia ? 'full' : '' }}">
                        <span class="label">Destino / Lugar de Entrega</span>
                        <span class="data-value">{{ $movimiento->destino }}</span>
                    </div>
                    @endif
                    @if($movimiento->descripcion)
                    <div class="data-item full">
                        <span class="label">Descripción</span>
                        <span class="data-value">{{ $movimiento->descripcion }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Productos del Movimiento -->
            <div class="seccion">
                <div class="seccion-header">
                    <span class="icon">📦</span>
                    <h2>Productos del Movimiento</h2>
                </div>

                @if($movimiento->detalles && $movimiento->detalles->count() > 0)
                    <!-- Tabla de productos -->
                    <div class="productos-tabla">
                        <table class="tabla-productos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Stock Anterior</th>
                                    <th>Stock Nuevo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movimiento->detalles as $index => $detalle)
                                <tr>
                                    <td class="tabla-numero">{{ $index + 1 }}</td>
                                    <td class="tabla-codigo">{{ $detalle->producto->codigo_producto }}</td>
                                    <td class="tabla-producto">
                                        <div class="producto-nombre">{{ $detalle->producto->nombre }}</div>
                                        <div class="producto-categoria">{{ $detalle->producto->categoria_texto }}</div>
                                    </td>
                                    <td class="tabla-cantidad">
                                        <span class="cantidad-badge">{{ $detalle->cantidad_formateada }} {{ $detalle->producto->unidad_medida }}</span>
                                    </td>
                                    <td class="tabla-stock anterior">{{ $detalle->cantidad_anterior_formateada }}</td>
                                    <td class="tabla-stock nueva">{{ $detalle->cantidad_nueva_formateada }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumen -->
                    <div class="resumen-productos">
                        <div class="resumen-item">
                            <span class="resumen-label">Total de Productos:</span>
                            <span class="resumen-value">{{ $movimiento->detalles->count() }}</span>
                        </div>
                    </div>
                @else
                    <!-- Fallback para movimientos antiguos sin detalles -->
                    @if($movimiento->producto)
                    <div class="data-grid">
                        <div class="data-item">
                            <span class="label">Código del Producto</span>
                            <span class="data-value">{{ $movimiento->producto->codigo_producto }}</span>
                        </div>
                        <div class="data-item">
                            <span class="label">Categoría</span>
                            <span class="data-value">{{ $movimiento->producto->categoria_texto }}</span>
                        </div>
                        <div class="data-item full">
                            <span class="label">Nombre del Producto</span>
                            <span class="data-value">{{ $movimiento->producto->nombre }}</span>
                        </div>
                        <div class="data-item">
                            <span class="label">Unidad de Medida</span>
                            <span class="data-value">{{ $movimiento->producto->unidad_medida }}</span>
                        </div>
                    </div>

                    <!-- Cantidad Destacada -->
                    <div class="cantidad-destacada">
                        <div class="cantidad-value">{{ $movimiento->cantidad_formateada }} {{ $movimiento->producto->unidad_medida }}</div>
                        <div class="cantidad-label">Cantidad Movida</div>
                    </div>

                    <!-- Control de Stock -->
                    <div class="stock-control">
                        <div class="stock-control-header">
                            <h3>📊 Control de Stock</h3>
                        </div>
                        <div class="stock-flow">
                            <div class="stock-item anterior">
                                <div class="cantidad">{{ $movimiento->cantidad_anterior_formateada }}</div>
                                <div class="stock-label">Stock Anterior</div>
                            </div>
                            <div class="stock-arrow">→</div>
                            <div class="stock-item nueva">
                                <div class="cantidad">{{ $movimiento->cantidad_nueva_formateada }}</div>
                                <div class="stock-label">Stock Nuevo</div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            </div>

            <!-- Observaciones -->
            @if($movimiento->observaciones)
            <div class="observaciones">
                <h4><span>⚠️</span> Observaciones</h4>
                <p>{{ $movimiento->observaciones }}</p>
            </div>
            @endif

            <!-- Firmas -->
            <div class="firmas-seccion">
                <div class="firmas-titulo">Firmas y Conformidad</div>
                <div class="firmas-grid">
                    <div class="firma-box">
                        <div class="firma-espacio"></div>
                        <div class="firma-rol">{{ $movimiento->tipo_movimiento === 'salida' ? 'Entrega' : 'Registra' }}</div>
                        <div class="firma-nombre">
                            {{ $movimiento->responsable ? $movimiento->responsable->nombre_completo : auth()->user()->nombre_usuario }}
                        </div>
                        @if($movimiento->responsable && $movimiento->responsable->rut)
                        <div class="firma-rut">RUT: {{ $movimiento->responsable->rut }}</div>
                        @endif
                    </div>
                    <div class="firma-box">
                        <div class="firma-espacio"></div>
                        <div class="firma-rol">{{ $movimiento->tipo_movimiento === 'salida' ? 'Recibe' : 'Autoriza' }}</div>
                        <div class="firma-nombre">
                            {{ $movimiento->destino ?? '___________________________' }}
                        </div>
                        <div class="firma-rut">RUT: ___________________________</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-title">Sistema APR - Agua Potable Rural</div>
            <div class="footer-subtitle">Manifiesto de Movimiento de Inventario</div>
            <div class="footer-fecha">Fecha de Emisión: {{ now()->format('d/m/Y H:i') }}</div>
            <div class="footer-validez">
                Este documento es válido como comprobante de {{ $movimiento->tipo_movimiento }} de materiales
            </div>
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
