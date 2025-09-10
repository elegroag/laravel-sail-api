<h4 class='text-primary'><%= nombre%> </h4>
<p>Detalle de el usuario externo de la plataforma de comfacaenlinea.com.co</p>

<form method="POST" action="#" id='formRequest'>
    <div class='row'>
        <div class='col-md-6 border-top border-right mb-1 border-left border-bottom'>
            <label class='form-control-label'>Tipo usuario</label>
            <% if (isEdit == -1) {%>
            <p class='pl-2 description'><%= tipo_detalle%></p>
            <%}else{%>
            <input class="form-control mb-1" id="tipo_detalle" name="tipo_detalle" type="text" value="<%=tipo_detalle%>" readonly />
            <%}%>
		</div>
		<div class='col-md-6 border-top border-right mb-1 border-left border-bottom'>
			<label class='form-control-label'>Tipo documento</label>
			<% if (isEdit == -1) {%>
            <p class='pl-2 description'><%= coddoc_detalle%> </p>
            <%}else{%>
            <div class="mb-1" id='component_coddoc'></div>
            <%}%>
		</div>
		<div class='col-md-6 border-top border-right mb-1 border-left border-bottom'>
			<label class='form-control-label'>Identificación</label>
			<% if (isEdit == -1) {%>
            <p class='pl-2 description'><%= documento%> </p>
            <%}else{%>
            <input class="form-control mb-1" id="documento" name="documento" type="text" value="<%=documento%>" readonly />
            <%}%>
		</div>
		<div class='col-md-6 border-top border-right mb-1 border-left border-bottom'>
			<label class='form-control-label'>Nombre</label>
			<% if (isEdit == -1) {%>
            <p class='pl-2 description'><%= nombre%> </p>
            <%}else{%>
            <input class="form-control mb-1" id="nombre" name="nombre" type="text" value="<%=nombre%>" style="text-transform: uppercase" />
            <%}%>
		</div>
		<div class='col-md-6 border-top border-right mb-1 border-left border-bottom'>
			<label class='form-control-label'>Email notificaciones</label>
			<% if (isEdit == -1) {%>
            <p class='pl-2 description'><%= email%> </p>
            <%}else{%>
            <input class="form-control mb-1" id="email" name="email" type="text" value="<%=email%>" style="text-transform: uppercase" />
            <%}%>
			<p class="description">Usa una dirección de email a la cual pueda acceder de forma recurrente, para consultar las notificaciones de procesos de afiliación.</p>
		</div>
		<div class='col-md-6 border-top border-right mb-1 border-left border-bottom'>
			<label class='form-control-label'>Fecha registro del usuario</label>
			<p class='pl-2 description'><%= fecreg%> </p>
        </div>
        <div class='col-md-6 border-top border-right mb-1 border-left border-bottom'>
            <label class='form-control-label'>Código ciudad</label>
            <% if (isEdit == -1) {%>
            <p class='pl-2 description'><%= codciu%></p>
            <%}else{%>
            <div class="mb-1" id='component_codciu'></div>
            <%}%>
		</div>
		<div class='col-md-6 border-top border-right mb-1 border-left border-bottom'>
			<label class='form-control-label'>Estado actual</label>
            <% if (isEdit == -1) {%>
            <p class='pl-2 description'><%= estado_detalle%></p>
            <%}else{%>
            <div class="mb-1" id='component_estado'></div>
            <%}%>
        </div>
        <div class='col-md-6 border-top border-right mb-1 border-left border-bottom'>
            <label class='form-control-label'>Clave actual</label>
            <% if (isEdit == -1) {%>
            <p class='pl-2 description'>XxxX. . . .</p>
            <%}else{%>
            <input class="form-control mb-1 disabled" id="clave" name="clave" type="password" value="<%=clave%>" disabled />
            <div class="text-right">
                <a type="button" class="link mb-4 text-info" data-has='N' id='bt_change_clave'>Cambiar clave de usuario</a>
            </div>
            <%}%>
		</div>
		<div class='col-md-6 border-top border-right mb-1 border-left border-bottom'>
			<div id="show_change_clave" class="d-none">
				<div class="row">
					<div class="col">
					<% if(isEdit == 1) {%>
            <label class='form-control-label'>Nueva clave</label>
            <div class="m-1">
                <input class="form-control mb-1" id="newclave1" name="newclave" placeholder="clave aquí" type="text" value="" />
            </div>
            <%}%>
			</div>
				</div>
				<div class="row">
					<div class="col">
							<div class="text-right">
							<a type="button" class="link mb-4 text-info mr-4" data-has='N' id='bt_nochange_clave'>No cambiar clave</a>
							<a type="button" class="link mb-4 text-info" data-has='N' id='bt_crea_clave'>Clave automatica</a>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row justify-content-center">
		<div class="col-auto mt-3">
			<% if (isEdit == -1) {%>
            <button type="button" class="btn btn-md btn-primary mb-4" id='bt_editar'>Editar Datos</button>
            <%}else{%>
            <button type="button" class="btn btn-md btn-success mb-4" id='bt_guardar'>Guardar</button>
            <button type="button" class="btn btn-md btn-default mb-4" id='bt_close'>Cerrar</button>
            <%}%>
		</div>
	</div>
</form>