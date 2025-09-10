import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { HeaderListView } from '@/Cajas/HeaderListView';
import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import CertificadoView from './views/CertificadoView';

export default class CertificadosListar extends ControllerValidation {
	constructor(options) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		this.App.layout = this.layout;
	}

	listRequests(props={}) {
		this.tipo = props.tipo;
		const view = new CertificadoView({collection: props});
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
				titulo: 'Lista certificados',
				detalle: 'Aprobar certificado ' + tipo_detalle,
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
