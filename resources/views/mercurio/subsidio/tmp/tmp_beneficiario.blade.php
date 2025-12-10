<% _.each(beneficiarios, function(beneficiario) { %>
<fieldset>
    <legend>Beneficiario <%=_parent[beneficiario.parent] %> </legend>
    <div class='col-auto'>
	    <div class='row g-3 mb-3'>
	        <div class="col-md-6 col-lg-4 mb-3">
	                <label class="form-control-label"><i class="fas fa-id-card text-muted me-1"></i>Documento</label>
	                <p class="pl-1 description"><%=beneficiario.documento%></p>
	            </div>
	            <div class="col-md-6 col-lg-4 mb-3">
	                <label class="form-control-label"><i class="fas fa-user text-muted me-1"></i>Nombre</label>
	                <p class="pl-1 description"><%=beneficiario.nombre%></p>
	            </div>
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-info-circle text-muted me-1"></i>Estado</label>
	                <p class="pl-1 description"><%=_estado[beneficiario.estado] %></p>
	            </div>
            <!-- 3x -->
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-calendar-check text-muted me-1"></i>Fecha Afiliacion</label>
	                <p class="pl-1 description"><%=beneficiario.fecafi%></p>
	            </div>
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-calendar-day text-muted me-1"></i>Fecha Estado</label>
	                <p class="pl-1 description"><%=beneficiario.fecret%></p>
	            </div>
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-users text-muted me-1"></i>Parentesco</label>
	                <p class="pl-1 description"><%=_parent[beneficiario.parent] %></p>
	            </div>
            <!-- 3x -->
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-calendar-alt text-muted me-1"></i>Fecha Nacimiento</label>
	                <p class="pl-1 description"><%=beneficiario.fecnac%></p>
	            </div>
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-briefcase text-muted me-1"></i>Capacidad Trabajo</label>
	                <p class="pl-1 description"><%=_captra[beneficiario.captra] %></p>
	            </div>
        </div>
    </div>
</fieldset>
<% }) %>