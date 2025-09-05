@extends('layouts.bone')

@section('content')
<div class="card mb-0">
	<div class="card-body">
		<form id="form" class="validation_form" autocomplete="off" novalidate>
		<div class="row">
			<div class="col-md-4 ml-auto">
				<div class="form-group">
					<label for="claant" class="form-control-label">Clave Anterior</label>
					<input type="password" id="claant" name="claant" class="form-control" placeholder="Clave Anterior" />
				</div>
			</div>
			<div class="col-md-4 ml-auto">
				<div class="form-group">
					<label for="clave" class="form-control-label">Clave Nueva</label>
					<input type="password" id="clave" name="clave" class="form-control" placeholder="Clave Nueva" />
				</div>
			</div>
			<div class="col-md-4 ml-auto">
				<div class="form-group">
					<label for="clacon" class="form-control-label">Clave Confirmacion</label>
					<input type="password" id="clacon" name="clacon" class="form-control" placeholder="Clave Confirmacion" />
				</div>
			</div>
			<div class="col-md-auto d-flex m-auto">
				<button type="button" class="btn btn-icon btn-primary align-self-center" id="bt_cambio_clave">
					<span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
					<span class="btn-inner--text">Cambiar Clave</span>
				</button>
			</div>
		</div>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('mercurio/CambioClave.js') }}"></script>
@endpush
