import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderListView } from '@/Cajas/HeaderListView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import EmpresasView from './views/EmpresasView';

class EmpresasListas extends ControllerValidation {
	tipo = null;
	headerMain = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
	}

	listRequests(tipo = '', pagina = 0) {
		this.tipo = tipo;
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);

		const view = new EmpresasView({
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
		const tipo_detalle = this.__estados[this.tipo];
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Lista empresas',
				detalle: 'Aprobar empresas ' + tipo_detalle,
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

export { EmpresasListas };
