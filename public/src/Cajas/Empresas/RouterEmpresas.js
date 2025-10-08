import { ControllerEmpresas } from './ControllerEmpresas';
import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';

class RouterEmpresas extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {
				'aportes/:id': 'aportesRute',
				reportes: 'reportesRute',
			},
			controller: window.App.startSubApplication(ControllerEmpresas),
		});
	}
}

export { RouterEmpresas };
