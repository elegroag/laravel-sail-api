<form id="formulario_beneficiario" class="validation_form" autocomplete="off" novalidate>
<input type="hidden" name="id" value="{{ $mercurio34->getId() }}">
<div class="row">
	<div class="col-md-2">
		<div class="form-group">
			<label for="parent" class="form-control-label">Parentesco beneficiario</label>
			<select name="parent" class="form-control">
				<option value="">Seleccione un tipo de documento</option>
				@foreach($_parent as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class='col-md-5'>
		<div class='form-group'>
			<label for='cedtra' class='form-control-label'>Trabajador afiliado activo</label>
			<select name="cedtra" class="form-control">
				<option value="">Seleccione un tipo de documento</option>
				@foreach($_cedtra as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
			<label id="cedtra-error" class="error" for="cedtra"></label>
		</div>
	</div>
	<div class="col-md-5">
		<div class="form-group">
			<label for="cedcon" class="form-control-label">Cónyuge trabajador</label>
			<span id='td_conyuge'>
				<select name="cedcon" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_cedcon as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
			</span>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="tipdoc" class="form-control-label">Tipo Documento Beneficiario</label>
			<select name="tipdoc" class="form-control">
				<option value="">Seleccione un tipo de documento</option>
				@foreach($_coddoc as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="numdoc" class="form-control-label">Documento del Beneficiario</label>
			<input type="text" name="numdoc" class="form-control" placeholder="Documento" value="{{ $mercurio34->getNumdoc() }}">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="form-group">
			<label for="priape" class="form-control-label">Primer Apellido</label>
			<input type="text" name="priape" class="form-control" placeholder="Primer Apellido" value="{{ $mercurio34->getPriape() }}">
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<label for="segape" class="form-control-label">Segundo Apellido</label>
			<input type="text" name="segape" class="form-control" placeholder="Segundo Apellido" value="{{ $mercurio34->getSegape() }}">
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<label for="prinom" class="form-control-label">Primer Nombre</label>
			<input type="text" name="prinom" class="form-control" placeholder="Primer Nombre" value="{{ $mercurio34->getPrinom() }}">
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<label for="segnom" class="form-control-label">Segundo Nombre</label>
			<input type="text" name="segnom" class="form-control" placeholder="Segundo Nombre" value="{{ $mercurio34->getSegnom() }}">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label for="fecnac" class="form-control-label">Fecha Nacimiento</label>
			<input type="text" name="fecnac" class="form-control" placeholder="Fecha Nacimiento" value="{{ $mercurio34->getFecnac() }}">
		</div>
	</div>
	<div class="col-md-8">
		<div class="form-group">
			<label for="ciunac" class="form-control-label">Ciudad Nacimiento</label>
			<select name="ciunac" class="form-control">
				<option value="">Seleccione una ciudad</option>
				@foreach($_ciunac as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label for="sexo" class="form-control-label">Sexo</label>
			<select name="sexo" class="form-control">
				<option value="">Seleccione un sexo</option>
				@foreach($_sexo as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="cedacu" class="form-control-label">Acudiente convive</label>
			<select name="cedacu" class="form-control">
				<option value="">Seleccione un tipo de documento</option>
				@foreach($_cedacu as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="cedacu" class="form-control-label">Cedula padre/madre</label>
			<div id="tb_cedacu">
				<input type="text" name="cedacu" class="form-control" placeholder="Cedula padre/madre" value="{{ $mercurio34->getCedacu() }}">
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label for="huerfano" class="form-control-label">Huerfano</label>
			<select name="huerfano" class="form-control">
				<option value="">Seleccione un tipo de documento</option>
				@foreach($_huerfano as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="tiphij" class="form-control-label">Tipo Hijo</label>
			<select name="tiphij" class="form-control">
				<option value="">Seleccione un tipo de documento</option>
				@foreach($_tiphij as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="nivedu" class="form-control-label">Nivel Educacion</label>
			<select name="nivedu" class="form-control">
				<option value="">Seleccione un tipo de documento</option>
				@foreach($_nivedu as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<label for="captra" class="form-control-label">Capacidad de Trabajo</label>
			<select name="captra" class="form-control">
				<option value="">Seleccione un tipo de documento</option>
				@foreach($_captra as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="tipdis" class="form-control-label">Tipo Discapacidad</label>
			<select name="tipdis" class="form-control">
				<option value="">Seleccione un tipo de documento</option>
				@foreach($_tipdis as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<label for="calendario" class="form-control-label">Calendario</label>
			<select name="calendario" class="form-control">
				<option value="">Seleccione un calendario</option>
				@foreach($_calendario as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="card-footer text-center">
	@if ($mercurio34->getEstado() != 'A')
		<button class="btn btn-md btn-primary" type="button" id='guardar_ficha'>Actualizar</button>
	@endif
</div>
</form>
