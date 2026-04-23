<div class="mb-2 request-info-card">
	<div class="card-header bg-light">
		<h5 class="mb-0">Información del Beneficiario</h5>
	</div>
	<div class="card-body">
		<div class="row justify-content-around">
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Documento del Beneficiario</label>
					<div class="form-control bg-light">{{ $beneficiario->getNumdoc() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombres Completos</label>
					<div class="form-control bg-light">{{ $beneficiario->getPrinom() . ' ' . $beneficiario->getSegnom() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Apellidos</label>
					<div class="form-control bg-light">{{ $beneficiario->getPriape() . ' ' . $beneficiario->getSegape() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha de Nacimiento</label>
					<div class="form-control bg-light">{{ $beneficiario->getFecnac() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad de Nacimiento</label>
					<div class="form-control bg-light">{{ $_codciu[$beneficiario->getCiunac()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Sexo</label>
					<div class="form-control bg-light">{{ $_sexo[$beneficiario->getSexo()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Parentesco</label>
					<div class="form-control bg-light">{{ $_parent[$beneficiario->getParent()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Condición de Huérfano</label>
					<div class="form-control bg-light">{{ $_huerfano[$beneficiario->getHuerfano()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Tipo de Hijo</label>
					<div class="form-control bg-light">{{ $_tiphij[$beneficiario->getTiphij()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nivel de Educación</label>
					<div class="form-control bg-light">{{ $_nivedu[$beneficiario->getNivedu()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Capacidad de Trabajo</label>
					<div class="form-control bg-light">{{ $_captra[$beneficiario->getCaptra()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Tipo de Discapacidad</label>
					<div class="form-control bg-light">{{ $_tipdis[$beneficiario->getTipdis()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Calendario</label>
					<div class="form-control bg-light">{{ $_calendario[$beneficiario->getCalendario()] ?? '' }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cédula Cónyuge</label>
					<div class="form-control bg-light">{{ $beneficiario->getCedcon() }}</div>
				</div>
			</div>
		</div>
	</div>
</div>