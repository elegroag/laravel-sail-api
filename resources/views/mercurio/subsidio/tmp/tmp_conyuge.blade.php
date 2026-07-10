<% if (!conyuges || conyuges.length === 0) { %>
    <div class="nucleo-empty-state">
        <i class="fas fa-user-friends" aria-hidden="true"></i>
        <strong>No hay cónyuges registrados</strong>
        <span>No se encontró información de cónyuge asociada al trabajador.</span>
    </div>
<% } else { %>
    <% _.each(conyuges, function(conyuge) { %>
        <article class="nucleo-member-card">
            <div class="nucleo-member-title">
                <h3>
                    <%= conyuge.priape + ' ' + conyuge.segape + ' ' + conyuge.prinom + ' ' + conyuge.segnom %>
                </h3>
                <span class="nucleo-badge <%= conyuge.estado === 'A' ? 'nucleo-badge-success' : 'nucleo-badge-muted' %>">
                    <%= _estado[conyuge.estado] || conyuge.estado %>
                </span>
            </div>
            <div class="nucleo-field-grid">
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Documento</span>
                    <strong class="nucleo-field-value"><%= conyuge.cedcon %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Tipo relación</span>
                    <strong class="nucleo-field-value"><%= (conyuge.comper == 'S') ? 'Compañero permanente' : 'Ex-cónyuge' %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Compañero permanente</span>
                    <strong class="nucleo-field-value"><%= _comper[conyuge.comper] || conyuge.comper %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Fecha afiliación</span>
                    <strong class="nucleo-field-value"><%= conyuge.fecafi || '-' %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Fecha estado</span>
                    <strong class="nucleo-field-value"><%= conyuge.fecret || '-' %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Salario</span>
                    <strong class="nucleo-field-value"><%= conyuge.salario || '-' %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Dirección</span>
                    <strong class="nucleo-field-value"><%= conyuge.direccion || '-' %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Teléfono</span>
                    <strong class="nucleo-field-value"><%= conyuge.telefono || '-' %></strong>
                </div>
                <div class="nucleo-field">
                    <span class="nucleo-field-label">Tipo pago</span>
                    <strong class="nucleo-field-value"><%= _tippag[conyuge.tippag] || conyuge.tippag %></strong>
                </div>
            </div>
        </article>
    <% }); %>
<% } %>
