<fieldset>
    <legend>Afiliación principal</legend>
    <div class='col-auto'>
        <div class='row justify-content-between'>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">NIT</label>
                <p class="pl-1 description"><%=nit%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Empresa</label>
                <p class="pl-1 description"><%=razsoc%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Dispone de Giro</label>
                <p class="pl-1 description"><%=(giro == 'S') ? 'SI' : 'NO' %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Nombre</label>
                <p class="pl-1 description"><%=fullname%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Documento</label>
                <p class="pl-1 description"><%=cedtra%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Nombre</label>
                <p class="pl-1 description"><%=fullname%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Fecha Nacimiento</label>
                <p class="pl-1 description"><%=fecnac%></p>
            </div>
            <!-- 3x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Fecha Afiliacion</label>
                <p class="pl-1 description"><%=fecafi%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Fecha Estado</label>
                <p class="pl-1 description"><%=fecest%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Estado</label>
                <p class="pl-1 description"><%=_estado[estado] %></p>
            </div>
            <!-- 3x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Email</label>
                <p class="pl-1 description"><%=email%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Direccion</label>
                <p class="pl-1 description"><%=direccion%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Telefono</label>
                <p class="pl-1 description"><%=telefono%></p>
            </div>
            <!-- 3x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Ciudad</label>
                <p class="pl-1 description"><%=_codciu[codciu] %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Zona</label>
                <p class="pl-1 description"><%=_codciu[codzon] %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Salario</label>
                <p class="pl-1 description"><%=salario%></p>
            </div>
            <!-- 3x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Categoria</label>
                <p class="pl-1 description"><%=_codcat[codcat] %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Genero</label>
                <p class="pl-1 description"><%=_sexo[sexo] %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">NIT</label>
                <p class="pl-1 description"><%=nit%></p>
            </div>
            <!-- 3x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Razon Social</label>
                <p class="pl-1 description"><%=razsoc%></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Estado Civil</label>
                <p class="pl-1 description"><%=_estciv[estciv] %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Ciudad Nacimiento</label>
                <p class="pl-1 description"><%=_codciu[ciunac] %></p>
            </div>
            <!-- 3x -->
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Vivienda</label>
                <p class="pl-1 description"><%=_vivienda[vivienda] %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Rural</label>
                <p class="pl-1 description"><%=_rural[rural] %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Nivel Educativo</label>
                <p class="pl-1 description"><%=_nivedu[nivedu] %></p>
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>Multiafiliación</legend>
    <div class='col-auto'>
        <div class='row justify-content-between'>
            <% if(multiafiliacion === true) {%>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Empresa</label>
                <p class="pl-1 description"><%=multiafiliacion_empresa %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Sucursal</label>
                <p class="pl-1 description"><%=multiafiliacion_sucursal %></p>
            </div>

            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Fecha Afiliacion</label>
                <p class="pl-1 description"><%=multiafiliacion_fecafi %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Estado</label>
                <p class="pl-1 description"><%=_estado[multiafiliacion_estado] %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Salario</label>
                <p class="pl-1 description"><%=multiafiliacion_salario %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Giro</label>
                <p class="pl-1 description"><%=multiafiliacion_codgir %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Razon Social</label>
                <p class="pl-1 description"><%=multiafiliacion_razsoc %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Representante Legal</label>
                <p class="pl-1 description"><%=multiafiliacion_repleg %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Direccion</label>
                <p class="pl-1 description"><%=multiafiliacion_direccion %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Telefono</label>
                <p class="pl-1 description"><%=multiafiliacion_telefono %></p>
            </div>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <label class="form-control-label">Ciudad</label>
                <p class="pl-1 description"><%=_codciu[multiafiliacion_codciu] %></p>
            </div>
            <% }else {%>
            <div class="col-md-6 col-lg-4 border-left border-bottom border-right rounded-bottom">
                <p class="pl-1 description">No tiene multiafiliación</p>
            </div>
            <% } %>
        </div>
    </div>
</fieldset>