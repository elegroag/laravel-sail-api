<form id="form" class="validation_form galeria-admin-form" autocomplete="off" novalidate>
    <div class="form-group">
        <label for="archivo" class="form-control-label">Archivo *</label>
        <div class="custom-file">
            <input
                type="file"
                class="custom-file-input"
                id="archivo"
                name="archivo"
                accept="image/jpeg,image/png,image/jpg,video/mp4"
                lang="es">
            <label class="custom-file-label" for="archivo">Seleccione un archivo</label>
        </div>
    </div>

    <div class="form-group">
        <label for="tipo" class="form-control-label">Tipo *</label>
        <select id="tipo" name="tipo" class="form-control">
            <option value="">Seleccione</option>
            <option value="F">Foto</option>
            <option value="V">Video</option>
        </select>
    </div>

    <div class="form-group mb-0">
        <label for="nota" class="form-control-label">Nota</label>
        <textarea
            id="nota"
            name="nota"
            class="form-control"
            rows="3"
            maxlength="500"
            placeholder="Descripción opcional del contenido"></textarea>
        <small class="form-text text-muted">Texto descriptivo visible en la galería del portal.</small>
    </div>
</form>
