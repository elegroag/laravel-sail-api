import { $App } from '@/App';
import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { ControllerIndependientes } from './ControllerIndependientes';

class RouterIndependientes extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {
				'aportes/:id': 'aportesRute',
				'notificar/:id': 'notificarRute',
			},
			controller: $App.startSubApplication(ControllerIndependientes, '', {
				content: '#boneLayout',
			}),
		});
	}
}

export { RouterIndependientes };
