//
import { $App } from '@/App';

import { langDataTable } from '../../../Core';

class ActualizadatosView extends Backbone.View {
    constructor(options = {}) {
        super(options);
    }

    get className() {
        return 'table-responsive-md';
    }

    initialize() {
        this.template = document.getElementById('tmp_table').innerHTML;
        this.tableView = void 0;
    }

    render() {
        const template = _.template(this.template);
        this.$el.html(template());

        this.trigger('load:table', {
            url: this.model.tipo ? 'actualizadatos/render_table/' + this.model.tipo : 'actualizadatos/render_table',
            callback: (html) => {
                this.$el.find('#consulta').html(html);
                this.__initTable();
                this.__setStyles();
            },
            silent: false,
        });
        return this;
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
                let _url = $App.kumbiaURL('' + target.attr('data-href'));
                window.location.href = _url;
            }
        });
    }

    __setStyles() {
        $('[type="search"]').addClass('row form-control');
        $('[type="search"]').css('display', 'inline-block');
        $('[type="search"]').css('width', '220px');
    }

    __initTable() {
        this.tableView = this.$el.find('#tb_actualiza').DataTable({
            paging: true,
            ordering: false,
            pageLength: 10,
            pagingType: 'numbers',
            info: true,
            searching: true,
            columnDefs: [
                {
                    targets: 0,
                    width: '5%',
                },
                {
                    targets: 1,
                    width: '30%',
                },
                {
                    targets: 2,
                    width: '5%',
                },
                {
                    targets: 3,
                    width: '30%',
                },
                {
                    targets: 4,
                    width: '20%',
                },
            ],
            order: [[0, 'desc']],
            language: langDataTable,
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

export { ActualizadatosView };
