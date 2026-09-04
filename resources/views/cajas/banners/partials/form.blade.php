<form id="form" class="validation_form galeria-admin-form" autocomplete="off" novalidate>
    <div class="form-group" id="preview_actual_wrap" style="display: none;">
        <label class="form-control-label">Imagen actual</label>
        <img id="preview_actual" class="img-fluid rounded border" src="" alt="Vista previa">
    </div>

    <div class="form-group">
        <label for="estado" class="form-control-label">Estado *</label>
        <select id="estado" name="estado" class="form-control" required>
            <option value="A">Activo</option>
            <option value="I">Inactivo</option>
        </select>
    </div>

    <div class="form-group">
        <label for="fecha_inicia" class="form-control-label">Fecha inicia *</label>
        <input type="date" class="form-control" id="fecha_inicia" name="fecha_inicia" required>
    </div>

    <div class="form-group">
        <label for="fecha_finaliza" class="form-control-label">Fecha finaliza *</label>
        <input type="date" class="form-control" id="fecha_finaliza" name="fecha_finaliza" required>
    </div>

    <div class="form-group">
        <label for="url_imagen" class="form-control-label">URL imagen</label>
        <input
            type="url"
            class="form-control"
            id="url_imagen"
            name="url_imagen"
            placeholder="https://ejemplo.com/banner.jpg"
            maxlength="500">
        <small class="form-text text-muted">Opcional. Si se indica, se usa en lugar del archivo local (o junto a él como alternativa).</small>
    </div>

    <div class="form-group">
        <label for="imagen" class="form-control-label" id="imagen_label">Imagen</label>
        <div class="custom-file">
            <input
                type="file"
                class="custom-file-input"
                id="imagen"
                name="imagen"
                accept="image/jpeg,image/png,image/jpg"
                lang="es">
            <label class="custom-file-label" for="imagen">Seleccione un archivo</label>
        </div>
        <small class="form-text text-muted" id="imagen_help">Formatos: JPG, JPEG y PNG. Requerido si no hay URL.</small>
    </div>

    <div class="form-group mb-0">
        <label for="content_html" class="form-control-label">Contenido HTML</label>
        <textarea
            class="form-control"
            id="content_html"
            name="content_html"
            rows="5"
            placeholder="Texto o HTML del diálogo promocional"></textarea>
    </div>
</form>
