import { AfiliationRouter } from '@/Componentes/Routers/AfiliationRouter';
import { AfiliationService } from '@/Componentes/Services/AfiliationService';
import { ControllerConyuges } from './ControllerConyuges';

class RouterConyuges extends AfiliationRouter {
	constructor(options = {}) {
		super({
			...options,
			controller: window.App.startSubApplication(
				ControllerConyuges,
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

export { RouterConyuges };
