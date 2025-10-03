import { AfiliationRouter } from '@/Componentes/Routers/AfiliationRouter';
import { AfiliationService } from '@/Componentes/Services/AfiliationService';
import { ControllerIndependientes } from './ControllerIndependientes';

class RouterIndependientes extends AfiliationRouter {
	constructor(options = {}) {
		super({
			...options,
			controller: window.App.startSubApplication(
				ControllerIndependientes,
				{
					name: 'afiService',
					inyectar: AfiliationService,
				},
				{
					form: '#formRequest',
					content: '#boneLayout',
				},
			),
		});
	}
}

export { RouterIndependientes };
