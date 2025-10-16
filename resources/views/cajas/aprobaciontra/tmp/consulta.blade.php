@php
$msexo = ($trabajador->getSexo() != 'N') ? $_sexos[$trabajador->getSexo()] : '';
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
					<div class="form-control bg-light">{{ $trabajador->getNit() }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Razón social</label>
					<div class="form-control bg-light">{{ capitalize($trabajador->getRazsoc()) }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cedula</label>
					<div class="form-control bg-light">{{ $trabajador->getCedtra() }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Apellidos</label>
					<div class="form-control bg-light">{{ capitalize($trabajador->getPriape() . ' ' . $trabajador->getSegape()) }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombres</label>
					<div class="form-control bg-light">{{ capitalize($trabajador->getPrinom() . ' ' . $trabajador->getSegnom()) }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Fecha nacimineto</label>
					<div class="form-control bg-light">{{ $trabajador->getFecnac() }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad nacimiento</label>
					<div class="form-control bg-light">{{ capitalize(@$_codciu[$trabajador->getCodciu()]) }}</div>
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
					<div class="form-control bg-light">{{ @$_estciv[$trabajador->getEstciv()] }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cabeza hogar</label>
					<div class="form-control bg-light">{{ @$_cabhog[$trabajador->getCabhog()] }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Ciudad</label>
					<div class="form-control bg-light">{{ capitalize(@$_codciu[$trabajador->getCodciu()]) }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Zona</label>
					<div class="form-control bg-light">{{ capitalize(@$_codzon[$trabajador->getCodzon()]) }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Direccion</label>
					<div class="form-control bg-light">{{ $trabajador->getDireccion() }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Barrio</label>
					<div class="form-control bg-light">{{ $trabajador->getBarrio() }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Telefono</label>
					<div class="form-control bg-light">{{ $trabajador->getTelefono() }}</div>
				</div>
			</div>
            <div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Celular</label>
					<div class="form-control bg-light">{{ $trabajador->getCelular() }}</div>
				</div>
			</div>
            <div class="col-md-8 col-lg-6">
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Cargo</label>
                    <div class="form-control bg-light">{{ @$_ocupaciones[$trabajador->getCargo()] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Email</label>
                    <div class="form-control bg-light">{{ $trabajador->getEmail() }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Fecha Ingreso</label>
                    <div class="form-control bg-light">{{ $trabajador->getFecing() }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Salario</label>
                    <div class="form-control bg-light">{{ $trabajador->getSalario() }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Capacidad de trabajar</label>
                    <div class="form-control bg-light">{{ @$_captra[$trabajador->getCaptra()] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Discapacidad</label>
                    <div class="form-control bg-light">{{ @$_tipdis[$trabajador->getTipdis()] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Nivel Educación</label>
                    <div class="form-control bg-light">{{ @$_nivedu[$trabajador->getNivedu()] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Rural</label>
                    <div class="form-control bg-light">{{ @$_rural[$trabajador->getRural()] }}</div>
                </div>    
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Horas</label>
                    <div class="form-control bg-light">{{ $trabajador->getHoras() }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Tipo Contrato</label>
                    <div class="form-control bg-light">{{ @$_tipcon[$trabajador->getTipcon()] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Vivienda</label>
                    <div class="form-control bg-light">{{ @$_vivienda[$trabajador->getVivienda()] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Tipo Afiliado</label>
                    <div class="form-control bg-light">{{ @$_tipafi[$trabajador->getTipafi()] }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Profesion</label>
                    <div class="form-control bg-light">{{ $trabajador->getProfesion() }}</div>
                </div>
            </div>
            <div class='col-md-4 col-lg-3'>
                <div class="form-group">
                    <label class="form-label text-muted small mb-1">Autoriza</label>
                    <div class="form-control bg-light">{{ ($trabajador->getAutoriza() == 'S') ? 'SI' : 'NO' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>