<% if(tipo == 'afiliacion'){ %>
    <a href="<%= url%>">
        <div class="company-affiliation-card">
            <div class="header-section">
                <img src='<%= imagen%>' class="img img-principal" alt="<%= name%>" loading="lazy" />
            </div>

            <h4 class="card-title pt-3"><%= name%></h4>

            <% if (_.isObject(cantidad)) { %>
            <div class="status-grid">
                <div class="d-flex status-item row-align align-items-center">
                    <div class="status-label">
                        Pendientes
                    </div>
                    <div class="status-value ms-auto"><%= cantidad.pendientes %></div>
                </div>
                <div class="d-flex status-item row-align align-items-center">
                    <div class="status-label">
                        Aprobados
                    </div>
                    <div class="status-value ms-auto"><%= cantidad.aprobados %></div>
                </div>
                <div class="d-flex status-item row-align align-items-center">
                    <div class="status-label">
                        Rechazados
                    </div>
                    <div class="status-value ms-auto"><%= cantidad.rechazados %></div>
                </div>
                <div class="d-flex status-item row-align align-items-center">
                    <div class="status-label">
                        Devueltos
                    </div>
                    <div class="status-value ms-auto"><%= cantidad.devueltos %></div>
                </div>
            </div>
            <% } %>

            <div class="divider"></div>
            <div class="temporary-section">
                <div class="temporary-label text-muted">Temporales</div>
                <div class="temporary-value"><%= cantidad.temporales %></div>
            </div>
        </div>
    </a>

<% } %>

<% if(tipo == 'consultas'){ %>
    <a href="<%= url%>" class="consulta-card-link">
        <div class="consulta-card">
            <div class="consulta-card-media">
                <img src='<%= imagen%>' class="img img-principal" alt="<%= name%>" loading="lazy" />
            </div>
            <div class="consulta-card-body">
                <h4 class="consulta-card-title"><%= name%></h4>
            </div>
        </div>
    </a>
<% } %>
