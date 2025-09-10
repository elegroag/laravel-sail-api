import { Controller } from '@/Common/Controller';
import { ConyugeModel } from './models/ConyugeModel';
import ConyugeDeshacer from './ConyugeDeshacer';
import ConyugeInformation from './ConyugeInformation';
import ConyugesListas from './ConyugesListas';
import ConyugeReaprobar from './ConyugeReaprobar';

class ControllerConyuges extends Controller {
	constructor(options = {}) {
		super(options);
	}

	listRequests(tipo = '', pagina = 0) {
		const app = this.startController(ConyugesListas);
		app.listRequests(tipo, pagina);
	}

	infoRequest(id) {
		const app = this.startController(ConyugeInformation);
		this.App.trigger('syncro', {
			url: 'infor',
			data: {
				id: id,
			},
			callback: (response) => {
				if (response) {
					app.infoRequest({
						...response,
						solicitud: new ConyugeModel(response.data)
					});
				}
			},
		});
	}

	deshacerRequest(_id) {
		const app = this.startController(ConyugeDeshacer);
		app.deshacerRequest(_id);
	}

	reaprobarRequest(_id) {
		const app = this.startController(ConyugeReaprobar);
		app.reaprobarRequest(_id);
	}
}

export { ControllerConyuges };
