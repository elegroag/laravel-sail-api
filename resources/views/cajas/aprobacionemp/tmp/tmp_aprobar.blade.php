
<h4>Aprobar</h4>
<p>Esta opción es para aprobar la empresa y enviar los datos a Subsidio</p>

<form id='formAprobar' class='validation_form' autocomplete='off' novalidate>
	<div class='row g-3'>
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
		<div class='col-md-6 col-lg-3' group-for='pymes'>
			<div class='d-flex align-items-center'>
				<label for='pymes' class='form-label me-2 mb-0 flex-shrink-0'>Pyme</label>
				<select name="pymes" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_pymes as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='contratista'>
			<div class='d-flex align-items-center'>
				<label for='contratista' class='form-label me-2 mb-0 flex-shrink-0'>Contratista</label>
				<select name="contratista" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_contratista as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='tipemp'>
			<div class='d-flex align-items-center'>
				<label for='tipemp' class='form-label me-2 mb-0 flex-shrink-0'>Tipo empresa</label>
				<select name="tipemp" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_tipemp as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-6 col-lg-3' group-for='tipapo'>
			<div class='d-flex align-items-center'>
				<label for='tipapo' class='form-label me-2 mb-0 flex-shrink-0'>Tipo aportante</label>
				<select name="tipapo" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_tipapo as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-6 col-lg-4' group-for='tipsoc'>
			<div class='d-flex align-items-center'>
				<label for='tipsoc' class='form-label me-2 mb-0 flex-shrink-0'>Tipo sociedad</label>
				<select name="tipsoc" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_tipsoc as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-6 col-lg-4' group-for='ofiafi'>
			<div class='d-flex align-items-center'>
				<label for='ofiafi' class='form-label me-2 mb-0 flex-shrink-0'>Oficina</label>
				<select name="ofiafi" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_ofiafi as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-6 col-lg-4' group-for='colegio'>
			<div class='d-flex align-items-center'>
				<label for='colegio' class='form-label me-2 mb-0 flex-shrink-0'>Colegio</label>
				<select name="colegio" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_colegio as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</div>
		</div>
		<div class='col-md-4' group-for='codsuc'>
			<div class='d-flex align-items-center'>
				<label for='codsuc' class='form-label me-2 mb-0 flex-shrink-0'>Sucursal planilla</label>
				<input type="text" name="codsuc" placeholder="Sucursal" class="form-control">
			</div>
		</div>
		<div class='col-md-4' group-for='actapr'>
			<div class='d-flex align-items-center'>
				<label for='actapr' class='form-label me-2 mb-0 flex-shrink-0'>Acta aprobación</label>
				<input type="text" name="actapr" placeholder="Acta de aprobación" class="form-control">
			</div>
		</div>
		<div class='col-md-4' group-for='diahab'>
			<div class='d-flex align-items-center'>
				<label for='diahab' class='form-label me-2 mb-0 flex-shrink-0'>Día habil de Pago</label>
				<input type="number" name="diahab" placeholder="Días habiles" class="form-control">
			</div>
		</div>
		<div class='col-md-3' group-for='fecafi'>
			<div class='d-flex align-items-center'>
				<label for='fecafi' class='form-label me-2 mb-0 flex-shrink-0'>Fecha afiliación</label>
				<input type="date" name="fecafi" placeholder="Fecha de afiliación" class="form-control">
			</div>
		</div>
		<div class='col-md-4' group-for='fecapr'>
			<div class='d-flex align-items-center'>
				<label for='fecapr' class='form-label me-2 mb-0 flex-shrink-0'>Fecha aprobación resolución</label>
				<input type="date" name="fecapr" placeholder="Fecha de aprobación" class="form-control">
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
