@extends('layouts.bone')

@push('styles')
	<style>
		#dataTable {
			font-size: 0.7rem;
		}

		#dataTable thead {
			background-color: #f0f0f0;
		}

		#dataTable th {
			padding: 0.3rem;
			text-align: left;
			vertical-align: middle;
			font-size: 0.85rem;
		}

		#dataTable td {
			padding: 0.3rem;
			text-align: center;
			vertical-align: middle;
		}
	</style>
	<link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')  
	<script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
	<script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>
	<script>
		const _TITULO = "{{ $title ?? 'Consulta de Nómina Empresa' }}";
		window.ServerController = 'subsidioemp';
	</script>
	<script src="{{ asset('mercurio/build/NominasEmpresa.js') }}"></script>
@endpush

@section('content')
<div class="col-12 mt-3">
	<div class="card mb-0">
		<div class="card-header p-3">
			<form id="form" class="validation_form" autocomplete="off" novalidate>
				<div class="row justify-content-center align-items-end">
					<div class="col-md-2">
						<div class="form-group">
							<label for="periodo" class="form-control-label">Periodo</label>
							<input
								type="text"
								id="periodo"
								name="periodo"
								date="month"
								class="form-control"
								placeholder="Periodo de consulta"
								value="{{ date('Ym', strtotime('-3 month')) }}">
						</div>
					</div>
					<div class="col-md-auto">
						<div class="form-group">
							<button type="button" class="btn btn-primary" id="btn_consulta_nomina">
								<i class="fa fa-search"></i> Consultar
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="card-body">
			<div id='consulta' class='col table-responsive'></div>
		</div>
	</div>
</div>
@endsection
