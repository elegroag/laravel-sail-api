<h4>Aprobar</h4>
<p>Esta opción es para aprobar la empresa y enviar los datos a Subsidio</p>

<form id='formAprobar'>
    <div class='row g-3'>
        <div class='col-md-6 col-lg-3' group-for='codind'>
            <div class='d-flex align-items-center'>
                <label for='codind' class='form-label me-2 mb-0 flex-shrink-0'>Indice</label>
                <select name="codind" id="codind" class="form-control" use_dummy="true" dummyValue="Seleccione un índice" value="">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_codind as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-6 col-lg-3' group-for='todmes'>
            <div class='d-flex align-items-center'>
                <label for='todmes' class='form-label me-2 mb-0 flex-shrink-0'>Paga mes</label>
                <select name="todmes" id="todmes" class="form-control" use_dummy="true" dummyValue="Seleccione opción">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_todmes as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-6 col-lg-3' group-for='tipapo'>
            <div class='d-flex align-items-center'>
                <label for='tipapo' class='form-label me-2 mb-0 flex-shrink-0'>Tipo aportante</label>
                <select name="tipapo" id="tipapo" class="form-control" use_dummy="true" dummyValue="Seleccione tipo aportante" value="">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_tipapo as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-6 col-lg-3' group-for='tipsoc'>
            <div class='d-flex align-items-center'>
                <label for='tipsoc' class='form-label me-2 mb-0 flex-shrink-0'>Tipo sociedad</label>
                <select name="tipsoc" id="tipsoc" class="form-control" use_dummy="true" dummyValue="Seleccione tipo sociedad" value="">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_tipsoc as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-4' group-for='codsuc'>
            <div class='d-flex align-items-center'>
                <label for='codsuc' class='form-label me-2 mb-0 flex-shrink-0'>Sucursal planilla</label>
                <input type="text" name="codsuc" id="codsuc" class="form-control" placeholder="Ingrese sucursal planilla">
            </div>
        </div>
        <div class='col-md-4' group-for='actapr'>
            <div class='d-flex align-items-center'>
                <label for='actapr' class='form-label me-2 mb-0 flex-shrink-0'>Acta aprobación</label>
                <input type="text" name="actapr" id="actapr" class="form-control" placeholder="Ingrese acta de aprobación">
            </div>
        </div>
        <div class='col-md-4' group-for='diahab'>
            <div class='d-flex align-items-center'>
                <label for='diahab' class='form-label me-2 mb-0 flex-shrink-0'>Día habil de Pago</label>
                <input type="number" name="diahab" id="diahab" class="form-control" placeholder="Ej: 15">
            </div>
        </div>
		<div class='col-md-4' group-for='tippag'>
            <div class='d-flex align-items-center'>
                <label for='tippag' class='form-label me-2 mb-0 flex-shrink-0'>Tipo medio pago cuota:</label>
                <select name="tippag" id="tippag" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de pago" value="">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_tippag as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-4' group-for='codban'>
            <div class='d-flex align-items-center'>
                <label for='codban' class='form-label me-2 mb-0 flex-shrink-0'>Banco</label>
                <select name="codban" id="codban" class="form-control" use_dummy="true" dummyValue="Seleccione banco" value="">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_bancos as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-4' group-for='numcue'>
            <div class='d-flex align-items-center'>
                <label for='numcue' class='form-label me-2 mb-0 flex-shrink-0'>Número cuenta</label>
                <input type="text" name="numcue" id="numcue" class="form-control" placeholder="Ingrese número de cuenta">
            </div>
        </div>
        <div class='col-md-4' group-for='tipcue'>
            <div class='d-flex align-items-center'>
                <label for='tipcue' class='form-label me-2 mb-0 flex-shrink-0'>Tipo cuenta</label>
                <select name="tipcue" id="tipcue" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de cuenta" value="">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_tipcue as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-3' group-for='giro'>
            <div class='d-flex align-items-center'>
                <label for='giro' class='form-label me-2 mb-0 flex-shrink-0'>Giro</label>
                <select name="giro" id="giro" class="form-control" use_dummy="true" dummyValue="Seleccione giro" value="">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_giro as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-4' group-for='codgir'>
            <div class='d-flex align-items-center'>
                <label for='codgir' class='form-label me-2 mb-0 flex-shrink-0'>Motivo no giro</label>
                <select name="codgir" id="codgir" class="form-control" use_dummy="true" dummyValue="Seleccione motivo" value="">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_codgir as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='col-md-4' group-for='fecapr'>
            <div class='d-flex align-items-center'>
                <label for='fecapr' class='form-label me-2 mb-0 flex-shrink-0'>Fecha aprobación resolución</label>
                <input type="date" name="fecapr" id="fecapr" class="form-control" placeholder="dd/mm/aaaa">
            </div>
        </div>
		<div class='col-md-3' group-for='fecafi'>
            <div class='d-flex align-items-center'>
                <label for='fecafi' class='form-label me-2 mb-0 flex-shrink-0'>Fecha afiliación</label>
                <input type="date" name="fecafi" id="fecafi" class="form-control" placeholder="dd/mm/aaaa">
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
