@php
$array_tipos = [
    '' => 'N/A',
    'P' => 'Particular',
    'E' => 'Empresa',
    'T' => 'Trabajador',
    'I' => 'Independiente',
    'O' => 'Pensionado',
    'F' => 'Facultativo',
];
@endphp
{{-- Sidebar Navigation --}}
<nav class="sidenav navbar navbar-vertical navbar-expand-xs navbar-light" id="sidenav-main">
    <div class="scrollbar-inner">
        {{-- Header del Sidebar con Logo y Toggler --}}
        <div class="sidenav-header d-flex align-items-center">
            <a class="navbar-brand text-white" href="{{ route('principal.index') }}">
                <div class="sidenav-logo-wrapper">
                    <img src="{{ asset('img/Mercurio/logo-min.png') }}" class="sidenav-logo-icon" alt="Comfaca" />
                    <span class="sidenav-logo">Comfaca En Linea</span>
                </div>
            </a>
            {{-- Botón para colapsar/expandir sidebar (siempre visible) --}}
            <div class="ms-auto d-flex align-items-center gap-2">
                <button class="sidenav-toggler sidenav-toggler-desktop" 
                        type="button"
                        title="Colapsar/Expandir menú"
                        aria-label="Toggle sidebar">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </div>
                </button>
                {{-- Botón cerrar para móviles --}}
                <button class="sidenav-close d-xl-none" 
                        type="button"
                        aria-label="Cerrar menú">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Contenido del Sidebar --}}
        <div class="navbar-inner">
            <div class="collapse navbar-collapse show" id="sidenav-collapse-main">
                {{-- Información del Usuario --}}
                <div class="sidenav-user-info">
                    <div class="sidenav-user-header">
                        <span class="sidenav-brand-text"></span>
                    </div>
                    <div class="sidenav-user-details">
                        <div class="sidenav-user-detail">
                            <span class="detail-label">Tipo:</span>
                            <span class="detail-value text-info">
                                {{ isset($array_tipos[$_tipo]) ? htmlspecialchars($array_tipos[$_tipo]) : 'N/A' }}
                            </span>
                        </div>
                        <div class="sidenav-user-detail">
                            <span class="detail-label">Estado:</span>
                            <span class="detail-value {{ $_estado_afiliado == 'I' ? 'text-warning' : 'text-success' }}">
                                {{ $_estado_afiliado == 'I' ? 'INACTIVO' : 'ACTIVO' }}
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="sidenav-divider">

                {{-- Menú de Navegación --}}
                <ul class="navbar-nav sidenav-menu">
                    @php
                    echo $menu;
                    @endphp
                </ul>
            </div>
        </div>
    </div>

    {{-- Indicador de Estado del Sistema --}}
    <div class="sidebar-status-indicator">
        <div class="sidebar-status-content">
            <div class="status-dot"></div>
            <span class="status-text">Sistema operativo</span>
        </div>
    </div>
</nav>
