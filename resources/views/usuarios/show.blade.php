@extends('layouts.app')

@section('title', 'Detalle de Usuario - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-user"></i>
        Detalle del Usuario
    </h2>
    <div class="header-actions">
        <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="content-grid">
    <!-- Información General -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i>
                Información General
            </h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Nombre de Usuario</label>
                    <p><strong>{{ $usuario->nombre_usuario }}</strong></p>
                </div>

                <div class="info-item">
                    <label>Nombre Completo</label>
                    <p>{{ $usuario->nombre_completo }}</p>
                </div>

                <div class="info-item">
                    <label>Email</label>
                    <p>{{ $usuario->email ?: 'No registrado' }}</p>
                </div>

                <div class="info-item">
                    <label>Rol</label>
                    <p>
                        @if($usuario->rol == 'admin')
                            <span class="badge badge-info">Administrador</span>
                        @elseif($usuario->rol == 'tesorero')
                            <span class="badge badge-success">Tesorero</span>
                        @elseif($usuario->rol == 'operador')
                            <span class="badge badge-info">Operador</span>
                        @elseif($usuario->rol == 'lecturista')
                            <span class="badge badge-warning">Lecturista</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($usuario->rol) }}</span>
                        @endif
                    </p>
                </div>

                <div class="info-item">
                    <label>Estado</label>
                    <p>
                        @if($usuario->activo)
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-danger">Inactivo</span>
                        @endif
                    </p>
                </div>

                <div class="info-item">
                    <label>Último Acceso</label>
                    <p>{{ $usuario->ultimo_acceso ? date('d/m/Y H:i', strtotime($usuario->ultimo_acceso)) : 'Nunca ha iniciado sesión' }}</p>
                </div>

                <div class="info-item">
                    <label>Fecha de Creación</label>
                    <p>{{ date('d/m/Y H:i', strtotime($usuario->fecha_creacion)) }}</p>
                </div>

                <div class="info-item">
                    <label>Última Actualización</label>
                    <p>{{ date('d/m/Y H:i', strtotime($usuario->fecha_actualizacion)) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Permisos -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-lock"></i>
                Permisos del Sistema
            </h3>
        </div>
        <div class="card-body">
            @php
                $permisos = is_array($usuario->permisos) ? $usuario->permisos : (is_string($usuario->permisos) ? json_decode($usuario->permisos, true) : []);
                $permisos = $permisos ?? [];

                $permisosDisponibles = [
                    'socios' => 'Socios',
                    'lecturas' => 'Lecturas',
                    'boletas' => 'Boletas',
                    'pagos' => 'Pagos',
                    'mantenciones' => 'Mantenciones',
                    'incidentes' => 'Incidentes',
                    'reportes' => 'Reportes',
                    'usuarios' => 'Usuarios',
                    'funcionarios' => 'Funcionarios',
                    'sueldos' => 'Sueldos',
                    'cortes' => 'Cortes de Suministro',
                    'trabajos' => 'Trabajos Realizados',
                    'renovaciones' => 'Renovaciones de Medidores',
                    'vacaciones' => 'Vacaciones',
                    'compras' => 'Compras',
                    'inventario' => 'Inventario',
                    'tickets' => 'Tickets',
                    'recordatorios' => 'Recordatorios',
                    'movimientos_inventario' => 'Movimientos de Inventario',
                    'giros_bancarios' => 'Giros Bancarios',
                    'directiva' => 'Directiva',
                    'historial_consumo' => 'Historial de Consumo',
                    'historial_pagos' => 'Historial de Pagos',
                ];
            @endphp

            @if(empty($permisos))
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Este usuario no tiene permisos asignados</p>
                </div>
            @else
                <div class="permissions-grid">
                    @foreach($permisosDisponibles as $key => $nombre)
                        <div class="permission-badge {{ in_array($key, $permisos) ? 'active' : 'inactive' }}">
                            @if(in_array($key, $permisos))
                                <i class="fas fa-check-circle"></i>
                            @else
                                <i class="fas fa-times-circle"></i>
                            @endif
                            <span>{{ $nombre }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
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

    .header-actions {
        display: flex;
        gap: 12px;
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

    .content-grid {
        display: grid;
        gap: 24px;
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
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: var(--primary);
        font-size: 1.1rem;
    }

    .card-body {
        padding: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .info-item label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-item p {
        font-size: 1rem;
        color: var(--dark);
        margin: 0;
    }

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }

    .permission-badge {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: var(--radius);
        font-weight: 500;
        transition: all 0.2s;
    }

    .permission-badge.active {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #059669;
    }

    .permission-badge.active i {
        color: #059669;
    }

    .permission-badge.inactive {
        background: var(--gray-100);
        color: var(--gray-500);
        border: 1px solid var(--gray-200);
    }

    .permission-badge.inactive i {
        color: var(--gray-400);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--gray-500);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--gray-400);
        margin-bottom: 16px;
    }

    .empty-state p {
        margin: 0;
        font-size: 1rem;
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

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
        background: #d97706;
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

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
        }

        .info-grid,
        .permissions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
