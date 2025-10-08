<h4>Aprobar</h4>
<p>Esta opcion es para aprobar la empresa y enviar los datos a Subsidio</p>

<form method="POST" action="" id='formAprobar'>
    <div class='row'>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='tipdoc' class='form-control-label'>Fecha Afiliación</label>
                <input type="text" class="form-control" id="fecafi" name="fecafi">
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='subpla' class='form-control-label'>Sucursal planilla</label>
                <input type="text" class="form-control" id="subpla" name="subpla">
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='actapr' class='form-control-label'>Acta Aprobación</label>
                <input type="text" class="form-control" id="actapr" name="actapr">
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='diahab' class='form-control-label'>Día habil de Pago </label>
                <input type="text" class="form-control" id="diahab" name="diahab">
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='feccap' class='form-control-label'>Fecha Resolución</label>
                <input type="text" class="form-control" id="feccap" name="feccap">
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='tippag' class='form-control-label'>Tipo Pago</label>
                <select class="form-control" id="tippag" name="tippag">
                    <option value="">Seleccione</option>
                    @foreach ($_tippag as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='tipdoc' class='form-control-label'>Fecha afiliación</label>
                <input type="text" class="form-control" id="fecafi" name="fecafi">
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='banco' class='form-control-label'>Banco</label>
                <select class="form-control" id="banco" name="banco">
                    <option value="">Seleccione</option>
                    @foreach ($_bancos as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='numcue' class='form-control-label'>Número cuenta</label>
                <input type="text" class="form-control" id="numcue" name="numcue">
                <label id="numcue-error" class="error" for="numcue"></label>
            </div>
        </div>
        <div class='col-md-4'>
            <div class='form-group'>
                <label for='tipcue' class='form-control-label'>Tipo cuenta</label>
                <select class="form-control" id="tipcue" name="tipcue">
                    <option value="">Seleccione</option>
                    @foreach ($_tipcue as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-2'>
            <div class='form-group'>
                <label for='giro' class='form-control-label'>Giro</label>
                <select class="form-control" id="giro" name="giro">
                    <option value="">Seleccione</option>
                    @foreach ($_giro as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-3'>
            <div class='form-group'>
                <label for='giro' class='form-control-label'>Motivo no giro</label>
                <select class="form-control" id="codgir" name="codgir">
                    <option value="">Seleccione</option>
                    @foreach ($_codgir as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
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
