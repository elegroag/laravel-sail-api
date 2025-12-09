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

        {{-- Título y Breadcrumbs --}}
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
                        <div class="shortcuts-grid">
                            <a href="{{ route('movimientos.historial') }}" class="shortcut-item">
                                <span class="shortcut-icon bg-gradient-danger">
                                    <i class="ni ni-book-bookmark"></i>
                                </span>
                                <span class="shortcut-label">Historial</span>
                            </a>
                            <a href="{{ route('movimientos.cambio_email_view') }}" class="shortcut-item">
                                <span class="shortcut-icon bg-gradient-warning">
                                    <i class="ni ni-email-83"></i>
                                </span>
                                <span class="shortcut-label">Email</span>
                            </a>
                            <a href="{{ route('movimientos.cambio_clave_view') }}" class="shortcut-item">
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