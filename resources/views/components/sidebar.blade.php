<aside class="sidebar">
    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>

        <!-- Socios -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-users"></i>
                <span>Gestión de Socios</span>
            </div>
            <a href="{{ route('socios.index') }}" class="nav-item {{ request()->routeIs('socios.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Ver Socios</span>
            </a>
            <a href="{{ route('socios.create') }}" class="nav-item">
                <i class="fas fa-user-plus"></i>
                <span>Nuevo Socio</span>
            </a>
        </div>

        <!-- Lecturas -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-tachometer-alt"></i>
                <span>Lecturas</span>
            </div>
            <a href="{{ route('lecturas.index') }}" class="nav-item {{ request()->routeIs('lecturas.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Lecturas</span>
            </a>
            <a href="{{ route('lecturas.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Nueva Lectura</span>
            </a>
            <a href="{{ route('lecturas.masivo') }}" class="nav-item">
                <i class="fas fa-clipboard-list"></i>
                <span>Registro Masivo</span>
            </a>
        </div>

        <!-- Boletas -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-file-invoice"></i>
                <span>Boletas</span>
            </div>
            <a href="{{ route('boletas.index') }}" class="nav-item {{ request()->routeIs('boletas.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Boletas</span>
            </a>
            <a href="{{ route('boletas.generar') }}" class="nav-item">
                <i class="fas fa-magic"></i>
                <span>Generar Boletas</span>
            </a>
            <a href="{{ route('boletas.vencidas') }}" class="nav-item">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Boletas Vencidas</span>
            </a>
            <a href="{{ route('folios-sii.index') }}" class="nav-item {{ request()->routeIs('folios-sii.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i>
                <span>Folios SII</span>
                <span class="badge-beta">BETA</span>
            </a>
            <a href="{{ route('dte.dashboard') }}" class="nav-item {{ request()->routeIs('dte.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Facturación Electrónica</span>
                <span class="badge-new">NUEVO</span>
            </a>
        </div>

        <!-- Pagos -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-money-bill-wave"></i>
                <span>Pagos</span>
            </div>
            <a href="{{ route('pagos.index') }}" class="nav-item {{ request()->routeIs('pagos.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Pagos</span>
            </a>
            <a href="{{ route('pagos.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Registrar Pago</span>
            </a>
            <a href="{{ route('pagos.reporteCaja') }}" class="nav-item">
                <i class="fas fa-cash-register"></i>
                <span>Reporte de Caja</span>
            </a>
        </div>

        <!-- Rendición Mensual -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Rendición Mensual</span>
                <span class="badge-beta">BETA</span>
            </div>
            <a href="{{ route('rendiciones-mensuales.index') }}" class="nav-item {{ request()->routeIs('rendiciones-mensuales.*') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Rendiciones</span>
            </a>
            <a href="{{ route('rendiciones-mensuales.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Nueva Rendición</span>
            </a>
        </div>

        <!-- Incidentes -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-exclamation-circle"></i>
                <span>Incidentes</span>
            </div>
            <a href="{{ route('incidentes.index') }}" class="nav-item {{ request()->routeIs('incidentes.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Incidentes</span>
            </a>
            <a href="{{ route('incidentes.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Reportar Incidente</span>
            </a>
        </div>

        <!-- Cortes de Suministro -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-plug"></i>
                <span>Cortes</span>
            </div>
            <a href="{{ route('cortes.index') }}" class="nav-item {{ request()->routeIs('cortes.*') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Cortes</span>
            </a>
            <a href="{{ route('cortes.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Registrar Corte</span>
            </a>
        </div>

        <!-- Trabajos Realizados -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-tools"></i>
                <span>Trabajos</span>
            </div>
            <a href="{{ route('trabajos.index') }}" class="nav-item {{ request()->routeIs('trabajos.*') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Trabajos</span>
            </a>
            <a href="{{ route('trabajos.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Registrar Trabajo</span>
            </a>
        </div>

        <!-- Renovaciones de Medidores -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-sync-alt"></i>
                <span>Renovaciones</span>
            </div>
            <a href="{{ route('renovaciones.index') }}" class="nav-item {{ request()->routeIs('renovaciones.*') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Renovaciones</span>
            </a>
            <a href="{{ route('renovaciones.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Registrar Renovación</span>
            </a>
        </div>

        <!-- Notificaciones -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-bell"></i>
                <span>Notificaciones</span>
            </div>
            <a href="{{ route('notificaciones.index') }}" class="nav-item {{ request()->routeIs('notificaciones.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Notificaciones</span>
            </a>
            <a href="{{ route('notificaciones.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Nueva Notificación</span>
            </a>
        </div>

        <!-- Usuarios -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-user-shield"></i>
                <span>Administración</span>
            </div>
            <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>Usuarios</span>
            </a>
            <a href="{{ route('usuarios.create') }}" class="nav-item">
                <i class="fas fa-user-plus"></i>
                <span>Nuevo Usuario</span>
            </a>
            <a href="{{ route('funcionarios.index') }}" class="nav-item {{ request()->routeIs('funcionarios.*') ? 'active' : '' }}">
                <i class="fas fa-user-tie"></i>
                <span>Funcionarios</span>
            </a>
            <a href="{{ route('funcionarios.create') }}" class="nav-item">
                <i class="fas fa-user-plus"></i>
                <span>Nuevo Funcionario</span>
            </a>
            <a href="{{ route('sueldos.index') }}" class="nav-item {{ request()->routeIs('sueldos.*') ? 'active' : '' }}">
                <i class="fas fa-money-check-alt"></i>
                <span>Sueldos</span>
            </a>
            <a href="{{ route('sueldos.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Registrar Sueldo</span>
            </a>
            <a href="{{ route('vacaciones.index') }}" class="nav-item {{ request()->routeIs('vacaciones.*') ? 'active' : '' }}">
                <i class="fas fa-umbrella-beach"></i>
                <span>Vacaciones</span>
            </a>
            <a href="{{ route('vacaciones.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Registrar Vacación</span>
            </a>
            <a href="{{ route('compras.index') }}" class="nav-item {{ request()->routeIs('compras.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Compras</span>
            </a>
            <a href="{{ route('compras.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Registrar Compra</span>
            </a>
            <a href="{{ route('activos-fijos.index') }}" class="nav-item {{ request()->routeIs('activos-fijos.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Activos Fijos</span>
            </a>
            <a href="{{ route('activos-fijos.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Registrar Activo</span>
            </a>
            <a href="{{ route('giros-bancarios.index') }}" class="nav-item {{ request()->routeIs('giros-bancarios.*') ? 'active' : '' }}">
                <i class="fas fa-money-check-alt"></i>
                <span>Giros Bancarios</span>
            </a>
            <a href="{{ route('giros-bancarios.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Nuevo Giro</span>
            </a>
            <a href="{{ route('directiva.index') }}" class="nav-item {{ request()->routeIs('directiva.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>Directiva</span>
            </a>
            <a href="{{ route('directiva.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Nuevo Miembro</span>
            </a>
            <a href="{{ route('inventario.index') }}" class="nav-item {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i>
                <span>Inventario</span>
            </a>
            <a href="{{ route('inventario.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Registrar Producto</span>
            </a>
            <a href="{{ route('movimientos-inventario.index') }}" class="nav-item {{ request()->routeIs('movimientos-inventario.*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt"></i>
                <span>Movimientos</span>
            </a>
            <a href="{{ route('movimientos-inventario.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Nuevo Movimiento</span>
            </a>
            <a href="{{ route('tickets.index') }}" class="nav-item {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i>
                <span>Tickets</span>
            </a>
            <a href="{{ route('tickets.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Crear Ticket</span>
            </a>
            <a href="{{ route('recordatorios.index') }}" class="nav-item {{ request()->routeIs('recordatorios.*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>
                <span>Recordatorios</span>
            </a>
            <a href="{{ route('recordatorios.create') }}" class="nav-item">
                <i class="fas fa-plus"></i>
                <span>Crear Recordatorio</span>
            </a>
            <a href="{{ route('historial-consumo.index') }}" class="nav-item {{ request()->routeIs('historial-consumo.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Historial Consumo</span>
            </a>
            <a href="{{ route('historial-consumo.comparar') }}" class="nav-item">
                <i class="fas fa-exchange-alt"></i>
                <span>Comparar Consumo</span>
            </a>
            <a href="{{ route('historial-pagos.index') }}" class="nav-item {{ request()->routeIs('historial-pagos.*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i>
                <span>Historial Pagos</span>
            </a>
            <a href="{{ route('historial-pagos.comparar') }}" class="nav-item">
                <i class="fas fa-exchange-alt"></i>
                <span>Comparar Pagos</span>
            </a>
            <a href="{{ route('configuraciones-tarifas.index') }}" class="nav-item {{ request()->routeIs('configuraciones-tarifas.index') || request()->routeIs('configuraciones-tarifas.create') || request()->routeIs('configuraciones-tarifas.edit') ? 'active' : '' }}">
                <i class="fas fa-sliders-h"></i>
                <span>Config. Tarifarias</span>
            </a>
            <a href="{{ route('configuraciones-tarifas.simulador') }}" class="nav-item {{ request()->routeIs('configuraciones-tarifas.simulador') ? 'active' : '' }}">
                <i class="fas fa-calculator"></i>
                <span>Simulador de Tarifas</span>
            </a>
        </div>

        <!-- Eventos -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-calendar-alt"></i>
                <span>Eventos</span>
            </div>
            <a href="{{ route('eventos.index') }}" class="nav-item {{ request()->routeIs('eventos.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>Ver Eventos</span>
            </a>
            <a href="{{ route('eventos.create') }}" class="nav-item">
                <i class="fas fa-calendar-plus"></i>
                <span>Nuevo Evento</span>
            </a>
        </div>

        <!-- Noticias (Solo Enterprise) -->
        @if(auth()->user()->organizacion && auth()->user()->organizacion->puedeAccederModulo('noticias'))
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-newspaper"></i>
                <span>Noticias</span>
            </div>
            <a href="{{ route('noticias.index') }}" class="nav-item {{ request()->routeIs('noticias.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Noticias</span>
            </a>
            <a href="{{ route('noticias.create') }}" class="nav-item {{ request()->routeIs('noticias.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                <span>Nueva Noticia</span>
            </a>
            <a href="{{ route('noticias.publicas', auth()->user()->organizacion->slug) }}" class="nav-item" target="_blank">
                <i class="fas fa-eye"></i>
                <span>Vista Pública</span>
            </a>
        </div>
        @endif

        <!-- Reportes -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-chart-pie"></i>
                <span>Reportes</span>
            </div>
            <a href="{{ route('reportes.index') }}" class="nav-item {{ request()->routeIs('reportes.index') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Centro de Reportes</span>
            </a>
            <a href="{{ route('reportes.socios') }}" class="nav-item {{ request()->routeIs('reportes.socios') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Reporte de Socios</span>
            </a>
            <a href="{{ route('reportes.financiero') }}" class="nav-item {{ request()->routeIs('reportes.financiero') ? 'active' : '' }}">
                <i class="fas fa-dollar-sign"></i>
                <span>Reporte Financiero</span>
            </a>
            <a href="{{ route('reportes.consumo') }}" class="nav-item {{ request()->routeIs('reportes.consumo') ? 'active' : '' }}">
                <i class="fas fa-tint"></i>
                <span>Reporte de Consumo</span>
            </a>
            <a href="{{ route('reportes.operacional') }}" class="nav-item {{ request()->routeIs('reportes.operacional') ? 'active' : '' }}">
                <i class="fas fa-cogs"></i>
                <span>Reporte Operacional</span>
            </a>
        </div>

        <!-- Actividad Reciente -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-history"></i>
                <span>Actividad</span>
            </div>
            <a href="{{ route('actividades.index') }}" class="nav-item {{ request()->routeIs('actividades.*') ? 'active' : '' }}">
                <i class="fas fa-list"></i>
                <span>Ver Actividades</span>
            </a>
        </div>

        <!-- Mi Organización -->
        <div class="nav-section">
            <div class="nav-section-title">
                <i class="fas fa-building"></i>
                <span>Organización</span>
            </div>
            <a href="{{ route('organizacion.index') }}" class="nav-item {{ request()->routeIs('organizacion.index') ? 'active' : '' }}">
                <i class="fas fa-info-circle"></i>
                <span>Mi Organización</span>
            </a>
            <a href="{{ route('organizacion.edit') }}" class="nav-item {{ request()->routeIs('organizacion.edit') ? 'active' : '' }}">
                <i class="fas fa-edit"></i>
                <span>Editar Organización</span>
            </a>
            <a href="{{ route('organizacion.upgrade') }}" class="nav-item {{ request()->routeIs('organizacion.upgrade') ? 'active' : '' }}">
                <i class="fas fa-arrow-up"></i>
                <span>Cambiar Plan</span>
            </a>
            <a href="{{ route('organizacion.pagos-suscripcion') }}" class="nav-item {{ request()->routeIs('organizacion.pagos-suscripcion') ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i>
                <span>Historial de Pagos</span>
            </a>
        </div>
    </nav>
