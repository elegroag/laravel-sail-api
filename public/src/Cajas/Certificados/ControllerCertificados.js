import { Controller } from '@/Common/Controller';
import CertificadosListar from './CertificadosListar';
import CertificadoInformation from './CertificadoInformation';

class ControllerCertificados extends Controller {
	constructor(options = {}) {
		super(options);
	}

	listRequests(tipo = '', pagina = 0) {
		const app = this.startController(CertificadosListar);
		app.listRequests({tipo, pagina});
	}

	infoRequest(id = 0) {
		this.App.trigger('syncro', {
			url: 'infor',
			data: {
				id,
			},
			callback: (response) => {
				if (response) {
					const app = this.startController(CertificadoInformation);
					app.infoRequest(response);
				}
			},
		});
	}
}

export { ControllerCertificados };
