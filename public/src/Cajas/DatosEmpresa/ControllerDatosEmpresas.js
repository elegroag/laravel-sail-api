import { $App } from '@/App';
import { Controller } from '@/Common/Controller';
import DatosEmpresaListar from './DatosEmpresaListar';
import DatosEmpresaInformation from './DatosEmpresaInformation';

class ControllerDatosEmpresas extends Controller {
	constructor(options = {}) {
		super(options);
	}

	listRequests(tipo = '', pagina = 0) {
		const app = this.startController(DatosEmpresaListar);
		app.listRequests(tipo, pagina);
	}

	infoRequest(id=0) {
		const app = this.startController(DatosEmpresaInformation);
		$App.trigger('syncro', {
			url: 'infor',
			data: {
				id
			},
			callback: (response) => {
				if (response && response.success) {
					app.infoRequest(response);
				}else{
					$App.trigger('alert:error', {
						message: response.message,
					});
				}
			},
		});
	}

	aportesRequest(_id) {}

	editarRequest(_id) {}
}

export { ControllerDatosEmpresas };
