<div class='row'>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Nit</label>
        <p class='pl-2 description'>{{$mercurio30->nit}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Estado</label>
        <p class='pl-2 description'>{{$mercurio30->getEstadoDetalle()}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Razsoc</label>
        <p class='pl-2 description'>{{$mercurio30->razsoc}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Sigla</label>
        <p class='pl-2 description'>{{$mercurio30->sigla}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Digito Verificacion</label>
        <p class='pl-2 description'>{{$mercurio30->digver}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Calidad Empresa</label>
        <p class='pl-2 description'>{{@$_calemp[$mercurio30->calemp]}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Cedula Representante</label>
        <p class='pl-2 description'>{{$mercurio30->cedrep}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Nombre Representante</label>
        <p class='pl-2 description'>{{$mercurio30->repleg}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Direccion de Notificacion</label>
        <p class='pl-2 description'>{{$mercurio30->direccion}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Ciudad Notificacion</label>
        <p class='pl-2 description'>{{@$_codciu[$mercurio30->codciu]}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Ciudad de Labor de Trajadores</label>
        <p class='pl-2 description'>{{@$_codzon[$mercurio30->codzon]}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Telefono de Notificacion</label>
        <p class='pl-2 description'>{{$mercurio30->telefono}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Celular de Notificacion</label>
        <p class='pl-2 description'>{{$mercurio30->celular}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Email de Notificacion</label>
        <p class='pl-2 description'>{{$mercurio30->email}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Actividad</label>
        <p class='pl-2 description'>{{(isset($_codact[$mercurio30->codact]) ? $_codact[$mercurio30->codact] : '')}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Fecha Inicial</label>
        <p class='pl-2 description'>{{$mercurio30->fecini}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Total Trabajadores</label>
        <p class='pl-2 description'>{{$mercurio30->tottra}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Valor Nomina</label>
        <p class='pl-2 description'>{{$mercurio30->valnom}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Tipo Sociedad</label>
        <p class='pl-2 description'>{{$tipsoc_detalle}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Direccion Comercial</label>
        <p class='pl-2 description'>{{$mercurio30->dirpri}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Ciudad Comercial</label>
        <p class='pl-2 description'>{{@$_codciu[$mercurio30->ciupri]}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Telefono Comercial</label>
        <p class='pl-2 description'>{{$mercurio30->celpri}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Email Comercial</label>
        <p class='pl-2 description'>{{$mercurio30->emailpri}}</p>
    </div>
    <div class='col-md-3 border-top border-right border-left border-bottom'>
        <label class='form-control-label'>Fecha aprobación resolución </label>
        <p class='pl-2 description'>{{$mercurio30->fecapr}}</p>
    </div>
</div>
