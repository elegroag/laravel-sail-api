@php
$msexo = ($trabajador->sexo != 'N') ? $_sexos[$trabajador->sexo] : '';
@endphp
<div class="mb-2 request-info-card">
	<div class="card-header bg-light">
		<h5 class="mb-0">Información del Trabajador</h5>
	</div>
	<div class="card-body">
		<div class="row justify-content-around">
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nit</label>
					<div class="form-control bg-light">{{ $trabajador->nit }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Razón social</label>
					<div class="form-control bg-light">{{ capitalize($trabajador->razsoc) }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cedula</label>
					<div class="form-control bg-light">{{ $trabajador->cedtra }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Apellidos</label>
					<div class="form-control bg-light">{{ capitalize($trabajador->priape . ' ' . $trabajador->segape) }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombres</label>
					<div class="form-control bg-light">{{ capitalize($trabajador->prinom . ' ' . $trabajador->segnom) }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha nacimineto</label>
					<div class="form-control bg-light">{{ $trabajador->fecnac }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad nacimiento</label>
					<div class="form-control bg-light">{{ capitalize($_codciu[$trabajador->codciu]) }}</div>
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
					<label class="form-label text-muted small mb-1">Estado civil</label>
					<div class="form-control bg-light">{{ $trabajador->estciv }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cabeza hogar</label>
					<div class="form-control bg-light">{{ $trabajador->cabhog }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad</label>
					<div class="form-control bg-light">{{ capitalize($_codciu[$trabajador->codciu]) }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Zona</label>
					<div class="form-control bg-light">{{ capitalize($_codzon[$trabajador->codzon]) }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Dirección</label>
					<div class="form-control bg-light">{{ $trabajador->direccion }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Barrio</label>
					<div class="form-control bg-light">{{ $trabajador->barrio }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Telefono</label>
					<div class="form-control bg-light">{{ $trabajador->telefono }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Celular</label>
					<div class="form-control bg-light">{{ $trabajador->celular }}</div>
				</div>
			</div>
            <div class="col-md-8 col-lg-6">
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Cargo</label>
                    <div class="form-control bg-light">{{ $_ocupaciones[$trabajador->cargo] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Email</label>
                    <div class="form-control bg-light">{{ $trabajador->email }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Fecha Ingreso</label>
                    <div class="form-control bg-light">{{ $trabajador->fecing }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Salario</label>
                    <div class="form-control bg-light">{{ $trabajador->salario }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Capacidad de trabajar</label>
                    <div class="form-control bg-light">{{ $_captra[$trabajador->captra] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Discapacidad</label>
                    <div class="form-control bg-light">{{ $_tipdis[$trabajador->tipdis] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Nivel Educación</label>
                    <div class="form-control bg-light">{{ $_nivedu[$trabajador->nivedu] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Rural</label>
                    <div class="form-control bg-light">{{ $_rural[$trabajador->rural] }}</div>
                </div>    
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Horas</label>
                    <div class="form-control bg-light">{{ $trabajador->horas }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Tipo Contrato</label>
                    <div class="form-control bg-light">{{ $_tipcon[$trabajador->tipcon] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Vivienda</label>
                    <div class="form-control bg-light">{{ $_vivienda[$trabajador->vivienda] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Tipo Afiliado</label>
                    <div class="form-control bg-light">{{ $_tipafi[$trabajador->tipafi] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Profesion</label>
                    <div class="form-control bg-light">{{ $trabajador->profesion }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Autoriza</label>
                    <div class="form-control bg-light">{{ ($trabajador->autoriza == 'S') ? 'SI' : 'NO' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>