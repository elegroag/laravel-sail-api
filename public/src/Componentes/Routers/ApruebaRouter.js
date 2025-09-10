import { $App } from '@/App';

class ApruebaRouter extends Backbone.Router {
	constructor(options) {
		super({
			routes: {
				...options.routes,
				list: 'listRute',
				'list/:tipo': 'listRute',
				'list/:tipo/:pagina': 'listRute',
				'edit/:id': 'editRute',
				'info/:id': 'infoRute',
				'deshacer/:id': 'deshacerRute',
				'reaprobar/:id': 'reaprobarRute',
				'aportes/:id': 'aportesRute',
				'trayectoria/:id': 'trayectoriaRute',
			},
		});

		this.currentApp = options.controller;
		this._bindRoutes();
	}

	listRute(tipo = undefined, pagina = 0) {
		if (tipo === null) tipo = 'P';
		if (tipo === false) tipo = 'P';
		this.currentApp.listRequests(tipo, pagina);
	}

	editRute(id = undefined) {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.editarRequest(id);
	}

	infoRute(id) {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.infoRequest(id);
	}

	deshacerRute(id) {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.deshacerRequest(id);
	}

	reaprobarRute(id) {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.reaprobarRequest(id);
	}

	aportesRute(id) {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.aportesRequest(id);
	}

	notificarRute(id) {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.notificarRequest(id);
	}

	trayectoriaRute(id) {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.trayectoriaRequest(id);
	}

	reportesRute() {
		this.currentApp.reportesRequest();
	}
}

export { ApruebaRouter };
