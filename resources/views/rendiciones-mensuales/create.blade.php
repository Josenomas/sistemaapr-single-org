@extends('layouts.app')

@section('title', 'Nueva Rendicion Mensual - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Nueva Rendicion Mensual
    </h2>
    <a href="{{ route('rendiciones-mensuales.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

@if(!$montosCalculados)
    {{-- PASO 1: Seleccionar periodo --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar"></i> Seleccionar Periodo a Rendir</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('rendiciones-mensuales.create') }}" method="GET">
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="mes" class="form-label required">Mes</label>
                        <select name="mes" id="mes" class="form-control" required>
                            <option value="">Seleccione mes</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>

                    <div class="form-group col-md-5">
                        <label for="anio" class="form-label required">Año</label>
                        <input type="number" name="anio" id="anio" class="form-control"
                               value="{{ date('Y') }}" min="2020" max="2100" required>
                    </div>

                    <div class="form-group col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-calculator"></i>
                            Calcular
                        </button>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Como funciona?</strong><br>
                    El sistema calculara automaticamente los montos desde:
                    <ul class="mb-0 mt-2">
                        <li><strong>Ingresos por consumo de agua:</strong> Desde pagos recibidos</li>
                        <li><strong>Egresos de remuneraciones:</strong> Desde sueldos pagados</li>
                        <li><strong>Egresos de compras:</strong> Desde compras registradas (energia, quimicos, reparaciones, etc.)</li>
                    </ul>
                    Luego podras revisar y ajustar los montos antes de guardar.
                </div>
            </form>
        </div>
    </div>

@else
    {{-- PASO 2: Formulario con montos calculados --}}
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <strong>Montos calculados automaticamente</strong><br>
        Los siguientes montos fueron calculados desde las transacciones registradas en el sistema.
        Puedes ajustarlos manualmente si es necesario.
    </div>

    <form action="{{ route('rendiciones-mensuales.store') }}" method="POST" id="formRendicion">
        @csrf

        <input type="hidden" name="mes" value="{{ request('mes') }}">
        <input type="hidden" name="anio" value="{{ request('anio') }}">

        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="fas fa-calendar"></i> Información del Periodo</h3>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="form-label">Periodo</label>
                        <input type="text" class="form-control" value="{{ date('F Y', strtotime(request('anio').'-'.request('mes').'-01')) }}" readonly>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="saldo_anterior" class="form-label required">Saldo Anterior</label>
                        <input type="number" name="saldo_anterior" id="saldo_anterior" class="form-control @error('saldo_anterior') is-invalid @enderror"
                               value="{{ old('saldo_anterior', $saldoAnterior) }}" step="0.01" min="0" required>
                        @error('saldo_anterior')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-help">Saldo final del mes anterior</small>
                    </div>

                    <div class="form-group col-md-4">
                        <a href="{{ route('rendiciones-mensuales.create') }}" class="btn btn-secondary mt-4">
                            <i class="fas fa-redo"></i>
                            Cambiar Periodo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumen de transacciones encontradas --}}
        <div class="card mb-3">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h3 class="mb-0"><i class="fas fa-database"></i> Transacciones Encontradas en el Sistema</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="transaction-stat-card bg-success">
                            <div class="stat-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">{{ $detalles['pagos']->count() }}</div>
                                <div class="stat-title">Pagos Recibidos</div>
                                <div class="stat-subtitle">Ingresos del periodo</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="transaction-stat-card bg-warning">
                            <div class="stat-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">{{ $detalles['compras']->count() }}</div>
                                <div class="stat-title">Compras Realizadas</div>
                                <div class="stat-subtitle">Egresos por compras</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="transaction-stat-card bg-info">
                            <div class="stat-icon">
                                <i class="fas fa-money-check-alt"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">{{ $detalles['sueldos']->count() }}</div>
                                <div class="stat-title">Sueldos Pagados</div>
                                <div class="stat-subtitle">Remuneraciones del periodo</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-3">
                    <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#detalleTransacciones">
                        <i class="fas fa-list-ul"></i>
                        Ver Detalle Completo de Transacciones
                    </button>
                </div>

                <div class="collapse" id="detalleTransacciones">
                    {{-- Pagos Recibidos --}}
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-hand-holding-usd"></i>
                                Pagos Recibidos ({{ $detalles['pagos']->count() }})
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="15%">Fecha</th>
                                            <th width="55%">Socio</th>
                                            <th width="30%" class="text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detalles['pagos'] as $pago)
                                        <tr>
                                            <td><i class="far fa-calendar"></i> {{ $pago->fecha_pago_formateada }}</td>
                                            <td><i class="fas fa-user"></i> {{ $pago->socio->nombre ?? '-' }}</td>
                                            <td class="text-right"><strong class="text-success">{{ $pago->monto_pagado_formateado }}</strong></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <br>Sin pagos registrados en este periodo
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    @if($detalles['pagos']->count() > 0)
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="2"><strong>TOTAL INGRESOS POR PAGOS</strong></td>
                                            <td class="text-right"><strong class="text-success">${{ number_format($detalles['pagos']->sum('monto_pagado'), 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Compras Realizadas --}}
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-cart"></i>
                                Compras Realizadas ({{ $detalles['compras']->count() }})
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="10%">Fecha</th>
                                            <th width="20%">Proveedor</th>
                                            <th width="35%">Descripción</th>
                                            <th width="15%">Tipo</th>
                                            <th width="20%" class="text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detalles['compras'] as $compra)
                                        <tr>
                                            <td><i class="far fa-calendar"></i> {{ $compra->fecha_compra_formateada }}</td>
                                            <td><i class="fas fa-truck"></i> {{ $compra->proveedor }}</td>
                                            <td>{{ \Str::limit($compra->descripcion, 50) }}</td>
                                            <td>{!! $compra->tipo_compra_badge !!}</td>
                                            <td class="text-right"><strong class="text-danger">{{ $compra->total_formateado }}</strong></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <br>Sin compras registradas en este periodo
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    @if($detalles['compras']->count() > 0)
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="4"><strong>TOTAL EGRESOS POR COMPRAS</strong></td>
                                            <td class="text-right"><strong class="text-danger">${{ number_format($detalles['compras']->sum('total'), 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Sueldos Pagados --}}
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-money-check-alt"></i>
                                Sueldos Pagados ({{ $detalles['sueldos']->count() }})
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="70%">Funcionario</th>
                                            <th width="30%" class="text-right">Monto Líquido</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detalles['sueldos'] as $sueldo)
                                        <tr>
                                            <td><i class="fas fa-user-tie"></i> {{ $sueldo->funcionario->nombre ?? '-' }}</td>
                                            <td class="text-right"><strong class="text-danger">{{ $sueldo->total_liquido_formateado }}</strong></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <br>Sin sueldos registrados en este periodo
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    @if($detalles['sueldos']->count() > 0)
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td><strong>TOTAL EGRESOS POR SUELDOS</strong></td>
                                            <td class="text-right"><strong class="text-danger">${{ number_format($detalles['sueldos']->sum('total_liquido'), 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- INGRESOS --}}
        <div class="card mb-3">
            <div class="card-header bg-success-light">
                <h3><i class="fas fa-arrow-down"></i> Ingresos</h3>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="ingresos_consumo_agua">Consumo de Agua</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white" title="Calculado automáticamente"><i class="fas fa-calculator"></i></span>
                            </div>
                            <input type="number" name="ingresos_consumo_agua" id="ingresos_consumo_agua" class="form-control ingreso-input"
                                   value="{{ old('ingresos_consumo_agua', $montosCalculados['ingresos_consumo_agua']) }}" step="0.01" min="0">
                        </div>
                        <small class="form-help text-success">✓ {{ $detalles['pagos']->count() }} pagos encontrados</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ingresos_subsidios">Subsidios</label>
                        <input type="number" name="ingresos_subsidios" id="ingresos_subsidios" class="form-control ingreso-input"
                               value="{{ old('ingresos_subsidios', $montosCalculados['ingresos_subsidios']) }}" step="0.01" min="0">
                        <small class="form-help text-muted">Ingresar manualmente</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ingresos_aportes_socios">Aportes de Socios</label>
                        <input type="number" name="ingresos_aportes_socios" id="ingresos_aportes_socios" class="form-control ingreso-input"
                               value="{{ old('ingresos_aportes_socios', $montosCalculados['ingresos_aportes_socios']) }}" step="0.01" min="0">
                        <small class="form-help text-muted">Ingresar manualmente</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ingresos_multas">Multas</label>
                        <input type="number" name="ingresos_multas" id="ingresos_multas" class="form-control ingreso-input"
                               value="{{ old('ingresos_multas', $montosCalculados['ingresos_multas']) }}" step="0.01" min="0">
                        <small class="form-help text-muted">Ingresar manualmente</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ingresos_incorporaciones">Incorporaciones</label>
                        <input type="number" name="ingresos_incorporaciones" id="ingresos_incorporaciones" class="form-control ingreso-input"
                               value="{{ old('ingresos_incorporaciones', $montosCalculados['ingresos_incorporaciones']) }}" step="0.01" min="0">
                        <small class="form-help text-muted">Ingresar manualmente</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ingresos_otros">Otros Ingresos</label>
                        <input type="number" name="ingresos_otros" id="ingresos_otros" class="form-control ingreso-input"
                               value="{{ old('ingresos_otros', $montosCalculados['ingresos_otros']) }}" step="0.01" min="0">
                        <small class="form-help text-muted">Ingresar manualmente</small>
                    </div>
                </div>
                <div class="total-box bg-success-light">
                    <strong>Total Ingresos:</strong>
                    <span id="totalIngresos">$0</span>
                </div>
            </div>
        </div>

        {{-- EGRESOS --}}
        <div class="card mb-3">
            <div class="card-header bg-danger-light">
                <h3><i class="fas fa-arrow-up"></i> Egresos</h3>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="egresos_energia_electrica">Energia Electrica</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white" title="Calculado automaticamente"><i class="fas fa-calculator"></i></span>
                            </div>
                            <input type="number" name="egresos_energia_electrica" id="egresos_energia_electrica" class="form-control egreso-input"
                                   value="{{ old('egresos_energia_electrica', $montosCalculados['egresos_energia_electrica']) }}" step="0.01" min="0">
                        </div>
                        <small class="form-help text-success">✓ Desde compras</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="egresos_productos_quimicos">Productos Quimicos</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white" title="Calculado automáticamente"><i class="fas fa-calculator"></i></span>
                            </div>
                            <input type="number" name="egresos_productos_quimicos" id="egresos_productos_quimicos" class="form-control egreso-input"
                                   value="{{ old('egresos_productos_quimicos', $montosCalculados['egresos_productos_quimicos']) }}" step="0.01" min="0">
                        </div>
                        <small class="form-help text-success">✓ Desde compras</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="egresos_reparaciones">Reparaciones</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white" title="Calculado automáticamente"><i class="fas fa-calculator"></i></span>
                            </div>
                            <input type="number" name="egresos_reparaciones" id="egresos_reparaciones" class="form-control egreso-input"
                                   value="{{ old('egresos_reparaciones', $montosCalculados['egresos_reparaciones']) }}" step="0.01" min="0">
                        </div>
                        <small class="form-help text-success">✓ Desde compras (materiales, herramientas)</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="egresos_remuneraciones">Remuneraciones</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white" title="Calculado automáticamente"><i class="fas fa-calculator"></i></span>
                            </div>
                            <input type="number" name="egresos_remuneraciones" id="egresos_remuneraciones" class="form-control egreso-input"
                                   value="{{ old('egresos_remuneraciones', $montosCalculados['egresos_remuneraciones']) }}" step="0.01" min="0">
                        </div>
                        <small class="form-help text-success">✓ {{ $detalles['sueldos']->count() }} sueldos encontrados</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="egresos_gastos_administrativos">Gastos Administrativos</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white" title="Calculado automáticamente"><i class="fas fa-calculator"></i></span>
                            </div>
                            <input type="number" name="egresos_gastos_administrativos" id="egresos_gastos_administrativos" class="form-control egreso-input"
                                   value="{{ old('egresos_gastos_administrativos', $montosCalculados['egresos_gastos_administrativos']) }}" step="0.01" min="0">
                        </div>
                        <small class="form-help text-success">✓ Desde compras (servicios, insumos)</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="egresos_otros">Otros Egresos</label>
                        <input type="number" name="egresos_otros" id="egresos_otros" class="form-control egreso-input"
                               value="{{ old('egresos_otros', $montosCalculados['egresos_otros']) }}" step="0.01" min="0">
                        <small class="form-help text-muted">Ingresar manualmente</small>
                    </div>
                </div>
                <div class="total-box bg-danger-light">
                    <strong>Total Egresos:</strong>
                    <span id="totalEgresos">$0</span>
                </div>
            </div>
        </div>

        {{-- RESUMEN --}}
        <div class="card mb-3">
            <div class="card-header bg-primary-light">
                <h3><i class="fas fa-calculator"></i> Resumen Final</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value text-muted" id="resumenSaldoAnterior">$0</div>
                            <div class="stat-label">Saldo Anterior</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value text-success" id="resumenIngresos">$0</div>
                            <div class="stat-label">Total Ingresos</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value text-danger" id="resumenEgresos">$0</div>
                            <div class="stat-label">Total Egresos</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value text-primary" id="resumenSaldoFinal">$0</div>
                            <div class="stat-label">Saldo Final</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- OBSERVACIONES --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="fas fa-comments"></i> Observaciones</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="observaciones">Observaciones (Opcional)</label>
                    <textarea name="observaciones" id="observaciones" class="form-control" rows="3"
                              placeholder="Notas o comentarios sobre esta rendición...">{{ old('observaciones') }}</textarea>
                </div>
            </div>
        </div>

        {{-- BOTONES --}}
        <div class="text-right mb-4">
            <a href="{{ route('rendiciones-mensuales.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Guardar Rendicion
            </button>
        </div>
    </form>
@endif

@endsection

@push('styles')
<style>
    .bg-success-light {
        background-color: #d4edda !important;
    }
    .bg-danger-light {
        background-color: #f8d7da !important;
    }
    .bg-primary-light {
        background-color: #d1ecf1 !important;
    }
    .bg-info-light {
        background-color: #d6eaf8 !important;
    }
    .total-box {
        padding: 15px;
        border-radius: 5px;
        margin-top: 15px;
        font-size: 18px;
        text-align: right;
    }
    .stat-card {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .stat-value {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 14px;
        color: #6c757d;
    }
    .form-help {
        display: block;
        margin-top: 5px;
        font-size: 12px;
    }

    /* Nuevos estilos para tarjetas de transacciones */
    .transaction-stat-card {
        border-radius: 12px;
        padding: 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.2s;
        margin-bottom: 15px;
    }
    .transaction-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .transaction-stat-card .stat-icon {
        font-size: 48px;
        opacity: 0.9;
    }
    .transaction-stat-card .stat-info {
        flex: 1;
    }
    .transaction-stat-card .stat-number {
        font-size: 36px;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 8px;
    }
    .transaction-stat-card .stat-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .transaction-stat-card .stat-subtitle {
        font-size: 13px;
        opacity: 0.9;
    }
    .table thead th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    // Calcular totales en tiempo real
    function calcularTotales() {
        const ingresoInputs = document.querySelectorAll('.ingreso-input');
        const egresoInputs = document.querySelectorAll('.egreso-input');
        const saldoAnterior = parseFloat(document.getElementById('saldo_anterior').value) || 0;

        let totalIngresos = 0;
        ingresoInputs.forEach(input => {
            totalIngresos += parseFloat(input.value) || 0;
        });

        let totalEgresos = 0;
        egresoInputs.forEach(input => {
            totalEgresos += parseFloat(input.value) || 0;
        });

        const saldoFinal = saldoAnterior + totalIngresos - totalEgresos;

        // Actualizar displays
        document.getElementById('totalIngresos').textContent = '$' + totalIngresos.toLocaleString('es-CL', {minimumFractionDigits: 0});
        document.getElementById('totalEgresos').textContent = '$' + totalEgresos.toLocaleString('es-CL', {minimumFractionDigits: 0});

        if (document.getElementById('resumenSaldoAnterior')) {
            document.getElementById('resumenSaldoAnterior').textContent = '$' + saldoAnterior.toLocaleString('es-CL', {minimumFractionDigits: 0});
            document.getElementById('resumenIngresos').textContent = '$' + totalIngresos.toLocaleString('es-CL', {minimumFractionDigits: 0});
            document.getElementById('resumenEgresos').textContent = '$' + totalEgresos.toLocaleString('es-CL', {minimumFractionDigits: 0});
            document.getElementById('resumenSaldoFinal').textContent = '$' + saldoFinal.toLocaleString('es-CL', {minimumFractionDigits: 0});
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Event listeners para calcular totales
        document.querySelectorAll('.ingreso-input, .egreso-input').forEach(input => {
            input.addEventListener('input', calcularTotales);
        });

        const saldoAnteriorInput = document.getElementById('saldo_anterior');
        if (saldoAnteriorInput) {
            saldoAnteriorInput.addEventListener('input', calcularTotales);
        }

        // Calcular totales iniciales
        calcularTotales();
    });
</script>
@endpush
