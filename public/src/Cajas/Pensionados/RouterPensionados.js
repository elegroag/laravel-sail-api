import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { ControllerPensionados } from './ControllerPensionados';

class RouterPensionados extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {
				'aportes/:id': 'aportesRute',
				'notificar/:id': 'notificarRute',
			},
			controller: window.App.startSubApplication(ControllerPensionados, '', {
				content: '#boneLayout',
			}),
		});
	}
}

export { RouterPensionados };
