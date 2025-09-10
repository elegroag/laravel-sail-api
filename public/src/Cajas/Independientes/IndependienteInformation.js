import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import IndependienteInfoView from './views/IndependienteInfoView';
import IndependienteAprobarModel from './models/IndependienteAprobarModel';

export default class IndependienteInformation extends ControllerValidation {
	tipo = null;
	headerMain = null;
	headerView = null;
	solicitudModel = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		this.App.layout = this.layout;
	}

	infoRequest(collection = {}) {
		this.solicitudModel = collection.solicitud;

		const view = new IndependienteInfoView({
			model: new IndependienteAprobarModel({ id: this.solicitudModel.get('id') }),
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
				titulo: 'Aprobar independiente',
				detalle: 'Independiente con identificación ' + this.solicitudModel.get('cedtra'),
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
					editar: false,
					notificar: false,
					info: false,
				},
			},
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:editar', this.__editarRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesInformation);
		this.listenTo(this.headerView, 'load:notificar', this.__notificarRequest);
		this.listenTo(this.headerView, 'load:deshacer', this.deshacerSolicitud);
		this.listenTo(this.headerView, 'load:reaprobar', this.reaprobarSolicitud);

		this.layout.getRegion('subheader').show(this.headerView);
	}

	deshacerSolicitud() {
		const id = this.solicitudModel.get('id');
		this.App.router.navigate('deshacer/' + id, { trigger: true, replace: true });
	}

	reaprobarSolicitud() {
		const id = this.solicitudModel.get('id');
		this.App.router.navigate('reaprobar/' + id, { trigger: true, replace: true });
	}
}
