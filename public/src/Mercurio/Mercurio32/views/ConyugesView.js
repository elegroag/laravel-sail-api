import { SolicitudesGridView } from '@/Componentes/Views/SolicitudesGridView';

class ConyugesView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.App = options.App || window.App;
    }

    get className() {
        return 'solicitudes-list';
    }

    render() {
        const template = _.template(document.getElementById('tmp_table').innerHTML);
        this.$el.html(template());
        this.__loadGrid();
        return this;
    }

    __loadGrid() {
        this.trigger('load:table', {
            url: this.model.tipo ? 'conyuge/render_table/' + this.model.tipo : 'conyuge/render_table',
            callback: (html) => {
                if (!html) {
                    this.App.trigger('alert:error', { message: 'No se pudo cargar el listado de solicitudes.' });
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
        this.App.router.navigate('proceso/' + id, { trigger: true });
    }

    cambioCuenta(event) {
        let target = $(event.currentTarget);
        Swal.fire({
            title: '¡Confirmar!',
            html: "<p style='font-size:0.95rem' >Se requiere de confirmar que estás de acuerdo en el cambio de cuenta para administrar la empresa seleccionada. Esta opción le permitirá afiliar trabajadores, cónyuges y beneficiarios</p>",
            showCancelButton: true,
            confirmButtonClass: 'btn btn-sm btn-success',
            cancelButtonClass: 'btn btn-sm btn-danger',
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (result.value) {
                let _url = this.App.kumbiaURL('' + target.attr('data-href'));
                window.location.href = _url;
            }
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

export { ConyugesView };
