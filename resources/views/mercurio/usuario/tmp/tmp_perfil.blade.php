<form method="POST" action="#" id='formRequest'>
    <input class="d-none" id="tipo" name="tipo" type="text" value="<%=tipo%>"/>

    <div class="perfil-notice">
        <i class="fas fa-info-circle" aria-hidden="true"></i>
        <p class="mb-0">
            El usuario de la plataforma de comfacaenlinea.com.co no es el mismo usuario de afiliado de la Caja de Compensación Familiar del Caquetá COMFACA.
        </p>
    </div>

    <div class="perfil-summary-grid">
        <div class="perfil-summary-card">
            <span class="perfil-summary-label">Tipo usuario</span>
            <span class="perfil-summary-value"><%= tipo_detalle %></span>
        </div>

        <div class="perfil-summary-card">
            <span class="perfil-summary-label">Tipo documento</span>
            <% if (isEdit == -1) { %>
                <span class="perfil-summary-value"><%= coddoc_detalle %></span>
            <% } else { %>
                <div id='component_coddoc'></div>
            <% } %>
        </div>

        <div class="perfil-summary-card">
            <span class="perfil-summary-label">Identificación</span>
            <% if (isEdit == -1) { %>
                <span class="perfil-summary-value"><%= documento %></span>
            <% } else { %>
                <input class="form-control" id="documento" name="documento" type="text" value="<%= documento %>" readonly />
            <% } %>
        </div>

        <div class="perfil-summary-card">
            <span class="perfil-summary-label">Nombre</span>
            <% if (isEdit == -1) { %>
                <span class="perfil-summary-value"><%= nombre %></span>
            <% } else { %>
                <input class="form-control" id="nombre" name="nombre" type="text" value="<%= nombre %>" style="text-transform: uppercase" />
            <% } %>
        </div>

        <div class="perfil-summary-card span-2">
            <span class="perfil-summary-label">Dirección email notificaciones</span>
            <% if (isEdit == -1) { %>
                <span class="perfil-summary-value"><%= email %></span>
            <% } else { %>
                <input class="form-control" id="email" name="email" type="text" value="<%= email %>" style="text-transform: uppercase" />
            <% } %>
            <p class="perfil-summary-help">
                Usa una dirección de email a la cual pueda acceder de forma recurrente, para consultar las notificaciones de procesos de afiliación.
            </p>
        </div>

        <div class="perfil-summary-card">
            <span class="perfil-summary-label">Fecha registro del usuario</span>
            <span class="perfil-summary-value"><%= fecreg %></span>
        </div>

        <div class="perfil-summary-card">
            <span class="perfil-summary-label">Ciudad</span>
            <% if (isEdit == -1) { %>
                <span class="perfil-summary-value"><%= codciu_detalle %></span>
            <% } else { %>
                <div id='component_codciu'></div>
            <% } %>
        </div>

        <div class="perfil-summary-card">
            <span class="perfil-summary-label">Estado actual</span>
            <span class="perfil-summary-value"><%= estado_detalle %></span>
        </div>

        <div class="perfil-summary-card">
            <span class="perfil-summary-label">Clave actual</span>
            <% if (isEdit == -1) { %>
                <span class="perfil-summary-value">XxxX. . . .</span>
            <% } else { %>
                <input class="form-control disabled" id="clave" name="clave" type="password" value="<%= clave %>" disabled />
                <div class="mt-2 text-end">
                    <a href="#" class="perfil-link-action text-info" data-has='N' id='bt_change_clave'>
                        <i class="fas fa-key" aria-hidden="true"></i>
                        Cambiar clave de usuario
                    </a>
                </div>
            <% } %>
        </div>

        <div class="perfil-summary-card">
            <div id="show_change_clave" class="d-none perfil-password-panel">
                <% if (isEdit == 1) { %>
                    <label class="perfil-summary-label">Nueva clave</label>
                    <input class="form-control mb-2" id="newclave1" name="newclave" placeholder="Clave aquí" type="text" value="" />

                    <label class="perfil-summary-label">Repetir la nueva clave</label>
                    <input class="form-control mb-2" id="newclave2" name="newclave" placeholder="Clave aquí" type="text" value="" />

                    <label id='show_error_clave' class='error'></label>

                    <div class="perfil-password-actions">
                        <a href="#" class="perfil-link-action text-secondary" data-has='N' id='bt_nochange_clave'>No cambiar clave</a>
                        <a href="#" class="perfil-link-action text-info" data-has='N' id='bt_crea_clave'>Clave automática</a>
                    </div>
                <% } %>
            </div>
        </div>
    </div>

    <div class="perfil-actions">
        <% if (isEdit == -1) { %>
            <a href='#' class="btn btn-primary" id='bt_editar'>
                <i class="fas fa-edit me-1" aria-hidden="true"></i>
                Editar datos
            </a>
        <% } else { %>
            <a href='#' class="btn btn-success" id='bt_guardar'>
                <i class="fas fa-save me-1" aria-hidden="true"></i>
                Guardar
            </a>
            <a href='#' class="btn btn-outline-secondary" id='bt_close'>
                <i class="fas fa-times me-1" aria-hidden="true"></i>
                Cerrar
            </a>
        <% } %>
    </div>
</form>
