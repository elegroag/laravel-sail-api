import { $App } from '@/App';
import { SolicitudesGridView } from '@/Componentes/Views/SolicitudesGridView';

class FacultativosView extends Backbone.View {
    constructor(options) {
        super(options);
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
        this.__loadGrid();
        return this;
    }

    __loadGrid() {
        const url = this.model.tipo ? 'facultativo/render_table/' + this.model.tipo : 'facultativo/render_table';

        this.trigger('load:table', {
            url,
            callback: (html) => {
                if (!html) {
                    $App.trigger('alert:error', { message: 'No se pudo cargar el listado de solicitudes.' });
                    return;
                }
                this.$el.find('#consulta').html(html);
                SolicitudesGridView.init(this.$el, { onReload: () => this.__loadGrid() });
            },
            silent: false,
        });
    }

    get events() {
        return {
            "click [data-toggle='event-proceso']": 'procesoPendiente',
            "click [data-toggle='event-cuenta']": 'cambioCuenta',
            "click [data-toggle='event-detalle']": 'procesoPendiente',
            "click [data-toggle='cancel-solicitud']": 'cancelarSolicitud',
        };
    }

    procesoPendiente(e) {
        const id = this.$el.find(e.currentTarget).attr('data-cid');
        this.remove();
        $App.router.navigate('proceso/' + id, { trigger: true });
    }

    cambioCuenta(event) {
        let target = $(event.currentTarget);
        const id = target.attr('data-cid');
        $App.trigger('confirma', {
            message:
                'Se requiere de confirmar que estás de acuerdo en el cambio de cuenta para administrar la empresa seleccionada. ' +
                'Esta opción le permitirá afiliar trabajadores, cónyuges y beneficiarios',
            callback: (status) => {
                if (status) {
                    window.location.href = $App.url('administrar_cuenta/' + id);
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

export { FacultativosView };
