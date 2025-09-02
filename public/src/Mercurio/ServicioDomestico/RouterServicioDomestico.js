import { $App } from '@/App';
import { AfiliationRouter } from '@/Componentes/Routers/AfiliationRouter';
import { ControllerServicioDomestico } from './ControllerServicioDomestico';
import { AfiliationService } from '@/Componentes/Services/AfiliationService';

class RouterServicioDomestico extends AfiliationRouter {
	constructor(options = {}) {
		super({
			...options,
			controller: $App.startSubApplication(
				ControllerServicioDomestico,
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

export { RouterServicioDomestico };
