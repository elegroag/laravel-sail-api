<?php
list($menu, $migas, $typehead) =  Menu::showMenu();

$user = Auth::getActiveIdentity();
if (!$user) {
    echo  "<script type=\"text/javascript\">window.location.href = '../login';</script>";
} else {
    $user = Tag::capitalize($user['nombre']);
}
?>

<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white" id="sidenav-main">
    <div class="scrollbar-inner">
        <div class="sidenav-header d-flex align-items-center">
            <a class="navbar-brand" href="<?= Utils::getKumbiaUrl('principal/index'); ?>">
                <?php echo Tag::image("Mercurio/logo-min.png", "class: navbar-brand-img"); ?>
            </a>
            <div class="ml-auto">
                <div class="sidenav-toggler d-none d-xl-block" data-action="sidenav-unpin" data-target="#sidenav-main">
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
                <ul class="navbar-nav">
                    <li class='nav-item'>
                        <div class="nav-link" href="#"><span class="nav-link-text"><b>Comfaca En línea<br />(ADMIN <?= (Core::$modeDeploy == 'development') ? '-DEV' : '-PRO' ?>)</b></span></div>
                    </li>
                    <?php echo $menu; ?>
                </ul>
                <hr class="my-3">
            </div>
        </div>
    </div>
</nav>

<div class="main-content" id="panel">
    <nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Search form -->
                <form class="navbar-search navbar-search-light form-inline mr-sm-3" id="navbar-search-main">
                    <div class="typeahead__container">
                        <div class="typeahead__field">
                            <div class="typeahead__query">
                                <div class="form-group mb-0">
                                    <div class="input-group input-group-alternative input-group-merge">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input class="form-control js-typeahead" placeholder="Buscar" type="search" autocomplete="off">
                                    </div>
                                </div>
                                <button type="button" class="close" data-action="search-close" data-target="#navbar-search-main" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Navbar links -->
                <ul class="navbar-nav align-items-center ml-md-auto">
                    <li class="nav-item d-xl-none">
                        <!-- Sidenav toggler -->
                        <div class="pr-3 sidenav-toggler sidenav-toggler-dark" data-action="sidenav-pin" data-target="#sidenav-main">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item d-sm-none">
                        <a class="nav-link" href="#" data-action="search-show" data-target="#navbar-search-main">
                            <i class="ni ni-zoom-split-in"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" role="button" onclick="openHelp()">
                            <i class="far fa-question-circle"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav align-items-center ml-auto ml-md-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link pr-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="media align-items-center">
                                <span class="avatar avatar-sm rounded-circle">
                                    <?php echo Tag::image("Mercurio/profile-a.png", "alt: Image placeholder") ?>
                                </span>
                                <div class="media-body ml-2 d-none d-lg-block">
                                    <span class="mb-0 text-sm  font-weight-bold"><?php echo $user ?></span>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-header noti-title">
                                <h6 class="text-overflow m-0">Welcome!</h6>
                            </div>
                            <a href="#!" class="dropdown-item">
                                <i class="ni ni-single-02"></i>
                                <span>Mi perfil</span>
                            </a>
                            <a href="#!" class="dropdown-item">
                                <i class="ni ni-settings-gear-65"></i>
                                <span>Ajustes</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo Utils::getKumbiaUrl('login/salir'); ?>" class="dropdown-item">
                                <i class="ni ni-user-run"></i>
                                <span>Cerrar sesión</span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="header bg-primary pb-9">
        <div class="container-fluid">
            <div class="header-body">
                <div class="row align-items-center py-4">
                    <div class="col-lg-7 col-auto mr-auto">
                        <h6 class="h2 text-white d-inline-block mb-0"><?php echo isset($title) ? $title : "Sin Titulo"; ?></h6>
                        <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                                <?php echo $migas; ?>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-lg-5 col-auto text-right">
                        <?php echo Tag::group_buttons(isset($buttons) ? $buttons : ""); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt--9">
        <div class="row container-main">
            <div class="col">
                <div class="card-group">
                                    </div>
            </div>
        </div>
    </div>
</div>
