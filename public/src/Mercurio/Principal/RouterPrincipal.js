import { $App } from '@/App';
import { ControllerPrincipal } from './ControllerPrincipal';
import FormClaveFirma from './FormClaveFirma';

class RouterPrincipal extends Backbone.Router {
    constructor(options = {}) {
        super({
            routes: {
                'section-:section': 'scrollToSection',
                list: 'listServices',
                doc: 'descargaDocumentos',
                'change-password': 'changePassword',
            },
        });
        _.extend(this, options);
        this._servicesLoaded = false;
        this._lastScrollSection = null;
        this._lastScrollAt = 0;
        this._bindRoutes();
    }

    initialize() {
        this.currentApp = $App.startSubApplication(ControllerPrincipal);
    }

    listServices() {
        this.currentApp.listServices();
        FormClaveFirma();
        this._servicesLoaded = true;
    }

    scrollToSection(section) {
        const now = Date.now();
        if (this._lastScrollSection === section && now - this._lastScrollAt < 800) {
            return;
        }
        this._lastScrollSection = section;
        this._lastScrollAt = now;

        if (!this._servicesLoaded) {
            this.listServices();
        }

        this.__tryGoToSection(section);
    }

    __tryGoToSection(section, attempt = 0) {
        if (attempt > 20) return;

        const layout = this.currentApp ? this.currentApp.layout : null;
        const canScroll = layout && typeof layout.goToSection === 'function' && layout.smoothNav;

        if (canScroll) {
            layout.goToSection(section);
            return;
        }

        setTimeout(() => this.__tryGoToSection(section, attempt + 1), 100);
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
