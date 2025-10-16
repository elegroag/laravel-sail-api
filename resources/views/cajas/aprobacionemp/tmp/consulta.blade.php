<div class="mb-2 request-info-card">
	<div class="card-header bg-light">
		<h5 class="mb-0">Información de la Empresa</h5>
	</div>
	<div class="card-body">
		<div class="row justify-content-around">
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">NIT</label>
					<div class="form-control bg-light">{{ $mercurio30->nit }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Estado</label>
					<div class="form-control bg-light">{{ $mercurio30->getEstadoDetalle() }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Razón Social</label>
					<div class="form-control bg-light">{{ $mercurio30->razsoc }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Sigla</label>
					<div class="form-control bg-light">{{ $mercurio30->sigla }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Dígito de Verificación</label>
					<div class="form-control bg-light">{{ $mercurio30->digver }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Calidad Empresa</label>
					<div class="form-control bg-light">{{ isset($_calemp[$mercurio30->calemp]) ? $_calemp[$mercurio30->calemp] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cédula Representante</label>
					<div class="form-control bg-light">{{ $mercurio30->cedrep }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombre Representante</label>
					<div class="form-control bg-light">{{ $mercurio30->repleg }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Dirección de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio30->direccion }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad Notificación</label>
					<div class="form-control bg-light">{{ isset($_codciu[$mercurio30->codciu]) ? $_codciu[$mercurio30->codciu] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad de Labor de Trabajadores</label>
					<div class="form-control bg-light">{{ isset($_codzon[$mercurio30->codzon]) ? $_codzon[$mercurio30->codzon] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Teléfono de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio30->telefono }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Celular de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio30->celular }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Email de Notificación</label>
					<div class="form-control bg-light">{{ $mercurio30->email }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Actividad</label>
					<div class="form-control bg-light">{{ isset($_codact[$mercurio30->codact]) ? $_codact[$mercurio30->codact] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha Inicial</label>
					<div class="form-control bg-light">{{ $mercurio30->fecini }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Total Trabajadores</label>
					<div class="form-control bg-light">{{ $mercurio30->tottra }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Valor Nómina</label>
					<div class="form-control bg-light">{{ $mercurio30->valnom }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Tipo Sociedad</label>
					<div class="form-control bg-light">{{ $tipsoc_detalle }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Dirección Comercial</label>
					<div class="form-control bg-light">{{ $mercurio30->dirpri }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad Comercial</label>
					<div class="form-control bg-light">{{ isset($_codciu[$mercurio30->ciupri]) ? $_codciu[$mercurio30->ciupri] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Teléfono Comercial</label>
					<div class="form-control bg-light">{{ $mercurio30->celpri }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Email Comercial</label>
					<div class="form-control bg-light">{{ $mercurio30->emailpri }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha aprobación resolución</label>
					<div class="form-control bg-light">{{ $mercurio30->fecapr }}</div>
				</div>
			</div>
		</div>
	</div>
</div>
