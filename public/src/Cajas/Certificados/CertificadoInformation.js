import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import CertificadoInfoView from './views/CertificadoInfoView';
import CertificadoModel from './models/CertificadoModel';
import CertificadoAprobarModel from './models/CertificadoAprobarModel';


export default class CertificadoInformation extends ControllerValidation {
	solicitudModel = null;

	constructor(options) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		this.App.layout = this.layout;
	}

	infoRequest(response={}) {
		this.solicitudModel = new CertificadoModel(response.data);
		const entity = new CertificadoAprobarModel({id: this.solicitudModel.get('id')});
		const view = new CertificadoInfoView({
			model: entity,
			collection: {
				solicitud: this.solicitudModel,
				mercurio11: response.mercurio11,
				consulta: response.consulta,
				adjuntos: response.adjuntos,
				seguimiento: response.seguimiento,
				campos_disponibles: response.campos_disponibles,
			}
		});

		this.listenTo(view, 'load:aprobar', this.__aprobarSolicitud);
		this.listenTo(view, 'load:devolver', this.__devolverSolicitud);
		this.listenTo(view, 'load:rechazar', this.__rechazaSolicitud);
		this.layout.getRegion('body').show(view);

		this.loadHeaders();
	}

	loadHeaders() {
		let tipo_detalle = this.__estados[this.solicitudModel.get('estado')];
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Aprobar certificado',
				detalle: 'Lista certificados ' + tipo_detalle,
				info: false
			},
		});
		this.App.layout.getRegion('header').show(this.headerMain);

		this.headerView = new HeaderInfoView({
			model: {
				id: this.solicitudModel.get('id'),
				estado: this.solicitudModel.get('estado'),
				option: {
					deshacer: this.solicitudModel.get('estado') == 'A' ? true : false,
					aportes: false,
					volver: true,
					editar: this.solicitudModel.get('estado') == 'A' ? false : true,
					notificar: false,
					info: false,
				}
			}
		});

		this.listenTo(this.headerView, 'load:deshacer', this.deshacerSolicitud);
		this.listenTo(this.headerView, 'load:reaprobar', this.reaprobarSolicitud);
		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.App.layout.getRegion('subheader').show(this.headerView);
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
