<%
var esObligatorio = (obliga === 'S') || (typeof obliga === 'string' && obliga.indexOf('Obligatorio') !== -1);
var textoNota = (nota && String(nota).trim() !== '') ? String(nota).trim() : '';

if(auto_generado == '1') {
%>
<div class="doc-adjunto-item__info">
    <a href="#" type="button" class="doc-adjunto-link" toggle-event="show" data-href="<%= diponible %>">
        <span class="btn-inner--icon"><i class="fas fa-file-download"></i></span>
        <span class="btn-inner--text"><%= detalle %></span>
    </a>
    <p class="doc-adjunto-nota mb-0">
        <span class="doc-adjunto-badge <%= esObligatorio ? 'doc-adjunto-badge--required' : 'doc-adjunto-badge--optional' %>">
            <%= esObligatorio ? 'Obligatorio' : 'Opcional' %>
        </span>
        <% if (textoNota) { %>
        <span class="doc-adjunto-nota__text"><%- textoNota %></span>
        <% } else { %>
        <span class="doc-adjunto-nota__text">Adjunto generado automáticamente (firma digital).</span>
        <% } %>
    </p>
</div>
<div class="doc-adjunto-item__action">
    <span class="doc-adjunto-status doc-adjunto-status--auto">Generado automáticamente</span>
</div>

<% } else { 
    if (diponible) { 
%>
<div class="doc-adjunto-item__info">
    <a href="#" type="button" class="doc-adjunto-link" toggle-event="show" data-href="<%= diponible %>">
        <span class="btn-inner--icon"><i class="fas fa-file-download"></i></span>
        <span class="btn-inner--text"><%= detalle %></span>
    </a>
    <%= (corrige)? '<span class="text-warning doc-adjunto-corrige">Archivo por devolución</span>' : '' %>
    <p class="doc-adjunto-nota mb-0">
        <span class="doc-adjunto-badge <%= esObligatorio ? 'doc-adjunto-badge--required' : 'doc-adjunto-badge--optional' %>">
            <%= esObligatorio ? 'Obligatorio' : 'Opcional' %>
        </span>
        <% if (textoNota) { %>
        <span class="doc-adjunto-nota__text"><%- textoNota %></span>
        <% } %>
    </p>
</div>
<div class="doc-adjunto-item__action">
<% if (puede_borrar) { %>
    <button class="btn btn-icon btn-danger btn-sm btn-outline-danger" type="button" toggle-event="borrar" data-id="<%= id %>" data-coddoc="<%= coddoc %>">
        <span class="btn-inner--icon"><i class="fas fa-trash-alt"></i> Borrar</span>
    </button>
<% } else { %>
    <span class="doc-adjunto-status">Sin acción requerida</span>
<% } %>
</div>

<% } else { %>
<div class="doc-adjunto-item__info">
    <p class="doc-adjunto-title mb-0"><%= detalle %></p>
    <p class="doc-adjunto-nota mb-0">
        <span class="doc-adjunto-badge <%= esObligatorio ? 'doc-adjunto-badge--required' : 'doc-adjunto-badge--optional' %>">
            <%= esObligatorio ? 'Obligatorio' : 'Opcional' %>
        </span>
        <% if (textoNota) { %>
        <span class="doc-adjunto-nota__text"><%- textoNota %></span>
        <% } %>
    </p>
</div>
<div class="doc-adjunto-item__action">
    <div class="doc-dropzone" data-coddoc="<%= coddoc %>" data-id="<%= id %>" toggle-event="dropzone">
        <input type="file" class="doc-dropzone__input" toggle-event="change" data-coddoc="<%= coddoc %>" id="archivo_<%= coddoc %>" name="archivo_<%= coddoc %>" accept="application/pdf" />
        <div class="doc-dropzone__content">
            <i class="fas fa-cloud-upload-alt doc-dropzone__icon"></i>
            <p class="doc-dropzone__title mb-1">Arrastra el PDF o haz clic</p>
            <p class="doc-dropzone__hint mb-0 toogle-show-name" data-code="<%= coddoc %>">Carga automática</p>
        </div>
        <div class="doc-dropzone__loading d-none" data-loading="<%= coddoc %>">
            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
            <span class="ms-2">Cargando...</span>
        </div>
    </div>
</div>
<% } %>
<% } %>
