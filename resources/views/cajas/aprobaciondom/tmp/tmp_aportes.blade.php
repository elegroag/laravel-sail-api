<?
$assets = Core::getInstancePath() . 'javascript/core/';
echo Tag::stylesheetLink('datatables/css/jquery.dataTables.min');
echo Tag::stylesheetLink('datatables/css/buttons.dataTables.min');
echo Tag::javascriptInclude('core/datatables/js/jquery.dataTables.min');
echo Tag::javascriptInclude('core/datatables/js/dataTables.buttons.min');
echo Tag::javascriptInclude('core/datatables/js/buttons.colVis');
echo Tag::javascriptInclude('core/datatables/js/buttons.html5');
echo Tag::javascriptInclude('core/datatables/js/buttons.print');
echo View::getContent();
?>

<script id='tmp_card_header' type="text/template">
    <div class='nav' id='submenu'>
        <div id="botones" class='row justify-content-end'>
            <a type='button' href='#' data-href="empresa_sisuweb/<%=idEmpresa%>/<%=cedtra%>" class='btn btn-sm btn-success' data-toggle="linkopt">
                <i class=''></i> Empresa Sisuweb</a>&nbsp;
            <a type='button' href='#' data-href="editar_ficha/<%=idEmpresa%>" class='btn btn-sm btn-warning' data-toggle="linkopt">
                Editar Ficha Empleador</a>&nbsp;
            <a type='button' href='#' data-href="info_empresa/<%=idEmpresa%>" class='btn btn-sm btn-primary' data-toggle="linkopt">
                Volver</a>&nbsp;   
        </div>
    </div>
</script>

<div class='card-header pt-2 pb-2' id='afiliacion_header'></div>

<div class='card-body'>
    <div class="row">
        <div class="col" id='show_aportes'></div>
    </div>
</div>

<script id='tmp_aportes' type='text/template'>
    <div class='pb-3'>        
        <% if(_.size(aportes) == 0){ %>
            <table class='table table-bordered table-hover' id='table_aportes'>
                <tbody>
                    <tr>
                        <td>Ningún registro de pago de aportes disponible...</td>
                    </tr>
                </tbody>
            </table>
        <% }else{
        _ai=1
        %>
        <table class='table table-sm align-items-center table-flush' id='table_aportes' width='100%'>
            <thead>
                <tr>
                    <td>Periodo aportes</td>
                    <td>Fecha recibo</td>
                    <td>Fecha sistema</td>
                    <td>Cedula trabajador</td>
                    <td>Sucursal</td>
                    <td>Nit</td>
                    <td>Número</td>    
                    <td>Valor aportes</td>
                    <td>Valor nomina</td>
                </tr>
            </thead>
            <tbody>
            <% _.each(aportes, function(row, ai){ %>
                <tr>
                    <td><%=row.perapo%></td>    
                    <td><%=row.fecrec%></td>
                    <td><%=row.fecsis%></td>    
                    <td><%=row.cedtra%></td>
                    <td><%=row.codsuc%></td>
                    <td><%=row.cedtra%></td>
                    <td><%=row.numero%></td>        
                    <td><%=row.valapo%></td>
                    <td><%=row.valnom%></td>
                </tr>
            <% _ai++ })} %>
            </tbody>
            </table>
        <br/>
    </div>    
</script>

<?= Tag::addJavascript('Cajas/src/ServicioDomesticos/aportes_view', true); ?>