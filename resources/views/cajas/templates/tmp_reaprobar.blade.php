<div class="card mb-1">
	<div class="card-header">
		<h4>Re-aprobar</h4>
	</div>
	<div class="card-body">
		<p>Esta opción es para aprobar al trabajador en comfaca en línea, cuando ya fue aprobada por sisuweb.</p>
		<form method="POST" id='form_reaprobar'>
			<div class="row mb-3">
				<div class="col-md-8">
					<div class="form-group">
						<label for='nota' class='form-label'>Nota</label>
						<textarea class='form-control summer_content' name="nota_reaprobar" id='nota_reaprobar' rows='3'></textarea>
					</div>
				</div>
			</div>
			<div class="row mb-3" style="display: none;" id="renderByTrabajador">
				<div class="col-md-4">
					<div class="form-group">
						<label for='codigo_giro' class='form-label'>Código giro</label>
						<input type="text" class="form-control" id="codigo_giro">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for='giro' class='form-label'>Giro</label>
						<select class="form-control" id="giro">
							<option value="">Seleccione</option>
							<option value="S">SI</option>
							<option value="N">NO</option>
						</select>
					</div>
				</div>
			</div>
			<button type='button' class='btn btn-success' id='procesarReaprobar'>Re-aprobar</button>
		</form>
	</div>
</div>