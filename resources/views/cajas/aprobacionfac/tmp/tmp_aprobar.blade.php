<h4>Aprobar</h4>
<p>Esta opción es para aprobar la empresa y enviar los datos a Subsidio</p>

<form method="POST" action="" id='formAprobar'>
	<div class='row g-3'>
		<!-- Primera fila -->
		<div class='col-md-6 col-lg-3' group-for='tipdur'>
			<div class='d-flex align-items-center'>
				<label for='tipdur' class='form-label me-2 mb-0 flex-shrink-0'>Duración</label>
				<?= Tag::selectStatic("tipdur", $_tipdur, "use_dummy: true", "dummyValue: Seleccione duración", "class: form-select", "placeholder: Seleccione duración"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='codind'>
			<div class='d-flex align-items-center'>
				<label for='codind' class='form-label me-2 mb-0 flex-shrink-0'>Indice</label>
				<?= Tag::selectStatic("codind", $_codind, "use_dummy: true", "dummyValue: Seleccione un índice", "class: form-select", "placeholder: Seleccione un índice"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='todmes'>
			<div class='d-flex align-items-center'>
				<label for='todmes' class='form-label me-2 mb-0 flex-shrink-0'>Paga mes</label>
				<?= Tag::selectStatic("todmes", $_todmes, "use_dummy: true", "dummyValue: Seleccione opción", "class: form-select", "placeholder: Seleccione opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='forpre'>
			<div class='d-flex align-items-center'>
				<label for='forpre' class='form-label me-2 mb-0 flex-shrink-0'>Forma presentación</label>
				<?= Tag::selectStatic("forpre", $_forpre, "use_dummy: true", "dummyValue: Seleccione forma", "class: form-select", "placeholder: Seleccione forma"); ?>
			</div>
		</div>

		<!-- Segunda fila -->
		<div class='col-md-4' group-for='codsuc'>
			<div class='d-flex align-items-center'>
				<label for='codsuc' class='form-label me-2 mb-0 flex-shrink-0'>Sucursal planilla</label>
				<?= Tag::textField("codsuc", "class: form-control", "placeholder: Ingrese sucursal planilla", "maxlength: 3"); ?>
			</div>
		</div>
		<div class='col-md-4' group-for='actapr'>
			<div class='d-flex align-items-center'>
				<label for='actapr' class='form-label me-2 mb-0 flex-shrink-0'>Acta aprobación</label>
				<?= Tag::textField("actapr", "class: form-control", "placeholder: Ingrese acta de aprobación"); ?>
			</div>
		</div>

		<!-- Tercera fila -->
		<div class='col-md-4' group-for='diahab'>
			<div class='d-flex align-items-center'>
				<label for='diahab' class='form-label me-2 mb-0 flex-shrink-0'>Día hábil de pago</label>
				<?= Tag::textField("diahab", "class: form-control", "type: number", "placeholder: Ej: 15"); ?>
			</div>
		</div>


		<div class='col-md-4' group-for='tippag'>
			<div class='d-flex align-items-center'>
				<label for='tippag' class='form-label me-2 mb-0 flex-shrink-0'>Tipo pago</label>
				<?= Tag::selectStatic("tippag", $_tippag, "use_dummy: true", "dummyValue: Seleccione tipo de pago", "class: form-select", "placeholder: Seleccione tipo de pago"); ?>
			</div>
		</div>

		<!-- Cuarta fila -->
		<div class='col-md-4' group-for='codban'>
			<div class='d-flex align-items-center'>
				<label for='codban' class='form-label me-2 mb-0 flex-shrink-0'>Banco</label>
				<?= Tag::selectStatic("codban", $_bancos, "use_dummy: true", "dummyValue: Seleccione banco", "class: form-select", "value:", "placeholder: Seleccione banco"); ?>
			</div>
		</div>
		<div class='col-md-4' group-for='numcue'>
			<div class='d-flex align-items-center'>
				<label for='numcue' class='form-label me-2 mb-0 flex-shrink-0'>Número cuenta</label>
				<?= Tag::textField("numcue", "class: form-control", "placeholder: Ingrese número de cuenta"); ?>
			</div>
			<label id="numcue-error" class="error" for="numcue"></label>
		</div>
		<div class='col-md-4' group-for='tipcue'>
			<div class='d-flex align-items-center'>
				<label for='tipcue' class='form-label me-2 mb-0 flex-shrink-0'>Tipo cuenta</label>
				<?= Tag::selectStatic("tipcue", $_tipcue, "use_dummy: true", "dummyValue: Seleccione tipo de cuenta", "class: form-select", "placeholder: Seleccione tipo de cuenta"); ?>
			</div>
		</div>

		<!-- Quinta fila -->
		<div class='col-md-4' group-for='giro'>
			<div class='d-flex align-items-center'>
				<label for='giro' class='form-label me-2 mb-0 flex-shrink-0'>Giro</label>
				<?= Tag::selectStatic("giro", $_giro, "use_dummy: true", "class: form-select", "placeholder: Seleccione giro"); ?>
			</div>
		</div>
		<div class='col-md-4' group-for='codgir'>
			<div class='d-flex align-items-center'>
				<label for='codgir' class='form-label me-2 mb-0 flex-shrink-0'>Motivo no giro</label>
				<?= Tag::selectStatic("codgir", $_codgir, "use_dummy: true", "class: form-select", "placeholder: Seleccione motivo"); ?>
			</div>
		</div>

		<div class='col-md-4' group-for='fecapr'>
			<div class='d-flex align-items-center'>
				<label for='fecapr' class='form-label me-2 mb-0 flex-shrink-0'>Fecha resolución</label>
				<?= TagUser::calendar("fecapr", "class: form-control", "placeholder: dd/mm/aaaa"); ?>
			</div>
		</div>

		<div class='col-md-4' group-for='fecafi'>
			<div class='d-flex align-items-center'>
				<label for='fecafi' class='form-label me-2 mb-0 flex-shrink-0'>Fecha afiliación</label>
				<?= TagUser::calendar("fecafi", "class: form-control", "placeholder: dd/mm/aaaa"); ?>
			</div>
		</div>

		<!-- Sexta fila -->
		<div class='col-12' group-for='nota_aprobar'>
			<div class='form-group'>
				<label for='nota_aprobar' class='form-label'>Nota</label>
				<textarea class='form-control summer_content' id='nota_aprobar' name='nota_aprobar' rows='3' placeholder='Ingrese una nota para la notificación por email'></textarea>
			</div>
		</div>
	</div>
</form>

<div class='form-group pt-3'>
	<button type='button' class='btn btn-success' id='aprobar_solicitud'><i class='fas fa-check'></i> Aprobar</button>
</div>