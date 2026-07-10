<div class="principal-gallery-layout">
    <aside class="principal-gallery-info" aria-live="polite">
        <% items.forEach(function(item, index) { %>
        <article
            class="principal-gallery-info-panel <%= index === 0 ? 'is-active' : '' %>"
            data-index="<%= index %>"
            aria-hidden="<%= index === 0 ? 'false' : 'true' %>"
        >
            <div class="principal-gallery-info-meta">
                <span class="principal-gallery-index"><%= (index + 1) < 10 ? '0' + (index + 1) : (index + 1) %></span>
                <span class="principal-gallery-total">/ <%= items.length < 10 ? '0' + items.length : items.length %></span>
            </div>

            <h3 class="principal-gallery-name"><%= item.nombre %></h3>

            <% if (item.descripcion) { %>
            <p class="principal-gallery-note"><%= item.descripcion %></p>
            <% } %>

            <a href="<%= catalogUrl %>" class="principal-gallery-cta">
                Ver en catálogo
                <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </article>
        <% }); %>

        <% if (items.length > 1) { %>
        <div class="principal-gallery-dots" role="tablist" aria-label="Slides de galería">
            <% items.forEach(function(item, index) { %>
            <button
                type="button"
                class="principal-gallery-dot <%= index === 0 ? 'is-active' : '' %>"
                data-bs-target="#<%= carouselId %>"
                data-bs-slide-to="<%= index %>"
                role="tab"
                aria-label="Slide <%= index + 1 %>"
                aria-selected="<%= index === 0 ? 'true' : 'false' %>"
            ></button>
            <% }); %>
        </div>
        <% } %>
    </aside>

    <div class="principal-gallery-visual">
        <div id="<%= carouselId %>" class="carousel slide principal-gallery-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                <% items.forEach(function(item, index) { %>
                <div class="carousel-item <%= index === 0 ? 'active' : '' %>">
                    <a href="<%= catalogUrl %>" class="principal-gallery-link" title="Ver catálogo de productos y servicios">
                        <img
                            src="<%= item.archivo %>"
                            class="principal-gallery-media"
                            alt="<%= item.nombre %>"
                            loading="lazy"
                        >
                    </a>
                </div>
                <% }); %>
            </div>

            <% if (items.length > 1) { %>
            <button class="carousel-control-prev principal-gallery-control" type="button" data-bs-target="#<%= carouselId %>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next principal-gallery-control" type="button" data-bs-target="#<%= carouselId %>" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
            <% } %>
        </div>
    </div>
</div>
