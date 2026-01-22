@extends('layouts.app')

@section('title', 'Nueva Rendición Mensual - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Nueva Rendición Mensual
    </h2>
    <a href="{{ route('rendiciones-mensuales.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

@if(!$montosCalculados)
    {{-- PASO 1: Seleccionar período --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar"></i> Seleccionar Período a Rendir</h3>
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
                    <strong>¿Cómo funciona?</strong><br>
                    El sistema calculará automáticamente los montos desde:
                    <ul class="mb-0 mt-2">
                        <li><strong>Ingresos por consumo de agua:</strong> Desde pagos recibidos</li>
                        <li><strong>Egresos de remuneraciones:</strong> Desde sueldos pagados</li>
                        <li><strong>Egresos de compras:</strong> Desde compras registradas (energía, químicos, reparaciones, etc.)</li>
                    </ul>
                    Luego podrás revisar y ajustar los montos antes de guardar.
                </div>
            </form>
        </div>
    </div>

@else
    {{-- PASO 2: Formulario con montos calculados --}}
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <strong>Montos calculados automáticamente</strong><br>
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
                            Cambiar Período
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumen de transacciones encontradas --}}
        <div class="card mb-3">
            <div class="card-header bg-info-light">
                <h3><i class="fas fa-database"></i> Transacciones Encontradas</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-value">{{ $detalles['pagos']->count() }}</div>
                            <div class="stat-label">Pagos Recibidos</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-value">{{ $detalles['compras']->count() }}</div>
                            <div class="stat-label">Compras Realizadas</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-value">{{ $detalles['sueldos']->count() }}</div>
                            <div class="stat-label">Sueldos Pagados</div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary mt-3" data-toggle="collapse" data-target="#detalleTransacciones">
                    <i class="fas fa-eye"></i>
                    Ver Detalle de Transacciones
                </button>

                <div class="collapse mt-3" id="detalleTransacciones">
                    <h5>Pagos Recibidos ({{ $detalles['pagos']->count() }})</h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Socio</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detalles['pagos'] as $pago)
                                <tr>
                                    <td>{{ $pago->fecha_pago_formateada }}</td>
                                    <td>{{ $pago->socio->nombre ?? '-' }}</td>
                                    <td>{{ $pago->monto_pagado_formateado }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center">Sin pagos registrados</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5>Compras Realizadas ({{ $detalles['compras']->count() }})</h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Proveedor</th>
                                    <th>Descripción</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detalles['compras'] as $compra)
                                <tr>
                                    <td>{{ $compra->fecha_compra_formateada }}</td>
                                    <td>{{ $compra->proveedor }}</td>
                                    <td>{{ $compra->descripcion }}</td>
                                    <td>{!! $compra->tipo_compra_badge !!}</td>
                                    <td>{{ $compra->total_formateado }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center">Sin compras registradas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5>Sueldos Pagados ({{ $detalles['sueldos']->count() }})</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Funcionario</th>
                                    <th>Monto Líquido</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detalles['sueldos'] as $sueldo)
                                <tr>
                                    <td>{{ $sueldo->funcionario->nombre ?? '-' }}</td>
                                    <td>{{ $sueldo->total_liquido_formateado }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center">Sin sueldos registrados</td></tr>
                                @endforelse
                            </tbody>
                        </table>
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
                        <label for="egresos_energia_electrica">Energía Eléctrica</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white" title="Calculado automáticamente"><i class="fas fa-calculator"></i></span>
                            </div>
                            <input type="number" name="egresos_energia_electrica" id="egresos_energia_electrica" class="form-control egreso-input"
                                   value="{{ old('egresos_energia_electrica', $montosCalculados['egresos_energia_electrica']) }}" step="0.01" min="0">
                        </div>
                        <small class="form-help text-success">✓ Desde compras</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="egresos_productos_quimicos">Productos Químicos</label>
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
                Guardar Rendición
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
