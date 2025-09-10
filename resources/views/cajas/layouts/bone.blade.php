<?php
list($menu, $migas, $typehead) =  Menu::showMenu();
$muser = Auth::getActiveIdentity();
$user = Tag::capitalize($muser['nombre']);
?>
<?= Tag::Assets('datatables.net.bs5/css/dataTables.bootstrap5.min', 'css'); ?>
<?= Tag::Assets('datatables.net/js/dataTables.min', 'js'); ?>
<?= Tag::Assets('datatables.net.bs5/js/dataTables.bootstrap5.min', 'js'); ?>
<?= Tag::Assets("summernote/summernote-bs5", 'css'); ?>
<?= Tag::Assets("summernote/summernote-bs5", 'js'); ?>
<?= Tag::Assets("summernote/lang/summernote-es-ES", 'js'); ?>

<?= View::render("templates/sidebar", array('menu' => $menu)); ?>

<div class="main-content" id="panel">
    <?= View::render("templates/navbar", array('user' => $user)); ?>
        <?= View::render("templates/footer"); ?>
</div>

<?= View::render("templates/modal"); ?>