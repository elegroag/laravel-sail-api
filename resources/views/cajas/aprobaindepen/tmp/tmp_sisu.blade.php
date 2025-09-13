<h4>Empresa Registrada</h4>
<div class='row pl-lg-4 pb-3'>
    <div class='col-md-3 border-top border-bottom border-right border-left'>
        <label class='form-control-label'>Nit:</label>
        <p class='descripcion'><%=nit%></p>
    </div>
    <div class='col-md-4 border-bottom border-top'>
        <label class='form-control-label'>Razsoc: </label>
        <p class='descripcion'><%=razsoc%></p>
    </div>
    <div class='col-md-4 border-bottom border-top border-right border-left'>
        <label class='form-control-label'>Representante: </label>
        <p class='descripcion'><%=repleg%></p>
    </div>

    <div class='col-md-3 border-bottom border-right border-left'>
        <label class='form-control-label'>Fecha afiliación:</label>
        <p class='descripcion'><%=fecafi%></p>
    </div>
    <div class='col-md-4 border-bottom'>
        <label class='form-control-label'>Fecha sistema: </label>
        <p class='descripcion'><%=fecsis%></p>
    </div>
    <div class='col-md-4 border-bottom border-right border-left'>
        <label class='form-control-label'>Estado: </label>
        <p class='descripcion'><%=(estado == 'A' || estado == 'D')? 'Activo': 'Inactivo'%></p>
    </div>

    <div class='col-md-3 border-bottom border-right border-left'>
        <label class='form-control-label'>Code actividad:</label>
        <p class='descripcion'><%=codact%></p>
    </div>
    <div class='col-md-4 border-bottom'>
        <label class='form-control-label'>Fecha estado:</label>
        <p class='descripcion'><%=fecest%></p>
    </div>
    <div class='col-md-4 border-bottom border-right border-left'>
        <label class='form-control-label'>Tipo persona: </label>
        <p class='descripcion'><%=tipper%></p>
    </div>

    <div class='col-md-3 border-bottom border-right border-left'>
        <label class='form-control-label'>Tipo empresa:</label>
        <p class='descripcion'><%=tipemp%></p>
    </div>
    <div class='col-md-4 border-bottom'>
        <label class='form-control-label'>Total trabajadores:</label>
        <p class='descripcion'><%=tottra%></p>
    </div>
    <div class='col-md-4 border-bottom border-right border-left'>
        <label class='form-control-label'>Digito verificación: </label>
        <p class='descripcion'><%=digver%></p>
    </div>

    <div class='col-md-3 border-bottom border-right border-left'>
        <label class='form-control-label'>Calemp:</label>
        <p class='descripcion'><%=calemp%></p>
    </div>
    <div class='col-md-4 border-bottom'>
        <label class='form-control-label'>Teléfono: </label>
        <p class='descripcion'><%=telefono%></p>
    </div>
    <div class='col-md-4 border-bottom border-right border-left'>
        <label class='form-control-label'>Email: </label>
        <p class='descripcion'><%=email%></p>
    </div>
</div>
