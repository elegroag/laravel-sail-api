<script id='tmp_beneficiario' type='text/template'>
    <div class='col-md-12'> 
        <h4>Beneficiario</h4>        
    </div>

    <div class='row pl-lg-4 pb-3'>
        <div class='col-md-4 border-bottom border-left border-top'>
            <label class='form-control-label'>Tipo documento:</label>
            <p class='descripcion'><%=coddoc%></p>
        </div>
        <div class='col-md-4 border-bottom border-top border-left'>
            <label class='form-control-label'>Documento:</label>
            <p class='descripcion'><%=documento%></p>
        </div>
        <div class='col-md-4 border-top border-bottom border-right border-left'>
            <label class='form-control-label'>Nombre:</label>
            <p class='descripcion'><%=prinom%> <%=segnom%> <%=priape%> <%=segape%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Estado: </label>
            <p class='descripcion'><%=(estado == 'A' || estado == 'D')? 'Activo': 'Inactivo'%></p>
        </div>
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Fecha estado:</label>
            <p class='descripcion'><%=fecest%></p>
        </div>
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Ciudad nacimiento:</label>
            <p class='descripcion'><%=ciunac%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Sexo:</label>
            <p class='descripcion'><%=sexo%></p>
        </div>
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Usuario:</label>
            <p class='descripcion'><%=usuario%></p>
        </div>
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Giro:</label>
            <p class='descripcion'><%=giro%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Código estado:</label>
            <p class='descripcion'><%=codest%></p>
        </div>
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Huerfano:</label>
            <p class='descripcion'><%=huerfano%></p>
        </div>        
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Parentesco:</label>
            <p class='descripcion'><%=parent%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Tipdis:</label>
            <p class='descripcion'><%=tipdis%></p>
        </div>
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Fecnac:</label>
            <p class='descripcion'><%=fecnac%></p>
        </div>
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Capacidad de trabajar:</label>
            <p class='descripcion'><%=captra%></p>
        </div>
    </div>
</script>

<script id='tmp_relaciones' type='text/template'>
    <h4>Relaciones</h4>        
    <div class='row pl-lg-4 pb-3'>        
        <% if(_.size(relaciones) == 0){ %>
        <table class='table table-bordered table-hover'>
            <tbody>
                <tr>
                    <td>Ninguna dato de trayectoria disponible...</td>
                </tr>
            </tbody>
        </table>
        <% } else { %>
            <table class='table table-bordered table-hover'>
                <thead>
                    <tr>
                        <th>Cedula trabajador</th>
                        <th>Conyuge trabajador</th>
                        <th>Fecha afiliación</th>
                        <th>Fecha presentación</th>
                        <th>Fecha sistema</th>
                        <th>Pago</th>
                        <th>Ruaf</th>
                    </tr>
                </thead>    
                <tbody>
                    <% _.each(relaciones, function(row){ %>
                        <tr>
                            <td><%=row.cedtra%></td>
                            <td><%=row.cedcon%></td>
                            <td><%=row.fecafi%></td>
                            <td><%=row.fecpre%></td>
                            <td><%=row.fecsis%></td>
                            <td><%=row.pago%></td>
                            <td><%=row.ruaf%></td>
                        </tr>
                    <% }) %>
                </tbody>
            </table>
        <% } %>
    </div>    
</script>

<script id='tmp_card_header' type="text/template">
    <div class='row'>
        <div class='col-md-4'>
            <div class='row justify-content-first' style='padding-top:10px'>    
                <h3>&nbsp;Opción Informativa</h3>
            </div>
        </div>
        <div class='col-md-8'>
            <div id="botones" class='row justify-content-end'>
                <a href="#" data-href="info_beneficiario/<%=cedtra+'/'+documento+'/'+id%>" class='btn btn-sm btn-primary' id='cancelar_volver'>
                <i class='fas fa-hand-point-up text-white'></i> Salir</a>&nbsp;   
            </div>
        </div>
    </div>
</script>

<div class="card-body">
    <div class="row">
        <div class="col-md-12" id='show_beneficiario'></div>
        <div class="col-md-12" id='show_relaciones'></div>
    </div>
</div>

<script type="text/javascript">
    var _SOLICITUD = <?= json_encode($mercurio34) ?>;
    var _BENEFICIARIO_SISU = <?= json_encode($beneficiario) ?>;
    var _RELACIONES = <?= json_encode($relaciones) ?>;
</script>

<?= Tag::addJavascript('Cajas/src/Beneficiarios/buscar_sisu_view', true); ?>