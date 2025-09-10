<?php
echo View::getContent();
$id = $mercurio32->getId();
?>

<script id='tmp_card_header' type="text/template">
	<div id="botones" class='row justify-content-end'>
        <button type='button' data-toggle="linkto" data-href="info_conyuge/<?= $mercurio32->getCedtra() ?>/<?= $mercurio32->getCedcon() ?>/<?= $mercurio32->getid() ?>" class='btn btn-sm btn-primary'><i class=''></i> Regresar</button>&nbsp;
        <button type='button' data-toggle="linkto" data-href="index" class='btn btn-sm btn-primary'><i class='fas fa-hand-point-up text-white'></i> Salir</button>&nbsp; 
    </div>    
</script>

<div class='card-header pt-2 pb-2' id='afiliacion_header'></div>

<div class='card-body'>
	<div class="col-xs-12">
		<form method='POST' action="#" id='formulario_trabajador'>
			<?= Tag::numericField("id", "class: d-none", "value: $id"); ?>
			<div class="row">
				<div class='col-md-5'>
					<div class='form-group'>
						<label for='cedtra' class='form-control-label'>Trabajador afiliado activo</label>
						<?= Tag::numericField("cedtra", "value: " . $mercurio32->getCedtra(), "use_dummy: true", "class: form-control", "disabled: ") ?>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="tipdoc" class="form-control-label">Tipo documento conyuge</label>
						<?
						$value = ($mercurio32) ? $mercurio32->getTipdoc() : '1';
						echo Tag::selectStatic("tipdoc", $_coddoc, "use_dummy: true", "dummyValue: ", "class: form-control", "value: {$value}"); ?>
						<?php echo Tag::hiddenField("id"); ?>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="cedcon" class="form-control-label">Cédula cónyuge</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getCedcon() : '';
						if (empty($value)) {
							echo Tag::numericField("cedcon", "class: form-control", "placeholder: Cedula", "value: {$value}");
						} else {
							echo Tag::numericField("cedcon", "class: form-control", "placeholder: Cedula", "value: {$value}", "readonly: true");
						}
						?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label for="priape" class="form-control-label">Primer apellido</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getPriape() : '';
						echo Tag::textUpperField("priape", "class: form-control", "placeholder: Primer Apellido", "value: {$value}"); ?>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="segape" class="form-control-label">Segundo apellido</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getSegape() : '';
						echo Tag::textUpperField("segape", "class: form-control", "placeholder: Segundo Apellido", "value: $value"); ?>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="prinom" class="form-control-label">Primer nombre</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getPrinom() : '';
						echo Tag::textUpperField("prinom", "class: form-control", "placeholder: Primer Nombre", "value: $value"); ?>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="segnom" class="form-control-label">Segundo nombre</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getSegnom() : '';
						echo Tag::textUpperField("segnom", "class: form-control", "placeholder: Segundo Nombre", "value: $value"); ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="fecnac" class="form-control-label">Fecha nacimiento</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getFecnac()->getUsingFormatDefault() : '';
						echo TagUser::calendar("fecnac", "class: form-control", "placeholder: Fecha Nacimiento", "value: $value"); ?>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="ciunac" class="form-control-label">Ciudad nacimiento</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getCiunac() : '';
						echo Tag::selectStatic("ciunac", $_codciu, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
						<label id="ciunac-error" class="error" for="ciunac"></label>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="sexo" class="form-control-label">Sexo</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getSexo() : '';
						echo Tag::selectStatic("sexo", $_sexo, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="estciv" class="form-control-label">Estado civil</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getEstciv() : '';
						echo Tag::selectStatic("estciv", $_estciv, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="comper" class="form-control-label">Compañer@ permanente</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getComper() : '';
						echo Tag::selectStatic("comper", $_comper, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="tiecon" class="form-control-label">Tiempo convivencia (Año)</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getTiecon() : '';
						echo Tag::numericField("tiecon", "class: form-control", "placeholder: Tiempo de Convivencia", "maxlength: 3", "minlength: 1", "value: $value"); ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="ciures" class="form-control-label">Ciudad residencia</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getComper() : '';
						echo Tag::selectStatic("ciures", $_ciures, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
						<label id="ciures-error" class="error" for="ciures"></label>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="codzon" class="form-control-label">Zona</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getCodzon() : '';
						echo Tag::selectStatic("codzon", $_codzon, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
						<label id="codzon-error" class="error" for="codzon"></label>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="tipviv" class="form-control-label">Tipo vivienda</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getTipviv() : '';
						echo Tag::selectStatic("tipviv", $_vivienda, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="direccion" class="form-control-label">Dirección</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getDireccion() : '';
						echo Tag::textField("direccion", "class: form-control", "placeholder: Direccion", "value: $value"); ?>
						<label id="direccion-error" class="error" for="direccion"></label>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="telefono" class="form-control-label">Teléfono</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getTelefono() : '';
						echo Tag::numericField("telefono", "class: form-control", "placeholder: Teléfono", "maxlength: 10", "minlength: 10", "value: $value"); ?>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="celular" class="form-control-label">Celular</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getCelular() : '';
						echo Tag::numericField("celular", "class: form-control", "placeholder: Celular", "maxlength: 10", "minlength: 10", "value: $value"); ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="email" class="form-control-label">Email</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getEmail() : '';
						echo Tag::textUpperField("email", "class: form-control", "placeholder: Email", "value: $value"); ?>
						<label id="email-error" class="error" for="email"></label>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="nivedu" class="form-control-label">Nivel educación</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getNivedu() : '';
						echo Tag::selectStatic("nivedu", $_nivedu, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="fecing" class="form-control-label">Fecha ingreso</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getFecing() : '';
						echo TagUser::calendar("fecing", "class: form-control", "placeholder: Fecha Ingreso", "value: $value"); ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label for="captra" class="form-control-label">Capacidad de trabajo</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getCaptra() : '';
						echo Tag::selectStatic("captra", $_captra, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="codocu" class="form-control-label">Ocupación</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getCodocu() : '';
						echo Tag::selectStatic("codocu", $_codocu, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="salario" class="form-control-label">Salario</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getSalario() : '';
						echo Tag::numericField("salario", "class: form-control", "placeholder: Salario", "value: $value"); ?>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="tipsal" class="form-control-label">Tipo salario</label>
						<?php
						$value = ($mercurio32) ? $mercurio32->getTipsal() : '';
						echo Tag::selectStatic("tipsal", $_tipsal, "use_dummy: true", "dummyValue: ", "class: form-control", "value: $value"); ?>
					</div>
				</div>
			</div>

			<div class="card-footer text-center">
				<button class="btn btn-md btn-primary" type="button" id='guardar_ficha'>Actualizar</button>
			</div>
		</form>
	</div>
</div>

<script>
	const _ID = "<?= $id ?>";
	const _SOLIICTUD = <?= json_encode($mercurio32->getArray()) ?>;
</script>
<?= Tag::addJavascript('Cajas/src/Conyuges/editar_view'); ?>