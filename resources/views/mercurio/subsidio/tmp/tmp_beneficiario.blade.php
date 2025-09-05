<% _.each(beneficiarios, function(beneficiario) { %>
<fieldset>
    <legend>Beneficiario <%=_parent[beneficiario.parent] %> </legend>
    <div class='col-auto'>
        <div class='row justify-content-between'>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Documento</label>
                <p class="pl-1 description"><%=beneficiario.documento%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Nombre</label>
                <p class="pl-1 description"><%=beneficiario.nombre%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Estado</label>
                <p class="pl-1 description"><%=_estado[beneficiario.estado] %></p>
            </div>
            <!-- 3x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Fecha Afiliacion</label>
                <p class="pl-1 description"><%=beneficiario.fecafi%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Fecha Estado</label>
                <p class="pl-1 description"><%=beneficiario.fecret%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Parentesco</label>
                <p class="pl-1 description"><%=_parent[beneficiario.parent] %></p>
            </div>
            <!-- 3x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Fecha Nacimiento</label>
                <p class="pl-1 description"><%=beneficiario.fecnac%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Capacidad Trabajo</label>
                <p class="pl-1 description"><%=_captra[beneficiario.captra] %></p>
            </div>
        </div>
    </div>
</fieldset>
<% }) %>