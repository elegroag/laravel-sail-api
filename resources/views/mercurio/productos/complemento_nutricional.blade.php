
<script type="text/template" id='tmp_registros'>
    <tr>
        <td>
            <% if(hasPin == 1){%>
                <button type="button" toggle='buscar' data-cid='<%=id%>' data-docu='<%=docben%>' class="btn btn-md btn-info">APLICADO</button>
            <%}else{ %>
                <button type="button" toggle='aplica' data-cid='<%=id%>' data-docu='<%=docben%>' class="btn btn-md btn-primary">APLICAR AQUÍ</button>
            <% }%>
        </td>
        <td><%=nomben%></td>
        <td><%=docben%></td>
        <td><%=categoria%></td>
    </tr>
</script>

<div class="col mt-2">
    <div class="card">
        <div class='card-header' id='afiliacion_header'>
            <div id="botones" class="nav justify-content-end">
                <a href="<?= $instancePath ?>principal/index" class='btn btn-sm btn-primary'><i class="fas fa-home fa-2x"></i> Salir</a>&nbsp;
            </div>
            <h4>COMPLEMENTO NUTRICIONAL</h4>
            <p>Adquiere el complemento nutricional para sus hijos, por una unica tarifa. Para más información puede ingresar al siguiente link y conocer los requisitos del producto:
                <br />
                <a href="https://comfaca.com/complemento-nutricional" target="blank">Información Complemento Nutricional COMFACA</a>
            </p>
        </div>

        <div class="card-body d-flex">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-items-center mb-0 mt-0" id='datatable' style="width:100%">
                                    <thead>
                                        <tr>
                                            <th width='10%'>Solicita PIN</th>
                                            <th width='70%'>Nombre Beneficiario</th>
                                            <th width='5%'>Documento</th>
                                            <th width='5%'>Categoria</th>
                                        </tr>
                                    </thead>
                                    <tbody id='showRegistros'>
                                        <tr>
                                            <td colspan="4">Procesando consulta de datos...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer" style="display:none" id='show_access_davivienda'>
                                <a class="text-left text-primary" href="https://portalpagos.davivienda.com/#/comercio/5910/COLEGIO%20COMFACA" target="blank">
                                    <?= Tag::image("Mercurio/davivienda.png", "class: navbar-brand-img img-responsive", "style: width:120px"); ?>
                                    <p><i class="ni ni-active-40 fa-2x" aria-hidden="true"></i> <b>Pago Seguro Con Davivienda</b>
                                </a> <br />
                                En el campo IDENTIFICACIÓN ingresa el PIN asignado desde la Plataforma Comfaca En Línea.<br />Captura el PIN en la opción APLICADO del beneficiario activo.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <p class="text-center">
                                <button type="button" class="btn btn-md btn-primary btn-icon-only rounded-circle" style="width:5rem; height:5rem">
                                    <span class="btn-inner--icon fa-2x"><b id='showNumCupos'><?= $cupos_disponibles ?></b></span>
                                </button>
                                <span><b>CUPOS DISPONIBLES</b></span>
                            </p>
                        </div>
                        <div class="card-body">
                            <p class="text-center"><?= Tag::image("Mercurio/complemento.jpg", "class: navbar-brand-img img-responsive", "style: width:250px"); ?></p><br>
                            <p>Los cupos estan disponibles para toda la población del departamento del CAQUETA, qué cumplan con los requisitos del producto.</p>
                            <p>El programa de Salud y Nutrición, va dirigido para las madres gestantes y los niños que aún no hayan cumplido los 6 años. Los cupos son limitados.<br />Para más información puede ingresar al siguiente link:<br />
                                <a href="https://comfaca.com/complemento-nutricional" target="blank">Información Complemento Nutricional COMFACA</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    const CODSER = "{{ $codser }}";
</script>
<script src="{{ asset('mercurio/ComplementoNutricional.js') }}"></script>
