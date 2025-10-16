<div class="mb-2 request-info-card">
	<div class="card-header bg-light">
		<h5 class="mb-0">Solicitud desde {{ $det_tipo }} | Datos Empresa</h5>
	</div>
	<div class="card-body">
		<div class="row justify-content-around">
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">NIT</label>
					<div class="form-control bg-light">{{ $mercurio36->getCedtra() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Estado</label>
					<div class="form-control bg-light">{{ $mercurio36->getEstadoDetalle() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Calidad Empresa</label>
					<div class="form-control bg-light">{{ isset($_calemp[$mercurio36->getCalemp()]) ? $_calemp[$mercurio36->getCalemp()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Dirección de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio36->getDireccion() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad de Notificación</label>
					<div class="form-control bg-light">{{ isset($_codciu[$mercurio36->getCodciu()]) ? $_codciu[$mercurio36->getCodciu()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad de Labor de Trabajadores</label>
					<div class="form-control bg-light">{{ isset($_codzon[$mercurio36->getCodzon()]) ? $_codzon[$mercurio36->getCodzon()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Teléfono de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio36->getTelefono() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Celular de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio36->getCelular() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Email de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio36->getEmail() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Actividad</label>
					<div class="form-control bg-light">{{ isset($_codact[$mercurio36->getCodact()]) ? $_codact[$mercurio36->getCodact()] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha Solicitud</label>
					<div class="form-control bg-light">{{ $mercurio36->getFecsol() }}</div>
				</div>
			</div>
		</div>
	</div>
</div>
