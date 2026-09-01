<form id="form" class="validation_form galeria-admin-form" autocomplete="off" novalidate>
    <div class="form-group" id="preview_actual_wrap" style="display: none;">
        <label class="form-control-label">Imagen actual</label>
        <img id="preview_actual" class="img-fluid rounded border" src="" alt="Vista previa">
    </div>

    <div class="form-group mb-0">
        <label for="archivo" class="form-control-label" id="archivo_label">Imagen *</label>
        <div class="custom-file">
            <input
                type="file"
                class="custom-file-input"
                id="archivo"
                name="archivo"
                accept="image/jpeg,image/png,image/jpg"
                lang="es">
            <label class="custom-file-label" for="archivo">Seleccione un archivo</label>
        </div>
        <small class="form-text text-muted" id="archivo_help">Formatos permitidos: JPG, JPEG y PNG.</small>
    </div>
</form>
