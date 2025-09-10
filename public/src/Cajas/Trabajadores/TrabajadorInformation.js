import { $App } from '@/App';
import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import TrabajadorInfoView from './views/TrabajadorInfoView';

export default class TrabajadorInformation extends ControllerValidation {
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

	infoRequest(collection={}) {
		this.solicitudModel = collection.solicitud;

		const view = new TrabajadorInfoView({
			model: collection.entity,
			collection: {
				solicitud: this.solicitudModel,
				empresa_sisuweb: collection.empresa_sisuweb,
				trabajador_sisu: collection.trabajador_sisu,
				mercurio11: collection.mercurio11,
				consulta: collection.consulta,
				adjuntos: collection.adjuntos,
				seguimiento: collection.seguimiento,
				campos_disponibles: collection.campos_disponibles,
				empresa_sisu: collection.empresa_sisu,
				componente_codsuc: collection.componente_codsuc,
				componente_codlis: collection.componente_codlis,
			},
		});

		this.listenTo(view, 'load:aprobar', this.__aprobarSolicitud);
		this.listenTo(view, 'load:devolver', this.__devolverSolicitud);
		this.listenTo(view, 'load:rechazar', this.__rechazaSolicitud);
		this.layout.getRegion('body').show(view);
		this.loadHeaders();
	}

	loadHeaders() {
		const estado = this.solicitudModel.get('estado');
		const id = this.solicitudModel.get('id');

		this.headerView = new HeaderInfoView({
			model: {
				estado: estado,
				id: id,
				option: {
					deshacer: estado == 'A' ? true : false,
					aportes: estado == 'A' ? false : true,
					volver: true,
					editar: estado == 'A' ? false : true,
					info: false,
					notificar: false,
					trayectoria: true,
				},
			},
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:editar', this.__editarRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesInformation);
		this.listenTo(this.headerView, 'load:reaprobar', this.reaprobarSolicitud);
		this.listenTo(this.headerView, 'load:deshacer', this.deshacerSolicitud);
		this.listenTo(this.headerView, 'load:trayectoria', this.trayectoriaRequest);

		this.layout.getRegion('subheader').show(this.headerView);

		const tipo_detalle = this.__estados[estado];
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Aprobar trabajador',
				detalle: 'Lista trabajadores ' + tipo_detalle,
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

	trayectoriaRequest() {
		const id = this.solicitudModel.get('id');
		this.App.router.navigate('trayectoria/' + id, { trigger: true, replace: true });
	}
}
