@php $tipsoc = $_tipsoc[$mercurio30->getTipsoc()]; @endphp

<div class='row'>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Nit</label>
        <p class='pl-2 description'>{{$mercurio30->getNit()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Estado</label>
        <p class='pl-2 description'>{{$mercurio30->getEstadoDetalle()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Razsoc</label>
        <p class='pl-2 description'>{{$mercurio30->getRazsoc()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Sigla</label>
        <p class='pl-2 description'>{{$mercurio30->getSigla()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Digito Verificacion</label>
        <p class='pl-2 description'>{{$mercurio30->getDigver()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Calidad Empresa</label>
        <p class='pl-2 description'>{{@$_calemp[$mercurio30->getCalemp()]}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Cedula Representante</label>
        <p class='pl-2 description'>{{$mercurio30->getCedrep()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Nombre Representante</label>
        <p class='pl-2 description'>{{$mercurio30->getRepleg()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Direccion de Notificacion</label>
        <p class='pl-2 description'>{{$mercurio30->getDireccion()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Ciudad Notificacion</label>
        <p class='pl-2 description'>{{@$_codciu[$mercurio30->getCodciu()]}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Ciudad de Labor de Trajadores</label>
        <p class='pl-2 description'>{{@$_codzon[$mercurio30->getCodzon()]}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Telefono de Notificacion</label>
        <p class='pl-2 description'>{{$mercurio30->getTelefono()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Celular de Notificacion</label>
        <p class='pl-2 description'>{{$mercurio30->getCelular()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Email de Notificacion</label>
        <p class='pl-2 description'>{{$mercurio30->getEmail()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Actividad</label>
        <p class='pl-2 description'>{{(isset($_codact[$mercurio30->getCodact()]) ? $_codact[$mercurio30->getCodact()] : '')}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Fecha Inicial</label>
        <p class='pl-2 description'>{{$mercurio30->getFecini()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Total Trabajadores</label>
        <p class='pl-2 description'>{{$mercurio30->getTottra()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Valor Nomina</label>
        <p class='pl-2 description'>{{$mercurio30->getValnom()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Tipo Sociedad</label>
        <p class='pl-2 description'>{{$tipsoc}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Direccion Comercial</label>
        <p class='pl-2 description'>{{$mercurio30->getDirpri()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Ciudad Comercial</label>
        <p class='pl-2 description'>{{@$_codciu[$mercurio30->getCiupri()]}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Telefono Comercial</label>
        <p class='pl-2 description'>{{$mercurio30->getCelpri()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Email Comercial</label>
        <p class='pl-2 description'>{{$mercurio30->getEmailpri()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Fecha aprobación resolución </label>
        <p class='pl-2 description'>{{$mercurio30->getFecapr()}}</p>
    </div>
</div>
