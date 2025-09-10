<h4>Aprobar</h4>
<p>Esta opción es para aprobar al beneficiario y enviar los datos a Subsidio</p>

<form id='formAprobar'>
	<div class='row g-3'>
		<div class='col-md-4' group-for='giro'>
			<div class='d-flex align-items-center'>
				<label for='giro' class='form-label me-2 mb-0 flex-shrink-0'>Giro</label>
				<?= Tag::selectStatic("giro", $_giro, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
				<label id="giro-error" class="error" for="giro"></label>
			</div>
		</div>

		<div class='col-md-4' group-for='codgir'>
			<div class='d-flex align-items-center'>
				<label for='codgir' class='form-label me-2 mb-0 flex-shrink-0'>Motivo no giro</label>
				<?= Tag::selectStatic("codgir", $_codgir, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
				<label id="codgir-error" class="error" for="codgir"></label>
			</div>
		</div>

		<div class='col-md-4' group-for='pago'>
			<div class='d-flex align-items-center'>
				<label for='pago' class='form-label me-2 mb-0 flex-shrink-0'>Pago</label>
				<?= Tag::selectStatic("pago", $_pago, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
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
				<?= TagUser::calendar("fecafi", "class: form-control", "placeholder: Fecha de afiliación"); ?>
			</div>
		</div>

		<div class='col-md-4' group-for='fecapr'>
			<div class='d-flex align-items-center'>
				<label for='fecapr' class='form-label me-2 mb-0 flex-shrink-0'>Fecha aprobación resolución</label>
				<?= TagUser::calendar("fecapr", "class: form-control", "placeholder: Fecha de aprobación"); ?>
			</div>
		</div>

		<div class='col-md-4' group-for='fecapr'>
			<div class='d-flex align-items-center'>
				<label for='fecpre' class='form-label me-2 mb-0 flex-shrink-0'>Fecha presentación</label>
				<?= TagUser::calendar("fecpre", "class: form-control", "placeholder: Fecha de aprobación"); ?>
			</div>
		</div>

		<div class='col-12' group-for='nota_aprobar'>
			<div class='form-group'>
				<label for='nota_aprobar' class='form-label'>Nota</label>
				<textarea class='form-control' id='nota_aprobar' name='nota_aprobar' rows='3' placeholder="Ingrese una nota"></textarea>
			</div>
		</div>
	</div>
</form>

<div class="form-group pt-3">
	<button type='button' class='btn btn-success' id='aprobar_solicitud'><i class='fas fa-check'></i> Aprobar</button>
</div>