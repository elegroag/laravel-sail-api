<? View::getContent() ?>
<?= Tag::Assets('datatables.net.bs5/css/dataTables.bootstrap5.min', 'css') ?>
<?= Tag::Assets('datatables.net/js/dataTables.min', 'js') ?>
<?= Tag::Assets('datatables.net.bs5/js/dataTables.bootstrap5.min', 'js') ?>

<div class="card mb-0">
    <div class="card-header">
        <div class="col-md-auto d-flex mr-auto">
            <button type="button" class="btn btn-primary align-self-center" id="btn_consulta_nomina">Consultar</button>
        </div>
    </div>
    <div class="card-body">
        <?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="d-flex justify-content-center">
            <div class="form-group">
                <label for="periodo" class="form-control-label">Periodo</label>
                <?php echo TagUser::periodo("periodo", "placeholder: Periodo", "class: form-control"); ?>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
        <div id='consulta' class='table-responsive'></div>
    </div>
</div>

<?= Tag::javascriptInclude('Mercurio/nominasempresa/nominasempresa.build'); ?>