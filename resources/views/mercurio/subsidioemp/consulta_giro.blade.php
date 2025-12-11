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
		const _TITULO = "{{ $title ?? 'Consulta de Giros Empresa' }}";
		window.ServerController = 'subsidioemp';
	</script>
	<script src="{{ asset('mercurio/build/ConsultasEmpresa.js') }}"></script>
@endpush

@section('content')
<script type="text/template" id="templateConsulta">
	<div class='p-2 m-0'>
		<p class='m-0'><span><b>Número de Cuotas:</b> <%= cuotas.reduce((total, item) => total + item.numcuo, 0) %></span><br/>
			<span><b>Valor Neto:</b> <%= cuotas.reduce((total, item) => total + item.valor, 0) %></span><br/>
		</p>
	</div>
	
	<table id='dataTable' class='table table-hover align-items-center table-bordered'>
		<thead>
			<tr>
				<th scope='col'>Periodo girado</th>
				<th scope='col'>Tipo</th>
				<th scope='col'>Nombre responsable</th>
				<th scope='col'>Nombre beneficiario</th>
				<th scope='col'>Forma pago</th>
				<th scope='col'>Número cuotas</th>
				<th scope='col'>Valor neto</th>
				<th scope='col'>Valor crédito</th>
				<th scope='col'>Valor ajuste</th>
			</tr>
		</thead>
		<tbody class='list'>
			<% if (cuotas.length == 0) { %>
				<tr align='center'>
					<td colspan=10><label class='text-center'>No hay datos para mostrar</label></td>
				</tr>
			<% } else { %>
				<% _.each(cuotas, function(item) { %>
					<tr>
						<td><%= item.pergir %></td>
						<td><%= item.tipo_pago %></td>
						<td><%= item.nomres %></td>
						<td><%= item.nombre %></td>
						<td><%= item.tippag %></td>
						<td><%= item.numcuo %></td>
						<td><%= item.valor %></td>
						<td><%= item.valcre %></td>
						<td><%= item.valaju %></td>
					</tr>
				<% }) %>
			<% } %>
			</tbody>
		</table>
</script>

<div class="col-12 mt-3">
	<div class="card mb-0">
		<div class="card-header p-3">
			<form id="form" class="validation_form" autocomplete="off" novalidate>
				<div class="row justify-content-center align-items-end">
					<div class="col-md-2">
						<div class="form-group">
							<label for="perini" class="form-control-label">Periodo Inicial</label>
							<input
								type="text"
								id="perini"
								name="perini"
								date="month"
								class="form-control"
								placeholder="Periodo Inicial"
								value="{{ date('Ym', strtotime('-3 month')) }}">
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="perfin" class="form-control-label">Periodo Final</label>
							  <input
									type="text"
									id="perfin"
									name="perfin"
									date="month"
									class="form-control"
									placeholder="Periodo Final"
									value="{{ date('Ym') }}">
						</div>
					</div>
					<div class="col-md-auto">
						<div class="form-group">
							<button type="button" class="btn btn-default" id='bt_consulta_giro'><i class="fa fa-search"></i> Consultar</button>
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