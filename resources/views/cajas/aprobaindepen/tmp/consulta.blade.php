@php $msexo = ($mercurio41->getSexo() != 'N') ? $_sexos[$mercurio41->getSexo()] : ''; @endphp

<div class="mb-2 request-info-card">
	<div class="card-header bg-light">
		<h5 class="mb-0">Información del Independiente</h5>
	</div>
	<div class="card-body">
		<div class="row justify-content-around">
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Identificación</label>
					<div class="form-control bg-light">{{ $mercurio41->getCedtra() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Estado</label>
					<div class="form-control bg-light">{{ $mercurio41->getEstadoDetalle() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Calidad Empresa</label>
					<div class="form-control bg-light">{{ $mercurio41->getCalempDetalle() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Tipo de Documento</label>
					<div class="form-control bg-light">{{ isset($_tipdoc[$mercurio41->getTipdoc()]) ? $_tipdoc[$mercurio41->getTipdoc()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cargo</label>
					<div class="form-control bg-light">{{ isset($_cargos[$mercurio41->getCargo()]) ? $_cargos[$mercurio41->getCargo()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombres</label>
					<div class="form-control bg-light">{{ $mercurio41->getPrinom() . ' ' . $mercurio41->getSegnom() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Apellidos</label>
					<div class="form-control bg-light">{{ $mercurio41->getPriape() . ' ' . $mercurio41->getSegape() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha de Nacimiento</label>
					<div class="form-control bg-light">{{ $mercurio41->getFecnac() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Sexo</label>
					<div class="form-control bg-light">{{ $msexo }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Estado Civil</label>
					<div class="form-control bg-light">{{ isset($_estciv[$mercurio41->getEstciv()]) ? $_estciv[$mercurio41->getEstciv()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Salario</label>
					<div class="form-control bg-light">{{ $mercurio41->getSalario() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Discapacidad</label>
					<div class="form-control bg-light">{{ isset($_tipdis[$mercurio41->getTipdis()]) ? $_tipdis[$mercurio41->getTipdis()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nivel de Educación</label>
					<div class="form-control bg-light">{{ isset($_nivedu[$mercurio41->getNivedu()]) ? $_nivedu[$mercurio41->getNivedu()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Tipo de Afiliado</label>
					<div class="form-control bg-light">{{ isset($_tipafi[$mercurio41->getTipafi()]) ? $_tipafi[$mercurio41->getTipafi()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Dirección Notificaciones</label>
					<div class="form-control bg-light">{{ $mercurio41->getDireccion() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad Notificaciones</label>
					<div class="form-control bg-light">{{ isset($_codciu[$mercurio41->getCodciu()]) ? $_codciu[$mercurio41->getCodciu()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad Labor Trabajadores</label>
					<div class="form-control bg-light">{{ isset($_codzon[$mercurio41->getCodzon()]) ? $_codzon[$mercurio41->getCodzon()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Teléfono Notificaciones</label>
					<div class="form-control bg-light">{{ $mercurio41->getTelefono() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Celular Notificaciones</label>
					<div class="form-control bg-light">{{ $mercurio41->getCelular() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Email Notificaciones</label>
					<div class="form-control bg-light">{{ $mercurio41->getEmail() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha Inicial</label>
					<div class="form-control bg-light">{{ $mercurio41->getFecini() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Actividad</label>
					<div class="form-control bg-light">{{ isset($_codact[$mercurio41->getCodact()]) ? $_codact[$mercurio41->getCodact()] : '' }}</div>
				</div>
			</div>
		</div>
	</div>
</div>
