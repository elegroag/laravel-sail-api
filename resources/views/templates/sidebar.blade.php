@php
$array_tipos = [
    '' => 'N/A',
    'P' => 'Particular',
    'E' => 'Empresa',
    'T' => 'Trabajador'
];
@endphp
<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light" id="sidenav-main">
    <div class="scrollbar-inner">
        <div class="sidenav-header d-flex align-items-center p-3">
            <a class="navbar-brand m-0" href="{{ route('principal.index') }}">
                <img src="{{ asset('img/Mercurio/logo-min.png') }}" class="img img-center" width="130px" alt="Logo">
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
                            <span class="nav-link-text fw-bold">
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
</nav>