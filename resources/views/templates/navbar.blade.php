{{-- Navbar Superior --}}
<nav class="navbar navbar-top navbar-expand-lg navbar-dark bg-gradient-primary" id="navbar-main">
    <div class="container-fluid">
        {{-- Botón hamburguesa para abrir sidebar en móviles --}}
        <button class="navbar-toggler-sidenav d-xl-none"
                type="button"
                aria-label="Abrir menú">
            <span class="navbar-toggler-icon-bar"></span>
            <span class="navbar-toggler-icon-bar"></span>
            <span class="navbar-toggler-icon-bar"></span>
        </button>

        <div class="navbar-header">
            <h1 class="navbar-title">{{ $pageTitle }}</h1>
            <nav aria-label="breadcrumb" class="navbar-breadcrumb d-none d-md-block">
                <ol class="breadcrumb">
                    @if(!empty($breadcrumbs) && is_array($breadcrumbs))
                        @foreach($breadcrumbs as $crumb)
                            <li class="breadcrumb-item {{ !empty($crumb['is_active']) ? 'active' : '' }}"
                                @if(!empty($crumb['is_active'])) aria-current="page" @endif>
                                @if(!empty($crumb['icon']))
                                    <i class="{{ $crumb['icon'] }} me-1"></i>
                                @endif
                                @if(!empty($crumb['is_active']))
                                    <span>{{ $crumb['title'] ?? '' }}</span>
                                @else
                                    <a href="{{ $crumb['url'] ?? '#' }}">{{ $crumb['title'] ?? '' }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                </ol>
            </nav>
        </div>

        {{-- Acciones del Navbar (derecha) --}}
        <ul class="navbar-nav navbar-actions ms-auto align-items-center">
            {{-- Notificaciones --}}
            <li class="nav-item dropdown" id="nav-notification-mercurio">
                <a class="nav-link nav-link-icon position-relative"
                   href="#"
                   role="button"
                   data-bs-toggle="dropdown"
                   aria-expanded="false"
                   title="Notificaciones"
                   id="btnNotificacionesMercurio">
                    <i class="ni ni-bell-55"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                          id="badgeNotificacionesMercurio"
                          style="font-size: 0.65rem;">
                        0
                        <span class="visually-hidden">notificaciones sin leer</span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="min-width: 22rem;">
                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Notificaciones</h6>
                        <span class="badge bg-primary rounded-pill" id="badgeNumNotificacionesMercurio">0 nuevas</span>
                    </div>
                    <div id="notificationListMercurio" class="notification-list" style="max-height: 300px; overflow-y: auto;">
                        <div class="px-3 py-3 text-muted small text-center">Cargando...</div>
                    </div>
                    <div class="p-2 border-top text-center">
                        <a href="{{ route('mercurio.notificaciones.consulta') }}" class="text-decoration-none">
                            Ver todas las notificaciones
                        </a>
                    </div>
                </div>
            </li>

            {{-- Accesos Rápidos --}}
            <li class="nav-item dropdown">
                <a class="nav-link nav-link-icon" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Accesos rápidos">
                    <i class="fas fa-th-large"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end dropdown-shortcuts">
                    <div class="dropdown-header">
                        <span>Accesos Rápidos</span>
                    </div>
                    <div class="dropdown-body">
                        @php
                            $tipo = session('tipo');
                            $action = in_array($tipo, ['T', 'I', 'O', 'F', 'P'], true) ? 'trabajador' : 'empresa';
                        @endphp
                        <div class="shortcuts-grid">
                            <a href="{{ route("{$action}.historial") }}" class="shortcut-item">
                                <span class="shortcut-icon bg-gradient-danger">
                                    <i class="ni ni-book-bookmark"></i>
                                </span>
                                <span class="shortcut-label">Historial</span>
                            </a>
                            <a href="#" class="shortcut-item" data-toggle="navbar-change-email" data-url="{{ route("{$action}.cambio_email") }}">
                                <span class="shortcut-icon bg-gradient-warning">
                                    <i class="ni ni-email-83"></i>
                                </span>
                                <span class="shortcut-label">Email</span>
                            </a>
                            <a href="#" class="shortcut-item" data-toggle="navbar-change-clave" data-url="{{ route("{$action}.cambio_clave") }}">
                                <span class="shortcut-icon bg-gradient-info">
                                    <i class="ni ni-key-25"></i>
                                </span>
                                <span class="shortcut-label">Contraseña</span>
                            </a>
                        </div>
                    </div>
                </div>
            </li>

            {{-- Perfil de Usuario --}}
            <li class="nav-item dropdown nav-item-user">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar avatar-sm">
                        <img src="{{ asset('img/Mercurio/profile-a.png') }}" alt="Avatar" />
                    </span>
                    <span class="nav-link-user-name d-none d-lg-inline">
                        {{ htmlspecialchars($user_name) }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-user">
                    <li class="dropdown-header-user">
                        <div class="dropdown-user-avatar">
                            <img src="{{ asset('img/Mercurio/profile-a.png') }}" alt="Avatar" />
                        </div>
                        <div class="dropdown-user-info">
                            <span class="dropdown-user-name">{{ htmlspecialchars($user_name) }}</span>
                            <span class="dropdown-user-role">Usuario</span>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item dropdown-item-logout" href="{{ route('login.salir') }}">
                            <i class="ni ni-user-run"></i>
                            <span>Cerrar sesión</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

@once
<script>
(function () {
    const refreshUrl = @json(route('mercurio.notificaciones.refresh'));
    const $list = $('#notificationListMercurio');
    const $badgeRound = $('#badgeNotificacionesMercurio');
    const $badgeNum = $('#badgeNumNotificacionesMercurio');

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderItem(item) {
        const estado = item.estado === 'P' ? 'Pendiente de lectura' : (item.estado_detalle || 'Procesada');
        const bg = item.estado === 'P' ? 'bg-info' : 'bg-secondary';
        return `
            <div class="dropdown-item py-3 border-bottom text-wrap">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="${bg} p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="ni ni-bell-55 text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <h6 class="mb-0 fw-bold">${escapeHtml(item.titulo || 'Sin título')}</h6>
                            <small class="text-muted text-nowrap">${escapeHtml(item.hora || '')}</small>
                        </div>
                        <p class="mb-1 small text-muted">${escapeHtml(estado)}</p>
                        <p class="mb-0 small"><i class="fas fa-user-tie me-1"></i>${escapeHtml(item.asesor || 'Asesor COMFACA')}</p>
                    </div>
                </div>
            </div>
        `;
    }

    function refreshNotificacionesMercurio() {
        if (!$list.length) return;

        $.ajax({
            url: refreshUrl,
            type: 'GET',
            dataType: 'json',
        }).done(function (response) {
            if (!response || response.success !== true) {
                $list.html('<div class="px-3 py-3 text-muted small text-center">No fue posible cargar las notificaciones.</div>');
                return;
            }

            const data = response.data || [];
            const badgenum = Number(response.badgenum || 0);

            if (!data.length) {
                $list.html('<div class="px-3 py-3 text-muted small text-center">No hay notificaciones recientes.</div>');
            } else {
                $list.html(data.map(renderItem).join(''));
            }

            $badgeNum.text(badgenum + ' nuevas');
            if (badgenum > 0) {
                $badgeRound.text(badgenum).removeClass('d-none');
            } else {
                $badgeRound.addClass('d-none');
            }
        }).fail(function () {
            $list.html('<div class="px-3 py-3 text-muted small text-center">Error al consultar notificaciones.</div>');
        });
    }

    $(function () {
        refreshNotificacionesMercurio();
        setInterval(refreshNotificacionesMercurio, 60000);
    });
})();
</script>
@endonce
