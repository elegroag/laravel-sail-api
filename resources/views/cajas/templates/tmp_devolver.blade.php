<h4>Devolver</h4>
<p>Esta opcion es para rechazar a la empresa e informarle la causal del rechazo</p>
<br />
<div class="col-md-12">
    <div class='form-group pb-2'>
        <label class='label'> Motivo:</label>
        <select name="codest_devolver" class="form-control">
            <option value="">Seleccione un motivo</option>
            @foreach ($mercurio11 as $codest)
                <option value="{{ $codest->getCodest() }}">{{ $codest->getDetalle() }}</option>
            @endforeach
        </select>
    </div>
    <div class='form-group mt-2 pb-2'>
        <label class='label'> Campos para corregir:</label>
        <select class="js-basic-multiple" name="campos_corregir[]" id='campos_corregir' multiple="multiple">
            <%
            _.each(campos_disponibles, function(campo, kei){ %>
            <option value="<%= kei %>"><%= campo %></option>
            <% }) %>
        </select>
    </div>
    <div class='form-group mt-2 pb-2'>
        <label class='label'> NOTA:</label>
        <textarea class='form-control' id='nota_devolver' rows='3'></textarea>
    </div>
</div>
<div class="col-md-12">
    <div class="box form-group pt-3">
        <button data-cid='<%=id%>' type='button' class='btn btn-md btn-warning' style='width:200px' id='devolver_solicitud'>Devolver</button>
    </div>
</div>