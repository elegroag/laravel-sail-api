import { $App } from '@/App';
import { AfiliationRouter } from '@/Componentes/Routers/AfiliationRouter';
import { AfiliationService } from '@/Componentes/Services/AfiliationService';
import { ControllerDatosTrabajador } from './ControllerDatosTrabajador';

class RouterDatosTrabajador extends AfiliationRouter {
	constructor(options = {}) {
		super({
			...options,
			controller: $App.startSubApplication(
				ControllerDatosTrabajador,
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

export { RouterDatosTrabajador };
