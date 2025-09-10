import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { ControllerDatosEmpresas } from './ControllerDatosEmpresas';
import { $App } from '@/App';

class RouterDatosEmpresas extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {},
			controller: $App.startSubApplication(ControllerDatosEmpresas, '', {
				content: '#boneLayout',
			}),
		});
	}
}

export { RouterDatosEmpresas };
