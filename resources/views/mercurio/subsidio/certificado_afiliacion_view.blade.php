<div class="card mb-0">
    <div class="card-body">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
        <div class="row">
            <div class="col-md-5 ml-auto">
                <div class="form-group">
                    <label for="tipo" class="form-control-label">Tipo</label>
                    {{!! Tag::selectStatic("tipo", $tipo, "use_dummy: true", "dummyValue: ", "class: form-control") }}
                </div>
            </div>
            <div class="col-md-auto d-flex mr-auto">
                <button type="button" class="btn btn-icon btn-primary align-self-center" id="bt_certificado_afiliacion">
                    <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                    <span class="btn-inner--text">Generar Certificado</span>
                </button>
            </div>
        </div>
        </form>
    </div>
</div>

<script>
    const CODSER = "<?= $codser ?>";
</script>
<script src="{{ asset('mercurio/subsidio/CertificadoAfiliacion_view.js') }}"></script>
