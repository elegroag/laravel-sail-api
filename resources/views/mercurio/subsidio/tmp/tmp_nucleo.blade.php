<%
    var formatMoney = function (value) {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            maximumFractionDigits: 0,
        }).format(Number(value || 0));
    };
    var estadoLabel = _estado[estado] || estado || '-';
    var estadoClass = estado === 'A' ? 'nucleo-badge-success' : 'nucleo-badge-muted';
%>

<div class="nucleo-summary-grid">
    <div class="nucleo-summary-card">
        <span class="nucleo-summary-label">Trabajador</span>
        <strong class="nucleo-summary-value"><%= fullname %></strong>
    </div>
    <div class="nucleo-summary-card">
        <span class="nucleo-summary-label">Documento</span>
        <strong class="nucleo-summary-value"><%= cedtra %></strong>
    </div>
    <div class="nucleo-summary-card">
        <span class="nucleo-summary-label">Empresa</span>
        <strong class="nucleo-summary-value"><%= razsoc %></strong>
    </div>
    <div class="nucleo-summary-card">
        <span class="nucleo-summary-label">Estado</span>
        <strong class="nucleo-summary-value"><span class="nucleo-badge <%= estadoClass %>"><%= estadoLabel %></span></strong>
    </div>
</div>

<section class="nucleo-section">
    <header class="nucleo-section-header">
        <i class="fas fa-id-card"></i>
        <h3>Identificación y afiliación</h3>
    </header>
    <div class="nucleo-field-grid">
        <div class="nucleo-field">
            <span class="nucleo-field-label">NIT</span>
            <strong class="nucleo-field-value"><%= nit %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Empresa</span>
            <strong class="nucleo-field-value"><%= razsoc %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Documento</span>
            <strong class="nucleo-field-value"><%= cedtra %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Nombre completo</span>
            <strong class="nucleo-field-value"><%= fullname %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Estado</span>
            <strong class="nucleo-field-value"><span class="nucleo-badge <%= estadoClass %>"><%= estadoLabel %></span></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Dispone de giro</span>
            <strong class="nucleo-field-value"><%= (giro == 'S') ? 'Sí' : 'No' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Fecha afiliación</span>
            <strong class="nucleo-field-value"><%= fecafi || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Fecha estado</span>
            <strong class="nucleo-field-value"><%= fecest || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Categoría</span>
            <strong class="nucleo-field-value"><%= _codcat[codcat] || codcat || '-' %></strong>
        </div>
    </div>
</section>

<section class="nucleo-section">
    <header class="nucleo-section-header">
        <i class="fas fa-user"></i>
        <h3>Datos personales</h3>
    </header>
    <div class="nucleo-field-grid">
        <div class="nucleo-field">
            <span class="nucleo-field-label">Fecha nacimiento</span>
            <strong class="nucleo-field-value"><%= fecnac || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Ciudad nacimiento</span>
            <strong class="nucleo-field-value"><%= _codciu[ciunac] || ciunac || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Género</span>
            <strong class="nucleo-field-value"><%= _sexo[sexo] || sexo || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Estado civil</span>
            <strong class="nucleo-field-value"><%= _estciv[estciv] || estciv || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Nivel educativo</span>
            <strong class="nucleo-field-value"><%= _nivedu[nivedu] || nivedu || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Vivienda</span>
            <strong class="nucleo-field-value"><%= _vivienda[vivienda] || vivienda || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Rural</span>
            <strong class="nucleo-field-value"><%= _rural[rural] || rural || '-' %></strong>
        </div>
    </div>
</section>

<section class="nucleo-section">
    <header class="nucleo-section-header">
        <i class="fas fa-address-book"></i>
        <h3>Contacto y ubicación</h3>
    </header>
    <div class="nucleo-field-grid">
        <div class="nucleo-field">
            <span class="nucleo-field-label">Email</span>
            <strong class="nucleo-field-value"><%= email || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Teléfono</span>
            <strong class="nucleo-field-value"><%= telefono || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Dirección</span>
            <strong class="nucleo-field-value"><%= direccion || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Ciudad</span>
            <strong class="nucleo-field-value"><%= _codciu[codciu] || codciu || '-' %></strong>
        </div>
        <div class="nucleo-field">
            <span class="nucleo-field-label">Zona</span>
            <strong class="nucleo-field-value"><%= _codciu[codzon] || codzon || '-' %></strong>
        </div>
    </div>
</section>

<section class="nucleo-section">
    <header class="nucleo-section-header">
        <i class="fas fa-briefcase"></i>
        <h3>Información laboral</h3>
    </header>
    <div class="nucleo-field-grid">
        <div class="nucleo-field">
            <span class="nucleo-field-label">Salario</span>
            <strong class="nucleo-field-value"><%= formatMoney(salario) %></strong>
        </div>
    </div>
</section>

<section class="nucleo-section">
    <header class="nucleo-section-header">
        <i class="fas fa-layer-group"></i>
        <h3>Multiafiliación</h3>
    </header>
    <% if (multiafiliacion === true) { %>
        <div class="nucleo-field-grid">
            <div class="nucleo-field">
                <span class="nucleo-field-label">Empresa</span>
                <strong class="nucleo-field-value"><%= multiafiliacion_empresa %></strong>
            </div>
            <div class="nucleo-field">
                <span class="nucleo-field-label">Sucursal</span>
                <strong class="nucleo-field-value"><%= multiafiliacion_sucursal %></strong>
            </div>
            <div class="nucleo-field">
                <span class="nucleo-field-label">Fecha afiliación</span>
                <strong class="nucleo-field-value"><%= multiafiliacion_fecafi %></strong>
            </div>
            <div class="nucleo-field">
                <span class="nucleo-field-label">Estado</span>
                <strong class="nucleo-field-value"><%= _estado[multiafiliacion_estado] || multiafiliacion_estado %></strong>
            </div>
            <div class="nucleo-field">
                <span class="nucleo-field-label">Salario</span>
                <strong class="nucleo-field-value"><%= formatMoney(multiafiliacion_salario) %></strong>
            </div>
            <div class="nucleo-field">
                <span class="nucleo-field-label">Giro</span>
                <strong class="nucleo-field-value"><%= multiafiliacion_codgir %></strong>
            </div>
            <div class="nucleo-field">
                <span class="nucleo-field-label">Razón social</span>
                <strong class="nucleo-field-value"><%= multiafiliacion_razsoc %></strong>
            </div>
            <div class="nucleo-field">
                <span class="nucleo-field-label">Representante legal</span>
                <strong class="nucleo-field-value"><%= multiafiliacion_repleg %></strong>
            </div>
            <div class="nucleo-field">
                <span class="nucleo-field-label">Dirección</span>
                <strong class="nucleo-field-value"><%= multiafiliacion_direccion %></strong>
            </div>
            <div class="nucleo-field">
                <span class="nucleo-field-label">Teléfono</span>
                <strong class="nucleo-field-value"><%= multiafiliacion_telefono %></strong>
            </div>
            <div class="nucleo-field">
                <span class="nucleo-field-label">Ciudad</span>
                <strong class="nucleo-field-value"><%= _codciu[multiafiliacion_codciu] || multiafiliacion_codciu %></strong>
            </div>
        </div>
    <% } else { %>
        <div class="nucleo-empty-state">
            <i class="fas fa-info-circle" aria-hidden="true"></i>
            <strong>Sin multiafiliación</strong>
            <span>El trabajador no registra multiafiliación activa.</span>
        </div>
    <% } %>
</section>
