import { $App } from '@/App';

/**
 * ServiciosView
 * Vista para renderizar cards de servicios con animaciones
 */
class ServiciosView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.tableView = undefined;
    }

    /**
     * Clase CSS del contenedor - se usa grid CSS ahora
     */
    get className() {
        return 'service-card-wrapper';
    }

    /**
     * Inicializa la vista
     */
    initialize() {
        this.template = document.getElementById('tmp_card').innerHTML;
    }

    /**
     * Renderiza la vista con animación de entrada
     */
    render() {
        const template = _.template(this.template);
        const path = $("[name='csrf-token']").attr('path');

        this.model.url = $App.url(this.model.url);
        this.model.imagen = path + '/img/Mercurio/' + this.model.imagen;
        this.$el.html(template(this.model));

        // Agregar clase para animación de entrada
        this.$el.addClass('animate-in');

        return this;
    }

    /**
     * Eventos de la vista
     */
    get events() {
        return {};
    }

    /**
     * Limpia la vista
     */
    remove() {
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}

export { ServiciosView };
