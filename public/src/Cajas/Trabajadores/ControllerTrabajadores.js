import { $App } from '@/App';
import { Controller } from '@/Common/Controller';
import { AportesCollection } from '@/Componentes/Collections/AportesCollection';

import TrabajadorModel from './models/TrabajadorModel';
import TrabajadorAprobarModel from './models/TrabajadorAprobarModel';
import TrabajadoresAportes from './TrabajadoresAportes';
import TrabajadorDeshacer from './TrabajadorDeshacer';
import TrabajadorInformation from './TrabajadorInformation';
import TrabajadoresListas from './TrabajadoresListas';
import TrabajadorReaprobar from './TrabajadorReaprobar';
import TrabajadorTrayectoria from './TrabajadorTrayectoria';

class ControllerTrabajadores extends Controller {
	constructor(options = {}) {
		super(options);
	}

	listRequests(tipo = '', pagina = 0) {
		const app = this.startController(TrabajadoresListas);
		app.listRequests(tipo, pagina);
	}

	infoRequest(id) {
		const app = this.startController(TrabajadorInformation);
		this.App.trigger('syncro', {
			url: 'infor',
			data: {
				id
			},
			callback: (response) => {
				if (response) {
					app.infoRequest({
						...response,
						solicitud: new TrabajadorModel(response.data),
						entity: new TrabajadorAprobarModel({id: response.data.id})
					});
				}
			},
		});
	}

	deshacerRequest(_id) {
		const app = this.startController(TrabajadorDeshacer);
		app.deshacerRequest(_id);
	}

	reaprobarRequest(_id) {
		const app = this.startController(TrabajadorReaprobar);
		app.reaprobarRequest(_id);
	}

	aportesRequest(_id) {
		const app = this.startController(TrabajadoresAportes);
		const url = this.App.url('aportes/' + _id);
		this.App.trigger('syncro', {
			url: url,
			data: {
				id: _id,
			},
			callback: (response) => {
				if (response) {
					const aportes = new AportesCollection(response.data);
					const solicitud = new TrabajadorModel(response.solicitud);
					app.aportesRequest(solicitud, aportes);
				} else {
					this.App.trigger('alert:error', { message: 'No se pudo cargar la solicitud' });
					this.App.router.navigate('list', { trigger: true });
				}
			},
		});
	}

	trayectoriaRequest(id) {
		const app = this.startController(TrabajadorTrayectoria);
		$App.trigger('syncro', {
			url: 'buscar_sisu',
			data: { id },
			callback: (response) => {
				if (response && response.success) {
					app.trayectoriaRequest(response.data);
				} else {
					this.App.trigger('alert:error', { message: 'No se pudo cargar la solicitud' });
					this.App.router.navigate('list', { trigger: true });
				}
			},
		});
	}
}

export { ControllerTrabajadores };
