import { $App } from '@/App';
import { Controller } from '@/Common/Controller';
import { AportesCollection } from '@/Componentes/Collections/AportesCollection';
import FacultativoListas from './FacultativoListas';
import FacultativoModel from './models/FacultativoModel';
import FacultativoInformation from './FacultativoInformation';
import FacultativoAportes from './FacultativoAportes';
import FacultativoDeshacer from './FacultativoDeshacer';
import FacultativoReaprobar from './FacultativoReaprobar';

class ControllerFacultativos extends Controller {
	constructor(options = {}) {
		super(options);
	}

	listRequests(tipo = '', pagina = 0) {
		const app = this.startController(FacultativoListas);
		app.listRequests(tipo, pagina);
	}

	infoRequest(id) {
		const app = this.startController(FacultativoInformation);
		$App.trigger('syncro', {
			url: 'infor',
			data: {
				id
			},
			callback: (response) => {
				if (response) {
					app.infoRequest({
						solicitud: new FacultativoModel(response.data),
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
		const app = this.startController(FacultativoAportes);
		const url = $App.url('aportes/' + id);
		$App.trigger('syncro', {
			url: url,
			data: {
				id,
			},
			callback: (response) => {
				if (response) {
					const aportes = new AportesCollection(response.data);
					const solicitud = new FacultativoModel(response.solicitud);
					app.aportesRequest(solicitud, aportes);
				} else {
					$App.trigger('alert:error', { message: 'No se pudo cargar la solicitud' });
					$App.router.navigate('list', { trigger: true });
				}
			},
		});
	}

	deshacerRequest(_id) {
		const app = this.startController(FacultativoDeshacer);
		app.deshacerRequest(_id);
	}

	reaprobarRequest(_id) {
		const app = this.startController(FacultativoReaprobar);
		app.reaprobarRequest(_id);
	}
}

export { ControllerFacultativos };
