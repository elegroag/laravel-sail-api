import { $App } from '@/App';
import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { ControllerTrabajadores } from './ControllerTrabajadores';

class RouterTrabajadores extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {},
			controller: $App.startSubApplication(ControllerTrabajadores, '', {
				content: '#boneLayout',
			}),
		});
	}
}

export { RouterTrabajadores };
