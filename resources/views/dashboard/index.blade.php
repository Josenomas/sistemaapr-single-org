@extends('layouts.app')

@section('title', 'Dashboard - Sistema APR')

@section('styles')
<style>
    /* Animaciones Globales */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Aplicar fade-in al cargar */
    .dashboard-header {
        animation: fadeIn 0.6s ease-out;
    }

    .stat-card {
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }

    .quick-action-btn {
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .quick-action-btn:nth-child(1) { animation-delay: 0.5s; }
    .quick-action-btn:nth-child(2) { animation-delay: 0.6s; }
    .quick-action-btn:nth-child(3) { animation-delay: 0.7s; }
    .quick-action-btn:nth-child(4) { animation-delay: 0.8s; }
    .quick-action-btn:nth-child(5) { animation-delay: 0.9s; }

    .section-card {
        animation: scaleIn 0.6s ease-out 1s;
        animation-fill-mode: both;
    }

    .activity-item {
        animation: fadeInLeft 0.4s ease-out;
        animation-fill-mode: both;
    }

    .activity-item:nth-child(1) { animation-delay: 1.1s; }
    .activity-item:nth-child(2) { animation-delay: 1.2s; }
    .activity-item:nth-child(3) { animation-delay: 1.3s; }
    .activity-item:nth-child(4) { animation-delay: 1.4s; }
    .activity-item:nth-child(5) { animation-delay: 1.5s; }

    .dashboard-header {
        @php
            $colorPrimario = auth()->user()->organizacion->color_primario ?? '#3b82f6';
            $colorSecundario = auth()->user()->organizacion->color_secundario ?? '#60a5fa';
        @endphp
        background: linear-gradient(135deg, {{ $colorPrimario }} 0%, {{ $colorSecundario }} 100%);
        padding: 32px;
        border-radius: 16px;
        margin-bottom: 32px;
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.25);
        color: white;
    }

    .dashboard-header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .dashboard-title {
        font-size: 2rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        margin: 0;
    }

    .dashboard-title i {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.75rem;
    }

    .header-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .info-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(255, 255, 255, 0.15);
        padding: 12px 20px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .info-badge:hover {
        background: rgba(255, 255, 255, 0.28);
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .info-badge-icon {
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .info-badge:hover .info-badge-icon {
        transform: scale(1.15) rotate(-10deg);
    }

    .info-badge-icon {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    .info-badge-content {
        flex: 1;
    }

    .info-badge-label {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-bottom: 2px;
    }

    .info-badge-value {
        font-size: 1rem;
        font-weight: 700;
    }

    .system-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: default;
    }

    .system-status:hover {
        transform: scale(1.05);
    }

    .system-status.online {
        background: rgba(16, 185, 129, 0.2);
        border: 1px solid rgba(16, 185, 129, 0.4);
    }

    .system-status.offline {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.4);
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        animation: pulse 2s ease-in-out infinite;
    }

    .status-dot.online {
        background: #10b981;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .status-dot.offline {
        background: #ef4444;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Respetar preferencias de accesibilidad */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
        padding-bottom: 32px;
        border-bottom: 1px solid #e5e7eb;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 12px 32px rgba(59, 130, 246, 0.15), 0 4px 16px rgba(0, 0, 0, 0.1);
        border-color: var(--primary);
    }

    .stat-icon {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
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
        padding: 16px;
        border-bottom: 1px solid var(--gray-100);
        display: flex;
        align-items: flex-start;
        gap: 16px;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 8px;
        margin-bottom: 8px;
    }

    .activity-item:hover {
        background: linear-gradient(90deg, rgba(59, 130, 246, 0.03) 0%, rgba(59, 130, 246, 0.01) 100%);
        transform: translateX(8px);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.08);
    }

    .activity-icon {
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .activity-item:hover .activity-icon {
        transform: scale(1.1);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .activity-icon.boletas {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .activity-icon.pagos {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .activity-icon.socios {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .activity-icon.incidentes {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .activity-icon.lecturas {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }

    .activity-icon.usuarios {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .activity-icon.sistema {
        background: linear-gradient(135deg, #6b7280, #4b5563);
    }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
        flex-wrap: wrap;
    }

    .activity-title {
        font-weight: 600;
        font-size: 0.9375rem;
        color: var(--gray-900);
        flex: 1;
    }

    .activity-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .activity-badge.boletas {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .activity-badge.pagos {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .activity-badge.socios {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
    }

    .activity-badge.incidentes {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .activity-badge.lecturas {
        background: rgba(6, 182, 212, 0.1);
        color: #0891b2;
    }

    .activity-badge.usuarios {
        background: rgba(139, 92, 246, 0.1);
        color: #7c3aed;
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
        font-size: 0.8125rem;
        color: var(--gray-600);
        font-weight: 500;
    }

    .activity-user i {
        font-size: 0.75rem;
        color: var(--primary);
    }

    .activity-time {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8125rem;
        color: var(--gray-500);
        font-weight: 500;
    }

    .activity-time i {
        font-size: 0.75rem;
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
        padding-bottom: 32px;
        border-bottom: 1px solid #e5e7eb;
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
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
    }

    .quick-action-btn:hover {
        transform: translateY(-6px) translateX(4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1), 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    .quick-action-btn:active {
        transform: translateY(-2px) translateX(2px);
    }

    .quick-action-icon {
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .quick-action-btn:hover .quick-action-icon {
        transform: scale(1.15) rotate(-5deg);
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

    /* Próximos Eventos */
    .events-section {
        animation: scaleIn 0.6s ease-out 1.6s;
        animation-fill-mode: both;
    }

    .events-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }

    .event-card {
        background: var(--white);
        border-radius: 12px;
        padding: 20px;
        border: 2px solid var(--gray-200);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .event-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .event-card.primary::before {
        background: linear-gradient(180deg, var(--primary), var(--primary-dark));
    }

    .event-card.success::before {
        background: linear-gradient(180deg, #10b981, #059669);
    }

    .event-card.warning::before {
        background: linear-gradient(180deg, #f59e0b, #d97706);
    }

    .event-card.danger::before {
        background: linear-gradient(180deg, #ef4444, #dc2626);
    }

    .event-card:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        border-color: var(--primary);
    }

    .event-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .event-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .event-card:hover .event-icon-wrapper {
        transform: scale(1.15) rotate(-5deg);
    }

    .event-icon-wrapper.primary-bg {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .event-icon-wrapper.success-bg {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .event-icon-wrapper.warning-bg {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .event-icon-wrapper.danger-bg {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .event-content {
        flex: 1;
    }

    .event-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 4px;
    }

    .event-type {
        font-size: 0.75rem;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .event-date {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px;
        background: var(--gray-50);
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .event-date i {
        color: var(--primary);
        font-size: 1rem;
    }

    .event-date-text {
        flex: 1;
    }

    .event-date-label {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-bottom: 2px;
    }

    .event-date-value {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--gray-900);
    }

    .event-countdown {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: rgba(59, 130, 246, 0.1);
        border-radius: 12px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--primary);
    }

    .event-countdown.urgent {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .event-countdown.soon {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .event-description {
        font-size: 0.8125rem;
        color: var(--gray-600);
        line-height: 1.5;
    }

    /* Responsive Mobile */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 20px;
            margin-bottom: 20px;
        }

        .dashboard-title {
            font-size: 1.5rem;
        }

        .header-info-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 20px;
            padding-bottom: 20px;
        }

        .quick-actions-grid {
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 20px;
        }

        .events-grid {
            grid-template-columns: 1fr;
        }

        .section-card {
            padding: 16px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.1rem;
        }

        .stat-value {
            font-size: 1.75rem;
        }
    }

    @media (max-width: 480px) {
        .dashboard-header {
            padding: 16px;
        }

        .dashboard-title {
            font-size: 1.25rem;
        }

        .dashboard-title i {
            font-size: 1.25rem;
        }

        .header-info-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .info-badge {
            padding: 10px 16px;
        }

        .info-badge-icon {
            font-size: 1.25rem;
        }

        .stat-card {
            padding: 16px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 1.25rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .quick-action-icon {
            width: 48px;
            height: 48px;
            font-size: 1.5rem;
        }

        .quick-action-title {
            font-size: 0.9375rem;
        }

        .quick-action-description {
            font-size: 0.75rem;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            font-size: 1.125rem;
        }

        .activity-item {
            padding: 12px;
        }

        .activity-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .event-card {
            padding: 16px;
        }

        .event-icon-wrapper {
            width: 40px;
            height: 40px;
            font-size: 1.125rem;
        }
    }

</style>
@endsection

@section('content')
<div class="dashboard">
    <div class="dashboard-header">
        <div class="dashboard-header-top">
            <h2 class="dashboard-title">
                <i class="fas fa-tachometer-alt"></i>
                Panel de Control
            </h2>
            <div class="system-status online">
                <span class="status-dot online"></span>
                Sistema Operativo
            </div>
        </div>

        <div class="header-info-grid">
            <div class="info-badge">
                <i class="fas fa-calendar-day info-badge-icon"></i>
                <div class="info-badge-content">
                    <div class="info-badge-label">Fecha</div>
                    <div class="info-badge-value">
                        <?php
                            setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
                            $dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                            $dia_semana = $dias[date('w')];
                            $dia = date('d');
                            $mes = $meses[date('n') - 1];
                            echo $dia_semana . ' ' . $dia . ' ' . $mes;
                        ?>
                    </div>
                </div>
            </div>

            <div class="info-badge">
                <i class="fas fa-clock info-badge-icon"></i>
                <div class="info-badge-content">
                    <div class="info-badge-label">Hora</div>
                    <div class="info-badge-value" id="current-time">
                        {{ date('H:i') }}
                    </div>
                </div>
            </div>

            <div class="info-badge">
                <i class="fas fa-user-circle info-badge-icon"></i>
                <div class="info-badge-content">
                    <div class="info-badge-label">Usuario</div>
                    <div class="info-badge-value">{{ auth()->user()->nombre ?? 'Admin' }}</div>
                </div>
            </div>
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

    <h2 class="section-title">
        <i class="fas fa-bolt"></i>
        Accesos Directos
    </h2>

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

    <div class="section-card events-section">
        <h3 class="section-title">
            <i class="fas fa-calendar-alt"></i>
            Próximos Eventos
        </h3>

        <div class="events-grid">
            @php
                // Obtener eventos activos de la base de datos
                $eventosDB = \App\Models\Evento::activos()
                    ->get()
                    ->sortBy('dias_restantes')
                    ->take(4); // Mostrar máximo 4 eventos
            @endphp

            @forelse($eventosDB as $evento)
                <div class="event-card {{ $evento->color }}">
                    <div class="event-header">
                        <div class="event-icon-wrapper {{ $evento->color }}-bg">
                            <i class="fas {{ $evento->icono }}"></i>
                        </div>
                        <div class="event-content">
                            <div class="event-title">{{ $evento->titulo }}</div>
                            <div class="event-type">{{ $evento->tipo }}</div>
                        </div>
                    </div>

                    <div class="event-date">
                        <i class="fas fa-calendar-day"></i>
                        <div class="event-date-text">
                            <div class="event-date-label">Fecha programada</div>
                            <div class="event-date-value">
                                {{ $evento->proxima_fecha->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                            </div>
                        </div>
                    </div>

                    <div class="event-countdown {{ $evento->countdown_class }}">
                        <i class="fas fa-clock"></i>
                        {{ $evento->countdown_texto }}
                    </div>

                    <p class="event-description" style="margin-top: 12px;">
                        {{ $evento->descripcion }}
                    </p>
                </div>
            @empty
                <div class="event-card primary">
                    <div class="event-header">
                        <div class="event-icon-wrapper primary-bg">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="event-content">
                            <div class="event-title">No hay eventos programados</div>
                            <div class="event-type">SISTEMA</div>
                        </div>
                    </div>
                    <p class="event-description">
                        <a href="{{ route('eventos.create') }}" style="color: var(--primary); text-decoration: none;">
                            <i class="fas fa-plus-circle"></i> Crear primer evento
                        </a>
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="section-card">
        <h3 class="section-title">
            <i class="fas fa-history"></i>
            Actividad Reciente
        </h3>

        <ul class="activity-list">
            @forelse($actividad as $item)
                @php
                    // Detectar tipo de actividad
                    $tipo = 'sistema';
                    $icono = 'fa-info-circle';

                    if (stripos($item->modulo, 'boleta') !== false) {
                        $tipo = 'boletas';
                        $icono = 'fa-file-invoice';
                    } elseif (stripos($item->modulo, 'pago') !== false) {
                        $tipo = 'pagos';
                        $icono = 'fa-cash-register';
                    } elseif (stripos($item->modulo, 'socio') !== false) {
                        $tipo = 'socios';
                        $icono = 'fa-users';
                    } elseif (stripos($item->modulo, 'incidente') !== false || stripos($item->modulo, 'ticket') !== false) {
                        $tipo = 'incidentes';
                        $icono = 'fa-exclamation-triangle';
                    } elseif (stripos($item->modulo, 'lectura') !== false) {
                        $tipo = 'lecturas';
                        $icono = 'fa-tint';
                    } elseif (stripos($item->modulo, 'usuario') !== false) {
                        $tipo = 'usuarios';
                        $icono = 'fa-user-shield';
                    }

                    // Calcular tiempo relativo
                    $fecha = strtotime($item->fecha_creacion);
                    $ahora = time();
                    $diferencia = $ahora - $fecha;

                    if ($diferencia < 60) {
                        $tiempoRelativo = 'Hace unos segundos';
                    } elseif ($diferencia < 3600) {
                        $minutos = floor($diferencia / 60);
                        $tiempoRelativo = 'Hace ' . $minutos . ' min';
                    } elseif ($diferencia < 86400) {
                        $horas = floor($diferencia / 3600);
                        $tiempoRelativo = 'Hace ' . $horas . ' ' . ($horas == 1 ? 'hora' : 'horas');
                    } elseif ($diferencia < 604800) {
                        $dias = floor($diferencia / 86400);
                        $tiempoRelativo = 'Hace ' . $dias . ' ' . ($dias == 1 ? 'día' : 'días');
                    } else {
                        $tiempoRelativo = date('d/m/Y', $fecha);
                    }
                @endphp
                <li class="activity-item">
                    <div class="activity-icon {{ $tipo }}">
                        <i class="fas {{ $icono }}"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-header">
                            <div class="activity-title">{{ $item->descripcion }}</div>
                            <span class="activity-badge {{ $tipo }}">{{ $item->modulo }}</span>
                        </div>
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
                                {{ $tiempoRelativo }}
                            </span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="activity-item">
                    <div class="activity-icon sistema">
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

<script>
// Actualizar reloj en tiempo real
function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('current-time').textContent = `${hours}:${minutes}`;
}

// Actualizar cada minuto
setInterval(updateClock, 60000);
updateClock();

// Obtener clima (usando geolocalización y API gratuita)
async function getWeather() {
    try {
        // Obtener geolocalización del navegador
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async (position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                // Usar API de Open-Meteo (gratuita, sin necesidad de API key)
                const response = await fetch(
                    `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&timezone=auto`
                );

                if (response.ok) {
                    const data = await response.json();
                    const temp = Math.round(data.current_weather.temperature);
                    const weatherCode = data.current_weather.weathercode;

                    // Actualizar temperatura
                    document.getElementById('weather-temp').textContent = `${temp}°C`;

                    // Actualizar icono según código del clima
                    const weatherIcon = document.querySelector('#weather-badge .info-badge-icon');
                    if (weatherCode === 0) {
                        weatherIcon.className = 'fas fa-sun info-badge-icon';
                    } else if (weatherCode >= 1 && weatherCode <= 3) {
                        weatherIcon.className = 'fas fa-cloud-sun info-badge-icon';
                    } else if (weatherCode >= 45 && weatherCode <= 67) {
                        weatherIcon.className = 'fas fa-cloud-rain info-badge-icon';
                    } else if (weatherCode >= 71 && weatherCode <= 86) {
                        weatherIcon.className = 'fas fa-snowflake info-badge-icon';
                    } else {
                        weatherIcon.className = 'fas fa-cloud info-badge-icon';
                    }
                }
            }, (error) => {
                // Si falla la geolocalización, mostrar mensaje genérico
                console.log('Geolocalización no disponible');
                document.getElementById('weather-temp').textContent = 'N/A';
            });
        } else {
            document.getElementById('weather-temp').textContent = 'N/A';
        }
    } catch (error) {
        console.error('Error obteniendo clima:', error);
        document.getElementById('weather-temp').textContent = 'N/A';
    }
}

// Obtener clima al cargar la página
getWeather();

// Actualizar clima cada 30 minutos
setInterval(getWeather, 1800000);
</script>
@endsection
