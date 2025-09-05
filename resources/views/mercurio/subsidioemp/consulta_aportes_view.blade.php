<? View::getContent() ?>
<?= Tag::Assets('datatables.net.bs5/css/dataTables.bootstrap5.min', 'css') ?>
<?= Tag::Assets('datatables.net/js/dataTables.min', 'js') ?>
<?= Tag::Assets('datatables.net.bs5/js/dataTables.bootstrap5.min', 'js') ?>

<div class="card mb-0">
    <div class="card-header">
        <div class="col-md-auto d-flex mr-auto">
            <button type="button" class="btn btn-primary align-self-center" id="bt_consulta_aportes">Consultar</button>
        </div>
    </div>

    <div class="card-body">
        <?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="d-flex justify-content-center">
            <div class="form-group">
                <label for="perini" class="form-control-label">Periodo Inicial</label>
                <?php echo TagUser::periodo("perini", "placeholder: Periodo Inicial", "class: form-control"); ?>
            </div>
            <div class="form-group ml-3">
                <label for="perfin" class="form-control-label">Periodo Final</label>
                <?php echo TagUser::periodo("perfin", "placeholder: Periodo Final", "class: form-control"); ?>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
        <div id='consulta' class='table-responsive'></div>
    </div>
</div>


<?= Tag::javascriptInclude('Mercurio/aportesempresa/aportesempresa.build'); ?>