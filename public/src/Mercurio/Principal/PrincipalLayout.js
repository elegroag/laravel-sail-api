import { Layout } from '@/Common/Layout';
import { SmoothScrollNavigation } from './SmoothScrollNavigation';

/**
 * PrincipalLayout
 * Layout principal con navegación fluida vertical y scroll suave
 */
class PrincipalLayout extends Layout {
    constructor(options = {}) {
        super({
            ...options,
            template: '#tmp_layout',
            tagRegions: options.regions || {
                afiliaciones: '#show_afiliaciones',
                productos: '#show_productos',
                consultas: '#show_consultas',
                totales: '#show_totales',
            },
        });

        // Referencia a la navegación suave
        this.smoothNav = null;
    }

    /**
     * Eventos del layout
     */
    get events() {
        return {
            "click [data-toggle='linkFilter']": 'linkFilter',
        };
    }

    /**
     * Se ejecuta después de renderizar el layout
     */
    onRender() {
        // Inicializar navegación suave después de que el DOM esté listo
        setTimeout(() => {
            this._initSmoothNavigation();
        }, 100);
    }

    /**
     * Inicializa el módulo de navegación suave
     */
    _initSmoothNavigation() {
        this.smoothNav = new SmoothScrollNavigation({
            offset: 80, // Ajustar según el header fijo
        });

        // Escuchar eventos de sección visible
        $(document).on('section:visible', (e, data) => {
            this.trigger('section:visible', data);
        });
    }

    /**
     * Navega a una sección específica
     * @param {string} sectionName - Nombre de la sección
     */
    goToSection(sectionName) {
        if (this.smoothNav) {
            this.smoothNav.goToSection(sectionName);
        }
    }

    /**
     * Marca una sección como cargada (remueve placeholder)
     * @param {string} sectionName - Nombre de la sección
     */
    markSectionLoaded(sectionName) {
        if (this.smoothNav) {
            this.smoothNav.markSectionLoaded(sectionName);
        }
    }

    /**
     * Limpieza al destruir el layout
     */
    onDestroy() {
        if (this.smoothNav) {
            this.smoothNav.destroy();
            this.smoothNav = null;
        }
        $(document).off('section:visible');
    }
}

export { PrincipalLayout };
