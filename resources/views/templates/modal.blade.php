<div class="modal fade" id="modal_generic" tabindex="-1" role="dialog" aria-labelledby="notice" aria-hidden="true">
    <div class="modal-dialog" id='size_modal_generic'>
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card mb-0" id="show_modal_generic"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalComponent" tabindex="-1" role="dialog" aria-labelledby="notice" aria-hidden="true">
    <div class="modal-dialog modal-notice">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="nc-icon nc-simple-remove"></i>
                </button>
                <h5 class="modal-title" id='mdl_set_title'></h5>
            </div>
            <div class="modal-body" id='mdl_set_body'></div>
            <div class="modal-footer justify-content-center" id='mdl_set_footer'>
                <button type="button" class="btn btn-info btn-round" data-dismiss="modal" id='mdl_set_button'>Continuar!</button>
            </div>
        </div>
    </div>
</div>

<script type="text/template" id="tmp_direction">
    <div class="card-header bg-primary">
		<div class="row">
			<div class="col">
				<h4 class="mb-0 text-white">Detalles dirección</h4>
			</div>
		</div>
	</div>
	<div class="card-body">
		<label class='mb-3'>Ejemplo CALLE 4 01 CENTRO</label>
        <input type='text' id='tagname' name='tagname' class='d-none'/>
		<div class="row align-items-center">
			<div class="col-md-3">
				<label class='form-label'>Zona</label>
				<select class="form-control mb-2" id="address_zona" name="address_zona">
					<option value="">Seleccionar aquí...</option>	
					<option value="R">Rural</option>
					<option value="U">Urbana</option>
				</select>
			</div>
			
            <div class="col-md-4">
				<label class='form-label'>Ubicación</label>
				<select class="form-control mb-2" id="address_one" name="address_one" disabled>
					<% _.each(adress, function(adres){ %>
						<option value="<%=adres.estado%>"><%=adres.detalle%></option>
					<% })%>
				</select>
				<label id="address_one-error" class="error" for="address_one"></label>
			</div>

			<div class="col-md-2" id="show_address_two">
				<label class='form-label' id='address_nombre_optional'>Número:</label>
				<div class="input-group mb-2">
					<input type="text" class="form-control" id="address_two" name="address_two" data-toggle="valida_caracteres">
				</div>
			</div>
			
            <div class="col-md-2" id="show_address_four">
				<label class='form-label'>Número:</label>
				<div class="input-group mb-2">	
					<input type="text" class="form-control" id="address_four" name="address_four" data-toggle="valida_caracteres">
				</div>
			</div>
			<div class="col-md-3" id="address_barrio" style="display:none">
				<label class='form-label'>Barrio o Zona:</label>
				<div class="input-group mb-2">
					<input type="text" class="form-control" id="address_five" name="address_five" data-toggle="valida_caracteres">
				</div>
			</div>
		</div>
        <div class='row'>
           <div class='col'>
             <p class='text-center'>
                <button type="button" class="btn btn-primary border-0" id='button_address_modal'>Asignar Dirección</button>
                <button type="button" class="btn btn-danger border-0" data-dismiss="modal">Cerrar</button>
            </p>
           </div>
        </div>
	</div>
</script>