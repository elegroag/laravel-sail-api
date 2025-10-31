@php
$codocu = str_pad($conyuge->codocu, 2, '0', STR_PAD_LEFT);
@endphp
<div class="mb-2 request-info-card">
	<div class="card-header bg-light">
		<h5 class="mb-0">Información del Cónyuge</h5>
	</div>
	<div class="card-body">
		<div class="row justify-content-around">
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cédula</label>
					<div class="form-control bg-light">{{ $conyuge->cedcon }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Apellidos</label>
					<div class="form-control bg-light">{{ $conyuge->priape . ' ' . $conyuge->segape }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombres</label>
					<div class="form-control bg-light">{{ $conyuge->prinom . ' ' . $conyuge->segnom }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha Nacimiento</label>
					<div class="form-control bg-light">{{ $conyuge->fecnac }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad Nacimiento</label>
					<div class="form-control bg-light">{{ $_codciu[$conyuge->ciunac] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Sexo</label>
					<div class="form-control bg-light">{{ $conyuge->sexo }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Estado Civil</label>
					<div class="form-control bg-light">{{ $conyuge->estciv }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Companera permanente</label>
					<div class="form-control bg-light">{{ ($conyuge->comper) ? $_comper[$conyuge->comper] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad Residencia</label>
					<div class="form-control bg-light">{{ $_codciu[$conyuge->ciures] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Zona</label>
					<div class="form-control bg-light">{{ $_codciu[$conyuge->codzon] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Tipo Vivienda</label>
					<div class="form-control bg-light">{{ ($conyuge->tipviv) ? $_vivienda[$conyuge->tipviv] : '' }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Direccion</label>
					<div class="form-control bg-light">{{ $conyuge->direccion }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Telefono</label>
					<div class="form-control bg-light">{{ $conyuge->telefono }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Celular</label>
					<div class="form-control bg-light">{{ $conyuge->celular }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Email</label>
					<div class="form-control bg-light">{{ $conyuge->email }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nivel Educacion</label>
					<div class="form-control bg-light">{{ $conyuge->nivedu }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha Ingreso</label>
					<div class="form-control bg-light">{{ $conyuge->fecing }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ocupacion</label>
					<div class="form-control bg-light">{{ $_codocu[$codocu] }}</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Salario</label>
					<div class="form-control bg-light">{{ $conyuge->salario }}</div>
				</div>
			</div>
		</div>
	</div>
</div>
