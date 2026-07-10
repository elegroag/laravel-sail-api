/**
 * GaleriaCarouselView
 * Carrusel de imágenes y videos de la galería principal (Mercurio26)
 */
import { $App } from '@/App';

class GaleriaCarouselView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.items = this.__normalizeItems(options.items || []);
        this.carouselId = options.carouselId || 'galeriaPrincipalCarousel';
        this.__onCarouselSlid = this.__onCarouselSlid.bind(this);
    }

    get className() {
        return 'principal-gallery-wrapper animate-in';
    }

    initialize() {
        this.template = document.getElementById('tmp_galeria_carousel').innerHTML;
    }

    __normalizeItems(items) {
        return items.map((item) => {
            const nota = (item.nota || '').trim();
            const lines = nota.split(/\n/).map((line) => line.trim()).filter(Boolean);

            return {
                ...item,
                nombre: lines[0] || 'Producto o servicio',
                descripcion: lines.slice(1).join('\n'),
            };
        });
    }

    render() {
        if (!this.items.length) {
            this.$el.html(
                '<p class="principal-gallery-empty">No hay productos o servicios publicados en la galería.</p>',
            );
            return this;
        }

        const template = _.template(this.template);
        this.$el.html(
            template({
                items: this.items,
                carouselId: this.carouselId,
                catalogUrl: $App.url('servicios/index'),
            }),
        );

        return this;
    }

    onShow() {
        const carouselElement = this.el.querySelector(`#${this.carouselId}`);
        if (!carouselElement || !window.bootstrap?.Carousel) {
            return;
        }

        window.bootstrap.Carousel.getOrCreateInstance(carouselElement, {
            interval: 5000,
            ride: 'carousel',
            wrap: true,
        });

        carouselElement.addEventListener('slid.bs.carousel', this.__onCarouselSlid);
    }

    __onCarouselSlid(event) {
        this.__syncInfoPanel(event.to);
    }

    __syncInfoPanel(index) {
        this.el.querySelectorAll('.principal-gallery-info-panel').forEach((panel, panelIndex) => {
            const isActive = panelIndex === index;
            panel.classList.toggle('is-active', isActive);
            panel.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        });

        this.el.querySelectorAll('.principal-gallery-dot').forEach((dot, dotIndex) => {
            const isActive = dotIndex === index;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
    }

    remove() {
        const carouselElement = this.el.querySelector(`#${this.carouselId}`);
        if (carouselElement) {
            carouselElement.removeEventListener('slid.bs.carousel', this.__onCarouselSlid);

            if (window.bootstrap?.Carousel) {
                const instance = window.bootstrap.Carousel.getInstance(carouselElement);
                if (instance) {
                    instance.dispose();
                }
            }
        }

        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}

export { GaleriaCarouselView };
