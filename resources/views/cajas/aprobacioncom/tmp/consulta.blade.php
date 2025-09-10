<h6 class='heading-small text-muted mb-3'>Solicitud</h6>
<div class='row pl-lg-4 pb-3'>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Nit</label>
        <p class='pl-2 description'><?= $mercurio38->getCedtra() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Estado</label>
        <p class='pl-2 description'><?= $mercurio38->getEstadoDetalle() ?></p>
    </div>

    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Calidad Empresa</label>
        <p class='pl-2 description'><?= @$_calemp[$mercurio38->getCalemp()] ?></p>
    </div>

    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Direccion de Notificacion</label>
        <p class='pl-2 description'><?= $mercurio38->getDireccion() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Ciudad Notificacion</label>
        <p class='pl-2 description'><?= @$_codciu[$mercurio38->getCodciu()] ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Ciudad de Labor de Trajadores</label>
        <p class='pl-2 description'><?= @$_codzon[$mercurio38->getCodzon()] ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Telefono de Notificacion</label>
        <p class='pl-2 description'><?= $mercurio38->getTelefono() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Celular de Notificacion</label>
        <p class='pl-2 description'><?= $mercurio38->getCelular() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Email de Notificacion</label>
        <p class='pl-2 description'><?= $mercurio38->getEmail() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Actividad</label>
        <p class='pl-2 description'><?= (isset($_codact[$mercurio38->getCodact()]) ? $_codact[$mercurio38->getCodact()] : '') ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Fecha Inicial</label>
        <p class='pl-2 description'><?= $mercurio38->getFecini() ?></p>
    </div>
</div>