<form id='formEditar' class="validation_form" autocomplete='off' novalidate >
    <input type='number' name="repleg" class="d-none" />
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="nit" class="form-control-label">NIT o documento empresa:</label>
                <input type="number" name="nit" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group" style="overflow: hidden">
                <label for="tipdoc" class="form-control-label">Tipo documento empresa:</label>
                <select name="tipdoc" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_coddoc as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <label id="tipdoc-error" class="error" for="tipdoc"></label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="digver" class="form-control-label">Digito verificación:</label>
                <input type="number" name="digver" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="tipson" class="form-control-label">Tipo empresa:</label>
                <select name="tipemp" class="form-control">
                    <option value="">Seleccione un tipo de empresa</option>
                    @foreach($_tipemp as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label for="tipper" class="form-control-label">Tipo persona:</label>
                <select name="tipper" class="form-control">
                    <option value="">Seleccione un tipo de persona</option>
                    @foreach($_tipper as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <label id="tipper-error" class="error" for="tipper"></label>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="razsoc" class="form-control-label">Razón social:</label>
                <input type="text" name="razsoc" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="sigla" class="form-control-label">Sigla:</label>
                <input type="text" name="sigla" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="matmer" class="form-control-label">Mat. mercantil:</label>
                <input type="text" name="matmer" class="form-control" value="">
            </div>
        </div>
    </div>
    <div class="row" id='show_natural' style="display:none;">
        <div class="col-md-3">
            <div class="form-group">
                <label for="priape" class="form-control-label">Primer apellido:</label>
                <input type="text" name="priape" class="form-control" value="">
                <label id="priape-error" class="error" for="priape"></label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="segape" class="form-control-label">Segundo apellido:</label>
                <input type="text" name="segape" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="prinom" class="form-control-label">Primer nombre:</label>
                <input type="text" name="prinom" class="form-control" value="">
                <label id="prinom-error" class="error" for="prinom"></label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="segnom" class="form-control-label">Segundo nombre:</label>
                <input type="text" name="segnom" class="form-control" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="calemp" class="form-control-label">Calidad afiliación:</label>
                <select name="calemp" class="form-control">
                    <option value="">Seleccione una calidad de afiliación</option>
                    @foreach($_calemp as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="coddocrepleg" class="form-control-label">Tipo documento rep. legal:</label>
                <select name="coddocrepleg" class="form-control">
                    <option value="">Seleccione un tipo de documento</option>
                    @foreach($_coddocrepleg as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <label id="coddocrepleg-error" class="error" for="priaperepleg"></label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="cedrep" class="form-control-label">Cedula representante legal:</label>
                <input type="text" name="cedrep" class="form-control" value="">
            </div>
        </div>
    </div>
    <div class="row" id='show_juridica' style="display:none;">
        <div class="col-md-3">
            <div class="form-group">
                <label for="priaperepleg" class="form-control-label">Primer apellido rep. legal:</label>
                <input type="text" name="priaperepleg" class="form-control" value="">
                <label id="priaperepleg-error" class="error" for="priaperepleg"></label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="segaperepleg" class="form-control-label">Segundo apellido rep. legal:</label>
                <input type="text" name="segaperepleg" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="prinomrepleg" class="form-control-label">Primer nombre rep. legal:</label>
                <input type="text" name="prinomrepleg" class="form-control" value="">
                <label id="prinomrepleg-error" class="error" for="prinomrepleg"></label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="segnomrepleg" class="form-control-label">Segundo nombre rep. legal:</label>
                <input type="text" name="segnomrepleg" class="form-control" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="direccion" class="form-control-label">Direccion notificación:</label>
                <input type="text" name="direccion" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="codciu" class="form-control-label">Ciudad notificación:</label>
                <select name="codciu" class="form-control">
                    <option value="">Seleccione una ciudad</option>
                    @foreach($_codciu as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <label id="codciu-error" class="error" for="codciu"></label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="codzon" class="form-control-label">Lugar donde laboran trabajadores:</label>
                <select name="codzon" class="form-control">
                    <option value="">Seleccione un lugar</option>
                    @foreach($_codzon as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <label id="codzon-error" class="error" for="codzon"></label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="telefono" class="form-control-label">Telefono notificación con indicativo:</label>
                <input type="text" name="telefono" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="celular" class="form-control-label">Celular notificación</label>
                <input type="text" name="celular" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="fax" class="form-control-label">Fax notificación</label>
                <input type="text" name="fax" class="form-control" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="email" class="form-control-label">Email notificación empresarial</label>
                <input type="text" name="email" class="form-control" value="">
                <label id="email-error" class="error" for="email"></label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group" style="overflow: hidden">
                <label for="codact" class="form-control-label">Digite el código CIUU-DIAN de la actividad economica:</label>
                <select name="codact" class="form-control">
                    <option value="">Seleccione un código</option>
                    @foreach($_codact as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <label id="codact-error" class="error" for="codact"></label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="fecini" class="form-control-label">Fecha inicio:</label>
                <input type="date" name="fecini" class="form-control" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label for="tottra" class="form-control-label">Total trabajadores:</label>
                <input type="text" name="tottra" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="valnom" class="form-control-label">Valor nomina:</label>
                <input type="text" name="valnom" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="tipsoc" class="form-control-label">Tipo sociedad:</label>
                <select name="tipsoc" class="form-control">
                    <option value="">Seleccione un tipo</option>
                    @foreach($_tipsoc as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <label id="tipsoc-error" class="error" for="tipsoc"></label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="ciupri" class="form-control-label">Ciudad comercial:</label>
                <select name="ciupri" class="form-control">
                    <option value="">Seleccione una ciudad</option>
                    @foreach($_ciupri as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <label id="ciupri-error" class="error" for="ciupri"></label>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-md-4">
            <div class="form-group">
                <label for="celpri" class="form-control-label">Celular comercial:</label>
                <input type="text" name="celpri" class="form-control" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="emailpri" class="form-control-label">Email comercial:</label>
                <input type="text" name="emailpri" class="form-control" value="">
                <label id="emailpri-error" class="error" for="emailpri"></label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="dirpri" class="form-control-label">Dirección comercial:</label>
                <input type="text" name="dirpri" class="form-control" value="">
                <label id="dirpri-error" class="error" for="dirpri"></label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="telpri" class="form-control-label">Teléfono comercial:</label>
                <input type="text" name="telpri" class="form-control" value="">
                <label id="telpri-error" class="error" for="telpri"></label>
            </div>
        </div>
    </div>
</form>
<div class="card-footer">
    <button type="button" class="btn btn-primary" id='guardar_ficha'><i class="fas fa-save"></i> Guardar los cambios</button>
</div>
