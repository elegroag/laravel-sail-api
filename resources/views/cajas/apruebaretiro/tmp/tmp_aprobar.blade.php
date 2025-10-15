<h4>Aprobar</h4>
<p>Esta opción es para aprobar al trabajador y enviar los datos a Subsidio</p>

<form id='formAprobar'>
	<div class='row g-3'>

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
