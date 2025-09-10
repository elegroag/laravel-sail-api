import { ApruebaRouter } from '@/Componentes/Routers/ApruebaRouter';
import { ControllerCertificados } from './ControllerCertificados';
import { $App } from '@/App';

class RouterCertificados extends ApruebaRouter {
	constructor(options) {
		super({
			...options,
			routes: {},
			controller: $App.startSubApplication(ControllerCertificados, '', {
				content: '#boneLayout',
			}),
		});
	}
}

export { RouterCertificados };
