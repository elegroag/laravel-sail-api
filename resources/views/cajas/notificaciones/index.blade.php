<div class="header bg-gradient-primary pb-9">
	<div class="container-fluid">
		<div class="header-body p-4">
			<div id='header_group_button'>
				<div class="col-lg-7 col-auto mr-auto">
					<h4 class="text-white d-inline-block mb-0">Notificaciones</h4>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<a class="btn btn-sm" data-href="principal/index"><i class="fas fa-home"></i></a>
								<label class="text-white">Lista de notificaciones</label>
							</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid mt--9 pb-4">
	<div class="row" id='boneLayout'></div>
	<div class="row">
		<div class="d-flex mb-4">
			<label class="py-2">Por página:</label>
			<select id="por_pagina" class="form-control" style="width: 80px;" placeholder="Por página">
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="20">20</option>
			</select>
			<p class="py-2 ml-2">Total registros: <span id="total_registros"></span></p>
		</div>
	</div>
</div>

<script type='text/template' id='notificationRender'>
	<div class="col">
		<div class="card">
			<div class='card-header bg-green-blue p-1' id='render_subheader'></div>
			<div class="card-body m-3">
				<div id='notificationListarTodo' class="notification-list">
				</div>
			</div>
			<div class="m-2 d-flex justify-content-center" id='pagination'></div>
		</div>
	</div>
</script>

<script type='text/template' id='itemNotification'>
	<%
	bg_estado = 'bg-warning';
	if (estado == 'P') {
		bg_estado = 'bg-info';
	}
	%>
	<a href="#" class="dropdown-item py-3 border-bottom" data-toggle='detail-note' data-id='<%= id %>'>
		<div class="d-flex">
			<div class="flex-shrink-0 me-3">
				<div class="<%=bg_estado %> bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
					<i class="ni ni-single-copy-04 text-white"></i>
				</div>
			</div>
			<div class="flex-grow-1">
				<div class="d-flex justify-content-between align-items-center">
					<h6 class="mb-0 fw-bold"><%= titulo %></h6>
					<small class="text-muted"><i class="fas fa-calendar-alt"></i> <%= dia %> <i class="fas fa-clock"></i> <%= hora %></small>
				</div>
				<p class="mb-0 small text-muted"><%= (estado === 'P')? 'Nota pendiente de lectura': 'Nota leida' %></p>
				<% if(progre > 0){ %>
				<label class="small text-muted">Progreso:</label>
				<div class="progress" style="height: 0.25rem;">
					<div class="progress-bar bg-success" role="progressbar" style="width: <%= progre %>; height: 0.25rem;" aria-valuenow="<%= progre %>" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
				<% } %>
			</div>
		</div>
	</a>
</script>

<script type="text/template" id='notificationDetail'>
	<div class="flex-grow-1">
		<div class="d-flex justify-content-between align-items-center">
			<h6 class="mb-0 fw-bold w-50"><%= titulo %></h6>
			<small class="text-muted"><i class="fas fa-calendar-alt"></i> <%= dia %> <br/><i class="fas fa-clock"></i> <%= hora %></small>
		</div>
		<p class="mb-0 small text-muted"><%= (estado === 'P')? 'Nota pendiente de lectura': 'Nota leida' %></p>
		<label>Descripción:</label>
		<div class='modal-text-notify'>
			<%= descri %>
		</div>
		<div class="mt-2">
			<% if(progre > 0){ %>
			<label class="small text-muted">Progreso:</label>
			<div class="progress" style="height: 0.25rem;">
				<div class="progress-bar bg-success" role="progressbar" style="width: <%= progre %>; height: 0.25rem;" aria-valuenow="<%= progre %>" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
			<% } %>
		</div>
	</div>
</script>

<?= Tag::javascriptInclude('Cajas/notificaciones/build.notificaciones'); ?>


<style>
	.modal-text-notify {
		font-size: .88rem;
		font-weight: 400
	}
	.modal-text-notify h1,
	.modal-text-notify h2,
	.modal-text-notify h3,
	.modal-text-notify h4,
	.modal-text-notify h5,
	.modal-text-notify h6 {
		font-size: 1rem !important;
		font-weight: 400 !important;
		line-height: 1.5 !important;
	}
</style>