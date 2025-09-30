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
<nav class="sidenav fixed-lef navbar navbar-vertical navbar-expand-xs navbar-light" id="sidenav-main">
    <div class="scrollbar-inner">
        <div class="sidenav-header d-flex align-items-center p-3">
            <a class="navbar-brand text-white img-center" href="{{ route('principal.index') }}">
                <div class="img-thumbnail bg-white border-4 border-success rounded-pill p-2">
                    <img src="{{ asset('img/Mercurio/logo-min.png') }}" width="80px" alt="Logo">
                </div>
            </a>
            <div class="ms-auto">
                <div class="sidenav-toggler d-none d-xl-block" data-action="sidenav-unpin" data-bs-target="#sidenav-main">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="navbar-inner">
            <div class="collapse navbar-collapse" id="sidenav-collapse-main">
                <div class="position-relative">
                    <div class="p-3">
                        <div class="nav-link p-0 mb-2">
                            <span class="nav-link-text">
                                COMFACA EN LÍNEA<br>
                                <small class="text-muted">Usuario tipo: <span class="text-primary">
                                    {{ isset($array_tipos[$_tipo]) ? htmlspecialchars($array_tipos[$_tipo]) : 'N/A' }}</span></small>
                            </span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <ul class="navbar-nav">
                        @php
                        echo $menu;
                        @endphp
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-status-indicator">
        <div class="sidebar-status-content">
            <div class="status-dot"></div>
            <span class="text-gray">Sistema operativo</span>
        </div>
    </div>
</nav>

<style rel="stylesheet">
.sidebar-status-indicator {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
    border-top: 1px solid #698cce; /* border-sidebar-border */
    background-color: rgba(94, 99, 107, 0.5); /* bg-sidebar/50 */
}

.sidebar-status-content {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem; /* text-xs */
    color: rgba(244, 244, 255, 0.856); /* text-sidebar-foreground/50 */
}

.status-dot {
    width: 0.5rem;
    height: 0.5rem;
    background-color: #10b981; /* bg-green-400 equivalente */
    border-radius: 9999px; /* rounded-full */
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; /* animate-pulse */
}

</style>