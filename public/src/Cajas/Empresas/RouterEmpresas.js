import { ControllerEmpresas } from './ControllerEmpresas';
import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { $App } from '@/App';

class RouterEmpresas extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {
				'aportes/:id': 'aportesRute',
				reportes: 'reportesRute',
			},
			controller: $App.startSubApplication(ControllerEmpresas),
		});
	}
}

export { RouterEmpresas };
