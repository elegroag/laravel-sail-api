<!-- Contenedor principal con scroll suave -->
<div class="principal-container">
    <!-- Navegación lateral flotante -->
    <nav class="nav-lateral" id="navLateral">
        <ul class="nav-lateral-list">
            <li class="nav-lateral-item active" data-section="productos">
                <a href="#section-productos" class="nav-lateral-link">
                    <span class="nav-dot"></span>
                    <span class="nav-label">Productos</span>
                </a>
            </li>
            <li class="nav-lateral-item" data-section="totales">
                <a href="#section-totales" class="nav-lateral-link">
                    <span class="nav-dot"></span>
                    <span class="nav-label">Resumen</span>
                </a>
            </li>
            <li class="nav-lateral-item" data-section="afiliaciones">
                <a href="#section-afiliaciones" class="nav-lateral-link">
                    <span class="nav-dot"></span>
                    <span class="nav-label">Movimientos</span>
                </a>
            </li>
            <li class="nav-lateral-item" data-section="consultas">
                <a href="#section-consultas" class="nav-lateral-link">
                    <span class="nav-dot"></span>
                    <span class="nav-label">Consultas</span>
                </a>
            </li>
        </ul>
        <!-- Indicador de progreso de scroll -->
        <div class="scroll-progress-container">
            <div class="scroll-progress-bar" id="scrollProgress"></div>
        </div>
    </nav>

    <!-- Contenido principal con secciones -->
    <div class="sections-wrapper" id="sectionsWrapper">
        <!-- Sección: Productos y Servicios -->
        <section class="principal-section" id="section-productos" data-section="productos">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="section-title-wrapper">
                    <h2 class="section-title">Productos y Servicios</h2>
                    <p class="section-subtitle">Productos y servicios adicionales de la CAJA de Compensación del Caquetá</p>
                </div>
            </div>
            <div class="section-content">
                <div id="show_galeria">
                    <div class="loading-placeholder">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección: Resumen / Totales -->
        <section class="principal-section" id="section-totales" data-section="totales">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="section-title-wrapper">
                    <h2 class="section-title">Resumen General</h2>
                    <p class="section-subtitle">Vista general del estado de todas las solicitudes</p>
                </div>
            </div>
            <div class="section-content">
                <div class="totales-grid" id="show_totales">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
        </section>

        <!-- Sección: Movimientos / Afiliaciones -->
        <section class="principal-section" id="section-afiliaciones" data-section="afiliaciones">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="section-title-wrapper">
                    <h2 class="section-title">Movimientos</h2>
                    <p class="section-subtitle">Gestiona y monitorea todas las solicitudes de afiliación en tiempo real</p>
                </div>
            </div>
            <div class="section-content">
                <div class="cards-grid" id="show_afiliaciones">
                    <!-- Contenido cargado dinámicamente -->
                    <div class="loading-placeholder">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección: Consultas -->
        <section class="principal-section" id="section-consultas" data-section="consultas">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="section-title-wrapper">
                    <h2 class="section-title">Consultas</h2>
                    <p class="section-subtitle">Acceso a las consultas generales de los afiliados</p>
                </div>
            </div>
            <div class="section-content">
                <div class="cards-grid cards-grid-sm" id="show_consultas">
                    <!-- Contenido cargado dinámicamente -->
                    <div class="loading-placeholder">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <!-- Mensaje informativo cuando el usuario está inactivo y no puede
                     acceder a las consultas. Se muestra/oculta desde Principal.js. -->
                <div id="show_consultas_message" class="principal-empty-state d-none" role="status">
                    <div class="principal-empty-state-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="principal-empty-state-title">Consultas no disponibles</h3>
                    <p class="principal-empty-state-text">
                        Esta sección solo está disponible para usuarios con afiliación <strong>activa</strong>.
                        Para acceder a las consultas de afiliados y al historial de servicios, es necesario
                        que su estado en el sistema principal de Subsidio se encuentre activo.
                    </p>
                </div>
            </div>
        </section>
    </div>

    <!-- Botón scroll to top -->
    <button class="scroll-to-top" id="scrollToTop" title="Volver arriba">
        <i class="fas fa-chevron-up"></i>
    </button>
</div>