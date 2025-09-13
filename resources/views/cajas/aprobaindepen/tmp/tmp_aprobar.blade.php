@php
use App\Services\Tag;
@endphp

<h4>Aprobar</h4>
<p>Esta opción es para aprobar la empresa y enviar los datos a Subsidio</p>

<form id='formAprobar'>
    <div class='row g-3'>
        <div class='col-md-6 col-lg-3' group-for='codind'>
            <div class='d-flex align-items-center'>
                <label for='codind' class='form-label me-2 mb-0 flex-shrink-0'>Indice</label>
                @php echo Tag::selectStatic("codind", $_codind, "use_dummy: true", "dummyValue: Seleccione un índice", "class: form-select"); @endphp
            </div>
        </div>
        <div class='col-md-6 col-lg-3' group-for='todmes'>
            <div class='d-flex align-items-center'>
                <label for='todmes' class='form-label me-2 mb-0 flex-shrink-0'>Paga mes</label>
                @php echo Tag::selectStatic("todmes", $_todmes, "use_dummy: true", "dummyValue: Seleccione opción", "class: form-select"); @endphp
            </div>
        </div>
        <div class='col-md-6 col-lg-3' group-for='tipapo'>
            <div class='d-flex align-items-center'>
                <label for='tipapo' class='form-label me-2 mb-0 flex-shrink-0'>Tipo aportante</label>
                @php echo Tag::selectStatic("tipapo", $_tipapo, "use_dummy: true", "dummyValue: Seleccione tipo aportante", "class: form-select"); @endphp
            </div>
        </div>
        <div class='col-md-6 col-lg-3' group-for='tipsoc'>
            <div class='d-flex align-items-center'>
                <label for='tipsoc' class='form-label me-2 mb-0 flex-shrink-0'>Tipo sociedad</label>
                @php echo Tag::selectStatic("tipsoc", $_tipsoc, "use_dummy: true", "dummyValue: Seleccione tipo sociedad", "class: form-select"); @endphp
            </div>
        </div>
        <div class='col-md-4' group-for='codsuc'>
            <div class='d-flex align-items-center'>
                <label for='codsuc' class='form-label me-2 mb-0 flex-shrink-0'>Sucursal planilla</label>
                @php echo Tag::textField("codsuc", "class: form-control", "placeholder: Ingrese sucursal planilla"); @endphp
            </div>
        </div>
        <div class='col-md-4' group-for='actapr'>
            <div class='d-flex align-items-center'>
                <label for='actapr' class='form-label me-2 mb-0 flex-shrink-0'>Acta aprobación</label>
                @php echo Tag::textField("actapr", "class: form-control", "placeholder: Ingrese acta de aprobación"); @endphp
            </div>
        </div>
        <div class='col-md-4' group-for='diahab'>
            <div class='d-flex align-items-center'>
                <label for='diahab' class='form-label me-2 mb-0 flex-shrink-0'>Día habil de Pago</label>
                @php echo Tag::textField("diahab", "class: form-control", "type: number", "placeholder: Ej: 15"); @endphp
            </div>
        </div>
		<div class='col-md-4' group-for='tippag'>
            <div class='d-flex align-items-center'>
                <label for='tippag' class='form-label me-2 mb-0 flex-shrink-0'>Tipo medio pago cuota:</label>
                @php echo Tag::selectStatic("tippag", @$_tippag, "use_dummy: true", "dummyValue: Seleccione tipo de pago", "class: form-select"); @endphp
            </div>
        </div>
        <div class='col-md-4' group-for='codban'>
            <div class='d-flex align-items-center'>
                <label for='codban' class='form-label me-2 mb-0 flex-shrink-0'>Banco</label>
                @php echo Tag::selectStatic("codban", @$_bancos, "use_dummy: true", "dummyValue: Seleccione banco", "class: form-select", "value:"); @endphp
            </div>
        </div>
        <div class='col-md-4' group-for='numcue'>
            <div class='d-flex align-items-center'>
                <label for='numcue' class='form-label me-2 mb-0 flex-shrink-0'>Número cuenta</label>
                @php echo Tag::textField("numcue", "class: form-control", "placeholder: Ingrese número de cuenta"); @endphp
            </div>
        </div>
        <div class='col-md-4' group-for='tipcue'>
            <div class='d-flex align-items-center'>
                <label for='tipcue' class='form-label me-2 mb-0 flex-shrink-0'>Tipo cuenta</label>
                @php echo Tag::selectStatic("tipcue", @$_tipcue, "use_dummy: true", "dummyValue: Seleccione tipo de cuenta", "class: form-select"); @endphp
            </div>
        </div>
        <div class='col-md-3' group-for='giro'>
            <div class='d-flex align-items-center'>
                <label for='giro' class='form-label me-2 mb-0 flex-shrink-0'>Giro</label>
                @php echo Tag::selectStatic("giro", @$_giro, "use_dummy: true", "dummyValue: Seleccione giro", "class: form-select"); @endphp
            </div>
        </div>
        <div class='col-md-4' group-for='codgir'>
            <div class='d-flex align-items-center'>
                <label for='codgir' class='form-label me-2 mb-0 flex-shrink-0'>Motivo no giro</label>
                @php echo Tag::selectStatic("codgir", @$_codgir, "use_dummy: true", "dummyValue: Seleccione motivo", "class: form-select"); @endphp
            </div>
        </div>
        <div class='col-md-4' group-for='fecapr'>
            <div class='d-flex align-items-center'>
                <label for='fecapr' class='form-label me-2 mb-0 flex-shrink-0'>Fecha aprobación resolución</label>
                @php echo Tag::calendar("fecapr", "class: form-control", "placeholder: dd/mm/aaaa"); @endphp
            </div>
        </div>
		<div class='col-md-3' group-for='fecafi'>
            <div class='d-flex align-items-center'>
                <label for='fecafi' class='form-label me-2 mb-0 flex-shrink-0'>Fecha afiliación</label>
                @php echo Tag::calendar("fecafi", "class: form-control", "placeholder: dd/mm/aaaa"); @endphp
            </div>
        </div>
        <div class='col-12' group-for='nota_aprobar'>
            <div class='form-group'>
                <label for='nota_aprobar' class='form-label'>Nota</label>
                <textarea class='form-control' id='nota_aprobar' name='nota_aprobar' rows='3' placeholder='Ingrese una nota para la notificación por email'></textarea>
            </div>
        </div>
    </div>
</form>

<div class='form-group pt-3'>
    <button type='button' class='btn btn-success' id='aprobar_solicitud'><i class='fas fa-check'></i> Aprobar</button>
</div>
