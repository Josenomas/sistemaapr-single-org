@extends('layouts.app')

@section('title', 'Notificaciones del Sistema')

@section('content')
<div class="container">
    <div class="page-header">
        <div class="header-content">
            <div>
                <h1><i class="fas fa-bell"></i> Notificaciones del Sistema</h1>
                <p class="text-muted">Centro de notificaciones y alertas importantes</p>
            </div>
            @if($contadorNoLeidas > 0)
                <button onclick="marcarTodasLeidas(event)" class="btn btn-primary">
                    <i class="fas fa-check-double"></i>
                    Marcar todas como leídas
                </button>
            @endif
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <div class="filters-row">
            <div class="filter-group">
                <label>Mostrar:</label>
                <select id="filtro" class="form-select" onchange="aplicarFiltros()">
                    <option value="todas" {{ $filtro === 'todas' ? 'selected' : '' }}>Todas</option>
                    <option value="no_leidas" {{ $filtro === 'no_leidas' ? 'selected' : '' }}>No leídas ({{ $contadorNoLeidas }})</option>
                    <option value="leidas" {{ $filtro === 'leidas' ? 'selected' : '' }}>Leídas</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Tipo:</label>
                <select id="tipo" class="form-select" onchange="aplicarFiltros()">
                    <option value="">Todos los tipos</option>
                    <option value="pago_pendiente" {{ $tipo === 'pago_pendiente' ? 'selected' : '' }}>Pagos pendientes</option>
                    <option value="pago_vencido" {{ $tipo === 'pago_vencido' ? 'selected' : '' }}>Pagos vencidos</option>
                    <option value="cuenta_suspendida" {{ $tipo === 'cuenta_suspendida' ? 'selected' : '' }}>Cuenta suspendida</option>
                    <option value="limite_socios" {{ $tipo === 'limite_socios' ? 'selected' : '' }}>Límite de socios</option>
                    <option value="limite_usuarios" {{ $tipo === 'limite_usuarios' ? 'selected' : '' }}>Límite de usuarios</option>
                    <option value="cambio_plan" {{ $tipo === 'cambio_plan' ? 'selected' : '' }}>Cambio de plan</option>
                    <option value="bienvenida" {{ $tipo === 'bienvenida' ? 'selected' : '' }}>Bienvenida</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Lista de notificaciones -->
    @if($notificaciones->count() > 0)
        <div class="notificaciones-lista">
            @foreach($notificaciones as $notificacion)
                <div class="notificacion-card {{ !$notificacion->leida ? 'no-leida' : '' }} prioridad-{{ $notificacion->prioridad }}">
                    <div class="notificacion-icono color-{{ $notificacion->color ?? 'primary' }}">
                        <i class="fas {{ $notificacion->icono ?? 'fa-bell' }}"></i>
                    </div>

                    <div class="notificacion-contenido">
                        <div class="notificacion-header">
                            <h3>{{ $notificacion->titulo }}</h3>
                            <span class="badge badge-{{ $notificacion->color ?? 'primary' }}">
                                {{ ucfirst($notificacion->prioridad) }}
                            </span>
                        </div>

                        <p>{{ $notificacion->mensaje }}</p>

                        <div class="notificacion-meta">
                            <span class="fecha">
                                <i class="fas fa-clock"></i>
                                {{ $notificacion->created_at->diffForHumans() }}
                            </span>

                            @if(!$notificacion->leida)
                                <span class="badge-no-leida">
                                    <i class="fas fa-circle"></i> No leída
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="notificacion-acciones">
                        @if($notificacion->url)
                            <a href="{{ $notificacion->url }}"
                               class="btn btn-sm btn-{{ $notificacion->color ?? 'primary' }}"
                               onclick="marcarLeida({{ $notificacion->id }}, event)">
                                {{ $notificacion->texto_accion ?? 'Ver más' }}
                            </a>
                        @endif

                        @if(!$notificacion->leida)
                            <button onclick="marcarLeida({{ $notificacion->id }}, event)"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="Marcar como leída">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif

                        <button onclick="eliminarNotificacion({{ $notificacion->id }}, event)"
                                class="btn btn-sm btn-outline-danger"
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="pagination-container">
            {{ $notificaciones->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <h3>No hay notificaciones</h3>
            <p>
                @if($filtro === 'no_leidas')
                    No tienes notificaciones sin leer.
                @else
                    No tienes notificaciones en este momento.
                @endif
            </p>
        </div>
    @endif
</div>

<style>
    .page-header {
        margin-bottom: 30px;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .filters-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--shadow);
        margin-bottom: 24px;
    }

    .filters-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filter-group label {
        font-weight: 600;
        color: var(--gray-700);
        margin: 0;
    }

    .form-select {
        min-width: 200px;
        padding: 8px 12px;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .notificaciones-lista {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .notificacion-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--shadow);
        display: flex;
        gap: 20px;
        align-items: flex-start;
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }

    .notificacion-card.no-leida {
        background: #f8f9fa;
        border-left-color: var(--primary);
    }

    .notificacion-card.prioridad-urgente {
        border-left-color: var(--danger);
    }

    .notificacion-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .notificacion-icono {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .notificacion-icono.color-primary {
        background: var(--primary-light);
        color: var(--primary);
    }

    .notificacion-icono.color-success {
        background: #d1fae5;
        color: #065f46;
    }

    .notificacion-icono.color-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .notificacion-icono.color-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .notificacion-icono.color-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .notificacion-contenido {
        flex: 1;
    }

    .notificacion-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
        gap: 12px;
    }

    .notificacion-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .notificacion-contenido p {
        color: var(--gray-600);
        margin: 0 0 12px 0;
        line-height: 1.6;
    }

    .notificacion-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 0.875rem;
    }

    .notificacion-meta .fecha {
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .badge-no-leida {
        color: var(--primary);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .badge-no-leida i {
        font-size: 0.5rem;
    }

    .notificacion-acciones {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-primary {
        background: var(--primary-light);
        color: var(--primary);
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

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow);
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--gray-300);
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        color: var(--gray-700);
        margin-bottom: 8px;
    }

    .empty-state p {
        color: var(--gray-500);
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.875rem;
    }

    .btn-outline-secondary {
        background: white;
        border: 1px solid var(--gray-300);
        color: var(--gray-700);
    }

    .btn-outline-secondary:hover {
        background: var(--gray-100);
    }

    .btn-outline-danger {
        background: white;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .btn-outline-danger:hover {
        background: #fee2e2;
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .notificacion-card {
            flex-direction: column;
        }

        .notificacion-acciones {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<script>
    function aplicarFiltros() {
        const filtro = document.getElementById('filtro').value;
        const tipo = document.getElementById('tipo').value;

        let url = '{{ route("notificaciones-sistema.index") }}?filtro=' + filtro;

        if (tipo) {
            url += '&tipo=' + tipo;
        }

        window.location.href = url;
    }

    function marcarLeida(id, event) {
        fetch(`/notificaciones-sistema/${id}/marcar-leida`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const card = event.target.closest('.notificacion-card');
                if (card) {
                    card.classList.remove('no-leida');
                    const badge = card.querySelector('.badge-no-leida');
                    if (badge) badge.remove();
                    const btn = event.target.closest('button');
                    if (btn) btn.remove();
                }
                const count = document.querySelectorAll('.notificacion-card.no-leida').length;
                const opt = document.querySelector('#filtro option[value="no_leidas"]');
                if (opt) opt.textContent = `No leídas (${count})`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al marcar la notificación');
        });
    }

    function marcarTodasLeidas(event) {
        if (!confirm('¿Marcar todas las notificaciones como leídas?')) {
            return;
        }

        fetch('{{ route("notificaciones-sistema.marcar-todas-leidas") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notificacion-card.no-leida').forEach(c => {
                    c.classList.remove('no-leida');
                    const b = c.querySelector('.badge-no-leida');
                    if (b) b.remove();
                    const btn = c.querySelector('button[onclick*="marcarLeida("]');
                    if (btn && btn.querySelector('.fa-check')) btn.remove();
                });
                event.target.style.display = 'none';
                const opt = document.querySelector('#filtro option[value="no_leidas"]');
                if (opt) opt.textContent = 'No leídas (0)';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al marcar las notificaciones');
        });
    }

    function eliminarNotificacion(id, event) {
        fetch(`/notificaciones-sistema/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const card = event.target.closest('.notificacion-card');
                if (card) {
                    card.style.transition = 'all 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        card.remove();
                        const remaining = document.querySelectorAll('.notificacion-card');
                        if (remaining.length === 0) {
                            document.querySelector('.notificaciones-lista').innerHTML = '<div class="empty-state"><i class="fas fa-bell-slash"></i><h3>No hay notificaciones</h3><p>No tienes notificaciones en este momento.</p></div>';
                        }
                        const count = document.querySelectorAll('.notificacion-card.no-leida').length;
                        const opt = document.querySelector('#filtro option[value="no_leidas"]');
                        if (opt) opt.textContent = `No leídas (${count})`;
                    }, 300);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la notificación');
        });
    }
</script>
@endsection

<script>
let prevCount = 0;
function checkNew() {
    fetch("/notificaciones-sistema/contador", {headers: {"Accept": "application/json", "X-Requested-With": "XMLHttpRequest"}})
    .then(r => r.json())
    .then(d => {
        if (prevCount > 0 && d.total > prevCount) {
            const a = document.createElement("div");
            a.style = "position:fixed;top:80px;right:20px;background:#10b981;color:#fff;padding:16px 24px;border-radius:8px;z-index:9999;cursor:pointer";
            a.innerHTML = "<i class=\"fas fa-bell\"></i> Nueva notificación <button onclick=\"location.reload()\" style=\"background:rgba(255,255,255,.2);border:none;color:#fff;padding:4px 12px;border-radius:4px;margin-left:8px\">Ver</button>";
            document.body.appendChild(a);
            setTimeout(() => a.remove(), 10000);
        }
        prevCount = d.total;
    });
}
if (window.location.pathname.includes("/notificaciones-sistema")) {
    setTimeout(() => { prevCount = document.querySelectorAll(".notificacion-card").length; }, 1000);
    setInterval(checkNew, 5000);
}
</script>
