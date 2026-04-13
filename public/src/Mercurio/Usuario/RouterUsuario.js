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
        this._bindRoutes();
    }

    initialize() {
        this.currentApp = $App.startSubApplication(ControllerUsuario);
    }

    renderPerfil() {
        this.initialize();
        this.currentApp.renderPerfil();
    }

    editaPerfil() {
        this.initialize();
        this.currentApp.editaPerfil();
    }
}

export { RouterUsuario };
