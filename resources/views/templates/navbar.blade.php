<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary">
    <div class="container-fluid">
        <button class="navbar-toggler d-xl-none" type="button" data-bs-toggle="collapse" 
        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Navbar links -->
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown me-3">
                    <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-dark dropdown-menu-lg dropdown-menu-end bg-default">
                        <div class="row shortcuts px-4">
                            <a href="{{ route('movimientos.historial') }}" class="col-4 shortcut-item text-decoration-none">
                                <span class="shortcut-media avatar rounded-circle bg-gradient-red d-flex align-items-center justify-content-center">
                                    <i class="ni ni-book-bookmark"></i>
                                </span>
                                <small class="d-block text-center mt-1">Historial</small>
                            </a>
                            <a href="{{ route('movimientos.cambio_email_view') }}" class="col-4 shortcut-item text-decoration-none">
                                <span class="shortcut-media avatar rounded-circle bg-gradient-orange d-flex align-items-center justify-content-center">
                                    <i class="ni ni-email-83"></i>
                                </span>
                                <small class="d-block text-center mt-1">Email</small>
                            </a>
                            <a href="{{ route('movimientos.cambio_clave_view') }}" class="col-4 shortcut-item text-decoration-none">
                                <span class="shortcut-media avatar rounded-circle bg-gradient-info d-flex align-items-center justify-content-center">
                                    <i class="ni ni-key-25"></i>
                                </span>
                                <small class="d-block text-center mt-1">Contraseña</small>
                            </a>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar avatar-sm rounded-circle me-2">
                            <img src="{{ asset('img/Mercurio/profile-a.png') }}" class="img-responsive" style="width:40px" />    
                        </span>
                        <span class="d-none d-lg-inline">
                            <span class="mb-0 text-sm fw-normal">{{ htmlspecialchars($user_name) }}</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">Vuelve pronto</h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('login.salir') }}">
                                <i class="ni ni-user-run me-2"></i>
                                <span>Cerrar sesión</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>