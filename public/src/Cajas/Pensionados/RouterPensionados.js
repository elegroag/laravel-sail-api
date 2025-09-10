import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { ControllerPensionados } from './ControllerPensionados';
import { $App } from '@/App';

class RouterPensionados extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {
				'aportes/:id': 'aportesRute',
			},
			controller: $App.startSubApplication(ControllerPensionados, '', {
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

export { RouterPensionados };
