@php 
use App\Services\Tag;
@endphp
<div class="container pt-3">
    <div class="row">
        <div class="col">
            <nav class="navbar navbar-expand-lg navbar-horizontal static-top" id='navbar-main'>
                <div class="container">
                    <a class="navbar-brand p-0" href="login#auth">
                        @php echo Tag::image("caja.png", "class: mx-auto d-block p-0"); @endphp
                    </a>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                @php 
                                echo Tag::linkTo(
                                    "login#register",
                                    'content: <span class="btn-inner--icon">
                                    <i class="fas fa-file-signature mr-2 fa-2x"></i>
                                    </span><span class="nav-link-inner--text">Registrar En Plataforma</span>',
                                    'class: nav-link btn-icon'
                                );
                                @endphp
                            </li>
                            <li class="nav-item">
                                @php 
                                echo Tag::linkTo(
                                    "login#auth",
                                    'content: <span class="btn-inner--icon">
                                    <i class="fas fa-users mr-2 fa-2x"></i>
                                    </span><span class="nav-link-inner--text">Sesión, Consulta Y Gestión</span>',
                                    'class: nav-link btn-icon'
                                );
                                @endphp
                            </li>
                        </ul>
                    </div>

                    <div class="sidebar">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                @php 
                                echo Tag::linkTo("login#auth", 'content: <i class="fa fa-fw fa-user"></i> Sesión cunsulta y gestión');
                                @endphp
                            </li>
                            <li class="nav-item">
                                @php 
                                echo Tag::linkTo("login#register", 'content: <i class="fa fa-fw fa-edit"></i> Registrar en plataforma');
                                @endphp
                            </li>
                            <li class="nav-item">
                                @php 
                                echo Tag::linkTo("login#guia", 'content: <i class="fas fa-file"></i> Guía afiliación');
                                @endphp
                            </li>
                        </ul>
                    </div>

                    <button class="navbar-toggler" type="button" data-bs-toggle='sidebar' data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon text-white">☰</span>
                    </button>
                </div>
            </nav>
        </div>
    </div>
</div>