</aside>

<style>
    .sidebar {
        width: 260px;
        background: var(--white);
        border-right: 1px solid var(--gray-200);
        height: calc(100vh - 74px);
        position: fixed;
        left: 0;
        top: 74px;
        overflow-y: auto;
        box-shadow: var(--shadow);
        z-index: 100;
    }

    .sidebar-nav {
        padding: 16px 0;
    }

    .nav-section {
        margin-bottom: 24px;
    }

    .nav-section-title {
        padding: 12px 20px;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--gray-500);
        font-weight: 700;
        letter-spacing: 0.1em;
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
    }

    .badge-beta {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 0.65rem;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 700;
        letter-spacing: 0.05em;
        margin-left: auto;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
    }

    .badge-new {
        display: inline-block;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        font-size: 0.65rem;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 700;
        letter-spacing: 0.05em;
        margin-left: auto;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
    }

    .nav-item .badge-beta,
    .nav-item .badge-new {
        font-size: 0.6rem;
        padding: 2px 5px;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: var(--gray-700);
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.875rem;
        font-weight: 500;
        border-left: 3px solid transparent;
    }

    .nav-item:hover {
        background: var(--primary-light);
        color: var(--primary-dark);
        border-left-color: var(--primary);
    }

    .nav-item.active {
        background: var(--primary-light);
        color: var(--primary-dark);
        border-left-color: var(--primary);
        font-weight: 600;
    }

    .nav-item i {
        width: 20px;
        text-align: center;
        font-size: 1rem;
    }

    .main-wrapper {
        display: flex;
    }

    .content-wrapper {
        flex: 1;
        margin-left: 260px;
        padding: 32px;
        min-height: calc(100vh - 74px);
        max-width: calc(100% - 260px);
        overflow-x: hidden;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .content-wrapper {
            padding: 20px;
            max-width: calc(100% - 260px);
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            left: -260px;
            transition: left 0.3s;
        }

        .sidebar.active {
            left: 0;
        }

        .content-wrapper {
            margin-left: 0;
            padding: 15px;
            max-width: 100%;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }

        .sidebar-overlay.active {
            display: block;
        }
    }

    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: var(--gray-100);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: var(--gray-300);
        border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: var(--gray-400);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');

    // Restaurar posición del scroll al cargar la página
    const savedScrollPosition = localStorage.getItem('sidebarScrollPosition');
    if (savedScrollPosition !== null) {
        sidebar.scrollTop = parseInt(savedScrollPosition);
    }

    // Guardar posición del scroll cuando el usuario hace scroll
    let scrollTimeout;
    sidebar.addEventListener('scroll', function() {
        // Usar debounce para evitar guardar en cada pixel de scroll
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            localStorage.setItem('sidebarScrollPosition', sidebar.scrollTop);
        }, 100);
    });

    // Guardar posición antes de navegar a otra página
    const navItems = sidebar.querySelectorAll('.nav-item');
    navItems.forEach(function(item) {
        item.addEventListener('click', function() {
            localStorage.setItem('sidebarScrollPosition', sidebar.scrollTop);
        });
    });
});
</script>
