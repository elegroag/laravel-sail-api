<div class="mb-2 request-info-card">
	<div class="card-header bg-light">
		<h5 class="mb-0">Solicitud</h5>
	</div>
	<div class="card-body">
		<div class="row justify-content-around">
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">RUUID</label>
					<div class="form-control bg-light">{{ $mercurio39->ruuid ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Documento</label>
					<div class="form-control bg-light">{{ $mercurio39->getCedtra() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Estado</label>
					<div class="form-control bg-light">{{ $mercurio39->getEstadoDetalle() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Calidad Empresa</label>
					<div class="form-control bg-light">{{ $_calemp[$mercurio39->getCalemp()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Dirección de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio39->getDireccion() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad de Notificación</label>
					<div class="form-control bg-light">{{ $_codciu[$mercurio39->getCodciu()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad de Labor de Trabajadores</label>
					<div class="form-control bg-light">{{ $_codzon[$mercurio39->getCodzon()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Teléfono de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio39->getTelefono() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Celular de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio39->getCelular() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Email de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio39->getEmail() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Actividad</label>
					<div class="form-control bg-light">{{ $_codact[$mercurio39->getCodact()] ?? '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha Ingreso</label>
					<div class="form-control bg-light">{{ $mercurio39->getFecing() }}</div>
				</div>
			</div>
		</div>
	</div>
</div>
