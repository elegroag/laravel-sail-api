<div class="card mb-4 request-info-card">
	<div class="card-header bg-light">
		<h5 class="mb-0">Información del certificado</h5>
	</div>
	<div class="card-body">
		<div class="row g-3">
			<div class="col-md-6 col-lg-4">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cedula de trabajador</label>
					<div class="form-control bg-light">{{$mercurio45->getCedtra()}}</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombres Completos</label>
					<div class="form-control bg-light">{{$mercurio45->getNombre()}}</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombre del certificado</label>
					<div class="form-control bg-light">{{$mercurio45->getNomcer()}}</div>
				</div>
			</div>
		</div>
	</div>
</div>