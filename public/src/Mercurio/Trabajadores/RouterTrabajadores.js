import { $App } from '@/App';
import { AfiliationRouter } from '@/Componentes/Routers/AfiliationRouter';
import { AfiliationService } from '@/Componentes/Services/AfiliationService';
import { ControllerTrabajadores } from './ControllerTrabajadores';

class RouterTrabajadores extends AfiliationRouter {
	constructor(options = {}) {
		super({
			...options,
			controller: $App.startSubApplication(
				ControllerTrabajadores,
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

export { RouterTrabajadores };
