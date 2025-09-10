import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { PensionadoInfoView } from './views/PensionadoInfoView';
import { PensionadoAprobarModel } from './models/PensionadoAprobarModel';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';

export default class PensionadoInformation extends ControllerValidation {
	tipo = null;
	headerMain = null;
	headerView = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		this.App.layout = this.layout;
	}

	infoRequest(collection = {}) {
		this.solicitudModel = collection.solicitud;

		const view = new PensionadoInfoView({
			model: new PensionadoAprobarModel({ id: this.solicitudModel.get('id') }),
			collection: collection,
		});

		this.listenTo(view, 'load:aprobar', this.__aprobarSolicitud);
		this.listenTo(view, 'load:devolver', this.__devolverSolicitud);
		this.listenTo(view, 'load:rechazar', this.__rechazaSolicitud);

		this.layout.getRegion('body').show(view);

		this.loadHeaders();
	}

	loadHeaders() {
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Aprobar pensionado',
				detalle:
					'Infomación de pensionado con identificación ' +
					this.solicitudModel.get('cedtra'),
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);

		this.headerView = new HeaderInfoView({
			model: {
				id: this.solicitudModel.get('id'),
				estado: this.solicitudModel.get('estado'),
				option: {
					deshacer: true,
					aportes: true,
					volver: true,
					editar: true,
					notificar: false,
					info: false,
				},
			},
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:editar', this.__editarRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesInformation);
		this.listenTo(this.headerView, 'load:notificar', this.__notificarRequest);

		this.layout.getRegion('subheader').show(this.headerView);
	}
}
