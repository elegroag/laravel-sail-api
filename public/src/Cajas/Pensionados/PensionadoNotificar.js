import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { PensionadoAprobarModel } from './models/PensionadoAprobarModel';
import { NotificarView } from '@/Componentes/Views/NotificarView';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';

export default class PensionadoNotificar extends ControllerValidation {
	#tipo = null;
	#headerMain = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
	}

	notificarRequest(solicitud) {
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		const entity = new PensionadoAprobarModel(solicitud);
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
