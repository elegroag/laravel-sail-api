import { $App } from '@/App';
import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { ControllerConyuges } from './ControllerConyuges';

class RouterConyuges extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {},
			controller: $App.startSubApplication(ControllerConyuges, '', {
				content: '#boneLayout',
			}),
		});
	}
}

export { RouterConyuges };
