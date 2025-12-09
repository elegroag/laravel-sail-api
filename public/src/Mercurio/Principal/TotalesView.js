/**
 * TotalesView
 * Vista para renderizar el resumen de totales con animaciones
 */
class TotalesView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.tableView = undefined;
    }

    /**
     * Clase CSS del contenedor - se usa grid CSS ahora
     */
    get className() {
        return 'totales-content';
    }

    /**
     * Inicializa la vista
     */
    initialize() {
        this.template = document.getElementById('tmp_totales').innerHTML;
    }

    /**
     * Renderiza la vista con animación de entrada
     */
    render() {
        const template = _.template(this.template);
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

export { TotalesView };
