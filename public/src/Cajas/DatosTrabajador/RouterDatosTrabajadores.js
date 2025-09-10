import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { ControllerDatosTrabajadores } from './ControllerDatosTrabajadores';
import { $App } from '@/App';

class RouterDatosTrabajadores extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {},
			controller: $App.startSubApplication(ControllerDatosTrabajadores, '', {
				content: '#boneLayout',
			}),
		});
	}
}

export { RouterDatosTrabajadores };
