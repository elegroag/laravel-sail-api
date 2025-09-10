import { $App } from '@/App';
import { Controller } from '@/Common/Controller';
import { UsuarioModel } from './models/UsuarioModel';
import { DetalleUsuario } from './DetalleUsuario';
import ListarUsuarios from './ListarUsuarios';

class ControllerUsuario extends Controller {
	constructor(options = {}) {
		super(options);
	}

	detalleUsuario(documento, tipo, coddoc) {
		this.startController(DetalleUsuario);
		$App.trigger('syncro', {
			data: {
				documento: documento,
				tipo: tipo,
				coddoc: coddoc,
			},
			silent: true,
			url: 'show_user',
			callback: (response) => {
				if (response) {
					const model = new UsuarioModel(response.data);
					model.set('isEdit', -1);
					this.currentController.detalleUsuario(model);
				}
			},
		});
	}

	editarUsuario(documento, tipo, coddoc) {
		this.startController(DetalleUsuario);
		$App.trigger('syncro', {
			data: {
				documento: documento,
				tipo: tipo,
				coddoc: coddoc,
			},
			silent: true,
			url: 'show_user',
			callback: (response) => {
				if (response) {
					const model = new UsuarioModel(response.data);
					model.set('isEdit', 1);
					this.currentController.detalleUsuario(model);
				}
			},
		});
	}

	listarUsuarios(tipo = '') {
		this.startController(ListarUsuarios);
		this.currentController.listarUsuarios(tipo);
	}
}

export { ControllerUsuario };
