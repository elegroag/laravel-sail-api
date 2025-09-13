@php
use App\Services\Tag;
@endphp

<h4>Aprobar</h4>
<p>Esta opcion es para aprobar la empresa y enviar los datos a Subsidio</p>

<form id='formAprobar'>
    <div class='row g-3'>
		<div class='col-md-4' group-for='codsuc'>
			<div class='d-flex align-items-center'>
				<label for='codsuc' class='form-label me-2 mb-0 flex-shrink-0'>Sucursal planilla</label>
				@php echo Tag::textField("codsuc", "placeholder: Sucursal", "class: form-control"); @endphp
			</div>
		</div>

		<div class='col-md-4' group-for='fecapr'>
			<div class='d-flex align-items-center'>
				<label for='fecapr' class='form-label me-2 mb-0 flex-shrink-0'>Fecha aprobación</label>
				@php echo Tag::calendar("fecapr", "placeholder: Fecha de aprobación", "class: form-control"); @endphp
			</div>
		</div>

		<div class='col-12' group-for='nota_aprobar'>
			<div class='form-group'>
				<label for='nota_aprobar' class='form-label'>Nota</label>
				@php echo Tag::textarea("nota_aprobar", "placeholder: Nota", "class: form-control"); @endphp
			</div>
		</div>
    </div>
</form>

<div class="form-group pt-3">
	<button type='button' class='btn btn-success' id='aprobar_solicitud'><i class='fas fa-check'></i> Aprobar</button>
</div>
