@extends('layouts.app')

@section('title', 'Registro Masivo de Lecturas - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-tachometer-alt"></i>
        Registro Masivo de Lecturas
    </h2>
    <div class="header-actions">
        <button type="button" id="startTourBtn" class="btn btn-info" title="Iniciar tutorial">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
        <a href="{{ route('lecturas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Formulario completo -->
<form action="{{ url('/lecturas-masivo') }}" method="POST" id="formMasivo">
    @csrf

    <!-- Filtros -->
    <!-- Botón Importar Excel -->
    <div class="alert alert-info mb-4" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; border: none;"
         data-intro="Si ya tienes las lecturas en un archivo Excel, puedes importarlas masivamente. La plantilla incluye todos tus socios con sus lecturas anteriores prellenadas."
         data-step="1">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h5 style="margin: 0 0 8px 0; color: white;"><i class="fas fa-file-excel"></i> ¿Tienes las lecturas en Excel?</h5>
                <p style="margin: 0; opacity: 0.9;">Ahorra tiempo importando lecturas masivamente desde un archivo Excel</p>
            </div>
            <a href="{{ route('lecturas.importar.index') }}" class="btn" style="background: white; color: #0284c7; font-weight: 600;">
                <i class="fas fa-upload"></i> Importar desde Excel
            </a>
        </div>
    </div>

    <div class="card mb-4"
         data-intro="Configura el mes y fecha de las lecturas que vas a registrar. También puedes filtrar por sector si tu APR está dividida en zonas."
         data-step="2">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter"></i>
                Filtros y Configuración
            </h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="mes" class="form-label required">Mes/Año</label>
                    <input type="month"
                           name="mes"
                           id="mes"
                           class="form-control @error('mes') is-invalid @enderror"
                           value="{{ old('mes', date('Y-m')) }}"
                           required>
                    @error('mes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-3">
                    <label for="fecha_lectura" class="form-label required">Fecha de Lectura</label>
                    <input type="date"
                           name="fecha_lectura"
                           id="fecha_lectura"
                           class="form-control @error('fecha_lectura') is-invalid @enderror"
                           value="{{ old('fecha_lectura', date('Y-m-d')) }}"
                           required>
                    @error('fecha_lectura')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-3">
                    <label for="filtro_sector" class="form-label">Filtrar por Sector</label>
                    <select id="filtro_sector" class="form-control">
                        <option value="">Todos los sectores</option>
                        @foreach($sectores as $sector)
                            <option value="{{ $sector }}">{{ $sector }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label class="form-label" style="opacity: 0;">Acciones</label>
                    <button type="button" class="btn btn-info btn-block" onclick="filtrarPorSector()">
                        <i class="fas fa-filter"></i>
                        Aplicar Filtro
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Lecturas -->
    <div class="card"
         data-intro="Aquí aparecen todos tus socios. La columna 'Lect. Anterior' muestra la última lectura registrada (en gris). Ingresa la nueva lectura en 'Lect. Actual' y el consumo se calculará automáticamente."
         data-step="3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i>
            Lecturas de Socios
        </h3>
        <div class="card-actions">
            <span class="badge badge-primary" id="contador-socios">{{ count($socios) }} socios</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Socio</th>
                        <th>Nombre Completo</th>
                        <th>Sector</th>
                        <th width="120">Lect. Anterior (m³)</th>
                        <th width="120">Lect. Actual (m³)</th>
                        <th width="120">Consumo (m³)</th>
                        <th width="200">Observaciones</th>
                    </tr>
                </thead>
                <tbody id="tablaSocios">
                    @foreach($socios as $socio)
                    <tr class="fila-socio" data-sector="{{ $socio->sector }}">
                        <td><strong>{{ $socio->numero_socio }}</strong></td>
                        <td>{{ $socio->nombre_completo }}</td>
                        <td>
                            @if($socio->sector)
                                <span class="badge badge-secondary">{{ $socio->sector }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <input type="number"
                                   name="lecturas[{{ $socio->id }}][lectura_anterior]"
                                   class="form-control form-control-sm lectura-anterior"
                                   min="0"
                                   step="0.01"
                                   value="{{ $socio->lecturas()->orderBy('fecha_lectura', 'desc')->first()->lectura_actual ?? 0 }}"
                                   data-socio="{{ $socio->id }}"
                                   readonly>
                            <input type="hidden" name="lecturas[{{ $socio->id }}][id_socio]" value="{{ $socio->id }}">
                        </td>
                        <td>
                            <input type="number"
                                   name="lecturas[{{ $socio->id }}][lectura_actual]"
                                   class="form-control form-control-sm lectura-actual"
                                   min="0"
                                   step="0.01"
                                   placeholder="0.00"
                                   data-socio="{{ $socio->id }}">
                        </td>
                        <td>
                            <input type="number"
                                   name="lecturas[{{ $socio->id }}][consumo]"
                                   class="form-control form-control-sm consumo-calculado"
                                   min="0"
                                   step="0.01"
                                   readonly
                                   placeholder="0.00"
                                   data-socio="{{ $socio->id }}">
                        </td>
                        <td>
                            <input type="text"
                                   name="lecturas[{{ $socio->id }}][observaciones]"
                                   class="form-control form-control-sm"
                                   placeholder="Opcional">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="form-actions mt-4"
             data-intro="Una vez ingresadas todas las lecturas, haz clic en 'Guardar Todas las Lecturas'. El sistema registrará todas las lecturas y podrás generar las boletas correspondientes."
             data-step="4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Guardar Todas las Lecturas
            </button>
            <a href="{{ route('lecturas.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancelar
            </a>
        </div>
    </div>
</div>
</form>
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

    .header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
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
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title i {
        color: var(--primary);
    }

    .card-body {
        padding: 24px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 0;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .col-md-3 { grid-column: span 3; }
    .col-md-4 { grid-column: span 4; }
    .col-md-6 { grid-column: span 6; }
    .col-md-12 { grid-column: span 12; }

    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-label.required::after {
        content: ' *';
        color: #ef4444;
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
        background: var(--white);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control-sm {
        padding: 6px 10px;
        font-size: 0.813rem;
    }

    .table-responsive {
        overflow-x: auto;
        margin: 0;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table thead {
        background: var(--gray-100);
    }

    .table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-200);
        white-space: nowrap;
    }

    .table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    .table tbody tr {
        transition: background-color 0.15s;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .consumo-calculado {
        background: #dbeafe !important;
        font-weight: 600;
        color: #1e40af;
        text-align: center;
    }

    .lectura-anterior {
        background: var(--gray-100);
        cursor: not-allowed;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
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
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    .btn-info {
        background: #0ea5e9;
        color: white;
    }

    .btn-info:hover {
        background: #0284c7;
    }

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .text-muted {
        color: var(--gray-500);
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-3,
        .col-md-4,
        .col-md-6,
        .col-md-12 {
            grid-column: span 1;
        }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcular consumo automáticamente
    const filas = document.querySelectorAll('.fila-socio');

    filas.forEach(fila => {
        const inputAnterior = fila.querySelector('.lectura-anterior');
        const inputActual = fila.querySelector('.lectura-actual');
        const inputConsumo = fila.querySelector('.consumo-calculado');

        function calcularConsumo() {
            const anterior = parseFloat(inputAnterior.value) || 0;
            const actual = parseFloat(inputActual.value) || 0;
            const consumo = Math.max(0, actual - anterior);
            inputConsumo.value = consumo > 0 ? consumo.toFixed(2) : '';
        }

        inputAnterior.addEventListener('input', calcularConsumo);
        inputActual.addEventListener('input', calcularConsumo);
    });
});

function filtrarPorSector() {
    const sectorSeleccionado = document.getElementById('filtro_sector').value;
    const filas = document.querySelectorAll('.fila-socio');
    let contador = 0;

    filas.forEach(fila => {
        const sectorFila = fila.getAttribute('data-sector');

        if (sectorSeleccionado === '' || sectorFila === sectorSeleccionado) {
            fila.style.display = '';
            contador++;
        } else {
            fila.style.display = 'none';
        }
    });

    // Actualizar contador
    document.getElementById('contador-socios').textContent = contador + ' socios';
}

// Validación antes de enviar
document.getElementById('formMasivo').addEventListener('submit', function(e) {
    const lecturasActuales = document.querySelectorAll('.lectura-actual');
    let hayLecturas = false;

    lecturasActuales.forEach(input => {
        if (input.value && parseFloat(input.value) > 0) {
            hayLecturas = true;
        }
    });

    if (!hayLecturas) {
        e.preventDefault();
        alert('Debe ingresar al menos una lectura actual antes de guardar.');
        return false;
    }

    return confirm('¿Está seguro de guardar todas las lecturas ingresadas?');
});

// Tutorial interactivo con Intro.js
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
const tourShown = localStorage.getItem('lecturasMasivoTourShown');
if (!tourShown) {
    setTimeout(function() {
        intro.start();
        localStorage.setItem('lecturasMasivoTourShown', 'true');
    }, 500);
}
</script>
@endsection
