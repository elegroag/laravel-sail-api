import { $App } from '@/App';
import { AfiliationRouter } from '@/Componentes/Routers/AfiliationRouter';
import { AfiliationService } from '@/Componentes/Services/AfiliationService';
import { ControllerEmpresas } from './ControllerEmpresas';

class RouterEmpresas extends AfiliationRouter {
	constructor(options) {
		super({
			...options,
			controller: $App.startSubApplication(
				ControllerEmpresas,
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

export { RouterEmpresas };
