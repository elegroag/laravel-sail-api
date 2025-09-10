<h4>Aprobar</h4>
<p>Esta opción es para aprobar al trabajador y enviar los datos a Subsidio</p>

<form id='formAprobar'>
	<div class='row g-3'>
		<div class='col-md-4' group-for='codsuc'>
			<div class='d-flex align-items-center'>
				<label for='codsuc' class='form-label me-2 mb-0 flex-shrink-0'>Sucursal</label>
				<div id='render_codsuc' class='w-100'>
					<%=$scope.componente_codsuc %>
				</div>
			</div>
		</div>

		<div class='col-md-4' group-for='codlis'>
			<div class='d-flex align-items-center'>
				<label for='codlis' class='form-label me-2 mb-0 flex-shrink-0'>Lista</label>
				<div class='w-100'>
					<%=$scope.componente_codlis %>
				</div>
			</div>
		</div>

		<div class='col-md-4' group-for='vendedor'>
			<div class='d-flex align-items-center'>
				<label for='vendedor' class='form-label me-2 mb-0 flex-shrink-0'>Vendedor</label>
				<?= Tag::selectStatic("vendedor", $_vendedor, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>

		<div class='col-md-4' group-for='empleador'>
			<div class='d-flex align-items-center'>
				<label for='empleador' class='form-label me-2 mb-0 flex-shrink-0'>Empleador</label>
				<?= Tag::selectStatic("empleador", $_empleador, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>

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
				<?= Tag::numericField("numcue", "class: form-control", "type: number", "placeholder: Número de cuenta"); ?>
				<label id="numcue-error" class="error" for="numcue"></label>
			</div>
		</div>

		<div class='col-md-4' group-for='tipcue'>
			<div class='d-flex align-items-center'>
				<label for='tipcue' class='form-label me-2 mb-0 flex-shrink-0'>Tipo cuenta</label>
				<?= Tag::selectStatic("tipcue", $_tipcue, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>

		<div class='col-md-3' group-for='giro'>
			<div class='d-flex align-items-center'>
				<label for='giro' class='form-label me-2 mb-0 flex-shrink-0'>Giro</label>
				<?= Tag::selectStatic("giro", $_giro, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>

		<div class='col-md-4' group-for='codgir'>
			<div class='d-flex align-items-center'>
				<label for='codgir' class='form-label me-2 mb-0 flex-shrink-0'>Motivo no giro</label>
				<?= Tag::selectStatic("codgir", $_codgir, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
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