import { $App } from '@/App';
import { ControllerRequest } from '@/Mercurio/ControllerRequest';
import { FormTrabajadorView } from './views/FormTrabajadorView';
import { TrabajadoresView } from './views/TrabajadoresView';
import { TrabajadorModel } from './models/TrabajadorModel';

class ControllerTrabajadores extends ControllerRequest {
	constructor(options = {}) {
		super({
			...options,
			EntityModel: TrabajadorModel,
			TableView: TrabajadoresView,
			FormRequest: FormTrabajadorView,
			tipo: 'T',
			headerOptions: {
				estado: '',
				tipo: 'T',
				url_nueva: $App.url('nueva'),
				breadcrumb_menu: 'Crear solicitud',
				titulo: 'Afiliación trabajadores',
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
	}
}

export { ControllerTrabajadores };
