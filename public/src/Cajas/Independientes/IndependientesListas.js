import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderListView } from '@/Cajas/HeaderListView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import IndependientesView from './views/IndependientesView';

export default class IndependientesListas extends ControllerValidation {
	tipo = null;
	headerMain = null;
	headerList = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		this.App.layout = this.layout;
	}

	listRequests(tipo = '', pagina = 0) {
		this.tipo = tipo;

		const view = new IndependientesView({
			collection: {
				tipo: this.tipo,
				pagina: pagina,
			},
		});

		this.loadHeaders();

		this.listenTo(view, 'load:table', this.__aplicarFiltro);
		this.listenTo(view, 'load:pagina', this.__buscarPagina);
		this.listenTo(view, 'change:pagina', this.__cambiarPagina);
		this.layout.getRegion('body').show(view);
	}

	loadHeaders() {
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Lista independientes',
				detalle: 'Aprobar independientes ' + this.__estados[this.tipo],
				info: true,
			},
		});

		this.listenTo(this.headerMain, 'show:filtro', this.__showFiltro);

		this.layout.getRegion('header').show(this.headerMain);
		this.headerList = new HeaderListView({
			model: {
				titulo: 'Listado',
			},
		});
		this.layout.getRegion('subheader').show(this.headerList);
	}
}
