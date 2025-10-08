<h4>Deshacer aprobación</h4>
<p>Esta opción es para deshacer la aprobación del trabajador e informarle la causal del rechazo</p>
<div class="container">
	<div class="row">
		<div class="col-12">
			<form action="#" method="post" id='formDeshacer'>
				<div class="row">
					<div class="col-6">
						<div class="form-group mb-3">
							<label class="form-label">Acción Comfaca En Línea:</label>
							<select name="action" class="form-control">
								<option value="">Seleccione un motivo</option>
								<option value="D">Devolver</option>
								<option value="R">Rechazar</option>
								<option value="I">Inactivo</option>
							</select>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group mb-3">
							<label class="form-label"> Motivo de la acción:</label>
							<select  name="codest" class="form-control">
								<option value="">Seleccione un motivo</option>
								@foreach ($mercurio11 as $codest)
								<option value="{{ $codest->getCodest() }}">{{ $codest->getDetalle() }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-3">
						<div class="form-group mb-3">
							<label class="form-label">Envíar email:</label>
							<div class="form-check">
								<input type="radio" class="form-check-input" name="send_email" value="S">
								<label class="form-check-label" for="send_email">SI</label>
							</div>
							<div class="form-check">
								<input type="radio" class="form-check-input" name="send_email" value="N" checked>
								<label class="form-check-label" for="send_email">NO</label>
							</div>
						</div>
					</div>
					<div class="col-9">
						<div class="form-group mb-3">
							<label class="form-label">Nota de seguimiento y trazabilidad:</label>
							<textarea class='form-control' name='nota_deshacer' id="nota_deshacer" rows='3'></textarea>
						</div>
					</div>
				</div>

				<div class="row justify-content-center">
					<div class="col-2">
						<button type='button' class='btn btn-md btn-warning' id='procesarDeshacer'>Procesar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>