
<script id='tmp_card_header' type="text/template">
    <div class="row">
        <div class="col-md-8">
            <h4>Ficha solicitud afiliación</h4>
            <p style='font-size:1rem'>Disponible para editar los campos del formulario digital de la solicitud.</p>
        </div>
        <div class="col-md-4">
            <div id="botones" class='row justify-content-end'>
                <a href="{{ route('cajas.aprobaindepen.index') }}" class='btn btn-md btn-primary'> Volver</a>&nbsp;
            </div>
        </div>
    </div>
</script>

<div class='card-header pt-2 pb-2' id='afiliacion_header'></div>

<div class="card-body">
    <div class="col-xs-12">
        <form id="formulario_empresa" class="validation_form" autocomplete="off" novalidate>
        <input type="hidden" name="id">
        <input type="hidden" name="repleg">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nit" class="form-control-label">NIT o documento empresa:</label>
                    <input type="number" name="nit" class="form-control" placeholder="Ingrese nit">
                    <label id="nit-error" class="error" for="nit"></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group" style="overflow: hidden">
                    <label for="tipdoc" class="form-control-label">Tipo documento empresa:</label>
                    <select name="tipdoc" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de pago" value="">
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
                    <input type="number" name="digver" class="form-control" placeholder="Ingrese digito verificacion">
                    <label id="digver-error" class="error" for="digver"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipson" class="form-control-label">Tipo empresa:</label>
                    <select name="tipson" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de pago" value="">
                        <option value="">Seleccione un tipo de documento</option>
                        @foreach($_tipson as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <label id="tipson-error" class="error" for="tipson"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="tipper" class="form-control-label">Tipo persona:</label>
                    <select name="tipper" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de pago" value="">
                        <option value="">Seleccione un tipo de documento</option>
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
                    <input type="text" name="razsoc" class="form-control" placeholder="Ingrese razon social">
                    <label id="razsoc-error" class="error" for="razsoc"></label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="sigla" class="form-control-label">Sigla:</label>
                    <input type="text" name="sigla" class="form-control" placeholder="Ingrese sigla">
                    <label id="sigla-error" class="error" for="sigla"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="matmer" class="form-control-label">Mat. mercantil:</label>
                    <input type="text" name="matmer" class="form-control" placeholder="Ingrese matricula mercantil">
                    <label id="matmer-error" class="error" for="matmer"></label>
                </div>
            </div>
        </div>
        <div class="row" id='show_natural' style="display:none;">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="priape" class="form-control-label">Primer apellido:</label>
                    <input type="text" name="priape" class="form-control" placeholder="Ingrese primer apellido">
                    <label id="priape-error" class="error" for="priape"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="segape" class="form-control-label">Segundo apellido:</label>
                    <input type="text" name="segape" class="form-control" placeholder="Ingrese segundo apellido">
                    <label id="segape-error" class="error" for="segape"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="prinom" class="form-control-label">Primer nombre:</label>
                    <input type="text" name="prinom" class="form-control" placeholder="Ingrese primer nombre">
                    <label id="prinom-error" class="error" for="prinom"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="segnom" class="form-control-label">Segundo nombre:</label>
                    <input type="text" name="segnom" class="form-control" placeholder="Ingrese segundo nombre">
                    <label id="segnom-error" class="error" for="segnom"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="calemp" class="form-control-label">Calidad afiliación:</label>
                    <select name="calemp" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de pago" value="">
                        <option value="">Seleccione un tipo de documento</option>
                        @foreach($_calemp as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <label id="calemp-error" class="error" for="calemp"></label>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="coddocrepleg" class="form-control-label">Tipo documento rep. legal:</label>
                    <select name="coddocrepleg" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de pago" value="">
                        <option value="">Seleccione un tipo de documento</option>
                        @foreach($_coddocrepleg as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <label id="coddocrepleg-error" class="error" for="coddocrepleg"></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="cedrep" class="form-control-label">Cedula representante legal:</label>
                    <input type="text" name="cedrep" class="form-control" placeholder="Ingrese cedula representante legal">
                    <label id="cedrep-error" class="error" for="cedrep"></label>
                </div>
            </div>
        </div>
        <div class="row" id='show_juridica' style="display:none;">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="priaperepleg" class="form-control-label">Primer apellido rep. legal:</label>
                    <input type="text" name="priaperepleg" class="form-control" placeholder="Ingrese primer apellido rep. legal">
                    <label id="priaperepleg-error" class="error" for="priaperepleg"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="segaperepleg" class="form-control-label">Segundo apellido rep. legal:</label>
                    <input type="text" name="segaperepleg" class="form-control" placeholder="Ingrese segundo apellido rep. legal">
                    <label id="segaperepleg-error" class="error" for="segaperepleg"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="prinomrepleg" class="form-control-label">Primer nombre rep. legal:</label>
                    <input type="text" name="prinomrepleg" class="form-control" placeholder="Ingrese primer nombre rep. legal">
                    <label id="prinomrepleg-error" class="error" for="prinomrepleg"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="segnomrepleg" class="form-control-label">Segundo nombre rep. legal:</label>
                    <input type="text" name="segnomrepleg" class="form-control" placeholder="Ingrese segundo nombre rep. legal">
                    <label id="segnomrepleg-error" class="error" for="segnomrepleg"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="direccion" class="form-control-label">Direccion notificación:</label>
                    <input type="text" name="direccion" class="form-control" placeholder="Ingrese direccion notificación">
                    <label id="direccion-error" class="error" for="direccion"></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="codciu" class="form-control-label">Ciudad notificación:</label>
                    <select name="codciu" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de pago" value="">
                        <option value="">Seleccione un tipo de documento</option>
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
                    <select name="codzon" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de pago" value="">
                        <option value="">Seleccione un tipo de documento</option>
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
                    <input type="text" name="telefono" class="form-control" placeholder="Ingrese telefono notificación con indicativo">
                    <label id="telefono-error" class="error" for="telefono"></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="celular" class="form-control-label">Celular notificación</label>
                    <input type="text" name="celular" class="form-control" placeholder="Ingrese celular notificación">
                    <label id="celular-error" class="error" for="celular"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="email" class="form-control-label">Email notificación empresarial</label>
                    <input type="text" name="email" class="form-control" placeholder="Ingrese email notificación empresarial">
                    <label id="email-error" class="error" for="email"></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group" style="overflow: hidden">
                    <label for="codact" class="form-control-label">Digite el código CIUU-DIAN de la actividad economica:</label>
                    <select name="codact" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de pago" value="">
                        <option value="">Seleccione un tipo de documento</option>
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
                    <input type="text" name="fecini" class="form-control" placeholder="Ingrese fecha inicio">
                    <label id="fecini-error" class="error" for="fecini"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="tottra" class="form-control-label">Total trabajadores:</label>
                    <input type="text" name="tottra" class="form-control" placeholder="Ingrese total trabajadores">
                    <label id="tottra-error" class="error" for="tottra"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="valnom" class="form-control-label">Valor nomina:</label>
                    <input type="text" name="valnom" class="form-control" placeholder="Ingrese valor nomina">
                    <label id="valnom-error" class="error" for="valnom"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipsoc" class="form-control-label">Tipo sociedad:</label>
                    <select name="tipsoc" class="form-control" use_dummy="true" dummyValue="Seleccione tipo de pago" value="">
                        <option value="">Seleccione un tipo de documento</option>
                        @foreach($_tipsoc as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <label id="tipsoc-error" class="error" for="tipsoc"></label>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label for="celpri" class="form-control-label">Celular comercial:</label>
                    <input type="text" name="celpri" class="form-control" placeholder="Ingrese celular comercial">
                    <label id="celpri-error" class="error" for="celpri"></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="emailpri" class="form-control-label">Email comercial:</label>
                    <input type="text" name="emailpri" class="form-control" placeholder="Ingrese email comercial">
                    <label id="emailpri-error" class="error" for="emailpri"></label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="dirpri" class="form-control-label">Dirección comercial:</label>
                    <input type="text" name="dirpri" class="form-control" placeholder="Ingrese dirección comercial">
                    <label id="dirpri-error" class="error" for="dirpri"></label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="telpri" class="form-control-label">Teléfono comercial:</label>
                    <input type="text" name="telpri" class="form-control" placeholder="Ingrese teléfono comercial">
                    <label id="telpri-error" class="error" for="telpri"></label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-primary" id='guardar_ficha'><i class="fas fa-save"></i> Guardar los cambios</button>
        </div>
    </form>
    </div>
</div>

<script>
    const _ID = {{$idModel}};
</script>
<script src="{{ asset('cajas/build/IndependienteEdita.js') }}"></script>
