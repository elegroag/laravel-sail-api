import { SolicitudesGridView } from '@/Componentes/Views/SolicitudesGridView';

class BeneficiariosView extends Backbone.View {
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
        this.__loadGrid();
        return this;
    }

    __loadGrid({ page = 1, perPage = null } = {}) {
        const url = this.model.tipo ? 'beneficiario/render_table/' + this.model.tipo : 'beneficiario/render_table';
        const $pageSize = this.$el.find('#solicitudes-page-size');
        const resolvedPerPage = perPage || parseInt($pageSize.val(), 10) || 10;

        this.trigger('load:table', {
            url,
            page,
            perPage: resolvedPerPage,
            callback: (response) => {
                if (!response) {
                    this.App.trigger('alert:error', { message: 'No se pudo cargar el listado de solicitudes.' });
                    return;
                }

                const html = response?.consulta ?? response;
                const meta = response?.meta ?? null;

                if (!html) {
                    this.App.trigger('alert:error', { message: 'No se pudo cargar el listado de solicitudes.' });
                    return;
                }

                this.$el.find('#consulta').html(html);
                SolicitudesGridView.init(this.$el, {
                    onReload: (params) => this.__loadGrid(params),
                    meta,
                });
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

export { BeneficiariosView };
