import { $App } from '@/App';
import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { ControllerMadres } from './ControllerMadres';

class RouterMadres extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {
				'aportes/:id': 'aportesRute',
			},
			controller: $App.startSubApplication(ControllerMadres, '', {
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

export { RouterMadres };
