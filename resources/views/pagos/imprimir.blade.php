<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Pago - {{ $pago->numero_recibo }}</title>
    <style>
        @page {
            size: legal; /* Tamaño oficio: 8.5" x 14" */
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }

        .comprobante-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 3px solid #2563eb;
            position: relative;
        }

        /* Header con degradado profesional */
        .header-main {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 25px 30px;
            position: relative;
            overflow: hidden;
        }

        .header-main::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header-content {
            display: table;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .empresa-info {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }

        .empresa-info h1 {
            font-size: 22pt;
            font-weight: 700;
            margin-bottom: 5px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .empresa-info p {
            font-size: 10pt;
            opacity: 0.95;
            margin: 2px 0;
        }

        .recibo-box {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: middle;
        }

        .recibo-inner {
            display: inline-block;
            background: white;
            color: #1e3a8a;
            border: 3px solid white;
            border-radius: 8px;
            padding: 15px 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .recibo-inner .tipo {
            font-size: 11pt;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .recibo-inner .numero {
            font-size: 20pt;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* Sello PAGADO más profesional */
        .sello-pagado {
            position: absolute;
            top: 120px;
            right: 40px;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 12px 30px;
            font-size: 18pt;
            font-weight: 700;
            border-radius: 50px;
            transform: rotate(-15deg);
            box-shadow: 0 6px 12px rgba(5, 150, 105, 0.4);
            border: 3px solid white;
            z-index: 10;
            letter-spacing: 3px;
        }

        /* Fecha y datos destacados */
        .info-destacada {
            background: linear-gradient(to right, #f0f9ff, #e0f2fe);
            padding: 15px 30px;
            border-bottom: 2px solid #2563eb;
            display: table;
            width: 100%;
        }

        .info-item {
            display: table-cell;
            width: 33.33%;
            padding: 0 10px;
            text-align: center;
        }

        .info-item strong {
            display: block;
            font-size: 8pt;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-bottom: 3px;
            font-weight: 600;
        }

        .info-item span {
            font-size: 12pt;
            font-weight: 700;
            color: #000;
        }

        /* Monto pagado destacado */
        .monto-section {
            background: linear-gradient(135deg, #dcfce7 0%, #86efac 100%);
            border: 3px solid #10b981;
            border-left: none;
            border-right: none;
            padding: 25px 30px;
            text-align: center;
        }

        .monto-label {
            font-size: 11pt;
            font-weight: 600;
            color: #065f46;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .monto-valor {
            font-size: 36pt;
            font-weight: 700;
            color: #065f46;
            line-height: 1;
        }

        /* Secciones de información */
        .seccion {
            padding: 20px 30px;
            border-bottom: 1px solid #e5e7eb;
        }

        .seccion-titulo {
            font-weight: 700;
            font-size: 11pt;
            margin-bottom: 15px;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-bottom: 8px;
            border-bottom: 3px solid #2563eb;
        }

        .datos-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .dato-item {
            display: flex;
            align-items: baseline;
        }

        .dato-label {
            font-weight: 600;
            color: #374151;
            min-width: 140px;
            font-size: 9pt;
        }

        .dato-valor {
            color: #000;
            font-weight: 500;
            font-size: 10pt;
        }

        /* Detalle de boleta */
        .detalle-boleta {
            background: linear-gradient(to right, #fef3c7, #fef08a);
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 20px 30px;
            margin: 0 30px 20px;
        }

        .detalle-boleta-titulo {
            font-weight: 700;
            font-size: 11pt;
            color: #92400e;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detalle-boleta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        /* Método de pago badge */
        .metodo-badge {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 10pt;
            letter-spacing: 0.5px;
        }

        /* Observaciones */
        .observaciones {
            background: #f9fafb;
            border-left: 4px solid #2563eb;
            padding: 15px 30px;
            margin: 0 30px 20px;
            font-style: italic;
            color: #374151;
        }

        /* Firma */
        .firma-section {
            padding: 30px;
            text-align: center;
        }

        .firma-box {
            display: inline-block;
            border-top: 2px solid #000;
            padding-top: 8px;
            min-width: 300px;
        }

        .firma-texto {
            font-size: 9pt;
            color: #374151;
            font-weight: 600;
        }

        /* Footer profesional */
        .footer {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 20px 30px;
            text-align: center;
        }

        .footer-title {
            font-size: 13pt;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .footer-text {
            font-size: 9pt;
            opacity: 0.95;
            line-height: 1.6;
        }

        .footer-marca {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            font-size: 8pt;
            opacity: 0.8;
        }

        /* Estilos de impresión */
        @media print {
            body {
                background: white;
            }

            .comprobante-container {
                border: 2px solid #2563eb;
                max-width: 100%;
            }

            .sello-pagado {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="comprobante-container">
        <!-- Sello PAGADO -->
        <div class="sello-pagado">✓ PAGADO</div>

        <!-- Header Profesional -->
        <div class="header-main">
            <div class="header-content">
                <div class="empresa-info">
                    <h1>SISTEMA APR</h1>
                    <p><strong>AGUA POTABLE RURAL</strong></p>
                    <p>Asociación de Agua Potable Rural</p>
                    <p>Teléfono: (XX) XXXX-XXXX | Email: contacto@apr.cl</p>
                </div>
                <div class="recibo-box">
                    <div class="recibo-inner">
                        <div class="tipo">RECIBO N°</div>
                        <div class="numero">{{ $pago->numero_recibo }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Destacada -->
        <div class="info-destacada">
            <div class="info-item" style="border-right: 1px solid #cbd5e1;">
                <strong>Fecha de Pago</strong>
                <span>{{ $pago->fecha_pago_formateada }}</span>
            </div>
            <div class="info-item" style="border-right: 1px solid #cbd5e1;">
                <strong>Método de Pago</strong>
                <span>{{ ucfirst($pago->metodo_pago) }}</span>
            </div>
            <div class="info-item">
                <strong>Estado</strong>
                <span style="color: #059669;">✓ Pagado</span>
            </div>
        </div>

        <!-- Monto Pagado Destacado -->
        <div class="monto-section">
            <div class="monto-label">Monto Pagado</div>
            <div class="monto-valor">{{ $pago->monto_pagado_formateado }}</div>
        </div>

        <!-- Datos del Socio -->
        <div class="seccion">
            <div class="seccion-titulo">📋 Datos del Socio</div>
            <div class="datos-grid">
                <div class="dato-item">
                    <div class="dato-label">Número de Socio:</div>
                    <div class="dato-valor">{{ $pago->socio->numero_socio }}</div>
                </div>
                <div class="dato-item">
                    <div class="dato-label">RUT:</div>
                    <div class="dato-valor">{{ $pago->socio->rut }}</div>
                </div>
                <div class="dato-item" style="grid-column: 1 / -1;">
                    <div class="dato-label">Nombre Completo:</div>
                    <div class="dato-valor" style="font-weight: 700; font-size: 11pt;">{{ $pago->socio->nombre_completo }}</div>
                </div>
                <div class="dato-item" style="grid-column: 1 / -1;">
                    <div class="dato-label">Dirección:</div>
                    <div class="dato-valor">{{ $pago->socio->direccion ?? 'No especificada' }}</div>
                </div>
                @if($pago->socio->telefono)
                <div class="dato-item">
                    <div class="dato-label">Teléfono:</div>
                    <div class="dato-valor">{{ $pago->socio->telefono }}</div>
                </div>
                @endif
                @if($pago->socio->email)
                <div class="dato-item">
                    <div class="dato-label">Email:</div>
                    <div class="dato-valor">{{ $pago->socio->email }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Detalle de Boleta Pagada -->
        <div class="detalle-boleta">
            <div class="detalle-boleta-titulo">
                <span>📄</span>
                <span>DETALLE DE LA BOLETA PAGADA</span>
            </div>
            <div class="detalle-boleta-grid">
                <div class="dato-item">
                    <div class="dato-label">N° Boleta:</div>
                    <div class="dato-valor" style="font-weight: 700;">{{ $pago->boleta->numero_boleta }}</div>
                </div>
                <div class="dato-item">
                    <div class="dato-label">Período:</div>
                    <div class="dato-valor" style="font-weight: 700;">{{ $pago->boleta->mes_texto }}</div>
                </div>
                <div class="dato-item">
                    <div class="dato-label">Total Boleta:</div>
                    <div class="dato-valor">{{ $pago->boleta->total_formateado }}</div>
                </div>
                <div class="dato-item">
                    <div class="dato-label">Estado Boleta:</div>
                    <div class="dato-valor" style="color: #059669; font-weight: 600;">{{ ucfirst($pago->boleta->estado) }}</div>
                </div>
            </div>
        </div>

        <!-- Detalle del Pago -->
        <div class="seccion">
            <div class="seccion-titulo">💳 Información del Pago</div>
            <div class="datos-grid">
                <div class="dato-item">
                    <div class="dato-label">Método de Pago:</div>
                    <div class="dato-valor">
                        <span class="metodo-badge">{{ strtoupper($pago->metodo_pago) }}</span>
                    </div>
                </div>
                @if($pago->numero_comprobante)
                <div class="dato-item">
                    <div class="dato-label">N° Comprobante:</div>
                    <div class="dato-valor" style="font-weight: 700;">{{ $pago->numero_comprobante }}</div>
                </div>
                @endif
                <div class="dato-item">
                    <div class="dato-label">Fecha de Registro:</div>
                    <div class="dato-valor">{{ $pago->fecha_creacion->format('d/m/Y H:i') }}</div>
                </div>
                @if($pago->usuarioRegistro)
                <div class="dato-item">
                    <div class="dato-label">Registrado por:</div>
                    <div class="dato-valor">{{ $pago->usuarioRegistro->nombre_usuario ?? $pago->usuarioRegistro->name }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Observaciones -->
        @if($pago->observaciones)
        <div class="observaciones">
            <strong style="font-style: normal; color: #1e3a8a;">📝 Observaciones:</strong><br>
            {{ $pago->observaciones }}
        </div>
        @endif

        <!-- Firma -->
        <div class="firma-section">
            <div class="firma-box">
                <div class="firma-texto">
                    Firma y Timbre Autorizado<br>
                    <strong>AGUA POTABLE RURAL</strong>
                </div>
            </div>
        </div>

        <!-- Footer Profesional -->
        <div class="footer">
            <div class="footer-title">✓ GRACIAS POR SU PAGO</div>
            <div class="footer-text">
                Este documento es un comprobante válido de pago.<br>
                Conserve este comprobante para cualquier consulta o reclamo futuro.<br>
                Para más información, contacte con nosotros.
            </div>
            <div class="footer-marca">
                Documento generado electrónicamente el {{ now()->format('d/m/Y H:i:s') }}<br>
                Sistema APR - Gestión de Agua Potable Rural
            </div>
        </div>
    </div>

    <script>
        // Auto-imprimir al cargar la página
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
