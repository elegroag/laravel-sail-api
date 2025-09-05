<?php
echo View::getContent();
echo TagUser::help($title, $help);
echo Tag::addJavascript('Mercurio/subsidioemp');
?>

<div class="card mb-0">
    <div class="card-body">
        <?php echo Tag::form("subsidioemp/actualiza_datos_basicos", "id: form", "class: validation_form", "autocomplete: off", "novalidate", "enctype: multipart/form-data"); ?>
        <?php
        foreach ($campos as $key => $value) {
            $key = strtolower($key);
            echo "<div class='row'>";
            echo "<div class='col-md-12 '>";
            echo "<div class='form-group'>";
            echo "<label for='$key' class='form-control-label'>$value</label>";
            echo Tag::TextField("$key", "placeholder: $value", "class: form-control", "value: {$datos["$key"]}");
            echo Tag::hiddenField("{$key}_ant", "value: {$datos["$key"]}");
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        $response = "<div class='row'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= "<thead>";
        $response .= "<tr>";
        $response .= "<th colspan=3>Archivos a adjuntar</th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody>";
        $mercurio13 = $Mercurio14->find("tipopc = '5'");
        foreach ($mercurio13 as $mmercurio13) {
            $mercurio12 = $Mercurio12->findFirst("coddoc='{$mmercurio13->getCoddoc()}'");
            $obliga = "";
            if ($mmercurio13->getObliga() == "S") $obliga = "<br><small class='text-muted'>Obligatorio</small>";
            $response .= "<tr>";
            $response .= "<td>{$mercurio12->getDetalle()} $obliga</td>";
            $response .= "<td>";
            $response .= "<div class='custom-file'>";
            $response .= "<input type='file' class='custom-file-input' id='archivo_{$mmercurio13->getCoddoc()}' name='archivo_{$mmercurio13->getCoddoc()}' accept='application/pdf, image/*'>";
            $response .= "<label class='custom-file-label' for='archivo_{$mmercurio13->getCoddoc()}'>Select file</label>";
            $response .= "</div>";
            $response .= "</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        $response .= "</div>";
        echo $response;
        ?>
        <div class="row">
            <div class="col-md-auto d-flex mr-auto">
                <button type="button" class="btn btn-primary align-self-center" id="bt_actualiza_datos_basicos">Actualizar</button>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
    </div>
</div>

<div id='consulta' class='table-responsive'>
</div>

<?= Tag::javascriptInclude('Mercurio/consultasempresa/consultasempresa.build'); ?>