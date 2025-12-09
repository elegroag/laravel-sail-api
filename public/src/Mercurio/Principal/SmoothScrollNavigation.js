/**
 * SmoothScrollNavigation.js
 * Módulo para navegación fluida vertical con scroll suave
 * Maneja la navegación entre secciones, indicador de progreso y carga dinámica
 */

class SmoothScrollNavigation {
    constructor(options = {}) {
        // Configuración por defecto
        this.config = {
            navSelector: '#navLateral',
            sectionsWrapper: '#sectionsWrapper',
            sectionSelector: '.principal-section',
            navItemSelector: '.nav-lateral-item',
            scrollProgressSelector: '#scrollProgress',
            scrollToTopSelector: '#scrollToTop',
            offset: 100, // Offset para el scroll
            throttleDelay: 100, // Delay para throttle del scroll
            ...options,
        };

        // Referencias a elementos del DOM
        this.$nav = $(this.config.navSelector);
        this.$sectionsWrapper = $(this.config.sectionsWrapper);
        this.$sections = $(this.config.sectionSelector);
        this.$navItems = $(this.config.navItemSelector);
        this.$scrollProgress = $(this.config.scrollProgressSelector);
        this.$scrollToTop = $(this.config.scrollToTopSelector);

        // Estado interno
        this.currentSection = null;
        this.isScrolling = false;
        this.sectionsLoaded = new Set();

        // Inicializar
        this._init();
    }

    /**
     * Inicializa el módulo
     */
    _init() {
        this._bindEvents();
        this._initIntersectionObserver();
        this._updateScrollProgress();
        this._checkInitialSection();
    }

    /**
     * Vincula los eventos necesarios
     */
    _bindEvents() {
        // Click en navegación lateral
        this.$navItems.find('a').on('click', (e) => this._handleNavClick(e));

        // Scroll de la página (throttled)
        $(window).on(
            'scroll',
            _.throttle(() => {
                this._updateScrollProgress();
                this._updateScrollToTopVisibility();
            }, this.config.throttleDelay),
        );

        // Click en botón scroll to top
        this.$scrollToTop.on('click', () => this._scrollToTop());

        // Teclas de navegación
        $(document).on('keydown', (e) => this._handleKeyNavigation(e));
    }

    /**
     * Inicializa el Intersection Observer para detectar secciones visibles
     */
    _initIntersectionObserver() {
        const observerOptions = {
            root: null,
            rootMargin: '-20% 0px -60% 0px',
            threshold: 0,
        };

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                const sectionId = entry.target.dataset.section;

                if (entry.isIntersecting) {
                    // Marcar sección como visible
                    $(entry.target).addClass('visible');
                    this._setActiveNavItem(sectionId);
                    this.currentSection = sectionId;

                    // Disparar evento de sección visible
                    $(document).trigger('section:visible', { section: sectionId });
                }
            });
        }, observerOptions);

        // Observar todas las secciones
        this.$sections.each((_, section) => {
            this.observer.observe(section);
        });
    }

    /**
     * Verifica la sección inicial visible
     */
    _checkInitialSection() {
        // Hacer visibles todas las secciones inicialmente
        setTimeout(() => {
            this.$sections.addClass('visible');
        }, 100);
    }

    /**
     * Maneja el click en la navegación
     * @param {Event} e - Evento de click
     */
    _handleNavClick(e) {
        e.preventDefault();
        const targetId = $(e.currentTarget).attr('href');
        this._scrollToSection(targetId);
    }

    /**
     * Realiza scroll suave a una sección
     * @param {string} targetId - ID de la sección destino
     */
    _scrollToSection(targetId) {
        const $target = $(targetId);
        if (!$target.length) return;

        this.isScrolling = true;

        $('html, body').animate(
            {
                scrollTop: $target.offset().top - this.config.offset,
            },
            {
                duration: 600,
                easing: 'swing',
                complete: () => {
                    this.isScrolling = false;
                    // Actualizar hash de URL sin scroll
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    }
                },
            },
        );
    }

    /**
     * Actualiza el item activo en la navegación
     * @param {string} sectionId - ID de la sección activa
     */
    _setActiveNavItem(sectionId) {
        this.$navItems.removeClass('active');
        this.$navItems.filter(`[data-section="${sectionId}"]`).addClass('active');
    }

    /**
     * Actualiza la barra de progreso del scroll
     */
    _updateScrollProgress() {
        const scrollTop = $(window).scrollTop();
        const docHeight = $(document).height() - $(window).height();
        const scrollPercent = (scrollTop / docHeight) * 100;

        this.$scrollProgress.css('height', `${Math.min(scrollPercent, 100)}%`);
    }

    /**
     * Actualiza la visibilidad del botón scroll to top
     */
    _updateScrollToTopVisibility() {
        const scrollTop = $(window).scrollTop();

        if (scrollTop > 300) {
            this.$scrollToTop.addClass('visible');
        } else {
            this.$scrollToTop.removeClass('visible');
        }
    }

    /**
     * Realiza scroll al inicio de la página
     */
    _scrollToTop() {
        $('html, body').animate(
            {
                scrollTop: 0,
            },
            {
                duration: 500,
                easing: 'swing',
            },
        );
    }

    /**
     * Maneja la navegación por teclado
     * @param {Event} e - Evento de teclado
     */
    _handleKeyNavigation(e) {
        // Solo si no hay un input enfocado
        if ($(e.target).is('input, textarea, select')) return;

        const sections = this.$sections.toArray();
        const currentIndex = sections.findIndex((s) => s.dataset.section === this.currentSection);

        switch (e.key) {
            case 'ArrowDown':
            case 'PageDown':
                e.preventDefault();
                if (currentIndex < sections.length - 1) {
                    this._scrollToSection(`#${sections[currentIndex + 1].id}`);
                }
                break;
            case 'ArrowUp':
            case 'PageUp':
                e.preventDefault();
                if (currentIndex > 0) {
                    this._scrollToSection(`#${sections[currentIndex - 1].id}`);
                }
                break;
            case 'Home':
                e.preventDefault();
                this._scrollToSection(`#${sections[0].id}`);
                break;
            case 'End':
                e.preventDefault();
                this._scrollToSection(`#${sections[sections.length - 1].id}`);
                break;
        }
    }

    /**
     * Navega a una sección específica por nombre
     * @param {string} sectionName - Nombre de la sección
     */
    goToSection(sectionName) {
        this._scrollToSection(`#section-${sectionName}`);
    }

    /**
     * Marca una sección como cargada
     * @param {string} sectionName - Nombre de la sección
     */
    markSectionLoaded(sectionName) {
        this.sectionsLoaded.add(sectionName);
        $(`#section-${sectionName}`)
            .find('.loading-placeholder')
            .fadeOut(300, function () {
                $(this).remove();
            });
    }

    /**
     * Verifica si una sección está cargada
     * @param {string} sectionName - Nombre de la sección
     * @returns {boolean}
     */
    isSectionLoaded(sectionName) {
        return this.sectionsLoaded.has(sectionName);
    }

    /**
     * Destruye el módulo y limpia eventos
     */
    destroy() {
        this.$navItems.find('a').off('click');
        $(window).off('scroll');
        this.$scrollToTop.off('click');
        $(document).off('keydown');

        if (this.observer) {
            this.observer.disconnect();
        }
    }
}

export { SmoothScrollNavigation };
