<?php
echo View::getContent();
?>

<script type="text/template" id='tmp_galeria'>
    <div class="col-lg-3 col-md-4 col-xs-6 mb-3">
        <button
            type="button"
            style="float: right; z-index:9999"
            class="btn btn-default btn-sm btn-icon-only rounded-circle mt-2"
            data-toggle="borrar"
            data-cid="<%=value.numero %>">
                <i class="fa fa-times"></i>
        </button >

		<% if (value.tipo == 'V') { %>
        <div class="thumbnail" style="position: absolute; width:100%">
            <video width="90%" height="240" controls> <source src="<%=value.archivo%>" type="video/mp4"></video>
		<% } else { %>
        <div class="thumbnail"
            style="opacity:1;background-image: url('<%=value.archivo%>');background-size: 100% 100%;border-top: solid 1px #e5e5e5;border-right: solid 2px #e5e5e5;border-bottom: solid 2px #e5e5e5;border-left: solid 1px #e5e5e5;border-color: #e5e5e5;cursor: zoom-in;"
            data-toggle="show-modal"
            data-cid='<%=value.numero%>'
            data-file='<%=value.archivo%>'
            >
		<% } %>
            <div class="caption" style="background: rgba(108, 108, 108, 0.6); margin-top: 65%; text-align: center;">
                <h4 class="text-white">Imagen N° <%= value.numero%> </h4>
                <div class="pb-2">
                    <button type="button" class="btn btn-icon-only btn-info" data-toggle="arriba" data-cid="<%=value.numero%>">
                        <i class="fas fa-long-arrow-alt-left"></i>
                    </button>
                    <button
                        type="button" class="btn btn-icon-only btn-info" data-toggle="abajo"
                        data-cid="<%=value.numero%>">
                            <i class="fas fa-long-arrow-alt-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</script>

<div class="card-body border-top">
    <?php echo Tag::form("id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
    <div class="row">
        <div class="col-md-6 ml-auto">
            <div class="form-group">
                <label for="tipo" class="form-control-label">Archivo</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="archivo" name="archivo" lang="es">
                    <label class="custom-file-label" for="customFileLang">Seleccione un archivo</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="tipo" class="form-control-label">Tipo</label>
                <?php echo Tag::selectStatic("tipo", array("F" => "FOTO", "V" => "VIDEO"), "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
            </div>
        </div>
        <div class="col-md-2 mr-auto">
            <button type="button" class="btn btn-primary" style="margin-top: 17%" data-toggle="guardar">Agregar</button>
        </div>
    </div>
    <?php echo Tag::endform(); ?>
    <div class="row border-top d-flex flex-wrap mt-2 pt-3" id="galeria"> </div>
</div>

<?= Tag::ModalGeneric(
    'Imagen Zoom',
    '<img id="img_zoom" class="img-fluid" src="" />'
) ?>

<?= Tag::javascriptInclude('Cajas/galeria/build.galeria'); ?>
