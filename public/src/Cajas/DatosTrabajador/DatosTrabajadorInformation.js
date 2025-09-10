import { $App } from "@/App";
import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import DatosTrabajadorModel  from './models/DatosTrabajadorModel';
import DatosTrabajadorAprobarModel from './models/DatosTrabajadorAprobarModel';
import DatosTrabajadorInfoView from './views/DatosTrabajadorInfoView';

export default class DatosTrabajadorInformation extends ControllerValidation {

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		$App.layout = this.layout;
	}

	infoRequest(response) {
		const solicitud = new DatosTrabajadorModel(response.data);
		const entity = new DatosTrabajadorAprobarModel();
		entity.set('id', solicitud.get('id'));

		const view = new DatosTrabajadorInfoView({
			model: entity,
			collection: {
				solicitud: solicitud,
				empresa_sisuweb: response.empresa_sisuweb,
				trabajador_sisu: response.trabajador_sisu,
				mercurio11: response.mercurio11,
				consulta: response.consulta,
				adjuntos: response.adjuntos,
				seguimiento: response.seguimiento,
				campos_disponibles: response.campos_disponibles,
				empresa_sisu: response.empresa_sisu,
				componente_codsuc: response.componente_codsuc,
				componente_codlis: response.componente_codlis,
			},
		});

		this.listenTo(view, 'load:aprobar', this.__aprobarSolicitud);
		this.listenTo(view, 'load:devolver', this.__devolverSolicitud);
		this.listenTo(view, 'load:rechazar', this.__rechazaSolicitud);

		$App.layout.getRegion('body').show(view);
	}
}
