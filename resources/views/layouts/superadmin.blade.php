<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Panel Super-Admin - Sistema APR')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @yield('styles')
    <style>
        :root {
            /* Tema oscuro para super-admin - colores brillantes y visibles */
            --primary: #60a5fa;
            --primary-dark: #3b82f6;
            --primary-light: #dbeafe;
            --secondary: #38bdf8;
            --dark: #0f172a;
            --dark-sidebar: #1e293b;
            --dark-card: #1e293b;
            --text-light: #e2e8f0;
            --text-muted: #94a3b8;
            --border: #334155;
            --success: #34d399;
            --warning: #fbbf24;
            --danger: #f87171;
            --info: #38bdf8;
        }

        /* Tema Claro */
        body.light-mode {
            --dark: #f8fafc;
            --dark-sidebar: #ffffff;
            --dark-card: #ffffff;
            --text-light: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        body.light-mode .sidebar {
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        }

        body.light-mode .menu-item {
            color: #475569;
        }

        body.light-mode .menu-item:hover {
            background: rgba(59, 130, 246, 0.08);
            color: var(--primary);
        }

        body.light-mode .menu-item.active {
            background: var(--primary);
            color: white;
        }

        body.light-mode .form-control,
        body.light-mode .form-select {
            background: #ffffff;
            border-color: #e2e8f0;
            color: #1e293b;
        }

        body.light-mode .form-control:focus,
        body.light-mode .form-select:focus {
            background: #f8fafc;
        }

        body.light-mode tbody tr:hover {
            background: rgba(59, 130, 246, 0.03);
        }

        body.light-mode .topbar,
        body.light-mode .card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        body.light-mode thead {
            background: #f8fafc;
        }

        body.light-mode .card-footer {
            background: #f8fafc;
        }

        /* Ajustes adicionales para modo claro */
        body.light-mode h1,
        body.light-mode h2,
        body.light-mode h3,
        body.light-mode h4,
        body.light-mode h5,
        body.light-mode h6 {
            color: #1e293b;
        }

        body.light-mode .card-header {
            background: #ffffff;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
        }

        body.light-mode .card-header h5 {
            color: #0f172a;
            font-weight: 700;
        }

        body.light-mode p {
            color: #475569;
        }

        body.light-mode label,
        body.light-mode .form-label {
            color: #475569;
        }

        body.light-mode .fw-bold.text-muted {
            color: #64748b !important;
        }

        body.light-mode small {
            color: #64748b;
        }

        body.light-mode .topbar-title {
            color: #1e293b;
        }

        body.light-mode .menu-section-title {
            color: #64748b;
        }

        body.light-mode .sidebar-header h1 {
            color: var(--primary);
        }

        body.light-mode .alert {
            color: inherit;
        }

        body.light-mode .alert-success {
            color: #065f46;
            background: #d1fae5;
        }

        body.light-mode .alert-danger {
            color: #991b1b;
            background: #fee2e2;
        }

        body.light-mode .alert-warning {
            color: #92400e;
            background: #fef3c7;
        }

        body.light-mode .alert-info {
            color: #075985;
            background: #e0f2fe;
        }

        body.light-mode th {
            color: #475569;
        }

        body.light-mode td {
            color: #475569;
        }

        body.light-mode .btn-outline-secondary {
            color: #475569;
            border-color: #cbd5e1;
        }

        body.light-mode .btn-outline-secondary:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        body.light-mode .user-name {
            color: #1e293b;
        }

        /* Mejoras para títulos y textos en cards */
        body.light-mode .card h3,
        body.light-mode .card h4 {
            color: #0f172a;
            font-weight: 600;
        }

        body.light-mode .h3,
        body.light-mode .h4 {
            color: #0f172a;
        }

        body.light-mode .card-body h5 {
            color: #0f172a;
            font-weight: 600;
        }

        body.light-mode .text-primary {
            color: #3b82f6 !important;
        }

        body.light-mode .text-success {
            color: #059669 !important;
        }

        body.light-mode .text-info {
            color: #0891b2 !important;
        }

        body.light-mode .text-warning {
            color: #d97706 !important;
        }

        body.light-mode .bg-white {
            background: #ffffff !important;
        }

        body.light-mode i {
            opacity: 0.8;
        }

        body.light-mode .card-header i {
            opacity: 1;
            color: var(--primary);
        }

        /* Asegurar que cards sean oscuras en modo oscuro */
        body:not(.light-mode) .card {
            background: var(--dark-card) !important;
        }

        body:not(.light-mode) .card-header.bg-white {
            background: var(--dark-card) !important;
        }

        body:not(.light-mode) .bg-white {
            background: var(--dark-card) !important;
        }

        /* Modo claro: mantener fondo blanco */
        body.light-mode .card {
            background: #ffffff !important;
        }

        body.light-mode .card-header.bg-white {
            background: #ffffff !important;
        }

        body.light-mode .bg-white {
            background: #ffffff !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--dark);
            color: var(--text-light);
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--dark-sidebar);
            border-right: 1px solid var(--border);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-header h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-header h1 i {
            font-size: 1.5rem;
            color: var(--secondary);
        }

        .sidebar-menu {
            padding: 1rem;
        }

        .menu-section {
            margin-bottom: 1.5rem;
        }

        .menu-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 0.5rem 0.75rem;
            letter-spacing: 0.05em;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            margin-bottom: 0.25rem;
            border-radius: 0.5rem;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .menu-item:hover {
            background: rgba(96, 165, 250, 0.15);
            color: var(--primary);
        }

        .menu-item.active {
            background: var(--primary);
            color: white;
        }

        .menu-item i {
            width: 20px;
            text-align: center;
        }

        .menu-item .badge {
            margin-left: auto;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .topbar {
            background: var(--dark-card);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-light);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: rgba(96, 165, 250, 0.15);
            border-radius: 0.5rem;
            border: 1px solid var(--primary);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--secondary);
        }

        /* Content Area */
        .content {
            padding: 2rem;
            flex: 1;
        }

        /* Cards */
        .card {
            background: var(--dark-card);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.2);
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--success);
            color: var(--success);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: var(--warning);
            color: var(--warning);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: var(--danger);
            color: var(--danger);
        }

        .alert-info {
            background: rgba(6, 182, 212, 0.1);
            border-color: var(--info);
            color: var(--info);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-outline-secondary {
            background: transparent;
            color: var(--text-light);
            border: 1px solid var(--border);
        }

        .btn-outline-secondary:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: rgba(0, 0, 0, 0.2);
        }

        th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
        }

        tbody tr:hover {
            background: rgba(96, 165, 250, 0.08);
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .bg-primary { background: var(--primary); color: white; }
        .bg-success { background: var(--success); color: white; }
        .bg-warning { background: var(--warning); color: white; }
        .bg-danger { background: var(--danger); color: white; }
        .bg-secondary { background: var(--text-muted); color: white; }
        .bg-info { background: var(--info); color: white; }

        /* Utility Classes */
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-muted { color: var(--text-muted); }
        .text-success { color: var(--success); }
        .text-warning { color: var(--warning); }
        .text-danger { color: var(--danger); }

        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }

        .d-flex { display: flex; }
        .align-items-center { align-items: center; }
        .justify-content-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }

        .w-100 { width: 100%; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                padding: 1rem;
            }

            .content {
                padding: 1rem;
            }
        }

        /* Forms */
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .form-control, .form-select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 0.375rem;
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(96, 165, 250, 0.08);
        }

        /* Grid */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -0.5rem;
        }

        [class*="col-"] {
            padding: 0 0.5rem;
        }

        .col-md-3 { width: 25%; }
        .col-md-4 { width: 33.333%; }
        .col-md-6 { width: 50%; }
        .col-md-8 { width: 66.666%; }
        .col-md-12 { width: 100%; }

        @media (max-width: 768px) {
            [class*="col-md-"] {
                width: 100%;
            }
        }

        /* Shadow utilities */
        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2);
        }

        .border-0 {
            border: none !important;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        /* Theme Toggle Button */
        .theme-toggle {
            position: relative;
            width: 60px;
            height: 30px;
            background: var(--border);
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            padding: 0;
        }

        .theme-toggle:hover {
            background: var(--primary);
        }

        .theme-toggle-slider {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        body.light-mode .theme-toggle {
            background: var(--primary);
        }

        body.light-mode .theme-toggle-slider {
            left: 33px;
        }

        .theme-toggle-icon {
            color: #f59e0b;
        }

        body.light-mode .theme-toggle-icon {
            color: #3b82f6;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h1>
                <i class="fas fa-crown"></i>
                Super Admin
            </h1>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Principal</div>
                <a href="{{ route('superadmin.dashboard') }}" class="menu-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Gestión</div>
                <a href="{{ route('superadmin.organizaciones') }}" class="menu-item {{ request()->routeIs('superadmin.organizaciones*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>Organizaciones</span>
                </a>
                <a href="{{ route('superadmin.registros.pendientes') }}" class="menu-item {{ request()->routeIs('superadmin.registros*') ? 'active' : '' }}">
                    <i class="fas fa-user-clock"></i>
                    <span>Registros Pendientes</span>
                    @php
                        $pendientes = \App\Models\RegistroOrganizacion::where('estado', 'pendiente')->count();
                    @endphp
                    @if($pendientes > 0)
                        <span class="badge bg-warning">{{ $pendientes }}</span>
                    @endif
                </a>
                <a href="{{ route('superadmin.dominios.index') }}" class="menu-item {{ request()->routeIs('superadmin.dominios.index') ? 'active' : '' }}">
                    <i class="fas fa-globe"></i>
                    <span>Dominios Personalizados</span>
                    @php
                        $dominiosVerificados = \App\Models\Organizacion::where('estado_dominio_personalizado', 'verificado_dns')->count();
                    @endphp
                    @if($dominiosVerificados > 0)
                        <span class="badge bg-success">{{ $dominiosVerificados }}</span>
                    @endif
                </a>
                <a href="{{ route('superadmin.solicitudes-dominio.index') }}" class="menu-item {{ request()->routeIs('superadmin.solicitudes-dominio.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Solicitudes Dominio</span>
                    @php
                        $solicitudesDominio = \App\Models\SolicitudCompraDominio::whereIn('estado', ['solicitado', 'verificado_disponible', 'pendiente_pago', 'pagado', 'comprado'])->count();
                    @endphp
                    @if($solicitudesDominio > 0)
                        <span class="badge bg-warning">{{ $solicitudesDominio }}</span>
                    @endif
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Suscripciones</div>
                <a href="{{ route('superadmin.renovaciones') }}" class="menu-item {{ request()->routeIs('superadmin.renovaciones') ? 'active' : '' }}">
                    <i class="fas fa-sync-alt"></i>
                    <span>Renovaciones</span>
                </a>
                <a href="{{ route('superadmin.solicitudes-pago.index') }}" class="menu-item {{ request()->routeIs('superadmin.solicitudes-pago.*') ? 'active' : '' }}">
                    <i class="fas fa-money-check-alt"></i>
                    <span>Pagos Manuales</span>
                    @php
                        $solicitudesPendientes = \App\Models\SolicitudPagoManual::where('estado', 'pendiente')->count();
                    @endphp
                    @if($solicitudesPendientes > 0)
                        <span class="badge bg-warning ms-auto">{{ $solicitudesPendientes }}</span>
                    @endif
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Reportes</div>
                <a href="{{ route('superadmin.reportes.financiero') }}" class="menu-item {{ request()->routeIs('superadmin.reportes.financiero') ? 'active' : '' }}">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Reporte Financiero</span>
                </a>
                <a href="{{ route('superadmin.reportes.uso') }}" class="menu-item {{ request()->routeIs('superadmin.reportes.uso') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reporte de Uso</span>
                </a>
                <a href="{{ route('superadmin.reportes.comparativo') }}" class="menu-item {{ request()->routeIs('superadmin.reportes.comparativo') ? 'active' : '' }}">
                    <i class="fas fa-balance-scale"></i>
                    <span>Comparativo</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Sistema</div>
                <a href="{{ route('superadmin.reclamos.index') }}" class="menu-item {{ request()->routeIs('superadmin.reclamos.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Libro de Reclamos</span>
                    @php
                        $reclamosPendientes = \App\Models\Reclamo::whereIn('estado', ['pendiente', 'en_revision'])->count();
                    @endphp
                    @if($reclamosPendientes > 0)
                        <span class="badge bg-danger">{{ $reclamosPendientes }}</span>
                    @endif
                </a>
                <a href="{{ route('superadmin.auditoria') }}" class="menu-item {{ request()->routeIs('superadmin.auditoria') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Auditoría y Logs</span>
                </a>
                <a href="{{ route('landing') }}" class="menu-item" target="_blank">
                    <i class="fas fa-home"></i>
                    <span>Ver Landing Page</span>
                    <i class="fas fa-external-link-alt" style="margin-left: auto; font-size: 0.75rem;"></i>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Configuración</div>
                <a href="{{ route('superadmin.perfil') }}" class="menu-item {{ request()->routeIs('superadmin.perfil') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span>Mi Perfil</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <span class="topbar-title">@yield('page-title', 'Panel Super-Administrador')</span>
            </div>
            <div class="topbar-right">
                <!-- Theme Toggle -->
                <button type="button" class="theme-toggle" id="themeToggle" title="Cambiar tema">
                    <div class="theme-toggle-slider">
                        <i class="fas fa-moon theme-toggle-icon"></i>
                    </div>
                </button>

                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellido, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <span class="user-name">{{ auth()->user()->nombre_completo }}</span>
                        <span class="user-role">Superadmin</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary" title="Cerrar Sesión">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.querySelector('.theme-toggle-icon');
            const body = document.body;

            // Cargar tema guardado desde localStorage
            const savedTheme = localStorage.getItem('superadmin-theme');
            if (savedTheme === 'light') {
                body.classList.add('light-mode');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }

            // Toggle theme al hacer clic
            themeToggle.addEventListener('click', function() {
                body.classList.toggle('light-mode');

                if (body.classList.contains('light-mode')) {
                    // Modo claro activado
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                    localStorage.setItem('superadmin-theme', 'light');
                } else {
                    // Modo oscuro activado
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                    localStorage.setItem('superadmin-theme', 'dark');
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
