<% if (!beneficiarios || beneficiarios.length === 0) { %>
    <div class="nucleo-empty-state">
        <i class="fas fa-child" aria-hidden="true"></i>
        <strong>No hay beneficiarios registrados</strong>
        <span>No se encontró información de beneficiarios asociados al trabajador.</span>
    </div>
<% } else { %>
    <% _.each(beneficiarios, function(beneficiario) { %>
        <article class="nucleo-member-card">
            <div class="nucleo-member-title">
                <h3><%= beneficiario.nombre %></h3>
                <span class="nucleo-badge <%= beneficiario.estado === 'A' ? 'nucleo-badge-success' : 'nucleo-badge-muted' %>">
                    <%= _estado[beneficiario.estado] || beneficiario.estado %>
                </span>
            </div>
            <div class="nucleo-field-grid">
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Documento</span>
                    <strong class="nucleo-field-value"><%= beneficiario.documento %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Parentesco</span>
                    <strong class="nucleo-field-value"><%= _parent[beneficiario.parent] || beneficiario.parent %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Fecha afiliación</span>
                    <strong class="nucleo-field-value"><%= beneficiario.fecafi || '-' %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Fecha estado</span>
                    <strong class="nucleo-field-value"><%= beneficiario.fecret || '-' %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Fecha nacimiento</span>
                    <strong class="nucleo-field-value"><%= beneficiario.fecnac || '-' %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Capacidad de trabajo</span>
                    <strong class="nucleo-field-value"><%= _captra[beneficiario.captra] || beneficiario.captra %></strong>
                </div>
            </div>
        </article>
    <% }); %>
<% } %>
