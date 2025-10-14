import { $App } from '@/App';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { HeaderListView } from '@/Cajas/HeaderListView';
import { FiltroView } from '@/Componentes/Views/FiltroView';
import loading from '@/Componentes/Views/Loading';

class RequestListView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.headerMain = undefined;
        this.headerList = undefined;
        this.filtro = undefined;
        this.titulo = undefined;
        this.titulo_detalle = undefined;
        _.extend(this, options);
    }

    get className() {
        return 'col';
    }

    __loadSubmenu() {
        const mestados = {
            '': 'Pendientes',
            P: 'Pendientes',
            R: 'Rechazadas',
            X: 'Rechazadas',
            A: 'Activas',
            I: 'Inactivas',
            D: 'Devueltas',
            T: 'Temporales',
        };

        const tipo_detalle = mestados[this.collection.tipo];
        this.headerMain = new HeaderCajasView({
            model: {
                titulo: this.titulo,
                detalle: this.titulo_detalle + tipo_detalle,
                info: true,
            },
        });
        this.listenTo(this.headerMain, 'show:filtro', this.__showFiltro);

        $App.layout.getRegion('header').show(this.headerMain);
        this.headerList = new HeaderListView({
            model: {
                tipo: this.collection.tipo,
            },
        });
        $App.layout.getRegion('subheader').show(this.headerList);
    }

    procesoPendiente(e) {
        const id = this.$el.find(e.currentTarget).attr('data-cid');
        this.remove();
        $App.router.navigate('proceso/' + id, { trigger: true });
    }

    __beforeRender() {
        this.filtro = new FiltroView();
        this.listenTo(this.filtro, 'change:filtro', () => {
            this.trigger('load:table', {
                ...this.collection,
                cantidad: cantidad,
                callback: (response) => {
                    this.$el.find('#consulta').html(response.consulta);
                    this.$el.find('#paginate').html(response.paginate);
                },
                silent: false,
            });
        });

        $('#filtroData').html(this.filtro.render().$el);

        const cantidad = $('#cantidad_paginate').val();
        this.trigger('load:table', {
            ...this.collection,
            cantidad: cantidad,
            callback: (response) => {
                this.$el.find('#consulta').html(response.consulta);
                this.$el.find('#paginate').html(response.paginate);
            },
            silent: false,
        });
    }

    infoDetalle(event) {
        event.preventDefault();
        let _target = $(event.currentTarget);
        const id = _target.attr('data-cid');
        this.remove();
        $App.router.navigate('info/' + id, { trigger: true });
    }

    buscarPagina(e) {
        e.preventDefault();
        const cantidad = this.$el.find('#cantidad_paginate').val();
        let pagina = parseInt(this.$el.find(e.currentTarget).find('a').text());

        if (isNaN(pagina) == true) pagina = parseInt(this.$el.find(e.currentTarget).attr('pagina'));
        if (pagina == 0) return;

        this.trigger('load:pagina', {
            cantidad: cantidad,
            pagina: pagina,
            tipo: this.collection.tipo,
            callback: (response) => {
                this.$el.find('#consulta').html(response.consulta);
                this.$el.find('#paginate').html(response.paginate);
            },
            silent: false,
        });
    }

    changeCantidad(e) {
        e.preventDefault();
        const cantidad = this.$el.find('#cantidad_paginate').val();
        let pagina = 1;
        this.trigger('change:pagina', {
            cantidad: cantidad,
            pagina: pagina,
            tipo: this.collection.tipo,
            callback: (response) => {
                this.$el.find('#consulta').html(response.consulta);
                this.$el.find('#paginate').html(response.paginate);
            },
            silent: false,
        });
    }

    irPendienteEmail(e) {
        e.preventDefault();
        loading.show();
        window.location.href = $App.url('pendiente_email');
    }

    __showFiltro() {
        const myModal = new bootstrap.Modal('#filtrar-modal', {
            keyboard: false,
        });
        myModal.show();
    }

    remove() {
        this.headerList && this.headerList.remove();
        this.headerMain && this.headerMain.remove();
        this.filtro && this.filtro.remove();
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}

export { RequestListView };
