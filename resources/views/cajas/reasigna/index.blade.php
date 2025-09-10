<?php
echo View::getContent();
Tag::addJavascript('core/global');

echo TagUser::help($title, $help);
$accion = array('C' => 'CONSULTA', 'P' => 'PROCESO');
?>

<?= View::renderView('reasigna/tmp/tmp_modal_info') ?>

<div class="card mb-0">
	<div class="card-body">
		<div class="row">
			<div class="col-md-6 center">
				<div class="form-group">
					<label for="accion" class="form-control-label">Accion a Realizar</label>
					<?php echo Tag::selectStatic("accion", $accion, "use_dummy: true", "dummyValue: ", "class: form-control", "onchange: cambiarAccion()"); ?>
				</div>
			</div>
		</div>
		<?php echo Tag::form("", "id: form_proceso", "class: validation_form", "autocomplete: off", "novalidate"); ?>
		<div id='procesar_form'>
			<div class="row justify-content-around">
				<div class='col-12'>
					<div class="alert alert-success m-3" role="alert" style='padding:3px'>
						<h4 class="text-white" style='padding:0px'>Proceso de Reasignación</h4>
						<p style='font-size: 14px;'>Esta opción se encarga de reasignar todas las solicitudes
							en estado<strong> PENDIENTE</strong> cuya fecha de solicitud se encuentre en el intervalo de fechas
							escogido, la reasignacion se realiza del usuario origen al usuario destino .</p>
					</div>
				</div>
				<div class="col-md-4 ml-auto">
					<div class="form-group">
						<label for="tipopc" class="form-control-label">Opción</label>
						<?php echo Tag::select("tipopc_proceso", $Mercurio09->find(), "using: tipopc,detalle", "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="usuori" class="form-control-label">Usuario Origen</label>
						<?php echo Tag::select("usuori", $Gener02->find("usuario in (select usuario from mercurio08)"), "using: usuario,nombre", "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="usudes" class="form-control-label">Usuario Destino</label>
						<?php echo Tag::select("usudes", $Gener02->find("usuario in (select usuario from mercurio08)"), "using: usuario,nombre", "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
					</div>
				</div>
			</div>
			<div class='row'>
				<div class="col-md-2">
					<div class="form-group">
						<label for="fecini" class="form-control-label">Fecha Inicio</label>
						<?php echo TagUser::calendar("fecini", "placeholder: Fecha Inicio", "class: form-control"); ?>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label for="fecfin" class="form-control-label">Fecha Final</label>
						<?php echo TagUser::calendar("fecfin", "placeholder: Fecha Final", "class: form-control"); ?>
					</div>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-3">
					<button type="button" class="btn btn-primary align-self-center" id='btnProcesoReasignarMasivo'>Procesar</button>
				</div>
			</div>
		</div>
		<?php echo Tag::endform(); ?>
		<?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
		<div class="row" id='consultar_form'>
			<div class="col-md-4 ml-auto">
				<div class="form-group">
					<label for="tipopc" class="form-control-label">Opcion</label>
					<?php echo Tag::select("tipopc", $Mercurio09->find(), "using: tipopc,detalle", "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="usuario" class="form-control-label">Usuario</label>
					<?php echo Tag::select("usuario", $Gener02->find("usuario in (select usuario from mercurio08)"), "using: usuario,nombre", "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
				</div>
			</div>
			<div class="col-md-auto d-flex mr-auto">
				<button type="button" class="btn btn-primary align-self-center" id='btnTraerDatos'">Consultar</button>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
    </div>
</div>

<div id='consulta' class='table-responsive'>
</div>

<?= Tag::javascriptInclude('Cajas/reasigna'); ?>