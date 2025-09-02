import { $App } from '@/App';
import { ControllerPrincipal } from './ControllerPrincipal';

class RouterPrincipal extends Backbone.Router {
	constructor(options = {}) {
		super({
			routes: {
				list: 'listServices',
				doc: 'descargaDocumentos',
			},
		});
		_.extend(this, options);
		this._bindRoutes();
	}

	initialize() {
		this.currentApp = $App.startSubApplication(ControllerPrincipal);
	}

	listServices() {
		this.currentApp.listServices();
	}

	descargaDocumentos() {
		this.currentApp.descargaDocumentos();
	}
}

export { RouterPrincipal };
