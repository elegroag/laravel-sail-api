import { $App } from '@/App';
import { AfiliationRouter } from '@/Componentes/Routers/AfiliationRouter';
import { AfiliationService } from '@/Componentes/Services/AfiliationService';
import { ControllerFacultativos } from './ControllerFacultativos';

class RouterFacultativos extends AfiliationRouter {
	constructor(options) {
		super({
			...options,
			controller: $App.startSubApplication(
				ControllerFacultativos,
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

export { RouterFacultativos };
