
<script id='tmp_filtro' type="text/template">
	<?= TagUser::filtro($campo_filtro, 'aplicar_filtro') ?>
</script>

<script id='tmp_list_header' type="text/template">
	<?= View::renderView("templates/tmp_list_header"); ?>
</script>

<script type="text/template" id='tmp_layout'>
	<?= View::renderView("templates/tmp_layout"); ?>
</script>

<script type="text/template" id='tmp_header'>
	<?= View::renderView("templates/tmp_header"); ?>
</script>

<script type="text/template" id='tmp_rechazar'>
	<?= View::renderView("templates/tmp_rechazar"); ?>
</script>

<script type="text/template" id='tmp_devolver'>
	<?= View::renderView("templates/tmp_devolver"); ?>
</script>

<script type="text/template" id="tmp_reaprobar">
	<?= View::renderView("templates/tmp_reaprobar"); ?>
</script>

<script type="text/template" id='tmp_info'>
	<?= View::renderView("templates/tmp_information"); ?>
</script>

<script type="text/template" id='tmp_aprobar'>
	<?= View::renderView("aprobaciontra/tmp/tmp_aprobar"); ?>
</script>

<script type='text/template' id='tmp_aportes'>
	<?= View::renderView("templates/tmp_aportes"); ?>
</script>

<script type="text/template" id='tmp_deshacer'>
	<?= View::renderView("templates/tmp_deshacer"); ?>
</script>

<script type="text/template" id='tmp_info_header'>
	<?= View::renderView("templates/tmp_info_header"); ?>
</script>

<script type='text/template' id='tmp_layout_trabajador'>
	<div class="row justify-content-between">
		<div class="col-md-12" id='show_trabajador'></div>
		<div class="col-md-6" id='show_trayectoria'></div>
		<div class="col-md-6" id='show_salario'></div>
	</div>
</script>

<script type='text/template' id='tmp_trabajador'>
	<h5>Trabajador SISU</h5>
    <div class='row pl-lg-4 pb-3'>
        <div class='col-md-4 border-bottom border-top border-left'>
            <label class='form-control-label'>Cedtra:</label>
            <p class='descripcion'><%=cedtra%></p>
        </div>
        <div class='col-md-4 border-top border-bottom border-right border-left'>
            <label class='form-control-label'>Nombre:</label>
            <p class='descripcion'><%=prinom%> <%=segnom%> <%=priape%> <%=segape%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left border-top'>
            <label class='form-control-label'>Nit:</label>
            <p class='descripcion'><%=nit%></p>
        </div>
        <div class='col-md-4 border-bottom border-left'>
            <label class='form-control-label'>Fecha sistema: </label>
            <p class='descripcion'><%=fecsis%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Estado: </label>
            <p class='descripcion'><%=(estado == 'A' || estado == 'D')? 'Activo': 'Inactivo'%></p>
        </div>
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Fecha estado:</label>
            <p class='descripcion'><%=fecest%></p>
        </div>
        <div class='col-md-4 border-bottom border-left'>
            <label class='form-control-label'>Teléfono: </label>
            <p class='descripcion'><%=telefono%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Email: </label>
            <p class='descripcion'><%=email%></p>
        </div>
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Lista: </label>
            <p class='descripcion'><%=codlis%></p>
        </div>
        <div class='col-md-4 border-bottom border-left'>
            <label class='form-control-label'>Sucursal: </label>
            <p class='descripcion'><%=codsuc%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Tipo pago: </label>
            <p class='descripcion'><%=tippag%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Giro: </label>
            <p class='descripcion'><%=giro%></p>
        </div>
        <div class='col-md-4 border-bottom border-left'>
            <label class='form-control-label'>Fecha giro: </label>
            <p class='descripcion'><%=fecha_giro%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Fecha afiliación: </label>
            <p class='descripcion'><%=fecafi%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Ciudad laboral: </label>
            <p class='descripcion'><%=ciulab%></p>
        </div>
    </div>
</script>

<script type='text/template' id='tmp_trayectoria'>
	<h5>Trayectoria</h5>
    <div class='row pl-lg-4 pb-3'>
        <% if(_.size(trayectorias) == 0){ %>
            <table class='table table-bordered table-hover'>
                <tbody>
                    <tr>
                        <td>Ninguna dato de trayectoria disponible...</td>
                    </tr>
                </tbody>
            </table>
        <% }else{
        _ai=1
        _.each(trayectorias, function(row, ai){ %>
            <table class='table table-bordered table-hover'>
            <tbody>
            <tr>
                <td rowspan='4' width='10pt' style="padding:2px"><%=_ai%></td>
                <td>Fecha inicia: <%=row.fecafi%></td>
            </tr>
            <tr>
                <td>Nit: <%=row.nit%></td>
            </tr>
            <tr>
                <td>sucursal: <%=row.codsuc%></td>
            </tr>
            <tr>
                <td>Fecha retiro: <%=(!!row.fecret)? row.fecret : 'X'%></td>
            </tr>
            </tbody>
        </table>
        <br/>
        <% _ai++ })} %>
    </div>
</script>

<script type='text/template' id='tmp_salario'>
	<h5>Salarios</h5>
    <div class='row pl-lg-4 pb-3'>
        <%  if(_.size(salarios) == 0){ %>
        <table class='table table-bordered table-hover'>
            <tbody>
                <tr>
                    <td>Ninguna dato de salarios disponible...</td>
                </tr>
            </tbody>
        </table>
        <% }else{
        _ai=1
        _.each(salarios, function(row, ai){ %>
        <table class='table table-bordered table-hover'>
            <tbody>
                <tr>
                    <td rowspan='2' width='10pt' style="padding:2px"><%=_ai%></td>
                    <td>Fecha: <%=row.fecha%></td>
                    <td>Salario: <%=row.salario%></td>
                </tr>
            </tbody>
        </table>
        <br/>
        <% _ai++ })} %>
    </div>
</script>

<script type="text/template" id='tmp_table'>
	<div id='consulta' class='table-responsive'></div>
	<div id='paginate' class='card-footer py-4'></div>
	<div id='filtro'></div>
</script>

<div id='boneLayout'></div>

<?= Tag::javascriptInclude('Cajas/trabajadores/build.trabajadores'); ?>