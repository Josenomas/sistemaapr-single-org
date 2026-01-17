<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Activos Fijos - Sistema APR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3b82f6;
        }

        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 18px;
            color: #64748b;
            font-weight: normal;
            margin-bottom: 10px;
        }

        .header .fecha {
            font-size: 11px;
            color: #64748b;
        }

        .info-box {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 11px;
        }

        .info-box strong {
            color: #1e40af;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #3b82f6;
            color: white;
        }

        thead th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody tr:hover {
            background: #f1f5f9;
        }

        tbody td {
            padding: 8px;
            font-size: 11px;
        }

        .codigo {
            font-weight: 600;
            color: #1e40af;
            font-family: monospace;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-excelente {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-bueno {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-regular {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-malo {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-en_reparacion {
            background: #fef3c7;
            color: #92400e;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #64748b;
        }

        .resumen {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .resumen-item {
            background: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            border-left: 3px solid #3b82f6;
            text-align: center;
        }

        .resumen-value {
            font-size: 20px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 3px;
        }

        .resumen-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media print {
            body {
                padding: 15px;
            }

            .no-print {
                display: none;
            }

            @page {
                margin: 15mm;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        .print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transition: all 0.3s;
        }

        .print-button:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .print-button i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Imprimir
    </button>

    <div class="header">
        <h1>Sistema APR</h1>
        <h2>Inventario de Activos Fijos</h2>
        <div class="fecha">
            Generado el {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    @if(request()->hasAny(['search', 'categoria', 'estado']))
        <div class="info-box">
            <p><strong>Filtros aplicados:</strong></p>
            @if(request('search'))
                <p>🔍 Búsqueda: <strong>{{ request('search') }}</strong></p>
            @endif
            @if(request('categoria'))
                <p>📁 Categoría: <strong>
                    @php
                        $categorias = [
                            'mobiliario' => 'Mobiliario',
                            'equipos_computo' => 'Equipos de Cómputo',
                            'equipos_oficina' => 'Equipos de Oficina',
                            'herramientas' => 'Herramientas',
                            'vehiculos' => 'Vehículos',
                            'equipamiento_tecnico' => 'Equipamiento Técnico',
                            'otros' => 'Otros'
                        ];
                        echo $categorias[request('categoria')] ?? request('categoria');
                    @endphp
                </strong></p>
            @endif
            @if(request('estado'))
                <p>📊 Estado: <strong>
                    @php
                        $estados = [
                            'excelente' => 'Excelente',
                            'bueno' => 'Bueno',
                            'regular' => 'Regular',
                            'malo' => 'Malo',
                            'en_reparacion' => 'En Reparación'
                        ];
                        echo $estados[request('estado')] ?? request('estado');
                    @endphp
                </strong></p>
            @endif
        </div>
    @endif

    <div class="resumen">
        <div class="resumen-item">
            <div class="resumen-value">{{ $activos->count() }}</div>
            <div class="resumen-label">Total Activos</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-value">${{ number_format($activos->sum('valor_adquisicion'), 0, ',', '.') }}</div>
            <div class="resumen-label">Valor Adquisición</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-value">${{ number_format($activos->sum('valor_actual'), 0, ',', '.') }}</div>
            <div class="resumen-label">Valor Actual</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-value">{{ $activos->groupBy('categoria')->count() }}</div>
            <div class="resumen-label">Categorías</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Marca/Modelo</th>
                <th>Ubicación</th>
                <th>Responsable</th>
                <th>Estado</th>
                <th>Valor Adq.</th>
                <th>Valor Actual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activos as $activo)
                <tr>
                    <td class="codigo">{{ $activo->codigo_activo }}</td>
                    <td><strong>{{ $activo->nombre }}</strong></td>
                    <td>{{ $activo->categoria_nombre }}</td>
                    <td>
                        @if($activo->marca || $activo->modelo)
                            {{ $activo->marca ?? '' }} {{ $activo->modelo ?? '' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $activo->ubicacion ?? '-' }}</td>
                    <td>{{ $activo->responsable->nombre_completo ?? '-' }}</td>
                    <td>
                        @php
                            $estadoClass = 'badge-' . $activo->estado;
                            $estadoTexto = [
                                'excelente' => 'Excelente',
                                'bueno' => 'Bueno',
                                'regular' => 'Regular',
                                'malo' => 'Malo',
                                'en_reparacion' => 'En Reparación'
                            ][$activo->estado] ?? $activo->estado;
                        @endphp
                        <span class="badge {{ $estadoClass }}">{{ $estadoTexto }}</span>
                    </td>
                    <td>${{ number_format($activo->valor_adquisicion, 0, ',', '.') }}</td>
                    <td>${{ number_format($activo->valor_actual ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #64748b;">
                        No hay activos registrados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Sistema APR</strong> - Gestión de Agua Potable Rural</p>
        <p>Documento generado automáticamente el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
