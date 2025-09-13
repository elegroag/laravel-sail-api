@php
use App\Services\Tag;
@endphp

<h4>Aprobar</h4>
<p>Esta opcion es para aprobar la empresa y enviar los datos a Subsidio</p>

<form method="POST" action="" id='formAprobar'>
    <div class='row'>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='tipdoc' class='form-control-label'>Fecha Afiliación</label>
                @php echo Tag::calendar("fecafi", "class: form-control"); @endphp
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='subpla' class='form-control-label'>Sucursal planilla</label>
                @php echo Tag::textField("subpla", "class: form-control"); @endphp
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='actapr' class='form-control-label'>Acta Aprobación</label>
                @php echo Tag::textField("actapr", "class: form-control"); @endphp
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='diahab' class='form-control-label'>Día habil de Pago </label>
                @php echo Tag::textField("diahab", "class: form-control"); @endphp
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='feccap' class='form-control-label'>Fecha Resolución</label>
                @php echo Tag::calendar("feccap", "class: form-control"); @endphp
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='tippag' class='form-control-label'>Tipo Pago</label>
                @php echo Tag::selectStatic("tippag", $_tippag, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='tipdoc' class='form-control-label'>Fecha afiliación</label>
                @php echo Tag::calendar("fecafi", "class: form-control"); @endphp
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='banco' class='form-control-label'>Banco</label>
                @php echo Tag::selectStatic("banco", $_bancos, "use_dummy: true", "dummyValue: ", "class: form-control", "value:"); @endphp
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='numcue' class='form-control-label'>Número cuenta</label>
                @php echo Tag::textField("numcue", "class: form-control"); @endphp
                <label id="numcue-error" class="error" for="numcue"></label>
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='tipcue' class='form-control-label'>Tipo cuenta</label>
                @php echo Tag::selectStatic("tipcue", $_tipcue, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-2'>
            <div class='form-group'>
                <label for='giro' class='form-control-label'>Giro</label>
                @php echo Tag::selectStatic("giro", $_giro, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='giro' class='form-control-label'>Motivo no giro</label>
                @php echo Tag::selectStatic("codgir", $_codgir, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-8'>
            <div class='form-group'>
                <label for='nota_aprobar' class='form-control-label'>Nota</label>
                <textarea class='form-control' id='nota_aprobar' name="nota_aprobar" rows='3'></textarea>
            </div>
        </div>
    </div>
</form>
<div class="box form-group pt-3">
    <button type='button' class='btn btn-md btn-success' style='width:200px' id='aprobar_solicitud'>Aprobar</button>
</div>
