@php
use App\Services\Tag;
@endphp
<h4>Aprobar</h4>
<p>Esta opcion es para aprobar la empresa y enviar los datos a Subsidio</p>

<form method="POST" action="" id='formAprobar'>
    <div class='row'>
        <div class='col-md-2'>
            <div class='form-group'>
                <label for='tipdur' class='form-control-label'>Duración</label>
                @component('components.select', ['name' => 'tipdur', 'options' => $_tipdur, 'dummy' => true, 'class' => 'form-control'])
                @endcomponent
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='codind' class='form-control-label'>Indice</label>
                @component('components.select', ['name' => 'codind', 'options' => $_codind, 'dummy' => true, 'class' => 'form-control'])
                @endcomponent
            </div>
        </div>
        <div class='col-md-2'>
            <div class='form-group'>
                <label for='todmes' class='form-control-label'>Paga mes</label>
                @component('components.select', ['name' => 'todmes', 'options' => $_todmes, 'dummy' => true, 'class' => 'form-control'])
                @endcomponent
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='forpre' class='form-control-label'>Forma presentación</label>
                @component('components.select', ['name' => 'forpre', 'options' => $_forpre, 'dummy' => true, 'class' => 'form-control'])
                @endcomponent
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='tipdoc' class='form-control-label'>Fecha Afiliación</label>
                <input type="text" name="fecafi" class="form-control">
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='subpla' class='form-control-label'>Sucursal planilla</label>
                <input type="text" name="subpla" class="form-control">
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='actapr' class='form-control-label'>Acta Aprobación</label>
                <input type="text" name="actapr" class="form-control">
                </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='diahab' class='form-control-label'>Día habil de Pago </label>
                <input type="text" name="diahab" class="form-control">
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='feccap' class='form-control-label'>Fecha Resolución</label>
                <input type="text" name="feccap" class="form-control">
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='tippag' class='form-control-label'>Tipo Pago</label>
                <input type="text" name="tippag" class="form-control">
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='tipdoc' class='form-control-label'>Fecha afiliación</label>
                <input type="text" name="fecafi" class="form-control">
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='banco' class='form-control-label'>Banco</label>
                @component('components.select', ['name' => 'banco', 'options' => $_bancos, 'dummy' => true, 'class' => 'form-control', 'value' => ''])
                @endcomponent
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='numcue' class='form-control-label'>Número cuenta</label>
                <input type="text" name="numcue" class="form-control">
                <label id="numcue-error" class="error" for="numcue"></label>
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='tipcue' class='form-control-label'>Tipo cuenta</label>
                @component('components.select', ['name' => 'tipcue', 'options' => $_tipcue, 'dummy' => true, 'class' => 'form-control'])
                @endcomponent
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-2'>
            <div class='form-group'>
                <label for='giro' class='form-control-label'>Giro</label>
                @component('components.select', ['name' => 'giro', 'options' => $_giro, 'dummy' => true, 'class' => 'form-control'])
                @endcomponent
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='giro' class='form-control-label'>Motivo no giro</label>
                @component('components.select', ['name' => 'codgir', 'options' => $_codgir, 'dummy' => true, 'class' => 'form-control'])
                @endcomponent
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-8'>
            <div class='form-group'>
                <label for='nota_aprobar' class='form-control-label'>Nota</label>
                <textarea class='form-control summer_content' id='nota_aprobar' name='nota_aprobar' rows='3'></textarea>
            </div>
        </div>
    </div>
</form>

<div class="box form-group pt-3">
    <button type='button' class='btn btn-md btn-success' style='width:200px' id='aprobar_solicitud'>Aprobar</button>
</div>
