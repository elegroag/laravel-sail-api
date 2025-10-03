import { ControllerRequest } from '../ControllerRequest';
import { FormIndependentView } from './views/FormIndependentView';
import { IndependientesView } from './views/IndependientesView';
import { IndependienteModel } from './models/IndependienteModel';

class ControllerIndependientes extends ControllerRequest {
	constructor(options) {
		super({
			...options,
			EntityModel: IndependienteModel,
			TableView: IndependientesView,
			FormRequest: FormIndependentView,
			tipo: 'I',
			headerOptions: {
				estado: '',
				tipo: 'I',
				url_nueva: options.App.url('nueva'),
				breadcrumb_menu: 'Crear solicitud',
				titulo: 'Afiliación independiente',
				url_masivo: null,
				isNew: null,
				create: 'enabled',
			},
		});

		if (this.services) _.extend(this, this.services);
		this.on('form:cancel', this.destroy);
		this.on('form:find', this.afiService.validePk);
		this.on('form:digit', this.afiService.digitVer);
		this.once('params', this.afiService.paramsServer);

		options.App.Collections.formParams = null;
		options.App.Collections.firmas = null;
	}
}

export { ControllerIndependientes };
