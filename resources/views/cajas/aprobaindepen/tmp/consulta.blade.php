<? $msexo = ($mercurio41->getSexo() != 'N') ? $_sexos[$mercurio41->getSexo()] : ''; ?>

<div class='row'>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Identificación</label>
        <p class='pl-2 description'><?= $mercurio41->getCedtra() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Estado</label>
        <p class='pl-2 description'><?= $mercurio41->getEstadoDetalle() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Calidad empresa</label>
        <p class='pl-2 description'><?= $mercurio41->getCalempDetalle() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Tipo documento</label>
        <p class='pl-2 description'><?= $_tipdoc[$mercurio41->getTipdoc()] ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Cargo</label>
        <p class='pl-2 description'><?= $_cargos[$mercurio41->getCargo()] ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Nombres</label>
        <p class='pl-2 description'><?= $mercurio41->getPrinom() . ' ' . $mercurio41->getSegnom() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Apellidos</label>
        <p class='pl-2 description'><?= $mercurio41->getPriape() . ' ' . $mercurio41->getSegape() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-right'>
        <label class='form-control-label'>Fecha nacimineto</label>
        <p class='pl-2 description'><?= $mercurio41->getFecnac() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-right border-left'>
        <label class='form-control-label'>Sexo</label>
        <p class='pl-2 description'><?= $msexo ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-right border-left'>
        <label class='form-control-label'>Estado civil</label>
        <p class='pl-2 description'><?= $_estciv[$mercurio41->getEstciv()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-right'>
        <label class='form-control-label'>Salario</label>
        <p class='pl-2 description'><?= $mercurio41->getSalario() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-right border-left'>
        <label class='form-control-label'>Discapacidad</label>
        <p class='pl-2 description'><?= @$_tipdis[$mercurio41->getTipdis()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-left'>
        <label class='form-control-label'>Nivel Educación</label>
        <p class='pl-2 description'><?= @$_nivedu[$mercurio41->getNivedu()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Tipo Afiliado</label>
        <p class='pl-2 description'><?= @$_tipafi[$mercurio41->getTipafi()] ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Dirección notificaciones</label>
        <p class='pl-2 description'><?= $mercurio41->getDireccion() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Ciudad notificaciones</label>
        <p class='pl-2 description'><?= @$_codciu[$mercurio41->getCodciu()] ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Ciudad labor trajadores</label>
        <p class='pl-2 description'><?= @$_codzon[$mercurio41->getCodzon()] ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Telefono notificaciones</label>
        <p class='pl-2 description'><?= $mercurio41->getTelefono() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Celular notificaciones</label>
        <p class='pl-2 description'><?= $mercurio41->getCelular() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Email notificaciones</label>
        <p class='pl-2 description'><?= $mercurio41->getEmail() ?></p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Fecha inicial</label>
        <p class='pl-2 description'><?= $mercurio41->getFecini() ?></p>
    </div>
    <div class='col-md-6 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Actividad</label>
        <p class='pl-2 description'><?= (isset($_codact[$mercurio41->getCodact()]) ? $_codact[$mercurio41->getCodact()] : '') ?></p>
    </div>

</div>