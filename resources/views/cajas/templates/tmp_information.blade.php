<div class="row justify-content-around">
	<% if($scope.solicitud.get('estado') === 'A') { %>
	<div class='col-4'>
		<div class='alert alert-success' role='alert'>
			<i class="fa fa-exclamation-triangle"></i>
			La solicitud ya se encuentra aprobada
		</div>
	</div>
	<%}%>
	<% if($scope.solicitud.get('estado') === 'T') { %>
	<div class='col-4'>
		<div class='alert bg-pink text-white' role='alert'>
			<i class="fa fa-exclamation-triangle"></i>
			La solicitud se encuentra en estado Temporal, no dispone de usuario asignado para validación
		</div>
	</div>
	<%}%>
	<div class='col-12'>
        <div class="nav nav-pills" id="nav-pill" role="tablist">
            <button
                class="nav-link active border-0 ml-2"
                id="nav-solicitud-tab"
                data-bs-toggle='tab'
                role='tab'
                data-bs-target="#nav-solicitud "
                type="button" role="tab"
                aria-controls="nav-solicitud"
                aria-selected="true">Solicitud
            </button>
            <button
                class="nav-link border-0 ml-2"
                id="nav-archivos-tab"
                data-bs-toggle='tab'
                role='tab'
                data-bs-target="#nav-archivos "
                type="button" role="tab"
                aria-controls="nav-archivos"
                aria-selected="true">Archivos
            </button>
            <button
                class="nav-link border-0 ml-2"
                id="nav-seguimiento-tab"
                data-bs-toggle='tab'
                role='tab'
                data-bs-target="#nav-seguimiento "
                type="button" role="tab"
                aria-controls="nav-seguimiento"
                aria-selected="true">Seguimiento
            </button>
			<% if($scope.solicitud.get('estado') != 'A' && $scope.solicitud.get('estado') != 'T') { %>
			<button
				class="nav-link border-0 ml-2"
				id="nav-aprobar-tab"
				data-bs-toggle='tab'
				role='tab'
				data-bs-target="#nav-aprobar"
				type="button" role="tab"
				aria-controls="nav-aprobar"
				aria-selected="true">Aprobar
			</button>
			<button
				class="nav-link border-0 ml-2"
				id="nav-devolver-tab"
				data-bs-toggle='tab'
				role='tab'
				data-bs-target="#nav-devolver"
				type="button"
				aria-controls="nav-devolver"
				aria-selected="false">Devolver
			</button>
			<button
				class="nav-link border-0 ml-2"
				id="nav-rechazar-tab"
				data-bs-toggle='tab'
				role='tab'
				data-bs-target="#nav-rechazar"
				type="button"
				aria-controls="nav-rechazar"
				aria-selected="false">Rechazar
			</button>
			<% } %>
		</div>
	</div>
	<div class='col-12'>
		<div class='tab-content' id='v-pills-tabContent'>
			<div class='tab-pane show active pt-3' id='nav-solicitud' role='tabpanel' aria-labelledby='nav-solicitud-tab'>
				<%= $scope.consulta %>
			</div>

			<div class='tab-pane fade pt-3' id='nav-archivos' role='tabpanel' aria-labelledby='nav-archivos-tab'>
				<%= $scope.adjuntos %>
			</div>

			<div class='tab-pane fade pt-3' id='nav-seguimiento' role='tabpanel' aria-labelledby='nav-seguimiento-tab'>
				<%= $scope.seguimiento %>
			</div>

			<div class='tab-pane fade pt-3' id='nav-aprobar' role='tabpanel' aria-labelledby='nav-aprobar-tab'>
				<div id='renderAprobar'></div>
			</div>

			<div class='tab-pane fade pt-3' id='nav-devolver' role='tabpanel' aria-labelledby='nav-devolver-tab'>
				<div id='renderDevolver'></div>
			</div>

			<div class='tab-pane fade pt-3' id='nav-rechazar' role='tabpanel' aria-labelledby='nav-rechazar-tab'>
				<div id='renderRechazar'></div>
			</div>
		</div>
	</div>
</div>