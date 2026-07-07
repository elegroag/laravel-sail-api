import { SolicitudesGridView } from '@/Componentes/Views/SolicitudesGridView';

class EmpresasView extends Backbone.View {
    constructor(options) {
        super(options);
        this.App = options.App || window.App;
    }

    get className() {
        return 'solicitudes-list';
    }

    initialize() {
        this.template = document.getElementById('tmp_table').innerHTML;
    }

    render() {
        const template = _.template(this.template);
        this.$el.html(template());
        const url = this.model.tipo ? 'empresa/render_table/' + this.model.tipo : 'empresa/render_table';

        this.trigger('load:table', {
            url,
            callback: (html) => {
                this.$el.find('#consulta').html(html);
                SolicitudesGridView.initSearch(this.$el);
            },
            silent: false,
        });
        return this;
    }

    get events() {
        return {
            "click [data-toggle='event-proceso']": 'procesoPendiente',
            "click [data-toggle='event-cuenta']": 'adminCuenta',
            "click [data-toggle='event-detalle']": 'procesoPendiente',
            "click [data-toggle='cancel-solicitud']": 'cancelarSolicitud',
        };
    }

    procesoPendiente(e) {
        const id = this.$el.find(e.currentTarget).attr('data-cid');
        this.remove();
        this.App.router.navigate('proceso/' + id, { trigger: true });
    }

    adminCuenta(event) {
        let target = $(event.currentTarget);
        const id = target.attr('data-cid');
        this.App.trigger('confirma', {
            message:
                'Se requiere de confirmar que estás de acuerdo en el cambio de cuenta para administrar la empresa seleccionada. ' +
                'Esta opción le permitirá afiliar trabajadores, cónyuges y beneficiarios',
            callback: (status) => {
                if (status) {
                    this.trigger('admin:cuenta', { id });
                }
            },
        });
    }

    cancelarSolicitud(e) {
        e.preventDefault();
        const id = this.$el.find(e.currentTarget).attr('data-cid');
        this.trigger('remove:solicitud', {
            id: id,
            callback: (res) => {
                if (res) Backbone.history.loadUrl();
            },
        });
    }

    remove() {
        console.log('OK remove');
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}

export { EmpresasView };
