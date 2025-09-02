import { $App } from '@/App';
import { ControllerUsuario } from './ControllerUsuario';

class RouterUsuario extends Backbone.Router {
	constructor(options = {}) {
		super({
			...options,
			routes: {
				datos: 'renderPerfil',
				editar: 'editaPerfil',
			},
		});

		this.currentApp = $App.startSubApplication(ControllerUsuario);
		this._bindRoutes();
	}

	renderPerfil() {
		this.currentApp.renderPerfil();
	}

	editaPerfil() {
		this.currentApp.editaPerfil();
	}
}

export { RouterUsuario };
