import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { ConyugeInfoView } from './views/ConyugeInfoView';
import { ConyugeAprobarModel } from './models/ConyugeAprobarModel';

export default class ConyugeInformation extends ControllerValidation {
	tipo = null;
	headerMain = null;
	headerView = null;
	solicitudModel = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.titulo = 'Aprobar cónyuge';
		this.titulo_detalle = 'Lista cónyuges';

		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		this.App.layout = this.layout;
	}

	infoRequest(collection={}) {
		this.solicitudModel = collection.solicitud;

		const entity = new ConyugeAprobarModel({
			id: this.solicitudModel.get('id'),
			cedtra: this.solicitudModel.get('cedtra'),
			tipdoc: this.solicitudModel.get('tipdoc'),
		})

		const view = new ConyugeInfoView({
			model: entity,
			collection: {
				solicitud: this.solicitudModel,
				conyuges_sisu: collection.conyuges_sisu,
				mercurio11: collection.mercurio11,
				consulta: collection.consulta,
				adjuntos: collection.adjuntos,
				seguimiento: collection.seguimiento,
				campos_disponibles: collection.campos_disponibles,
			},
		});

		this.listenTo(view, 'load:aprobar', this.__aprobarSolicitud);
		this.listenTo(view, 'load:devolver', this.__devolverSolicitud);
		this.listenTo(view, 'load:rechazar', this.__rechazaSolicitud);

		this.layout.getRegion('body').show(view);

		this.loadHeaders();
	}

	loadHeaders() {
		this.headerView = new HeaderInfoView({
			model: {
				id: this.solicitudModel.get('id'),
				estado: this.solicitudModel.get('estado'),
				option: {
					deshacer: this.solicitudModel.get('estado') == 'A' ? true : false,
					aportes: false,
					volver: true,
					editar: this.solicitudModel.get('estado') == 'A' ? false : true,
					info: false,
					notificar: false,
				}
			}
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:editar', this.__editarRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesInformation);
		this.listenTo(this.headerView, 'load:reaprobar', this.reaprobarSolicitud);
		this.listenTo(this.headerView, 'load:deshacer', this.deshacerSolicitud);

		this.layout.getRegion('subheader').show(this.headerView);

		this.headerMain = new HeaderCajasView({
			model: {
				titulo: this.titulo,
				detalle: this.titulo_detalle + ' - ' + this.__estados[this.solicitudModel.get('estado')],
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);
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
