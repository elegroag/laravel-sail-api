@extends('layouts.bone')

@push('styles')
	<style>
		.mora-sucursal-card .table {
			font-size: 0.85rem;
			margin-bottom: 0;
		}

		.mora-sucursal-card .table thead {
			background-color: #f0f0f0;
		}

		.mora-sucursal-card .table th,
		.mora-sucursal-card .table td {
			padding: 0.35rem 0.5rem;
			vertical-align: middle;
		}

		.mora-sucursal-card .badge-estado {
			font-size: 0.75rem;
		}

		.mora-empty {
			color: #6c757d;
			font-size: 0.9rem;
		}
	</style>
@endpush

@push('scripts')
<script>
	const _TITULO = "{{ $title ?? 'Mora Presunta Empresa' }}";
	window.ServerController = 'subsidioemp';
</script>
<script type="text/template" id="tmp_mora_presunta">
	<% if (!bloques || _.size(bloques) === 0) { %>
		<div class="alert alert-info mb-0">No hay sucursales registradas para esta empresa.</div>
	<% } else {
		_.each(bloques, function (bloque) { %>
		<div class="card mb-3 mora-sucursal-card">
			<div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
				<div>
					<strong>Sucursal <%- bloque.codsuc %></strong>
					<% if (bloque.detalle) { %>
						<span class="text-muted">— <%- bloque.detalle %></span>
					<% } %>
				</div>
				<div class="d-flex align-items-center gap-2">
					<% if (bloque.estado_label) { %>
						<span class="badge badge-estado <%- bloque.estado_class %>"><%- bloque.estado_label %></span>
					<% } %>
					<% if (bloque.tiene_cartera) { %>
						<span class="badge bg-warning text-dark">Cartera: <%- bloque.total_fmt %></span>
					<% } else { %>
						<span class="badge bg-secondary">Sin cartera</span>
					<% } %>
				</div>
			</div>
			<div class="card-body">
				<% if (!bloque.tiene_cartera) { %>
					<p class="mora-empty mb-0">Esta sucursal no tiene cartera pendiente.</p>
				<% } else { %>
					<div class="table-responsive">
						<table class="table table-sm table-bordered table-hover align-middle">
							<thead>
								<tr>
									<th scope="col">Periodo</th>
									<th scope="col" class="text-end">Valor cartera</th>
									<th scope="col" class="text-center">Detalle</th>
								</tr>
							</thead>
							<tbody>
								<% _.each(bloque.filas, function (fila) { %>
								<tr>
									<td><%- fila.periodo_fmt %></td>
									<td class="text-end"><%- fila.valcar_fmt %></td>
									<td class="text-center">
										<% if (fila.tiene_valor) { %>
										<button type="button" class="btn btn-outline-primary btn-sm btn-detalle-cartera"
											data-codsuc="<%- bloque.codsuc %>" data-periodo="<%- fila.periodo %>">
											Ver detalle
										</button>
										<% } %>
									</td>
								</tr>
								<tr class="detalle-cartera-row d-none">
									<td colspan="3" class="detalle-cartera-container bg-light"></td>
								</tr>
								<% }); %>
							</tbody>
							<tfoot>
								<tr>
									<th>Total</th>
									<th class="text-end"><%- bloque.total_fmt %></th>
								</tr>
							</tfoot>
						</table>
					</div>
				<% } %>
			</div>
		</div>
		<% });
	} %>
</script>

<script type="text/template" id="tmp_layout">
	<div class="row">
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-header">
					<h5 class="mb-0">Cartera mora presunta</h5>
				</div>
				<div class="card-body" id="mora-bloques">
				</div>
			</div>
		</div>
	</div>
</script>
<script src="{{ versioned_asset('mercurio/build/MoraPresunta.js') }}"></script>
@endpush

@section('content')
<div class="col-12 mt-3">
	<div id='boneLayout'></div>
</div>
@endsection
