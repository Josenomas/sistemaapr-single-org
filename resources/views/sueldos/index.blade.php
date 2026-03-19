@extends('layouts.app')

@section('title', 'Sueldos - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-money-check-alt"></i>
        Gestión de Sueldos
    </h2>
    <div class="header-actions">
        <button id="startTourBtn" class="btn btn-info" title="Iniciar tutorial">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
        <a href="{{ route('sueldos.create') }}" class="btn btn-primary" data-intro="Registra el pago de sueldo a un funcionario: período (mes/año), sueldo base, bonos, descuentos y fecha de pago." data-step="1">
            <i class="fas fa-plus"></i>
            Registrar Sueldo
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="card" data-intro="Filtra pagos de sueldos por funcionario, estado (pendiente, pagado, anulado), año o período específico." data-step="2">
    <div class="card-header">
        <h3 class="card-title">Filtros de Búsqueda</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('sueldos.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="id_funcionario">Funcionario</label>
                    <select name="id_funcionario" id="id_funcionario" class="form-control">
                        <option value="">Todos los funcionarios</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ request('id_funcionario') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="anio">Año</label>
                    <select name="anio" id="anio" class="form-control">
                        <option value="">Todos los años</option>
                        @foreach($anios as $anio)
                            <option value="{{ $anio }}" {{ request('anio') == $anio ? 'selected' : '' }}>
                                {{ $anio }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="periodo">Período (YYYY-MM)</label>
                    <input type="text" name="periodo" id="periodo" class="form-control"
                           value="{{ request('periodo') }}" placeholder="2024-01">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Buscar
                </button>
                <a href="{{ route('sueldos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Sueldos</h3>
        <div class="card-stats">
            Total: <strong>{{ $sueldos->total() }}</strong> registros
        </div>
    </div>
    <div class="card-body">
        @if($sueldos->count() > 0)
            <div class="table-responsive" data-intro="Listado de pagos de sueldos registrados con sueldo base, bonos, descuentos y total líquido a pagar." data-step="3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Funcionario</th>
                            <th>Período</th>
                            <th>Sueldo Base</th>
                            <th>Bonos</th>
                            <th>Descuentos</th>
                            <th data-intro="Total Líquido = Sueldo Base + Bonos - Descuentos. Monto final a pagar al funcionario." data-step="4">Total Líquido</th>
                            <th>Fecha Pago</th>
                            <th data-intro="Estados: Pendiente (no pagado), Pagado (cancelado), Anulado (eliminado/rechazado)." data-step="5">Estado</th>
                            <th data-intro="Ver detalles completos (liquidación) o Editar información del pago." data-step="6">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sueldos as $sueldo)
                            <tr>
                                <td>
                                    <div class="funcionario-info">
                                        <div class="funcionario-avatar-small">
                                            {{ $sueldo->funcionario->iniciales }}
                                        </div>
                                        <div>
                                            <strong>{{ $sueldo->funcionario->nombre_completo }}</strong>
                                            <small>{{ $sueldo->funcionario->cargo }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $sueldo->periodo_formateado }}</td>
                                <td>{{ $sueldo->sueldo_base_formateado }}</td>
                                <td>{{ $sueldo->bonos_formateado }}</td>
                                <td>{{ $sueldo->descuentos_formateado }}</td>
                                <td><strong>{{ $sueldo->total_liquido_formateado }}</strong></td>
                                <td>{{ $sueldo->fecha_pago->format('d/m/Y') }}</td>
                                <td>
                                    @if($sueldo->estado === 'pendiente')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($sueldo->estado === 'pagado')
                                        <span class="badge badge-success">Pagado</span>
                                    @else
                                        <span class="badge badge-danger">Anulado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('sueldos.show', $sueldo->id) }}" class="btn-icon" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sueldos.edit', $sueldo->id) }}" class="btn-icon" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('sueldos.destroy', $sueldo->id) }}"
                                              onsubmit="return confirm('¿Está seguro de eliminar este sueldo?')" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $sueldos->appends(request()->only(['id_funcionario', 'estado', 'anio', 'periodo']))->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-money-check-alt"></i>
                <p>No se encontraron registros de sueldos</p>
                <a href="{{ route('sueldos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Registrar Primer Sueldo
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .page-title i {
        color: var(--primary);
    }

    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #059669;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 2px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-stats {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .card-body {
        padding: 24px;
    }

    .filter-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
    }

    .form-control {
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-actions {
        display: flex;
        gap: 12px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-600);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-700);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background: var(--gray-50);
        padding: 12px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--gray-600);
        border-bottom: 2px solid var(--gray-200);
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.875rem;
        color: var(--gray-700);
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .funcionario-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .funcionario-avatar-small {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 700;
    }

    .funcionario-info strong {
        display: block;
        color: var(--dark);
    }

    .funcionario-info small {
        display: block;
        color: var(--gray-500);
        font-size: 0.75rem;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        background: var(--gray-100);
        color: var(--gray-600);
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .btn-icon:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--gray-300);
        margin-bottom: 16px;
    }

    .empty-state p {
        color: var(--gray-500);
        margin-bottom: 20px;
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .header-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }

    .header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn-info {
        background: #06b6d4;
        color: white;
        padding: 10px 20px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-info:hover {
        background: #0891b2;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* Estilos personalizados para Intro.js */
    .custom-tooltip {
        max-width: 400px;
    }

    .introjs-tooltip {
        border-radius: 12px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
    }

    .introjs-button {
        border-radius: 6px !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        text-shadow: none !important;
    }

    .introjs-nextbutton {
        background: var(--primary) !important;
        border: none !important;
    }

    .introjs-prevbutton {
        background: var(--gray-500) !important;
        border: none !important;
    }

    .introjs-skipbutton {
        color: var(--gray-600) !important;
    }

    .introjs-donebutton {
        background: var(--success) !important;
        border: none !important;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el tour
        const intro = introJs();
        intro.setOptions({
            nextLabel: 'Siguiente',
            prevLabel: 'Anterior',
            doneLabel: 'Finalizar',
            skipLabel: 'Salir',
            showProgress: true,
            showBullets: false,
            exitOnOverlayClick: false,
            disableInteraction: true,
            tooltipClass: 'custom-tooltip'
        });

        // Botón para iniciar el tour
        document.getElementById('startTourBtn').addEventListener('click', function() {
            intro.start();
        });

        // Mostrar tour automáticamente solo la primera vez
        const tourShown = localStorage.getItem('sueldosTourShown');
        if (!tourShown) {
            setTimeout(function() {
                intro.start();
                localStorage.setItem('sueldosTourShown', 'true');
            }, 500);
        }
    });
</script>
@endsection
