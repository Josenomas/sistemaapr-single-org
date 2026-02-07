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
            padding: 25px;
            background: #f8fafc;
            color: #1e293b;
        }

        .documento-container {
            max-width: 850px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        /* Header Superior con Logo y Tipo */
        .header-top {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            padding: 25px 40px;
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
            gap: 15px;
        }

        .logo-icon {
            font-size: 52px;
        }

        .logo-text h1 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .logo-text p {
            font-size: 13px;
            opacity: 0.9;
            font-weight: 400;
        }

        .tipo-badge-header {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 12px 24px;
            border-radius: 8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            text-align: center;
        }

        .tipo-badge-header .tipo-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            margin-bottom: 4px;
        }

        .tipo-badge-header .tipo-value {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Número de Movimiento */
        .numero-section {
            background: #0f172a;
            padding: 20px 40px;
            text-align: center;
            border-bottom: 4px solid #2563eb;
        }

        .numero-section .label {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 6px;
        }

        .numero-section .numero {
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 2px;
        }

        /* Contenido Principal */
        .contenido-principal {
            padding: 35px 40px;
        }

        /* Secciones de Información */
        .seccion {
            margin-bottom: 30px;
        }

        .seccion-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            background: linear-gradient(90deg, #f1f5f9 0%, #ffffff 100%);
            border-left: 5px solid #2563eb;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .seccion-header .icon {
            font-size: 22px;
        }

        .seccion-header h2 {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Grid de Información */
        .data-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .data-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            transition: all 0.2s ease;
        }

        .data-item:hover {
            border-color: #2563eb;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
        }

        .data-item.full {
            grid-column: span 2;
        }

        .data-item .label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
            display: block;
        }

        .data-item .data-value {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            word-wrap: break-word;
            display: block;
        }

        /* Destacado de Cantidad */
        .cantidad-destacada {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #2563eb;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 25px 0;
        }

        .cantidad-destacada .cantidad-value {
            font-size: 36px;
            font-weight: 900;
            color: #1e40af;
            margin-bottom: 6px;
        }

        .cantidad-destacada .cantidad-label {
            font-size: 13px;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        /* Control de Stock - Mejorado */
        .stock-control {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid #16a34a;
            border-radius: 12px;
            padding: 28px;
            margin: 25px 0;
        }

        .stock-control-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .stock-control-header h3 {
            font-size: 14px;
            font-weight: 800;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .stock-flow {
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 15px;
        }

        .stock-item {
            flex: 1;
            text-align: center;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .stock-item .cantidad {
            font-size: 32px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .stock-item.anterior .cantidad {
            color: #dc2626;
        }

        .stock-item.nueva .cantidad {
            color: #16a34a;
        }

        .stock-item .stock-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stock-arrow {
            font-size: 40px;
            color: #16a34a;
            font-weight: 700;
        }

        /* Observaciones */
        .observaciones {
            background: #fffbeb;
            border: 2px solid #f59e0b;
            border-left-width: 6px;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .observaciones h4 {
            font-size: 13px;
            font-weight: 800;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .observaciones p {
            font-size: 14px;
            color: #78350f;
            line-height: 1.7;
        }

        /* Firmas */
        .firmas-seccion {
            margin-top: 50px;
            padding-top: 35px;
            border-top: 3px solid #e2e8f0;
        }

        .firmas-titulo {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 45px;
        }

        .firmas-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 50px;
        }

        .firma-box {
            text-align: center;
        }

        .firma-espacio {
            height: 70px;
            border-bottom: 2px solid #1e293b;
            margin-bottom: 12px;
        }

        .firma-rol {
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .firma-nombre {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .firma-rut {
            font-size: 13px;
            color: #64748b;
        }

        /* Footer */
        .footer {
            background: #f8fafc;
            padding: 25px 40px;
            text-align: center;
            border-top: 3px solid #e2e8f0;
            margin-top: 40px;
        }

        .footer-title {
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .footer-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 15px;
        }

        .footer-fecha {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 12px;
        }

        .footer-validez {
            font-size: 11px;
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

            <!-- Información del Producto -->
            <div class="seccion">
                <div class="seccion-header">
                    <span class="icon">📦</span>
                    <h2>Información del Producto</h2>
                </div>
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
