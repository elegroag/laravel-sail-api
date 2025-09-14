<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light" id="sidenav-main">
    <div class="scrollbar-inner">
        <div class="sidenav-header d-flex align-items-center p-3">
            <a class="navbar-brand m-0" href="{{ Utils::getKumbiaUrl('principal/index') }}">
                @php echo Tag::image("Mercurio/logo-min.png", ["class" => "navbar-brand-img"]); @endphp
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
                <ul class="navbar-nav flex-column">
                    <li class="nav-item px-3 py-2">
                        <div class="nav-link p-0">
							<span class="nav-link-text fw-bold">
                                COMAFAC EN LÍNEA<br>
                                <small class="text-muted">(ADMIN {{ (Core::$modeDeploy == 'development') ? 'DEVELOPMENT' : 'PRODUCTION' }})</small>
                            </span>
                        </div>
                    </li>
                    @php echo $menu; @endphp
                </ul>
                <hr class="my-3">
            </div>
        </div>
    </div>
</nav>