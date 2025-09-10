import { $App } from '@/App';
import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { ControllerBeneficiarios } from './ControllerBeneficiarios';

class RouterBeneficiarios extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {},
			controller: $App.startSubApplication(ControllerBeneficiarios, '', {
				content: '#boneLayout',
			}),
		});
	}

	aportesRute(id) {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.aportesRequest(id);
	}
}

export { RouterBeneficiarios };
