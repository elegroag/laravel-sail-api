<?php
list($menu, $migas, $typehead) =  Menu::showMenu();
$muser = Auth::getActiveIdentity();
$user = Tag::capitalize($muser['nombre']);
?>

<?php echo View::render("templates/sidebar", array('menu' => $menu)); ?>

<div class="main-content" id="panel">

    <?php echo View::render("templates/navbar", array('user' => $user)); ?>

    <div class="header bg-gradient-primary pb-6 navbar-dark">
        <div class="container-fluid">
            <div class="header-body">
                <div class="row align-items-center py-4">
                    <div class="col-lg-7 col-auto mr-auto">
                        <h4 class="text-white d-inline-block mb-0"><?php echo isset($title) ? $title : ""; ?></h4>
                        <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                                <?php echo $migas; ?>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-lg-5 col-auto text-right">
                        <?php echo TagUser::group_buttons(isset($buttons) ? $buttons : ""); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt--6" id='contentView'>
        <div class="row container-main">
            <div class="col">
                <div class="card-group">
                    <div class="card">
                        <?php if (isset($hide_header)) { ?>
                        <? } else { ?>
                            <div class="card-header border-0">
                                <label class="mb-0"><?= (isset($title)) ? $title : "Sin Titulo"; ?></label>
                            </div>
                        <? } ?>
                                            </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <?php
    echo View::render("templates/footer");
    Tag::addJavascript('core/messages');
    Tag::addJavascript('core/base-source');
    ?>
</div>