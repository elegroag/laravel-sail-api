import { $App } from '@/App';
import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderListView } from '@/Cajas/HeaderListView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import TrabajadoresView from './views/TrabajadoresView';

export default class TrabajadoresListas extends ControllerValidation {
	tipo = null;
	headerMain = null;
	headerList = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		$App.layout = this.layout;
	}

	listRequests(tipo = '', pagina = 0) {
		this.tipo = tipo;

		const view = new TrabajadoresView({
			collection: {
				tipo: tipo,
				pagina: pagina,
			},
		});

		this.listenTo(view, 'load:table', this.__aplicarFiltro);
		this.listenTo(view, 'load:pagina', this.__buscarPagina);
		this.listenTo(view, 'change:pagina', this.__cambiarPagina);
		this.layout.getRegion('body').show(view);
		this.loadHeaders();
	}

	loadHeaders() {
		console.log(this.tipo);

		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Lista trabajadores',
				detalle: 'Aprobar trabajador de ' + this.__estados[this.tipo],
				info: true,
			},
		});

		this.listenTo(this.headerMain, 'show:filtro', this.__showFiltro);

		this.layout.getRegion('header').show(this.headerMain);
		this.headerList = new HeaderListView({
			model: {
				tipo: this.tipo,
			},
		});

		this.layout.getRegion('subheader').show(this.headerList);
	}
}
