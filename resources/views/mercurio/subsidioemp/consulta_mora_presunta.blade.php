@extends('layouts.bone')

@push('styles')
	<link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}">
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

		.list-group-item {
			cursor: pointer;
		}

		.btn-sucursal {
			min-width: 120px;
		}

		.table th,
		.table td {
			vertical-align: middle;
		}

		#periodo-list {
			max-height: 600px;
			overflow-y: auto;
		}

		.badge-afiliado,
		.badge-pago {
			font-size: 0.8rem;
			padding: 0.25rem 0.5rem;
		}

		.list-group-item.active {
			background-color: rgb(171, 231, 184);
			border-color: #c2ffcd;
		}
	</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
	const _TITULO = "{{ $title ?? 'Mora Presunta Empresa' }}";
	window.ServerController = 'subsidioemp';
</script>
<script type="text/template" id="tmp_mora_presunta">
	<div class="mb-0">
		<div class="col-auto">
			<table id='dataTable' class='table table-hover align-items-center table-bordered table-sm'>
				<thead>
					<tr>
						<th scope='col'>Cédula</th>
						<th scope='col'>Afiliado</th>
						<th scope='col'>Valor</th>
						<th scope='col'>Pago</th>
						<th scope='col'>Nombre Completo</th>
					</tr>
				</thead>
				<tbody class='list'>
					<% if (!cartera || _.size(cartera) == 0) { %>
						<tr align='center'>
							<td colspan=5><label class='text-center'>No hay datos para mostrar</label></td>
						</tr>
					<% } else { 
						_.each(cartera, function (item) {
						%>
						<tr>
							<td><%=item.cedtra%></td>
							<td>
								<% if (item.afiliado == 'S') { %>
									<span class="badge bg-success badge-afiliado">Sí</span>
								<% } else { %>
									<span class="badge bg-secondary badge-afiliado">No</span>
								<% } %>
							</td>
							<td class="text-end"><%=item.valcar%></td>
							<td>
								<% if (item.pago == 'S') { %>
									<span class="badge bg-success badge-pago">Pagado</span>
								<% } else { %>
									<span class="badge bg-warning text-dark badge-pago">Pendiente</span>
								<% } %>
							</td>
							<td><%=item.fullname%></td>
						</tr>
						<% }); %>
					<% } %>
				</tbody>
			</table> 
		</div>
	</div>
</script>

<script type="text/template" id="tmp_layout">
	<div class="row">
		<!-- Tabla de datos principal -->
		<div class="col-md-10">
			<div class="card mb-4 d-none" id="mora-table-card">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h5 class="mb-0">Datos de mora presunta <span id="view_mora_title" class="fw-normal"></span></h5>
					<div class="btn-group d-none" id="sucursal-tabs"></div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped table-hover mb-0" id="data-table">
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Filtros de búsqueda -->
		<div class="col-md-2">
			<div class="card h-100">
				<div class="card-header py-2">
					<h6 class="mb-0">Búsqueda</h6>
				</div>
				<div class="card-body p-2" id="periodo-list">
					<!-- Aquí se generarán los selects de sucursal/período dinámicamente -->
				</div>
			</div>
		</div>
	</div>
</script>
<script src="{{ asset('mercurio/build/MoraPresunta.js') }}"></script>
@endpush

@section('content')
<div class="col-12 mt-3">
	<div id='boneLayout'></div>
</div>
@endsection