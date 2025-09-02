import { $App } from '@/App';
import { ControllerRequest } from '../ControllerRequest';
import { FormBeneficiarioView } from './views/FormBeneficiarioView';
import { BeneficiariosView } from './views/BeneficiariosView';
import { BeneficiarioModel } from './models/BeneficiarioModel';

class ControllerBeneficiarios extends ControllerRequest {
	constructor(options = {}) {
		super({
			...options,
			EntityModel: BeneficiarioModel,
			TableView: BeneficiariosView,
			FormRequest: FormBeneficiarioView,
			tipo: 'B',
			headerOptions: {
				estado: '',
				tipo: 'B',
				url_nueva: $App.url('nueva'),
				breadcrumb_menu: 'Crear solicitud',
				titulo: 'Afiliación beneficiarios',
				url_masivo: null,
				isNew: null,
				create: 'enabled',
			},
		});

		if (this.services) _.extend(this, this.services);
		this.on('form:cancel', this.destroy);
		this.on('form:digit', this.afiService.digitVer);
		this.on('params', this.afiService.paramsServer);

		$App.Collections.formParams = null;
		$App.Collections.firmas = null;
	}

	__validaBeneficiario(transfer) {
		const { cedcon, callback } = transfer;
		$App.trigger('syncro', {
			url: $App.url('valida'),
			data: {
				cedcon,
			},
			callback: (response) => {
				if (response) {
					if (response.success) {
						let solicitud = false;

						if (response.solicitud_previa !== false) {
							solicitud = response.solicitud_previa;
						}
						if (response.conyuge !== false) {
							solicitud = response.beneficiario;
						}
						return callback(solicitud);
					} else {
						$App.trigger('alert:error', { message: response.msj });
					}
				}
				return callback(false);
			},
		});
	}

	__traerConyugue(transfer) {
		const { cedcon, callback } = transfer;
		$App.trigger('suncro', {
			url: $App.url('traerConyugue'),
			data: {
				cedcon,
			},
			callback: (response) => {
				if (response) {
					callback(response);
				}
			},
		});
	}
}

export { ControllerBeneficiarios };
