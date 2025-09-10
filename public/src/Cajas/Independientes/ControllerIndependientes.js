import { $App } from '@/App';
import { Controller } from '@/Common/Controller';
import { AportesCollection } from '@/Componentes/Collections/AportesCollection';

import IndependienteModel from './models/IndependienteModel';
import IndependientesListas from './IndependientesListas';
import IndependienteInformation from './IndependienteInformation';
import IndependienteNotificar from './IndependienteNotificar';
import IndependienteAportes from './IndependienteAportes';
import IndependienteDeshacer from './IndependienteDeshacer';
import IndependienteReaprobar from './IndependienteReaprobar';
import IndependienteEditar from './IndependienteEditar';

class ControllerIndependientes extends Controller {
	constructor(options = {}) {
		super(options);
	}

	listRequests(tipo = '', pagina = 0) {
		const app = this.startController(IndependientesListas);
		app.listRequests(tipo, pagina);
	}

	infoRequest(id) {
		const app = this.startController(IndependienteInformation);
		$App.trigger('syncro', {
			url: 'infor',
			data: {
				id: id,
			},
			callback: (response) => {
				if (response) {
					app.infoRequest({
						solicitud: new IndependienteModel(response.data),
						empresa_sisuweb: response.empresa_sisuweb,
						mercurio11: response.mercurio11,
						consulta: response.consulta_empresa,
						adjuntos: response.adjuntos,
						seguimiento: response.seguimiento,
						campos_disponibles: response.campos_disponibles,
					});
				}
			},
		});
	}

	aportesRequest(id = 0) {
		const app = this.startController(IndependienteAportes);
		const url = $App.kumbiaURL('aprobaindepen/aportes/' + id);
		$App.trigger('syncro', {
			url: url,
			data: {
				id,
			},
			callback: (response) => {
				if (response) {
					const aportes = new AportesCollection(response.data);
					const solicitud = new IndependienteModel(response.solicitud);
					app.aportesRequest(solicitud, aportes);
				} else {
					$App.trigger('alert:error', { message: 'No se pudo cargar la solicitud' });
					$App.router.navigate('list', { trigger: true });
				}
			},
		});
	}

	editarRequest(id = 0) {
		const app = this.startController(IndependienteEditar);
		$App.trigger('syncro', {
			url: 'infor',
			data: {
				id,
			},
			callback: (response) => {
				if (response) {
					app.editarRequest({
						solicitud: new IndependienteModel(response.data),
						empresa_sisuweb: response.empresa_sisuweb,
						mercurio11: response.mercurio11,
						consulta: response.consulta_empresa,
						adjuntos: response.adjuntos,
						seguimiento: response.seguimiento,
						campos_disponibles: response.campos_disponibles,
					});
				}
			},
		});
	}

	notificarRequest(id) {
		const app = this.startController(IndependienteNotificar);
		$App.trigger('syncro', {
			url: 'infor',
			data: {
				id: id,
			},
			callback: (response) => {
				if (response) {
					const solicitud = new IndependienteModel(response.data);
					app.notificarRequest(solicitud);
				}
			},
		});
	}

	deshacerRequest(_id) {
		const app = this.startController(IndependienteDeshacer);
		app.deshacerRequest(_id);
	}

	reaprobarRequest(_id) {
		const app = this.startController(IndependienteReaprobar);
		app.reaprobarRequest(_id);
	}
}

export { ControllerIndependientes };
