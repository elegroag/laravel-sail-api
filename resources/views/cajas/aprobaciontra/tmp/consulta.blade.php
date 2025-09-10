<?php
$msexo = ($trabajador->getSexo() != 'N') ? $_sexos[$trabajador->getSexo()] : '';
?>
<div class='row'>
    <div class='col-md-4 col-lg-3 border-start border-top border-end'>
        <label class='form-control-label'>Nit</label>
        <p class='pl-1 description'><?= $trabajador->getNit() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Razón social</label>
        <p class='pl-1 description'><?= Tag::capitalize($trabajador->getRazsoc()) ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Cedula</label>
        <p class='pl-1 description'><?= $trabajador->getCedtra() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Apellidos</label>
        <p class='pl-1 description'><?= Tag::capitalize($trabajador->getPriape() . ' ' . $trabajador->getSegape()) ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-start border-end'>
        <label class='form-control-label'>Nombres</label>
        <p class='pl-1 description'><?= Tag::capitalize($trabajador->getPrinom() . ' ' . $trabajador->getSegnom()) ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Fecha nacimineto</label>
        <p class='pl-1 description'><?= $trabajador->getFecnac() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Ciudad nacimiento</label>
        <p class='pl-1 description'><?= Tag::capitalize(@$_codciu[$trabajador->getCodciu()]) ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Sexo</label>
        <p class='pl-1 description'><?= $msexo ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-start border-end'>
        <label class='form-control-label'>Estado civil</label>
        <p class='pl-1 description'><?= @$_estciv[$trabajador->getEstciv()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Cabeza hogar</label>
        <p class='pl-1 description'><?= @$_cabhog[$trabajador->getCabhog()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Ciudad</label>
        <p class='pl-1 description'><?= Tag::capitalize(@$_codciu[$trabajador->getCodciu()]) ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Zona</label>
        <p class='pl-1 description'><?= Tag::capitalize(@$_codzon[$trabajador->getCodzon()]) ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-start border-end'>
        <label class='form-control-label'>Direccion</label>
        <p class='pl-1 description'><?= $trabajador->getDireccion() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Barrio</label>
        <p class='pl-1 description'><?= @$trabajador->getBarrio() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Telefono</label>
        <p class='pl-1 description'><?= $trabajador->getTelefono() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Celular</label>
        <p class='pl-1 description'><?= $trabajador->getCelular() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-start border-end'>
        <label class='form-control-label'>Cargo</label>
        <p class='pl-1 description'><?= @$_ocupaciones[$trabajador->getCargo()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Email</label>
        <p class='pl-1 description'><?= $trabajador->getEmail() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Fecha Ingreso</label>
        <p class='pl-1 description'><?= $trabajador->getFecing() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Salario</label>
        <p class='pl-1 description'><?= $trabajador->getSalario() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-start border-end'>
        <label class='form-control-label'>Capacidad de trabajar</label>
        <p class='pl-1 description'><?= @$_captra[$trabajador->getCaptra()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Discapacidad</label>
        <p class='pl-1 description'><?= @$_tipdis[$trabajador->getTipdis()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Nivel Educación</label>
        <p class='pl-1 description'><?= @$_nivedu[$trabajador->getNivedu()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Rural</label>
        <p class='pl-1 description'><?= @$_rural[$trabajador->getRural()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-start border-end'>
        <label class='form-control-label'>Horas</label>
        <p class='pl-1 description'><?= $trabajador->getHoras() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end'>
        <label class='form-control-label'>Tipo Contrato</label>
        <p class='pl-1 description'><?= @$_tipcon[$trabajador->getTipcon()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end border-bottom'>
        <label class='form-control-label'>Vivienda</label>
        <p class='pl-1 description'><?= @$_vivienda[$trabajador->getVivienda()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end border-bottom'>
        <label class='form-control-label'>Tipo Afiliado</label>
        <p class='pl-1 description'><?= @$_tipafi[$trabajador->getTipafi()] ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-start border-end border-bottom'>
        <label class='form-control-label'>Profesion</label>
        <p class='pl-1 description'><?= $trabajador->getProfesion() ?></p>
    </div>
    <div class='col-md-4 col-lg-3 border-top border-end border-bottom'>
        <label class='form-control-label'>Autoriza</label>
        <p class='pl-1 description'><?= ($trabajador->getAutoriza() == 'S') ? 'SI' : 'NO' ?></p>
    </div>
</div>