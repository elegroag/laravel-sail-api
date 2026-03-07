import { ControllerUsuario } from '@/Cajas/Usuario/ControllerUsuario';

export default class RouterUsuario extends Backbone.Router {
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
        this._bindRoutes();
    }

    init() {
        return window.App.startSubApplication(ControllerUsuario);
    }

    detalleUsuario(id = '', tipo = '', coddoc = '') {
        if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
            window.App.trigger('alert:error', {
                message: 'No hay un usuario seleccionado para continuar.',
            });
            this.navigate('list', { trigger: true });
            return false;
        }
        const currentApp = this.init();
        currentApp.detalleUsuario(id, tipo, coddoc);
    }

    editarUsuario(id = '', tipo = '', coddoc = '') {
        if (_.isUndefined(id) == true || _.isNull(id) == true || id == '') {
            window.App.trigger('alert:error', {
                message: 'No hay un usuario seleccionado para continuar.',
            });
            this.navigate('list', { trigger: true });
            return false;
        }
        const currentApp = this.init();
        currentApp.editarUsuario(id, tipo, coddoc);
    }

    listarUsuarios(tipo = '') {
        const currentApp = this.init();
        currentApp.listarUsuarios(tipo);
    }
}
