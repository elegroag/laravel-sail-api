<h4>Aprobar</h4>
<p>Esta opción es para aprobar cónyuge y enviar los datos a Subsidio</p>

<form id='formAprobar'>
	<div class='row g-3'>
		<div class='col-md-4' group-for='tippag'>
			<div class='d-flex align-items-center'>
				<label for='tippag' class='form-label me-2 mb-0 flex-shrink-0'>Tipo pago cuota:</label>
				<?= Tag::selectStatic("tippag", $_tippag, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>

		<div class='col-md-4' group-for='codban'>
			<div class='d-flex align-items-center'>
				<label for='codban' class='form-label me-2 mb-0 flex-shrink-0'>Banco</label>
				<?= Tag::selectStatic("codban", $_bancos, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>

		<div class='col-md-4' group-for='numcue'>
			<div class='d-flex align-items-center'>
				<label for='numcue' class='form-label me-2 mb-0 flex-shrink-0'>Número cuenta</label>
				<?= Tag::textField("numcue", "class: form-control", "placeholder: Número de cuenta"); ?>
				<label id="numcue-error" class="error" for="numcue"></label>
			</div>
		</div>

		<div class='col-md-4' group-for='tipcue'>
			<div class='d-flex align-items-center'>
				<label for='tipcue' class='form-label me-2 mb-0 flex-shrink-0'>Tipo cuenta</label>
				<?= Tag::selectStatic("tipcue", $_tipcue, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>

		<div class='col-md-4' group-for='recsub'>
			<div class='d-flex align-items-center'>
				<label for='recsub' class='form-label me-2 mb-0 flex-shrink-0'>Recibe Subsidio en otra caja?</label>
				<?= Tag::selectStatic("recsub", $_recsub, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
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