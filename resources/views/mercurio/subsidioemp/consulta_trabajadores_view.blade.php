<? View::getContent(); ?>
<?= Tag::Assets('datatables.net.bs5/css/dataTables.bootstrap5.min', 'css') ?>
<?= Tag::Assets('datatables.net/js/dataTables.min', 'js') ?>
<?= Tag::Assets('datatables.net.bs5/js/dataTables.bootstrap5.min', 'js') ?>

<div class="card mb-0">
    <div class="card-header">
        <div class="col-md-auto d-flex mr-auto">
            <button type="button" class="btn btn-primary align-self-center" id='bt_consulta_trabajadores'>Consultar</button>
        </div>
    </div>
    <div class="card-body">
        <?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="d-flex justify-content-center">
            <div class="form-group">
                <label for="estado" class="form-control-label">Indica el estado de afiliación</label>
                <?php echo Tag::selectStatic("estado", array("A" => "ACTIVOS", "I" => "INACTIVOS", "T" => "TODOS"), "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
        <div id='consulta' class='table-responsive'></div>
    </div>
</div>

<div class="modal fade" id="modalConsultaNucleo" tabindex="-1" role="dialog" aria-labelledby="notice" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">
                    <i class="nc-icon nc-simple-remove"></i>
                </button>
                <h5 class="modal-title" id='mdl_set_title'>Consulta nucleo familiar</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id='render_conyuges'></div>
                    <div class="col-md-12" id='render_beneficiarios'></div>
                </div>
            </div>
            <div class="modal-footer justify-content-center" id='mdl_set_footer'>
                <button type="button" class="btn btn-info btn-round" data-bs-dismiss="modal" id='mdl_set_button'>Continuar!</button>
            </div>
        </div>
    </div>
</div>

<script type="text/template" id="templateConyuge">
    <?= View::renderView('subsidio/tmp/tmp_conyuge') ?>
</script>

<script type="text/template" id="templateBeneficiario">
    <?= View::renderView('subsidio/tmp/tmp_beneficiario') ?>
</script>

<?= Tag::javascriptInclude('Mercurio/trabajadoresempresa/trabajadoresempresa.build'); ?>

<style>
    #dataTable {
        font-size: 0.7rem;
    }

    #dataTable thead {
        background-color: #f0f0f0;
    }

    #dataTable th {
        padding: 0.3rem;
        text-align: left;
        vertical-align: middle;
        font-size: 0.85rem;
    }

    #dataTable td {
        padding: 0.3rem;
        text-align: center;
        vertical-align: middle;
    }
</style>