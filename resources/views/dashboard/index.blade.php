@extends('layouts.app')

@section('title', 'Dashboard - Sistema APR')

@section('styles')
<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .dashboard-title {
        font-size: 2rem;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        margin: 0;
    }

    .dashboard-title i {
        color: var(--primary);
        font-size: 1.75rem;
    }

    .dashboard-date {
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, var(--primary-light), rgba(37, 99, 235, 0.1));
        padding: 12px 24px;
        border-radius: var(--radius);
        color: var(--primary-dark);
        font-weight: 600;
        font-size: 0.95rem;
        border: 1px solid var(--primary);
        box-shadow: var(--shadow);
    }

    .dashboard-date i {
        font-size: 1.125rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        transition: all 0.3s;
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--primary), var(--primary-dark));
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .stat-title {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 600;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .primary-bg {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .success-bg {
        background: linear-gradient(135deg, var(--success), #059669);
    }

    .warning-bg {
        background: linear-gradient(135deg, var(--warning), #d97706);
    }

    .danger-bg {
        background: linear-gradient(135deg, var(--danger), #dc2626);
    }

    .stat-value {
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--gray-900);
    }

    .stat-description {
        font-size: 0.875rem;
        color: var(--gray-500);
    }

    .section-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        margin-bottom: 32px;
        border: 1px solid var(--gray-200);
    }

    .section-title {
        font-size: 1.25rem;
        margin-bottom: 24px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
    }

    .section-title i {
        color: var(--primary);
    }

    .activity-list {
        list-style: none;
    }

    .activity-item {
        padding: 16px 0;
        border-bottom: 1px solid var(--gray-100);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 0.875rem;
        color: var(--gray-800);
    }

    .activity-meta {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        align-items: center;
    }

    .activity-user {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        color: var(--primary);
        font-weight: 600;
    }

    .activity-user i {
        font-size: 0.7rem;
    }

    .activity-time {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        color: var(--gray-500);
    }

    .activity-time i {
        font-size: 0.7rem;
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        text-decoration: none;
        color: var(--dark);
        transition: all 0.3s;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
    }

    .quick-action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        transition: width 0.3s;
    }

    .quick-action-btn:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }

    .quick-action-btn:hover::before {
        width: 100%;
        opacity: 0.05;
    }

    .quick-action-btn.primary::before {
        background: var(--primary);
    }

    .quick-action-btn.success::before {
        background: var(--success);
    }

    .quick-action-btn.warning::before {
        background: var(--warning);
    }

    .quick-action-btn.danger::before {
        background: var(--danger);
    }

    .quick-action-btn.info::before {
        background: #0ea5e9;
    }

    .quick-action-btn.purple::before {
        background: #8b5cf6;
    }

    .quick-action-icon {
        width: 56px;
        height: 56px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        flex-shrink: 0;
    }

    .quick-action-icon.primary-bg {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .quick-action-icon.success-bg {
        background: linear-gradient(135deg, var(--success), #059669);
    }

    .quick-action-icon.warning-bg {
        background: linear-gradient(135deg, var(--warning), #d97706);
    }

    .quick-action-icon.danger-bg {
        background: linear-gradient(135deg, var(--danger), #dc2626);
    }

    .quick-action-icon.info-bg {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
    }

    .quick-action-icon.purple-bg {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .quick-action-content {
        flex: 1;
    }

    .quick-action-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 4px;
    }

    .quick-action-description {
        font-size: 0.8125rem;
        color: var(--gray-500);
    }
</style>
@endsection

@section('content')
<div class="dashboard">
    <div class="dashboard-header">
        <h2 class="dashboard-title">
            <i class="fas fa-tachometer-alt"></i>
            Panel de Control
        </h2>
        <div class="dashboard-date">
            <i class="fas fa-calendar-day"></i>
            <?php
                setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
                $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
                $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                $dia_semana = $dias[date('w')];
                $dia = date('d');
                $mes = $meses[date('n') - 1];
                $año = date('Y');
                echo ucfirst($dia_semana) . ', ' . $dia . ' de ' . $mes . ' de ' . $año;
            ?>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Total Clientes</div>
                <div class="stat-icon primary-bg">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-value">{{ $totalClientes }}</div>
            <div class="stat-description">Socios activos</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Boletas Emitidas</div>
                <div class="stat-icon success-bg">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
            <div class="stat-value">{{ $boletasEmitidas }}</div>
            <div class="stat-description">Del mes {{ $mesPasado }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Pagos Pendientes</div>
                <div class="stat-icon warning-bg">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="stat-value">{{ $pagosPendientes }}</div>
            <div class="stat-description">Del mes {{ $mesPasado }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Incidentes Abiertos</div>
                <div class="stat-icon danger-bg">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ $incidentesAbiertos }}</div>
            <div class="stat-description">Requieren atención</div>
        </div>
    </div>

    <div class="quick-actions-grid">
        <a href="{{ route('lecturas.create') }}" class="quick-action-btn primary">
            <div class="quick-action-icon primary-bg">
                <i class="fas fa-tint"></i>
            </div>
            <div class="quick-action-content">
                <div class="quick-action-title">Registrar Lectura</div>
                <div class="quick-action-description">Nueva lectura de medidor</div>
            </div>
        </a>

        <a href="{{ route('boletas.generar') }}" class="quick-action-btn success">
            <div class="quick-action-icon success-bg">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="quick-action-content">
                <div class="quick-action-title">Generar Boletas</div>
                <div class="quick-action-description">Crear boletas del período</div>
            </div>
        </a>

        <a href="{{ route('pagos.create') }}" class="quick-action-btn warning">
            <div class="quick-action-icon warning-bg">
                <i class="fas fa-cash-register"></i>
            </div>
            <div class="quick-action-content">
                <div class="quick-action-title">Registrar Pago</div>
                <div class="quick-action-description">Nuevo pago de socio</div>
            </div>
        </a>

        <a href="{{ route('tickets.create') }}" class="quick-action-btn danger">
            <div class="quick-action-icon danger-bg">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="quick-action-content">
                <div class="quick-action-title">Nuevo Ticket</div>
                <div class="quick-action-description">Reportar incidente o reclamo</div>
            </div>
        </a>

        <a href="{{ route('reportes.index') }}" class="quick-action-btn info">
            <div class="quick-action-icon info-bg">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="quick-action-content">
                <div class="quick-action-title">Centro de Reportes</div>
                <div class="quick-action-description">Ver estadísticas y reportes</div>
            </div>
        </a>
    </div>

    <div class="section-card">
        <h3 class="section-title">
            <i class="fas fa-history"></i>
            Actividad Reciente
        </h3>

        <ul class="activity-list">
            @forelse($actividad as $item)
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">{{ $item->descripcion }}</div>
                        <div class="activity-meta">
                            <span class="activity-user">
                                <i class="fas fa-user"></i>
                                @if($item->nombre && $item->apellido)
                                    {{ $item->nombre }} {{ $item->apellido }}
                                @elseif($item->nombre_usuario)
                                    {{ $item->nombre_usuario }}
                                @else
                                    Sistema
                                @endif
                            </span>
                            <span class="activity-time">
                                <i class="fas fa-clock"></i>
                                {{ date('d/m/Y H:i', strtotime($item->fecha_creacion)) }}
                            </span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">No hay actividad reciente.</div>
                    </div>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
