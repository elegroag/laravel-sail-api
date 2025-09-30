<% if(tipo == 'afiliacion'){ %>
    <div class="company-affiliation-card">
        <div class="header-section">         
            <img src='<%= imagen%>' class="img img-principal" width="100" height="100"/>
        </div>

        <h4 class="card-title pt-3"><%= name%></h4>

        <% if (_.isObject(cantidad)) { %>
        <div class="status-grid">
            <div class="d-flex status-item row-align align-items-center">
                <div class="status-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
                    </svg>
                    Pendientes
                </div>
                <div class="status-value ms-auto"><%= cantidad.pendientes %></div>
            </div>
            <div class="d-flex status-item row-align align-items-center">
                <div class="status-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.423 5.525 7.475a.235.235 0 0 0-.02-.022A.5.5 0 0 0 5.146 7l-.003.003-.004.004-.005.005a.5.5 0 0 0 .708.708l1.414 1.414 3.536-3.536a.5.5 0 0 0 .001-.707.502.502 0 0 0-.707-.001z"/>
                    </svg>
                    Aprobados
                </div>
                <div class="status-value ms-auto"><%= cantidad.aprobados %></div>
            </div>
            <div class="d-flex status-item row-align align-items-center">
                <div class="status-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                    Rechazados
                </div>
                <div class="status-value ms-auto"><%= cantidad.rechazados %></div>
            </div>
            <div class="d-flex status-item row-align align-items-center">
                <div class="status-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-counterclockwise" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
                    </svg>
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

<% } %>

<% if(tipo == 'consultas'){ %>

<div class="card card-stats">
    <a href="<%= url%>" data-type="profile">
        <div class="card-header card-header-warning card-header-icon">
            <p class="card-category"><%= name%></p>
            <img src='<%= imagen%>' class="img img-principal" width="100" height="100" />
        </div>
    </a>
</div>

<% } %>

<% if(tipo == 'productos'){ %>

<div class="card card-stats">
    <a href="<%= url%>" data-type="profile">
        <div class="card-header card-header-warning card-header-icon pt-3">
            <p class="card-category"><%= name%></p>
            <img src='<%= imagen%>' class="img img-principal" width="100" height="100" />
        </div>
    </a>
</div>

<% } %>