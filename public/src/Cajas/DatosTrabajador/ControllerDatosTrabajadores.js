import { $App } from '@/App';
import { Controller } from '@/Common/Controller';
import DatosTrabajadorInformation from './DatosTrabajadorInformation';
import DatosTrabajadorListar from './DatosTrabajadorListar';

class ControllerDatosTrabajadores extends Controller {
	constructor(options = {}) {
		super(options);
	}

	listRequests(tipo = '', pagina = 0) {
		const app = this.startController(DatosTrabajadorListar);
		app.listRequests(tipo, pagina);
	}

	infoRequest(id=0) {
		$App.trigger('syncro', {
			url: 'infor',
			data: {
				id
			},
			callback: (response) => {
				if (response) {
					const app = this.startController(DatosTrabajadorInformation);
					app.infoRequest(response);
				}
			},
		});
	}
}

export { ControllerDatosTrabajadores };
