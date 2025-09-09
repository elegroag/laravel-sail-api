<div class="card-header">
    <div class="nav-wrapper p-0">
        <ul class="nav nav-pills" id="tabs-icons-text" role="tablist">
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 active"
                    id="tabsTrabajadorTab"
                    data-bs-toggle="tab"
                    href="#tabsTrabajador"
                    role="tab"
                    aria-controls="tabsTrabajador"
                    aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Trabajador
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0"
                    id="tabsConyugeTab"
                    data-bs-toggle="tab"
                    href="#tabsConyuge"
                    role="tab"
                    aria-controls="tabsConyuge"
                    aria-selected="false">
                    <i class="fas fa-user-friends mr-2"></i>Conyuges
                </a>
            </li>
            <li class="nav-item">
                <a
                    class="nav-link mb-sm-3 mb-md-0"
                    id="tabsBeneficiarioTab"
                    data-bs-toggle="tab"
                    href="#tabsBeneficiario"
                    role="tab"
                    aria-controls="tabsBeneficiario"
                    aria-selected="false">
                    <i class="fas fa-child mr-2"></i>Beneficiarios
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="card-body">
    <div id="myTabContent"></div>
</div>

<script type="text/template" id="tmp_layout">
    <div
        class="tab-pane fade show active p-2"
        id="tabsTrabajador"
        role="tabpanel"
        aria-labelledby="tabsTrabajadorTab">
    </div>

    <div
        class="tab-pane fade p-2"
        id="tabsConyuge"
        role="tabpanel"
        aria-labelledby="tabsConyugeTab">
    </div>

    <div
        class="tab-pane fade p-2"
        id="tabsBeneficiario"
        role="tabpanel"
        aria-labelledby="tabsBeneficiarioTab">
    </div>
</script>

<script type="text/template" id="templateTrabajador">
    <?= View::renderView('subsidio/tmp/tmp_nucleo') ?>
</script>

<script type="text/template" id="templateConyuge">
    <?= View::renderView('subsidio/tmp/tmp_conyuge') ?>
</script>

<script type="text/template" id="templateBeneficiario">
    <?= View::renderView('subsidio/tmp/tmp_beneficiario') ?>
</script>

<?= Tag::javascriptInclude('Mercurio/consultanucleo/consultanucleo.build'); ?>