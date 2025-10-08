<h4>Aprobar</h4>
<p>Esta opción es para aprobar la empresa y enviar los datos a Subsidio</p>

<form method="POST" action="" id='formAprobar'>
	<div class='row g-3'>
		<!-- Primera fila -->
		<div class='col-md-6 col-lg-3' group-for='tipdur'>
			<div class='d-flex align-items-center'>
				<label for='tipdur' class='form-label me-2 mb-0 flex-shrink-0'>Duración</label>
				<select name="tipdur" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_tipdur as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='codind'>
			<div class='d-flex align-items-center'>
				<label for='codind' class='form-label me-2 mb-0 flex-shrink-0'>Indice</label>
				<select name="codind" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_codind as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='todmes'>
			<div class='d-flex align-items-center'>
				<label for='todmes' class='form-label me-2 mb-0 flex-shrink-0'>Paga mes</label>
				<select name="todmes" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_todmes as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='forpre'>
			<div class='d-flex align-items-center'>
				<label for='forpre' class='form-label me-2 mb-0 flex-shrink-0'>Forma presentación</label>
				<select name="forpre" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_forpre as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>

		<!-- Segunda fila -->
		<div class='col-md-4' group-for='codsuc'>
			<div class='d-flex align-items-center'>
				<label for='codsuc' class='form-label me-2 mb-0 flex-shrink-0'>Sucursal planilla</label>
				<input type="text" name="codsuc" class="form-control" placeholder="Ingrese sucursal planilla" maxlength="3">
			</div>
		</div>
		<div class='col-md-4' group-for='actapr'>
			<div class='d-flex align-items-center'>
				<label for='actapr' class='form-label me-2 mb-0 flex-shrink-0'>Acta aprobación</label>
				<input type="text" name="actapr" class="form-control" placeholder="Ingrese acta de aprobación">
			</div>
		</div>

		<!-- Tercera fila -->
		<div class='col-md-4' group-for='diahab'>
			<div class='d-flex align-items-center'>
				<label for='diahab' class='form-label me-2 mb-0 flex-shrink-0'>Día hábil de pago</label>
				<input type="number" name="diahab" class="form-control" placeholder="Ej: 15">
			</div>
		</div>


		<div class='col-md-4' group-for='tippag'>
			<div class='d-flex align-items-center'>
				<label for='tippag' class='form-label me-2 mb-0 flex-shrink-0'>Tipo pago</label>
				<select name="tippag" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_tippag as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>

		<!-- Cuarta fila -->
		<div class='col-md-4' group-for='codban'>
			<div class='d-flex align-items-center'>
				<label for='codban' class='form-label me-2 mb-0 flex-shrink-0'>Banco</label>
				<select name="codban" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_bancos as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-4' group-for='numcue'>
			<div class='d-flex align-items-center'>
				<label for='numcue' class='form-label me-2 mb-0 flex-shrink-0'>Número cuenta</label>
				<input type="text" name="numcue" class="form-control" placeholder="Ingrese número de cuenta">
			</div>
			<label id="numcue-error" class="error" for="numcue"></label>
		</div>
		<div class='col-md-4' group-for='tipcue'>
			<div class='d-flex align-items-center'>
				<label for='tipcue' class='form-label me-2 mb-0 flex-shrink-0'>Tipo cuenta</label>
				<select name="tipcue" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_tipcue as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>

		<!-- Quinta fila -->
		<div class='col-md-4' group-for='giro'>
			<div class='d-flex align-items-center'>
				<label for='giro' class='form-label me-2 mb-0 flex-shrink-0'>Giro</label>
				<select name="giro" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_giro as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-4' group-for='codgir'>
			<div class='d-flex align-items-center'>
				<label for='codgir' class='form-label me-2 mb-0 flex-shrink-0'>Motivo no giro</label>
				<select name="codgir" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_codgir as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>

		<div class='col-md-4' group-for='fecapr'>
			<div class='d-flex align-items-center'>
				<label for='fecapr' class='form-label me-2 mb-0 flex-shrink-0'>Fecha resolución</label>
				<input type="date" name="fecapr" class="form-control" placeholder="dd/mm/aaaa">
			</div>
		</div>

		<div class='col-md-4' group-for='fecafi'>
			<div class='d-flex align-items-center'>
				<label for='fecafi' class='form-label me-2 mb-0 flex-shrink-0'>Fecha afiliación</label>
				<input type="date" name="fecafi" class="form-control" placeholder="dd/mm/aaaa">
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
