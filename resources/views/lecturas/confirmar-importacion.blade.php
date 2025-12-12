@extends('layouts.app')

@section('title', 'Confirmar Importación - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-check-circle"></i>
        Confirmar Importación de Lecturas
    </h2>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Revisión de Datos</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <div>
                Archivo procesado correctamente. Se encontraron <strong>{{ count($datos) }} lecturas</strong> para importar.
                <br>Revisa los datos a continuación y confirma la importación.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fila</th>
                        <th>N° Socio</th>
                        <th>Nombre</th>
                        <th>Mes</th>
                        <th>Lectura Actual</th>
                        <th>Fecha Lectura</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos as $dato)
                    <tr>
                        <td>{{ $dato['fila'] }}</td>
                        <td><strong>{{ $dato['socio']->numero_socio }}</strong></td>
                        <td>{{ $dato['socio']->nombre_completo }}</td>
                        <td>
                            @php
                                $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                                          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                $fecha = explode('-', $dato['mes']);
                                echo $meses[(int)$fecha[1]] . ' ' . $fecha[0];
                            @endphp
                        </td>
                        <td>{{ format_lectura($dato['lectura_actual']) }} m³</td>
                        <td>{{ date('d/m/Y', strtotime($dato['fecha_lectura'])) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Importante:</strong>
                <ul>
                    <li>La lectura anterior se obtendrá automáticamente de la última lectura registrada</li>
                    <li>El consumo se calculará automáticamente (lectura actual - lectura anterior)</li>
                    <li>Si ya existe una lectura para ese socio y mes, se omitirá (no se duplicará)</li>
                </ul>
            </div>
        </div>

        <form action="{{ route('lecturas.importar.confirmar') }}" method="POST">
            @csrf
            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i>
                    Confirmar e Importar {{ count($datos) }} Lecturas
                </button>
                <a href="{{ route('lecturas.importar.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
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
        color: var(--success);
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-body {
        padding: 24px;
    }

    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    .alert-success i {
        color: #10b981;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .info-box {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 16px 20px;
        margin: 24px 0;
        border-radius: 4px;
        display: flex;
        gap: 16px;
    }

    .info-box i {
        color: #f59e0b;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .info-box ul {
        margin: 8px 0 0 0;
        padding-left: 20px;
    }

    .info-box li {
        margin: 4px 0;
    }

    .table-responsive {
        overflow-x: auto;
        margin-bottom: 24px;
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
        border-bottom: 2px solid var(--gray-300);
        white-space: nowrap;
    }

    .table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-200);
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
    }

    .btn-success:hover {
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
</style>
@endsection
