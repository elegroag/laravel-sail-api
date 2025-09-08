import { $App } from '@/App';
import { Controller } from '@/Common/Controller';
import { LayoutView } from '@/Componentes/Views/LayoutView';
import loading from '@/Componentes/Views/Loading';
import { UsuarioModel } from './models/UsuarioModel';
import { PerfilView } from './views/PerfilView';

class ControllerUsuario extends Controller {
    constructor(options = {}) {
        super(options);
        _.extend(this, Backbone.Events);
        _.extend(this, options);
        $App.Collections.formParams = null;

        $App.layout = new LayoutView();
        this.region.show($App.layout);
        loading.hide();
    }

    renderPerfil() {
        this.__requestController({
            data: {},
            silent: true,
            url: $App.url('show_perfil', window.ServerController ?? 'usuario'),
            callback: (response) => {
                if (response) {
                    const entity = new UsuarioModel(response.data);
                    entity.set('isEdit', -1);
                    const view = new PerfilView({ model: entity });
                    $App.layout.getRegion('body').show(view);
                }
            },
        });
    }

    editaPerfil() {
        this.__requestController({
            data: {},
            silent: true,
            url: 'params',
            callback: (response) => {
                if (response) {
                    if (_.isNull($App.Collections.formParams)) $App.Collections.formParams = [];
                    _.extend($App.Collections.formParams, response.data);
                    this.__requestController({
                        data: {},
                        silent: true,
                        url: $App.url('show_perfil', window.ServerController ?? 'usuario'),
                        callback: (result) => {
                            if (result) {
                                const entity = new UsuarioModel(result.data);
                                entity.set('isEdit', 1);
                                const view = new PerfilView({
                                    model: entity,
                                    collection: this.__serealizeParams(),
                                });

                                this.listenTo(view, 'form:save', this.__savePerfil);
                                $App.layout.getRegion('body').show(view);
                            }
                        },
                    });
                }
            },
        });
    }

    __serealizeParams() {
        const resources = _.keys($App.Collections.formParams);
        const collection = _.map(resources, (item) => {
            return {
                name: item,
                type: 'select',
                placeholder: item,
                search: item,
            };
        });
        return collection;
    }

    __savePerfil(transfer) {
        const { entity, callback } = transfer;
        const url = $App.url('guardar', window.ServerController ?? 'usuario');
        $App.trigger('syncro', {
            url,
            data: entity.toJSON(),
            callback: (response) => {
                if (response) {
                    return callback(response);
                }
                return callback(false);
            },
        });
    }

    __requestController(transfer = {}) {
        const callback = transfer.callback || false;
        const silent = transfer.silent || false;
        const datos = transfer.data || {};

        $App.trigger('syncro', {
            url: transfer.url,
            data: datos,
            silent,
            callback: (response) => {
                if (response) {
                    if (response.success === true) {
                        return callback !== false ? callback(response) : '';
                    }
                }
                return callback !== false ? callback(false) : '';
            },
        });
    }

    serealizeParams() {
        const resources = _.keys($App.Collections.formParams);
        const collection = _.map(resources, (item) => {
            return {
                name: item,
                type: 'select',
                placeholder: item,
                search: item,
            };
        });
        return collection;
    }

    destroy() {
        this.stopListening();
        if (this.region && this.region instanceof Region) this.region.remove();
    }
}

export { ControllerUsuario };
