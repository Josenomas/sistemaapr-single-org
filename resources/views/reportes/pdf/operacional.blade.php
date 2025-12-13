<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Operacional - Sistema APR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #3b82f6;
        }

        .header h1 {
            color: #3b82f6;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 10px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-row {
            display: table-row;
        }

        .stat-card {
            display: table-cell;
            padding: 15px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            text-align: center;
            width: 33.33%;
        }

        .stat-card h3 {
            font-size: 9px;
            color: #666;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #3b82f6;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        thead {
            background: #3b82f6;
            color: white;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge.pendiente {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.en_proceso, .badge.asignado {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge.resuelto, .badge.completado, .badge.reconectado {
            background: #d1fae5;
            color: #065f46;
        }

        .badge.cerrado {
            background: #e5e7eb;
            color: #374151;
        }

        .badge.ejecutado {
            background: #d1fae5;
            color: #065f46;
        }

        .badge.alta {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge.media {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.baja {
            background: #e5e7eb;
            color: #374151;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #999;
        }

        .breakdown-table {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .breakdown-row {
            display: table-row;
        }

        .breakdown-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        .breakdown-cell:last-child {
            padding-right: 0;
            padding-left: 10px;
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mini-table th {
            background: #e5e7eb;
            color: #374151;
            padding: 6px;
            font-size: 8px;
        }

        .mini-table td {
            padding: 6px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE OPERACIONAL</h1>
        <p>Sistema APR - Generado el {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-card">
                <h3>TOTAL TICKETS</h3>
                <div class="value">{{ $estadisticas['total_tickets'] }}</div>
            </div>
            <div class="stat-card">
                <h3>TRABAJOS REALIZADOS</h3>
                <div class="value">{{ $estadisticas['total_trabajos'] }}</div>
            </div>
            <div class="stat-card">
                <h3>CORTES DE SUMINISTRO</h3>
                <div class="value">{{ $estadisticas['total_cortes'] }}</div>
            </div>
        </div>
    </div>

    <!-- TICKETS -->
    <h2 class="section-title">TICKETS DE SOPORTE</h2>

    <div class="breakdown-table">
        <div class="breakdown-row">
            <div class="breakdown-cell">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estadisticas['tickets_por_estado'] as $estado => $cantidad)
                        <tr>
                            <td>{{ ucfirst($estado) }}</td>
                            <td class="text-center">{{ $cantidad }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">Sin datos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="breakdown-cell">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Prioridad</th>
                            <th class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estadisticas['tickets_por_prioridad'] as $prioridad => $cantidad)
                        <tr>
                            <td>{{ ucfirst($prioridad) }}</td>
                            <td class="text-center">{{ $cantidad }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">Sin datos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Asunto</th>
                <th>Solicitante</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr>
                <td><strong>#{{ $ticket->id }}</strong></td>
                <td>{{ $ticket->asunto }}</td>
                <td>{{ $ticket->socio->nombre_completo ?? 'N/A' }}</td>
                <td><span class="badge {{ $ticket->prioridad }}">{{ ucfirst($ticket->prioridad) }}</span></td>
                <td><span class="badge {{ $ticket->estado }}">{{ ucfirst(str_replace('_', ' ', $ticket->estado)) }}</span></td>
                <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No hay tickets para mostrar</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TRABAJOS REALIZADOS -->
    <h2 class="section-title">TRABAJOS REALIZADOS</h2>

    <div class="breakdown-table">
        <div class="breakdown-row">
            <div class="breakdown-cell">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Tipo de Trabajo</th>
                            <th class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estadisticas['trabajos_por_tipo'] as $tipo => $cantidad)
                        <tr>
                            <td>{{ ucfirst($tipo) }}</td>
                            <td class="text-center">{{ $cantidad }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">Sin datos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="breakdown-cell">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estadisticas['trabajos_por_estado'] as $estado => $cantidad)
                        <tr>
                            <td>{{ ucfirst($estado) }}</td>
                            <td class="text-center">{{ $cantidad }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">Sin datos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Funcionario</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trabajos as $trabajo)
            <tr>
                <td>{{ \Carbon\Carbon::parse($trabajo->fecha)->format('d/m/Y') }}</td>
                <td>{{ ucfirst($trabajo->tipo) }}</td>
                <td>{{ $trabajo->descripcion }}</td>
                <td>{{ $trabajo->funcionario->nombre_completo ?? 'N/A' }}</td>
                <td><span class="badge {{ $trabajo->estado }}">{{ ucfirst($trabajo->estado) }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No hay trabajos para mostrar</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- CORTES DE SUMINISTRO -->
    <h2 class="section-title">CORTES DE SUMINISTRO</h2>

    <div class="breakdown-table">
        <div class="breakdown-row">
            <div class="breakdown-cell">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estadisticas['cortes_por_estado'] as $estado => $cantidad)
                        <tr>
                            <td>{{ ucfirst($estado) }}</td>
                            <td class="text-center">{{ $cantidad }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">Sin datos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="breakdown-cell">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Motivo</th>
                            <th class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estadisticas['cortes_por_motivo'] as $motivo => $cantidad)
                        <tr>
                            <td>{{ ucfirst($motivo) }}</td>
                            <td class="text-center">{{ $cantidad }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">Sin datos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Socio</th>
                <th>Motivo</th>
                <th>Fecha Corte</th>
                <th>Fecha Reconexión</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cortes as $corte)
            <tr>
                <td>{{ $corte->socio->nombre_completo ?? 'N/A' }}</td>
                <td>{{ ucfirst($corte->motivo) }}</td>
                <td>{{ \Carbon\Carbon::parse($corte->fecha_corte)->format('d/m/Y') }}</td>
                <td>{{ $corte->fecha_reconexion ? \Carbon\Carbon::parse($corte->fecha_reconexion)->format('d/m/Y') : '-' }}</td>
                <td><span class="badge {{ $corte->estado }}">{{ ucfirst($corte->estado) }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No hay cortes para mostrar</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema APR - Reporte generado automáticamente</p>
        <p>Página 1 de 1</p>
    </div>
</body>
</html>
