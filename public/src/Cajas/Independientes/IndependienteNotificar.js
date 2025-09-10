import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { NotificarView } from '@/Componentes/Views/NotificarView';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import IndependienteAprobarModel from './models/IndependienteAprobarModel';

export default class IndependienteNotificar extends ControllerValidation {
	tipo = null;
	headerMain = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		this.App.layout = this.layout;
	}

	notificarRequest(solicitud) {
		const entity = new IndependienteAprobarModel(solicitud);
		const view = new NotificarView({
			model: entity,
		});

		this.listenTo(view, 'load:aprobar', this.__aprobarSolicitud);
		this.listenTo(view, 'load:devolver', this.__devolverSolicitud);
		this.listenTo(view, 'load:rechazar', this.__rechazaSolicitud);

		this.layout.getRegion('body').show(view);

		this.loadHeaders(solicitud.toJSON());
	}

	loadHeaders(model) {
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Notificar',
				detalle: 'Notificar solicitud',
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);

		model.option = {
			deshacer: true,
			aportes: true,
			volver: true,
			editar: true,
			notificar: false,
			info: true,
		};

		this.headerView = new HeaderInfoView({
			model: model,
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:editar', this.__editarRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesInformation);
		this.listenTo(this.headerView, 'load:info', this.__infoRequest);
		this.layout.getRegion('subheader').show(this.headerView);
	}
}
