import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { HeaderListView } from '@/Cajas/HeaderListView';
import DatosEmpresasView from './views/DatosEmpresasView';

export default class DatosEmpresaListar extends ControllerValidation {
	tipo = null;
	headerMain = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
	}

	listRequests(tipo = '', pagina = 0) {
		this.tipo = tipo;
		const view = new DatosEmpresasView({
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
		const tipo_detalle = this.__estados[this.tipo];
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Aprobar datos empresa',
				detalle: 'Lista datos empresa ' + tipo_detalle,
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

		this.listenTo(this.headerList, 'show:reporte', this.__showReporte);
		this.layout.getRegion('subheader').show(this.headerList);
	}
}
