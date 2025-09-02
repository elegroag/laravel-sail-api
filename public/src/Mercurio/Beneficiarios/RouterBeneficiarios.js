import { $App } from '@/App';
import { AfiliationRouter } from '@/Componentes/Routers/AfiliationRouter';
import { AfiliationService } from '@/Componentes/Services/AfiliationService';
import { ControllerBeneficiarios } from './ControllerBeneficiarios';

class RouterBeneficiarios extends AfiliationRouter {
	constructor(options) {
		super({
			...options,
			controller: $App.startSubApplication(
				ControllerBeneficiarios,
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

export { RouterBeneficiarios };
