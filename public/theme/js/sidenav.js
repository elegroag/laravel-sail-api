/**
 * Sidenav.js
 * Sistema de navegación lateral responsive
 * Maneja estados en desktop y móviles
 */
(function () {
    'use strict';

    // Constantes
    var BREAKPOINT_XL = 1200;
    var TRANSITION_DURATION = 300;

    /**
     * Verifica si estamos en vista móvil
     * @returns {boolean}
     */
    function isMobile() {
        return $(window).width() < BREAKPOINT_XL;
    }

    /**
     * Verifica si el sidebar está expandido
     * @returns {boolean}
     */
    function isSidenavOpen() {
        return $('body').hasClass('g-sidenav-pinned') || $('body').hasClass('g-sidenav-show');
    }

    /**
     * Abre/Expande el sidenav
     */
    function openSidenav() {
        var $body = $('body');

        // Remover clases de estado oculto
        $body.removeClass('g-sidenav-hidden g-sidenav-hide');

        // Agregar clases de estado visible
        $body.addClass('g-sidenav-show g-sidenav-pinned');

        // Actualizar togglers
        $('.sidenav-toggler').addClass('active');
        $('.navbar-toggler-sidenav').addClass('active');

        // Solo guardar estado en desktop
        if (!isMobile()) {
            Cookies.set('sidenav-state', 'pinned');
        }

        // Prevenir scroll del body en móviles
        if (isMobile()) {
            $body.css('overflow', 'hidden');
        }

        console.log('Sidenav abierto');
    }

    /**
     * Cierra/Colapsa el sidenav
     */
    function closeSidenav() {
        var $body = $('body');

        // Remover clases de estado visible
        $body.removeClass('g-sidenav-pinned g-sidenav-show');

        // Agregar clase de transición
        $body.addClass('g-sidenav-hide');

        // Actualizar togglers
        $('.sidenav-toggler').removeClass('active');
        $('.navbar-toggler-sidenav').removeClass('active');

        // Después de la transición, agregar clase hidden
        setTimeout(function () {
            $body.removeClass('g-sidenav-hide').addClass('g-sidenav-hidden');
        }, TRANSITION_DURATION);

        // Solo guardar estado en desktop
        if (!isMobile()) {
            Cookies.set('sidenav-state', 'unpinned');
        }

        // Restaurar scroll del body
        $body.css('overflow', '');

        console.log('Sidenav cerrado');
    }

    /**
     * Alterna el estado del sidenav
     */
    function toggleSidenav() {
        if (isSidenavOpen()) {
            closeSidenav();
        } else {
            openSidenav();
        }
    }

    /**
     * Inicializa el estado del sidenav según el dispositivo
     */
    function initSidenavState() {
        var $body = $('body');

        if (isMobile()) {
            // En móviles: siempre oculto por defecto
            $body.removeClass('g-sidenav-show g-sidenav-pinned');
            $body.addClass('g-sidenav-hidden');
            $('.sidenav-toggler, .navbar-toggler-sidenav').removeClass('active');
        } else {
            // En desktop: respetar la cookie, por defecto abierto
            var sidenavState = Cookies.get('sidenav-state');

            // Si no hay cookie o es 'pinned', abrir el sidebar
            if (!sidenavState || sidenavState === 'pinned') {
                openSidenav();
            } else {
                closeSidenav();
            }
        }
    }

    /**
     * Maneja el cambio de tamaño de ventana
     */
    function handleResize() {
        var $body = $('body');

        if (isMobile()) {
            // Al cambiar a móvil: cerrar sidebar
            if (isSidenavOpen()) {
                $body.removeClass('g-sidenav-show g-sidenav-pinned');
                $body.addClass('g-sidenav-hidden');
                $body.css('overflow', '');
            }
        } else {
            // Al cambiar a desktop: restaurar estado guardado
            var sidenavState = Cookies.get('sidenav-state');

            if (!sidenavState || sidenavState === 'pinned') {
                if (!$body.hasClass('g-sidenav-pinned')) {
                    openSidenav();
                }
            }
        }

        // Ajustar altura mínima del body
        $('body').css('min-height', '100vh');
        $('body').css('overflow-x', 'hidden');
    }

    /**
     * Inicialización
     */
    function init() {
        console.log('Sidenav: Inicializando...');

        // Aplicar estado inicial sin animación
        $('body').addClass('no-animate');

        // Inicializar estado del sidenav
        initSidenavState();

        // Reactivar transiciones después del primer frame de pintura
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                $('body').removeClass('no-animate');
            });
        });

        // Manejar clicks en botones del sidenav
        $(document).on('click', '.sidenav-toggler, .navbar-toggler-sidenav, .sidenav-close', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidenav();
        });

        // Manejar click en overlay para cerrar
        $(document).on('click', '.sidenav-overlay', function (e) {
            e.preventDefault();
            if (isMobile() && isSidenavOpen()) {
                closeSidenav();
            }
        });

        // Manejar clicks en data-action para compatibilidad
        $(document).on('click', '[data-action]', function (e) {
            var action = $(this).attr('data-action');

            // Solo procesar acciones de sidenav si no es un toggler (ya manejado arriba)
            if (
                $(this).hasClass('sidenav-toggler') ||
                $(this).hasClass('navbar-toggler-sidenav') ||
                $(this).hasClass('sidenav-close') ||
                $(this).hasClass('sidenav-overlay')
            ) {
                return;
            }

            switch (action) {
                case 'sidenav-pin':
                    e.preventDefault();
                    openSidenav();
                    break;

                case 'sidenav-unpin':
                    e.preventDefault();
                    closeSidenav();
                    break;
            }
        });

        // Cerrar sidenav al presionar Escape (solo en móviles)
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && isMobile() && isSidenavOpen()) {
                closeSidenav();
            }
        });

        // Manejar resize de ventana con debounce
        var resizeTimeout;
        $(window).on('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleResize, 150);
        });

        // Inicializar altura del body
        handleResize();

        console.log('Sidenav: Inicialización completa. Estado:', isSidenavOpen() ? 'abierto' : 'cerrado');
    }

    // Ejecutar inicialización cuando el DOM esté listo
    $(document).ready(init);
})();
