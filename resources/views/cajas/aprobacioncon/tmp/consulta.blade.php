@php
$codocu = str_pad($conyuge->getCodocu(), 2, '0', STR_PAD_LEFT);
@endphp
<div class='row pl-lg-4 pb-3'>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Cedula</label>
		<p class='pl-2 description'>{{ $conyuge->getCedcon() }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Apellidos</label>
		<p class='pl-2 description'>{{ $conyuge->getPriape() }} {{ $conyuge->getSegape() }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Nombres</label>
		<p class='pl-2 description'>{{ $conyuge->getPrinom() }} {{ $conyuge->getSegnom() }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Fecha Nacimiento</label>
		<p class='pl-2 description'>{{ $conyuge->getFecnac() }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Ciudad Nacimiento</label>
		<p class='pl-2 description'>{{ @$_codciu[$conyuge->getCiunac()] }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Sexo</label>
		<p class='pl-2 description'>{{ $_sexo[$conyuge->getSexo()] }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Estado Civil</label>
		<p class='pl-2 description'>{{ $_estciv[$conyuge->getEstciv()] }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Companera permanente</label>
		<p class='pl-2 description'>{{ ($conyuge->getComper()) ? $_comper[$conyuge->getComper()] : '' }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Ciudad Residencia</label>
		<p class='pl-2 description'>{{ @$_codciu[$conyuge->getCiures()] }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Zona</label>
		<p class='pl-2 description'>{{ @$_codciu[$conyuge->getCodzon()] }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Tipo Vivienda</label>
		<p class='pl-2 description'>{{ ($conyuge->getTipviv()) ? $_vivienda[$conyuge->getTipviv()] : '' }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Direccion</label>
		<p class='pl-2 description'>{{ $conyuge->getDireccion() }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Telefono</label>
		<p class='pl-2 description'>{{ $conyuge->getTelefono() }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Celular</label>
		<p class='pl-2 description'>{{ $conyuge->getCelular() }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Email</label>
		<p class='pl-2 description'>{{ $conyuge->getEmail() }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Nivel Educacion</label>
		<p class='pl-2 description'>{{ @$_nivedu[$conyuge->getNivedu()] }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Fecha Ingreso</label>
		<p class='pl-2 description'>{{ $conyuge->getFecing() }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Ocupacion</label>
		<p class='pl-2 description'>{{ @$_codocu["$codocu"] }}</p>
	</div>
	<div class='col-md-4 border-top border-right border-left border-bottom'>
		<label class='form-control-label'>Salario</label>
		<p class='pl-2 description'>{{ $conyuge->getSalario() }}</p>
	</div>
</div>
