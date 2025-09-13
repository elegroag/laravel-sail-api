@php
use App\Services\Tag;
@endphp

@php echo Tag::form("#", "id: formulario_beneficiario", "class: validation_form", "autocomplete: off", "novalidate"); @endphp
@php echo Tag::numericField("id", "class: d-none", "placeholder: id", "value: {$mercurio34->getId()}"); @endphp
<div class="row">
	<div class="col-md-2">
		<div class="form-group">
			<label for="parent" class="form-control-label">Parentesco beneficiario</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getParent() : '';
			if (!empty($value)) {
				echo Tag::selectStatic("parent", $_parent, "class: form-control", "value: $value", "disabled: ");
			} else {
				echo Tag::selectStatic("parent", $_parent, "use_dummy: true", "dummyValue: ", "class: form-control");
			}
		@endphp
		</div>
	</div>
	<div class='col-md-5'>
		<div class='form-group'>
			<label for='cedtra' class='form-control-label'>Trabajador afiliado activo</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getCedtra() : '';
			if (!empty($value)) {
				if (isset($_cedtra[$value])) {
					$_cedtra = array($value => $_cedtra[$value]);
				} else {
					$_cedtra = array($value => $value);
				}
				echo Tag::selectStatic("cedtra", $_cedtra, "class: form-control", "value: {$value}", "disabled: ");
			} else {
				echo Tag::selectStatic("cedtra", $_cedtra, "use_dummy: true", "dummyValue: ", "class: form-control");
			}
		@endphp
			<label id="cedtra-error" class="error" for="cedtra"></label>
		</div>
	</div>
	<div class="col-md-5">
		<div class="form-group">
			<label for="cedcon" class="form-control-label">Cónyuge trabajador</label>
			<span id='td_conyuge'>
				@php
				$value = ($mercurio34) ? $mercurio34->getCedcon() : '';
				if (!empty($value)) {

					$_cedcon = array("$value" => $value);
					echo Tag::selectStatic("cedcon", $_cedcon, "class: form-control", "value: {$value}", "disabled: ");
				} else {
					$_cedcon = array('' => 'Pendiente de seleccionar trabajador...');
					echo Tag::selectStatic("cedcon", $_cedcon, "class: form-control");
				}
			@endphp
			</span>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="tipdoc" class="form-control-label">Tipo Documento Beneficiario</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getTipdoc() : '';
			echo Tag::selectStatic("tipdoc", $_coddoc, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value");
		@endphp
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="numdoc" class="form-control-label">Documento del Beneficiario</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getNumdoc() : '';
			echo Tag::numericField("numdoc", "class: form-control", "placeholder: Documento", "value: $value");
		@endphp
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="form-group">
			<label for="priape" class="form-control-label">Primer Apellido</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getPriape() : '';
			Tag::textUpperField("priape", "class: form-control", "placeholder: Primer Apellido", "value: $value");
		@endphp
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<label for="segape" class="form-control-label">Segundo Apellido</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getSegape() : '';
			echo Tag::textUpperField("segape", "class: form-control", "placeholder: Segundo Apellido", "value: {$value}");
		@endphp
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<label for="prinom" class="form-control-label">Primer Nombre</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getPrinom() : '';
			echo Tag::textUpperField("prinom", "class: form-control", "placeholder: Primer Nombre", "value: {$value}");
		@endphp
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<label for="segnom" class="form-control-label">Segundo Nombre</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getSegnom() : '';
			echo Tag::textUpperField("segnom", "class: form-control", "placeholder: Segundo Nombre", "value: {$value}");
		@endphp
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label for="fecnac" class="form-control-label">Fecha Nacimiento</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getFecnac() : '';
			echo Tag::calendar("fecnac", "class: form-control", "placeholder: Fecha Nacimiento", "value: {$value}");
		@endphp
		</div>
	</div>
	<div class="col-md-8">
		<div class="form-group">
			<label for="ciunac" class="form-control-label">Ciudad Nacimiento</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getCiunac() : '';
			echo Tag::selectStatic("ciunac", $_ciunac, "use_dummy: true", "dummyValue: ", "class: form-control", "select2: true", "value: {$value}");
		@endphp
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label for="sexo" class="form-control-label">Sexo</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getSexo() : '';
			echo Tag::selectStatic("sexo", $_sexo, "use_dummy: true", "dummyValue: ", "class: form-control", "value: {$value}");
		@endphp
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="cedacu" class="form-control-label">Acudiente convive</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getCedacu() : '';
			if (!empty($value)) {
				$value = ($mercurio34->getCedacu() == $mercurio34->getCedtra()) ? 2 : $value;
				$value = ($mercurio34->getCedacu() == $mercurio34->getCedcon()) ? 1 : $value;
				$value = ($mercurio34->getCedacu() == '') ? 3 : $value;
				if (empty($value)) {
					$value = 4;
				}
			}
			echo Tag::selectStatic("convive", array(
				"1" => "Conyuge",
				"2" => "Trabajador",
				"3" => "No aplica",
				"4" => "Otras personas",
			), "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value");
		@endphp
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="cedacu" class="form-control-label">Cedula padre/madre</label>
			<div id="tb_cedacu">
				@php
				$value = ($mercurio34) ? $mercurio34->getCedacu() : '';
				echo Tag::numericField("cedacu", "class: form-control", "placeholder: Pendiente definir acudiente convive", "readonly: ", "value: {$value}");
			@endphp
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label for="huerfano" class="form-control-label">Huerfano</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getPriape() : '';
			echo Tag::selectStatic("huerfano", $_huerfano, "use_dummy: true", "dummyValue: ", "class: form-control", "value: {$value}");
		@endphp
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="tiphij" class="form-control-label">Tipo Hijo</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getTiphij() : '';
			echo Tag::selectStatic("tiphij", $_tiphij, "use_dummy: true", "dummyValue: ", "class: form-control", "value: {$value}");
		@endphp
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="nivedu" class="form-control-label">Nivel Educacion</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getNivedu() : '';
			echo Tag::selectStatic("nivedu", $_nivedu, "use_dummy: true", "dummyValue: ", "class: form-control", "value: {$value}");
		@endphp
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label for="captra" class="form-control-label">Capacidad de Trabajo</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getCaptra() : '';
			echo Tag::selectStatic("captra", $_captra, "use_dummy: true", "dummyValue: ", "class: form-control", "value: {$value}");
		@endphp
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="tipdis" class="form-control-label">Tipo Discapacidad</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getTipdis() : '';
			echo Tag::selectStatic("tipdis", $_tipdis, "use_dummy: true", "dummyValue: ", "class: form-control", "value: {$value}");
		@endphp
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="calendario" class="form-control-label">Calendario</label>
			@php
			$value = ($mercurio34) ? $mercurio34->getCalendario() : '';
			echo Tag::selectStatic("calendario", $_calendario, "use_dummy: true", "dummyValue: ", "class: form-control", "value: {$value}");
		@endphp
		</div>
	</div>
</div>
<div class="card-footer text-center">
	@if ($mercurio34->getEstado() != 'A')
		<button class="btn btn-md btn-primary" type="button" id='guardar_ficha'>Actualizar</button>
	@endif
</div>
@php echo Tag::endform() @endphp
