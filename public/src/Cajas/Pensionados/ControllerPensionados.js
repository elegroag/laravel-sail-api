import { $App } from '@/App';
import { Controller } from '@/Common/Controller';
import { AportesCollection } from '@/Componentes/Collections/AportesCollection';
import { PensionadoModel } from './models/PensionadoModel';

import PensionadosListas from './PensionadosListas';
import PensionadoInformation from './PensionadoInformation';
import PensionadoNotificar from './PensionadoNotificar';
import PensionadoAportes from './PensionadoAportes';

class ControllerPensionados extends Controller {
	constructor(options = {}) {
		super(options);
	}

	listRequests(tipo = '', pagina = 0) {
		const app = this.startController(PensionadosListas);
		app.listRequests(tipo, pagina);
	}

	infoRequest(id = 0) {
		const app = this.startController(PensionadoInformation);
		$App.trigger('syncro', {
			url: 'infor',
			data: {
				id,
			},
			callback: (response) => {
				if (response) {
					app.infoRequest({
						solicitud: new PensionadoModel(response.data),
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
		const app = this.startController(PensionadoAportes);
		const url = $App.url('aportes/' + id);
		$App.trigger('syncro', {
			url: url,
			data: {
				id,
			},
			callback: (response) => {
				if (response) {
					const aportes = new AportesCollection(response.data);
					const solicitud = new PensionadoModel(response.solicitud);
					app.aportesRequest(solicitud, aportes);
				} else {
					$App.trigger('alert:error', { message: 'No se pudo cargar la solicitud' });
					$App.router.navigate('list', { trigger: true });
				}
			},
		});
	}

	notificarRequest(id) {
		const app = this.startController(PensionadoNotificar);
		$App.trigger('syncro', {
			url: 'infor',
			data: {
				id: id,
			},
			callback: (response) => {
				if (response) {
					const solicitud = new PensionadoModel(response.data);
					app.notificarRequest(solicitud);
				}
			},
		});
	}

	editarRequest(id) {}
}

export { ControllerPensionados };
