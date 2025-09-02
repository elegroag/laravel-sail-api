import { ControllerRequest } from '../ControllerRequest';
import { ConyugeModel } from './models/ConyugeModel';
import { ConyugesView } from './views/ConyugesView';
import { FormConyugeView } from './views/FormConyugeView';
import { $App } from '@/App';

class ControllerConyuges extends ControllerRequest {
	constructor(options) {
		super({
			...options,
			EntityModel: ConyugeModel,
			TableView: ConyugesView,
			FormRequest: FormConyugeView,
			tipo: 'C',
			headerOptions: {
				estado: '',
				tipo: 'C',
				url_nueva: $App.url('nueva'),
				breadcrumb_menu: 'Crear solicitud',
				titulo: 'Afiliación cónyuge',
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

	__validaConyuge(transfer) {
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
							solicitud = response.conyuge;
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

export { ControllerConyuges };
