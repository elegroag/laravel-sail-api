<?php 
echo View::getContent();
echo Tag::addJavascript('Cajas/reportes');
//echo TagUser::help($title,$help);
?>
<div class="card mb-0">
	<div class="card-body">
		<?php echo Tag::form("reportes/novedades_Subsidio","id: form","class: validation_form","autocomplete: off","novalidate"); ?>
		<div class="row">
		<div class="col-md-2 ml-auto">
			<div class="form-group">
				<label for="fecini" class="form-control-label">Fecha Inicial</label>
				<?php echo TagUser::calendar("fecini","placeholder: Fecha Inicial","class: form-control"); ?>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label for="fecfin" class="form-control-label">Fecha Final</label>
				<?php echo TagUser::calendar("fecfin","placeholder: Fecha Final","class: form-control"); ?>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label for="tipnov" class="form-control-label">Tipo Novedad</label>
				 <?php echo Tag::selectStatic("tipnov",array("1"=>"INGRESO 1 VEZ","2"=>"INGRESO 2 VEZ","5"=>"DESAFILIACION A UNA CAJA","7"=>"PERDIDA DE AFILIACION CAUSA GRAVE","8"=>"INICIO LABORAL","9"=>"TERMINACION LABORAL","10"=>"SUSPENSION DEL CONTRATO DE TRABAJO","11"=>"LICENCIAS REMUNERADAS Y NO REMUNERADAS","12"=>"MODIFICACION DE SALARIO"),"use_dummy: true","dummyValue: ","class: form-control"); ?>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label for="nit" class="form-control-label">Nit</label>
				<?php echo Tag::numericField("nit","class: form-control","placeholder: Nit"); ?>
		     </div>
		</div>
		<div class="col-md-auto d-flex mr-auto">
			<button type="button" class="btn btn-danger align-self-center" onclick="reporte_novedades();" >Reporte</button>
		</div>
	</div>
<?php echo Tag::endform(); ?>
</div>
</div>
