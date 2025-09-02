import { $App } from '@/App';

class AfiliationRouter extends Backbone.Router {
	constructor(options = {}) {
		super({
			routes: {
				list: 'listRute',
				'list/:tipo': 'listRute',
				create: 'createRute',
				'proceso/:id': 'procesoRute',
			},
			...options,
		});

		this.currentApp = options.controller;
		this._bindRoutes();
	}

	listRute(tipo = '') {
		this.currentApp.listRequests(tipo);
	}

	createRute() {
		this.currentApp.createRequest();
	}

	procesoRute(id) {
		if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			this.navigate('list', { trigger: true });
			return false;
		}
		this.currentApp.procesoRute(id);
	}
}

export { AfiliationRouter };
