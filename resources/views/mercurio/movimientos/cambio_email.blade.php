@extends('layouts.bone')

@section('content')
<div class="card mb-0">
	<div class="card-body">
		<form id="form" class="validation_form" autocomplete="off" novalidate>
		<div class="row">
			<div class="col-md-4 ml-auto">
				<div class="form-group">
					<label for="email" class="form-control-label">Email</label>
					<input type="email" id="email" name="email" class="form-control" placeholder="Email" />
				</div>
			</div>
			<div class="col-md-auto d-flex m-auto">
				<button type="button" class="btn btn-icon btn-primary align-self-center" id="bt_cambio_email">
					<span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
					<span class="btn-inner--text">Cambiar Email de Aviso</span>
				</button>
			</div>
		</div>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('mercurio/CambioEmail.js') }}"></script>
@endpush
