<div class='col-xs-12'>
    <p>Complete el campo requerido para emitir el correo de aprobación al trabajador que ha quedado pendiente.</p>
    <?= Tag::form("aprobaciontra/rezagoCorreo", "id: form_pendiente"); ?>
    <div class='form-horizontal'>
        <div class="form-group">
            <label class='col-md-4 control-label'>Cedula del trabajador:</label>
            <div class='col-md-4'>
                <input name='cedtra' id='cedtra' type='text' placeholder="Cedula trabajador" class='form-control' />
                <p class='help text-danger' id='error_cedtra'></p>
            </div>
        </div>
        <div class="form-group">
            <label class='col-md-4 control-label'>Anexar notificación al inicio <small>(Opcional)</small>:</label>
            <div class='col-md-5'>
                <textarea rows="2" name='anexo_inicial' id='anexo_inicial' type='text' placeholder="mensaje anexo al inicio" class='form-control'></textarea>
                <p class='help text-danger' id='error_anexo_inicial'></p>
            </div>
        </div>
        <div class="form-group">
            <label class='col-md-4 control-label'>Anexar notificación al final <small>(Opcional)</small>:</label>
            <div class='col-md-5'>
                <textarea rows="2" name='anexo_final' id='anexo_final' type='text' placeholder="mensaje anexo al final" class='form-control'></textarea>
                <p class='help text-danger' id='error_anexo_final'></p>
            </div>
        </div>
    </div>
    <div class="form-group">
        <button type='button' class='btn btn-md btn-primary' id='btenviar'>Enviar email</button>
    </div>
    <?= Tag::endForm(); ?>
    <p class='help'><?= $flash_mensaje ?></p>
</div>