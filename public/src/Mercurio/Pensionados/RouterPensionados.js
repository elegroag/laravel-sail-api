import { $App } from '@/App';
import { AfiliationRouter } from '@/Componentes/Routers/AfiliationRouter';
import { AfiliationService } from '@/Componentes/Services/AfiliationService';
import { ControllerPensionados } from './ControllerPensionados';

class RouterPensionados extends AfiliationRouter {
	constructor(options) {
		super({
			...options,
			controller: $App.startSubApplication(
				ControllerPensionados,
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

export { RouterPensionados };
