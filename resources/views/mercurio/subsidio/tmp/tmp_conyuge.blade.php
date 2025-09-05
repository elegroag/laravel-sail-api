<% _.each(conyuges, function(conyuge) { %>
<fieldset>
    <legend>Conyuge <%=(conyuge.comper == 'S') ? 'Compañero Permanente' : 'Ex-Conyuge' %></legend>
    <div class='col-auto'>
        <div class='row justify-content-between'>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Documento</label>
                <p class="pl-1 description"><%=conyuge.cedcon%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Nombre</label>
                <p class="pl-1 description"><%=conyuge.priape + " " + conyuge.segape + " " + conyuge.prinom + " " + conyuge.segnom%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Estado</label>
                <p class="pl-1 description"><%=_estado[conyuge.estado] %></p>
            </div>
            <!-- 3x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Fecha Afiliacion</label>
                <p class="pl-1 description"><%=conyuge.fecafi%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Fecha Estado</label>
                <p class="pl-1 description"><%=conyuge.fecret%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Salario</label>
                <p class="pl-1 description"><%=conyuge.salario%></p>
            </div>
            <!-- 3x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Direccion</label>
                <p class="pl-1 description"><%=conyuge.direccion%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Telefono</label>
                <p class="pl-1 description"><%=conyuge.telefono%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Tipo Pago</label>
                <p class="pl-1 description"><%=_tippag[conyuge.tippag] %></p>
            </div>
            <!-- 1x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Compañero Permanente</label>
                <p class="pl-1 description"><%=_comper[conyuge.comper] %></p>
            </div>
        </div>
    </div>
</fieldset>

<% }) %>