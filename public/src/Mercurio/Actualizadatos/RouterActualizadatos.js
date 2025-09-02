import { $App } from '@/App';
import { AfiliationRouter } from '@/Componentes/Routers/AfiliationRouter';
import { AfiliationService } from '@/Componentes/Services/AfiliationService';
import { ControllerActualizadatos } from './ControllerActualizadatos';

class RouterActualizadatos extends AfiliationRouter {
	constructor(options = {}) {
		super({
			...options,
			controller: $App.startSubApplication(
				ControllerActualizadatos,
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

export { RouterActualizadatos };
