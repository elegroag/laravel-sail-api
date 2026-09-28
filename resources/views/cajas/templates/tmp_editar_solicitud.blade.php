<div class="ficha-principal">
	<div class="card-body">
		<form id="formEditarSolicitud" class="validation_form" autocomplete="off" novalidate>
			<div class="d-none">
				<input type="number" name="id" id="id" class="d-none" value="<%- id %>" />
			</div>
			<% _.each(grupos, function(grupo) { %>
			<div class="row mb-2">
				<div class="col-12">
					<fieldset>
						<legend><%- grupo.titulo %></legend>
						<div class="row justify-content-start">
							<% _.each(grupo.campos, function(campo) { %>
							<div class="col-md-3">
								<div class="form-group" group-for="<%- campo.name %>">
									<label for="<%- campo.name %>" class="control-label"><%- campo.label %></label>
									<span id="component_<%- campo.name %>"></span>
								</div>
							</div>
							<% }); %>
						</div>
					</fieldset>
				</div>
			</div>
			<% }); %>
		</form>
	</div>
	<div class="card-footer">
		<button type="button" class="btn btn-primary" id="guardar_ficha"><i class="fas fa-save"></i> Guardar los cambios</button>
	</div>
</div>
