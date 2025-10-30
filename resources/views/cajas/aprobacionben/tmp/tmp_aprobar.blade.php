<h4>Aprobar</h4>
<p>Esta opción es para aprobar al beneficiario y enviar los datos a Subsidio</p>

<form id='formAprobar'>
	<div class='row g-3'>
		<div class='col-md-4' group-for='giro'>
			<div class='d-flex align-items-center'>
				<label for='giro' class='form-label me-2 mb-0 flex-shrink-0'>Giro</label>
				<select name="giro" id="giro" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_giro as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
				<label id="giro-error" class="error" for="giro"></label>
			</div>
		</div>

		<div class='col-md-4' group-for='codgir'>
			<div class='d-flex align-items-center'>
				<label for='codgir' class='form-label me-2 mb-0 flex-shrink-0'>Motivo no giro</label>
				<select name="codgir" id="codgir" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_codgir as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
				<label id="codgir-error" class="error" for="codgir"></label>
			</div>
		</div>

		<div class='col-md-4' group-for='pago'>
			<div class='d-flex align-items-center'>
				<label for='pago' class='form-label me-2 mb-0 flex-shrink-0'>Pago</label>
				<select name="pago" id="pago" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_pago as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
				<label id="pago-error" class="error" for="pago"></label>
			</div>
		</div>

		<div class='col-md-4' group-for='numhij'>
			<div class='d-flex align-items-center'>
				<label for='numhij' class='form-label me-2 mb-0 flex-shrink-0'>Número hijo</label>
				<input type="number" id="numhij" name="numhij" class="form-control" placeholder="Número de hijo">
				<label id="numhij-error" class="error" for="numhij"></label>
			</div>
		</div>

		<div class='col-md-4' group-for='fecafi'>
			<div class='d-flex align-items-center'>
				<label for='fecafi' class='form-label me-2 mb-0 flex-shrink-0'>Fecha afiliación</label>
				<input type="date" name="fecafi" id="fecafi" class="form-control" placeholder="Fecha de afiliación">
			</div>
		</div>

		<div class='col-md-4' group-for='fecapr'>
			<div class='d-flex align-items-center'>
				<label for='fecapr' class='form-label me-2 mb-0 flex-shrink-0'>Fecha aprobación resolución</label>
				<input type="date" name="fecapr" id="fecapr" class="form-control" placeholder="Fecha de aprobación">
			</div>
		</div>

		<div class='col-md-4' group-for='fecpre'>
			<div class='d-flex align-items-center'>
				<label for='fecpre' class='form-label me-2 mb-0 flex-shrink-0'>Fecha presentación</label>
				<input type="date" name="fecpre" id="fecpre" class="form-control" placeholder="Fecha de presentación">
			</div>
		</div>

		<div class='col-12' group-for='nota_aprobar'>
			<div class='form-group'>
				<label for='nota_aprobar' class='form-label'>Nota</label>
				<textarea class='form-control' id='nota_aprobar' name='nota_aprobar' id='nota_aprobar' rows='3' placeholder="Ingrese una nota"></textarea>
			</div>
		</div>
	</div>
</form>

<div class="form-group pt-3">
	<button type='button' class='btn btn-success' id='aprobar_solicitud'><i class='fas fa-check'></i> Aprobar</button>
</div>
