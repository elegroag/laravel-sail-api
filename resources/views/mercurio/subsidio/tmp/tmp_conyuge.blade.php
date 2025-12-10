<% _.each(conyuges, function(conyuge) { %>
<fieldset>
    <legend>Conyuge <%=(conyuge.comper == 'S') ? 'Compañero Permanente' : 'Ex-Conyuge' %></legend>
    <div class='col-auto'>
	    <div class='row g-3'>
	        <div class="col-md-6 col-lg-4 mb-3">
	                <label class="form-control-label"><i class="fas fa-id-card text-muted me-1"></i>Documento</label>
	                <p class="pl-1 description"><%=conyuge.cedcon%></p>
	            </div>
	            <div class="col-md-6 col-lg-4 mb-3">
	                <label class="form-control-label"><i class="fas fa-user text-muted me-1"></i>Nombre</label>
	                <p class="pl-1 description"><%=conyuge.priape + " " + conyuge.segape + " " + conyuge.prinom + " " + conyuge.segnom%></p>
	            </div>
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-info-circle text-muted me-1"></i>Estado</label>
	                <p class="pl-1 description"><%=_estado[conyuge.estado] %></p>
	            </div>
            <!-- 3x -->
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-calendar-check text-muted me-1"></i>Fecha Afiliacion</label>
	                <p class="pl-1 description"><%=conyuge.fecafi%></p>
	            </div>
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-calendar-day text-muted me-1"></i>Fecha Estado</label>
	                <p class="pl-1 description"><%=conyuge.fecret%></p>
	            </div>
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-money-bill-wave text-muted me-1"></i>Salario</label>
	                <p class="pl-1 description"><%=conyuge.salario%></p>
	            </div>
            <!-- 3x -->
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-map-marker-alt text-muted me-1"></i>Direccion</label>
	                <p class="pl-1 description"><%=conyuge.direccion%></p>
	            </div>
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-phone text-muted me-1"></i>Telefono</label>
	                <p class="pl-1 description"><%=conyuge.telefono%></p>
	            </div>
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-credit-card text-muted me-1"></i>Tipo Pago</label>
	                <p class="pl-1 description"><%=_tippag[conyuge.tippag] %></p>
	            </div>
            <!-- 1x -->
	            <div class="col-md-6 col-lg-4">
	                <label class="form-control-label"><i class="fas fa-user-friends text-muted me-1"></i>Compañero Permanente</label>
	                <p class="pl-1 description"><%=_comper[conyuge.comper] %></p>
	            </div>
        </div>
    </div>
</fieldset>

<% }) %>