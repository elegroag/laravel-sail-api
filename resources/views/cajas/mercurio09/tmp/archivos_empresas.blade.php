<?
foreach ($mercurio12 as $mmercurio12) {
    $mercurio14 = $Mercurio14->findFirst("tipopc='{$tipopc}' and tipsoc='{$tipsoc}' and coddoc='{$mmercurio12->getCoddoc()}' ");
    $value = "";
    if ($mercurio14 != false) $value = 'checked';
?>
    <div class='col-md-6'>
        <div class='row'>
            <label class="text-primary"><?= $mmercurio12->getDetalle() ?></label>
        </div>
        <div class='row'>
            <div class='col-6 col-md-3 p-0'>Adjuntar</div>
            <div class='col-4 col-md-2 p-0'>
                <div class='custom-checkbox mb-3'>
                    <label class='custom-toggle'>
                        <input
                            type='checkbox'
                            id='coddoc_<?= $mmercurio12->getCoddoc() ?>'
                            name='coddoc_<?= $mmercurio12->getCoddoc() ?>'
                            value='<?= $mmercurio12->getCoddoc() ?>'
                            <?= $value ?>
                            data-toggle='empresa-guardar'
                            data-tipopc='<?= $tipopc ?>'
                            data-coddoc='<?= $mmercurio12->getCoddoc() ?>' />
                        <span class='custom-toggle-slider rounded-circle' data-label-off='No' data-label-on='Si'></span>
                    </label>
                </div>
            </div>

            <div class='col-6 col-md-3 pr-0'>
                <? if ($value == "checked") echo "Obliga"; ?>
            </div>

            <div class='col-4 col-md-2 p-0'>
                <?
                if ($value == "checked") {
                    $value = "";
                    if ($mercurio14->getObliga() == "S") $value = 'checked'; ?>
                    <div class='custom-checkbox mb-3'>
                        <label class='custom-toggle'>
                            <input
                                type='checkbox'
                                id='obliga_<?= $mmercurio12->getCoddoc() ?>'
                                name='obliga_<?= $mmercurio12->getCoddoc() ?>'
                                value='<?= $mmercurio12->getCoddoc() ?>'
                                <?= $value ?>
                                data-toggle='empresa-archivo-obliga'
                                data-tipopc='<?= $tipopc ?>'
                                data-coddoc='<?= $mmercurio12->getCoddoc() ?>' />
                            <span class='custom-toggle-slider rounded-circle' data-label-off='No' data-label-on='Si'></span>
                        </label>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
<? } ?>