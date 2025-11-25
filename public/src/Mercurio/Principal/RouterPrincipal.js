import { $App } from '@/App';
import { ControllerPrincipal } from './ControllerPrincipal';
import FormClaveFirma from './FormClaveFirma';

class RouterPrincipal extends Backbone.Router {
    constructor(options = {}) {
        super({
            routes: {
                list: 'listServices',
                doc: 'descargaDocumentos',
                'change-password': 'changePassword',
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
        FormClaveFirma();
    }

    descargaDocumentos() {
        this.currentApp.descargaDocumentos();
        FormClaveFirma();
    }

    changePassword() {
        this.currentApp.changePassword();
    }
}

export { RouterPrincipal };
