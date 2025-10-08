import { ControllerUsuario } from './ControllerUsuario';

class RouterUsuario extends Backbone.Router {
	constructor(options = {}) {
		super({
			...options,
			routes: {
				list: 'listarUsuarios',
				'list/:id': 'listarUsuarios',
				'detalle/:id/:tipo/:coddoc': 'detalleUsuario',
				'editar/:id/:tipo/:coddoc': 'editarUsuario',
			},
		});

		this.currentApp = window.App.startSubApplication(ControllerUsuario);
		this._bindRoutes();
	}

	detalleUsuario(id = '', tipo = '', coddoc = '') {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			window.App.trigger('alert:error', {
				message: 'No hay un usuario seleccionado para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.detalleUsuario(id, tipo, coddoc);
	}

	editarUsuario(id = '', tipo = '', coddoc = '') {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			window.App.trigger('alert:error', {
				message: 'No hay un usuario seleccionado para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.editarUsuario(id, tipo, coddoc);
	}

	listarUsuarios(tipo = '') {
		this.currentApp.listarUsuarios(tipo);
	}
}

export { RouterUsuario };
