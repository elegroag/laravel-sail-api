import tmp_politica from '@/Componentes/Views/Templates/politica_tratamiento_datos.hbs?raw';

export class PoliticaModalView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.template = _.template(tmp_politica);
    }

    render() {
        this.$el.html(`
            <div class="politica-modal-view">
                ${this.template()}
            </div>
        `);
        return this;
    }
}
