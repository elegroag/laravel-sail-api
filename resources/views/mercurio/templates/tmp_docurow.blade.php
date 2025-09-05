<%
if(auto_generado == '1') {
%>
<td>
    <a href="#" type='button' class='p-2' toggle-event='show' data-href='<%= diponible %>'>
        <span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>
        <span class='btn-inner--text'><%= detalle %> <%= obliga %></span>
    </a>
</td>
<td colspan='2'>
    <p class="p-1 text-gray">Adjunto posee firma digital.</p>
</td>

<% } else { 
    if (diponible) { 
%>

<td>
    <a href="#" type='button' class='p-2' toggle-event='show' data-href='<%= diponible %>'>
        <span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>
        <span class='btn-inner--text'><%= detalle %> <%= obliga %></span>
    </a>
    <%= (corrige)? '<span class="text-warning">Archivo por devolución</span>' : '' %>
</td>

<% if (puede_borrar) { 
    %>
<td>
    <button class='btn btn-icon btn-danger btn-sm btn-outline-danger' type='button' toggle-event='borrar' data-id='<%= id %>' data-coddoc='<%= coddoc %>'>
        <span class='btn-inner--icon'>
            <i class='fas fa-save'></i> Borrar
        </span>
    </button>
</td>
<% } else { %>
<td colspan='2'>
    <p class="p-1 text-gray">No se requiere de ninguna acción</p>
</td>
<% } 
} else {  
%>
<td>
    <p><%= detalle %> <%= obliga %></p>
</td>
<td>
    <div class='custom-file'>
        <input type='file' class='custom-file-input form-control border-0' toggle-event='change' data-coddoc='<%= coddoc %>' id='archivo_<%= coddoc %>' name='archivo_<%= coddoc %>' accept='application/pdf, image/*' />
        <label class='custom-file-label toogle-show-name border-0 text-primary' for='customFileLang' data-code='<%= coddoc %>'>
            Selecciona y carga aquí...</label>
    </div>
</td>
<td>
    <button class='btn btn-icon btn-primary btn-sm btn-outline-primary mt-2' type='button' toggle-event='salvar' data-id='<%= id %>' data-coddoc='<%= coddoc %>'>
        <span class='btn-inner--icon'>
            <i class='fas fa-save'></i>
            Guardar</span>
    </button>
</td>

<% } %>
<% } %>