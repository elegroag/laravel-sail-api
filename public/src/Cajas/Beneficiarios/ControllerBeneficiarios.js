import { $App } from '@/App';
import { Controller } from '@/Common/Controller';
import { BeneficiarioModel } from './models/BeneficiarioModel';
import { BeneficiarioAprobarModel } from './models/BeneficiarioAprobarModel';

import BeneficiarioDeshacer from './BeneficiarioDeshacer';
import BeneficiarioInformation from './BeneficiarioInformation';
import BeneficiariosListar from './BeneficiariosListar';
import BeneficiarioReaprobar from './BeneficiarioReaprobar';

class ControllerBeneficiarios extends Controller {
	constructor(options = {}) {
		super(options);
	}

	listRequests(tipo = '', pagina = 0) {
		const app = this.startController(BeneficiariosListar);
		app.listRequests(tipo, pagina);
	}

	infoRequest(_id) {
		const app = this.startController(BeneficiarioInformation);
		this.App.trigger('syncro', {
			url: 'infor',
			data: {
				id: _id,
			},
			callback: (response) => {
				if (response) {
					const solicitud = new BeneficiarioModel(response.data);
					const entity = new BeneficiarioAprobarModel();
					entity.set('id', solicitud.get('id'));
					app.infoRequest(solicitud, entity, response);
				}
			},
		});
	}

	deshacerRequest(_id){
		const app = this.startController(BeneficiarioDeshacer);
		app.deshacerRequest(_id);
	}

	reaprobarRequest(_id){
		const app = this.startController(BeneficiarioReaprobar);
		app.reaprobarRequest(_id);
	}
}

export { ControllerBeneficiarios };
