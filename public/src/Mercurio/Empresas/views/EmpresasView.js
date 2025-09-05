import { $App } from '@/App';
import { langDataTable } from '@/Core';

class EmpresasView extends Backbone.View {
    constructor(options) {
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
        const url = this.model.tipo ? 'empresa/renderTable/' + this.model.tipo : 'empresa/renderTable';

        this.trigger('load:table', {
            url,
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
            "click [data-toggle='event-cuenta']": 'adminCuenta',
            "click [data-toggle='event-detalle']": 'procesoPendiente',
            "click [data-toggle='cancel-solicitud']": 'cancelarSolicitud',
        };
    }

    procesoPendiente(e) {
        const id = this.$el.find(e.currentTarget).attr('data-cid');
        this.remove();
        $App.router.navigate('proceso/' + id, { trigger: true });
    }

    adminCuenta(event) {
        let target = $(event.currentTarget);
        const id = target.attr('data-cid');
        $App.trigger('confirma', {
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

    __setStyles() {
        $('[type="search"]').addClass('row form-control');
        $('[type="search"]').css('display', 'inline-block');
        $('[type="search"]').css('width', '220px');
    }

    __initTable() {
        this.tableView = this.$el.find('#tb_empresas').DataTable({
            paging: true,
            ordering: false,
            pageLength: 10,
            pagingType: 'numbers',
            info: true,
            searching: true,
            columnDefs: [
                {
                    targets: 0,
                    width: '10%',
                },
                {
                    targets: 1,
                    width: '40%',
                },
                {
                    targets: 2,
                    width: '10%',
                },
                {
                    targets: 3,
                    width: '10%',
                },
                {
                    targets: 4,
                    width: '10%',
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

export { EmpresasView };
