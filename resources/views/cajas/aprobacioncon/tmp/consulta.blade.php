@php
$codocu = str_pad($conyuge->getCodocu(), 2, '0', STR_PAD_LEFT);
@endphp
<div class="mb-2 request-info-card">
	<div class="card-header bg-light">
		<h5 class="mb-0">Información del Cónyuge</h5>
	</div>
	<div class="card-body">
		<div class="row justify-content-around">
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cedula</label>
					<div class="form-control bg-light">{{ $conyuge->getCedcon() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Apellidos</label>
					<div class="form-control bg-light">{{ $conyuge->getPriape() . ' ' . $conyuge->getSegape() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombres</label>
					<div class="form-control bg-light">{{ $conyuge->getPrinom() . ' ' . $conyuge->getSegnom() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha Nacimiento</label>
					<div class="form-control bg-light">{{ $conyuge->getFecnac() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad Nacimiento</label>
					<div class="form-control bg-light">{{ @$_codciu[$conyuge->getCiunac()] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Sexo</label>
					<div class="form-control bg-light">{{ $_sexo[$conyuge->getSexo()] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Estado Civil</label>
					<div class="form-control bg-light">{{ $_estciv[$conyuge->getEstciv()] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Sexo</label>
					<div class="form-control bg-light">{{ $_sexo[$conyuge->getSexo()] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Estado Civil</label>
					<div class="form-control bg-light">{{ $_estciv[$conyuge->getEstciv()] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Companera permanente</label>
					<div class="form-control bg-light">{{ ($conyuge->getComper()) ? $_comper[$conyuge->getComper()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad Residencia</label>
					<div class="form-control bg-light">{{ @$_codciu[$conyuge->getCiures()] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Zona</label>
					<div class="form-control bg-light">{{ @$_codciu[$conyuge->getCodzon()] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Tipo Vivienda</label>
					<div class="form-control bg-light">{{ ($conyuge->getTipviv()) ? $_vivienda[$conyuge->getTipviv()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Direccion</label>
					<div class="form-control bg-light">{{ $conyuge->getDireccion() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Telefono</label>
					<div class="form-control bg-light">{{ $conyuge->getTelefono() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Celular</label>
					<div class="form-control bg-light">{{ $conyuge->getCelular() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Email</label>
					<div class="form-control bg-light">{{ $conyuge->getEmail() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nivel Educacion</label>
					<div class="form-control bg-light">{{ @$_nivedu[$conyuge->getNivedu()] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha Ingreso</label>
					<div class="form-control bg-light">{{ $conyuge->getFecing() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ocupacion</label>
					<div class="form-control bg-light">{{ @$_codocu["$codocu"] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Salario</label>
					<div class="form-control bg-light">{{ $conyuge->getSalario() }}</div>
				</div>
			</div>
		</div>
	</div>
</div>
