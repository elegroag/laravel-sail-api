<div style="width: 100%; background: #fff; height: auto; padding: 0; margin: 0; font-family: Helvetica, Arial, sans-serif;">
	<div style="
		max-width: 270mm;
		height: auto;
		margin: 0 auto;
		background-size: contain;
		background-repeat: no-repeat;
		background-image: url(<?= $membrete ?>);
		padding: 118px 8px 0;
		box-sizing: border-box;
	">
		<div style="
			display: grid;
			grid-template-rows: auto;
			gap: 15px;
			max-width: 100%;
			margin: 0;
		">
			<div style="
				text-align: left;
				font-size: 15px;
				color: #000;
				line-height: 20px;
			">
				Fecha <?= date('Y/m/d') ?><br />
				Florencia
			</div>

			<div style="
				text-align: left;
				font-size: 15px;
				color: #000;
				line-height: 20px;
				margin-bottom: 15px;
			">
				Señor(a) <br />
				<?= $razsoc ?><br />
				Florencia, Caquetá
			</div>

			<p style="
				text-align: left;
				font-family: Helvetica, Arial;
				font-size: 16px;
				color: #000;
				font-weight: bold;
				margin: 0 0 15px 0;
			">
				<?= (isset($titulo) && !empty($titulo)) ? $titulo : 'Proceso de afiliación, Caja De Compensación Familiar del Caquetá COMFACA' ?>
			</p>

			<div style="
				text-align: justify;
				font-family: Helvetica, Arial;
				font-size: 14px;
				color: #000;
				margin-bottom: 15px;
				line-height: 1.5;
			">
				Reciba un cordial saludo deseando éxitos en sus diferentes labores.<br>
				<?= $msj ?>
			</div>

			<div style="
				text-align: left;
				font-size: 14px;
				color: #000;
				margin-bottom: 15px;
			">
				Atentamente,<br>
			</div>

			<div style="
				text-align: left;
				font-family: Helvetica, Arial;
				font-size: 14px;
				font-style: italic;
				color: #000;
				line-height: 20px;
			">
				<b>YENNY PATRICIA ESTRADA OTALORA</b><br />
				Jefe Departamento de Aportes y Subsidio<br />
				<br />
			</div>
		</div>
	</div>
</div>