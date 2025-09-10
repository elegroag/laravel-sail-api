<div class="row">
    <div class="col-md-4">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="nit" name="nit" placeholder="">
            <label for="nit">Nit empresa:</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="actapr" name="actapr" placeholder="">
            <label for="actapr">Acta aprobación:</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="feccap" name="feccap" placeholder="">
            <label for="feccap">Fecha Resolucion:</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="radio" name="email" id="email1" value="1" checked>
            <label class="form-check-label" for="email1">
                Enviar a email
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="email" id="email2" value="0">
            <label class="form-check-label" for="email2">
                No enviar a email
            </label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="feccap" name="feccap" placeholder="">
            <label for="feccap">Fecha Aprobación:</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">

        <div class="form-group mb-3">
            <label for="anexo_final">Anexar notificación <small>(Opcional)</small>:</label>
            <textarea class="form-control" id="anexo_final" name="anexo_final" rows="4" placeholder=""></textarea>

        </div>
    </div>
</div>
<div class="form-group">
    <button type="button" class="btn btn-md btn-primary" id="btnNotificar">Notificar respuesta</button>
</div>