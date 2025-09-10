<h4>Aprobar</h4>
<p>Esta opción es para aprobar la empresa y enviar los datos a Subsidio</p>

<form id='formAprobar'>
	<div class='row g-3'>
		<div class='col-md-6 col-lg-3' group-for='tipdur'>
			<div class='d-flex align-items-center'>
				<label for='tipdur' class='form-label me-2 mb-0 flex-shrink-0'>Duración</label>
				<?= Tag::selectStatic("tipdur", $_tipdur, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='codind'>
			<div class='d-flex align-items-center'>
				<label for='codind' class='form-label me-2 mb-0 flex-shrink-0'>Indice</label>
				<?= Tag::selectStatic("codind", $_codind, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='todmes'>
			<div class='d-flex align-items-center'>
				<label for='todmes' class='form-label me-2 mb-0 flex-shrink-0'>Paga mes</label>
				<?= Tag::selectStatic("todmes", $_todmes, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='forpre'>
			<div class='d-flex align-items-center'>
				<label for='forpre' class='form-label me-2 mb-0 flex-shrink-0'>Forma presentación</label>
				<?= Tag::selectStatic("forpre", $_forpre, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='pymes'>
			<div class='d-flex align-items-center'>
				<label for='pymes' class='form-label me-2 mb-0 flex-shrink-0'>Pyme</label>
				<?= Tag::selectStatic("pymes", $_pymes, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='contratista'>
			<div class='d-flex align-items-center'>
				<label for='contratista' class='form-label me-2 mb-0 flex-shrink-0'>Contratista</label>
				<?= Tag::selectStatic("contratista", $_contratista, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='tipemp'>
			<div class='d-flex align-items-center'>
				<label for='tipemp' class='form-label me-2 mb-0 flex-shrink-0'>Tipo empresa</label>
				<?= Tag::selectStatic("tipemp", $_tipemp, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='tipapo'>
			<div class='d-flex align-items-center'>
				<label for='tipapo' class='form-label me-2 mb-0 flex-shrink-0'>Tipo aportante</label>
				<?= Tag::selectStatic("tipapo", $_tipapo, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-4' group-for='tipsoc'>
			<div class='d-flex align-items-center'>
				<label for='tipsoc' class='form-label me-2 mb-0 flex-shrink-0'>Tipo sociedad</label>
				<?= Tag::selectStatic("tipsoc", $_tipsoc, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-4' group-for='ofiafi'>
			<div class='d-flex align-items-center'>
				<label for='ofiafi' class='form-label me-2 mb-0 flex-shrink-0'>Oficina</label>
				<?= Tag::selectStatic("ofiafi", $_ofiafi, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-6 col-lg-4' group-for='colegio'>
			<div class='d-flex align-items-center'>
				<label for='colegio' class='form-label me-2 mb-0 flex-shrink-0'>Colegio</label>
				<?= Tag::selectStatic("colegio", $_colegio, "use_dummy: true", "dummyValue: ", "class: form-select", "placeholder: Selecciona una opción"); ?>
			</div>
		</div>
		<div class='col-md-4' group-for='codsuc'>
			<div class='d-flex align-items-center'>
				<label for='codsuc' class='form-label me-2 mb-0 flex-shrink-0'>Sucursal planilla</label>
				<?= Tag::textField("codsuc", "placeholder: Sucursal", "class: form-control"); ?>
			</div>
		</div>
		<div class='col-md-4' group-for='actapr'>
			<div class='d-flex align-items-center'>
				<label for='actapr' class='form-label me-2 mb-0 flex-shrink-0'>Acta aprobación</label>
				<?= Tag::textField("actapr", "placeholder: Acta de aprobación", "class: form-control"); ?>
			</div>
		</div>
		<div class='col-md-4' group-for='diahab'>
			<div class='d-flex align-items-center'>
				<label for='diahab' class='form-label me-2 mb-0 flex-shrink-0'>Día habil de Pago</label>
				<?= Tag::textField("diahab", "placeholder: Días habiles", "class: form-control"); ?>
			</div>
		</div>
		<div class='col-md-3' group-for='fecafi'>
			<div class='d-flex align-items-center'>
				<label for='fecafi' class='form-label me-2 mb-0 flex-shrink-0'>Fecha afiliación</label>
				<?= TagUser::calendar("fecafi", "placeholder: Fecha de afiliación", "class: form-control"); ?>
			</div>
		</div>
		<div class='col-md-4' group-for='fecapr'>
			<div class='d-flex align-items-center'>
				<label for='fecapr' class='form-label me-2 mb-0 flex-shrink-0'>Fecha aprobación resolución</label>
				<?= TagUser::calendar("fecapr", "placeholder: Fecha de aprobación", "class: form-control"); ?>
			</div>
		</div>
		<div class='col-12' group-for='nota_aprobar'>
			<div class='form-group'>
				<label for='nota_aprobar' class='form-label'>Nota</label>
				<textarea class='form-control' id='nota_aprobar' name='nota_aprobar' rows='3' placeholder="Please provide a note"></textarea>
			</div>
		</div>
	</div>
</form>

<div class="form-group pt-3">
	<button type='button' class='btn btn-success' id='aprobar_solicitud'><i class='fas fa-check'></i> Aprobar</button>
</div>
