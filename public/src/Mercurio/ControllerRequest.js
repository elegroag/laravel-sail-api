import { $App } from '@/App';
import { Region } from '@/Common/Region';
import { LayoutView } from '@/Componentes/Views/LayoutView';
import loading from '@/Componentes/Views/Loading';

class ControllerRequest {
    constructor(options = {}) {
        this.region = {};
        this.afiService = {};
        this.TableView = undefined;
        this.EntityModel = undefined;
        this.headerOptions = undefined;
        this.FormRequest = undefined;
        this.trigger = undefined;
        this.layout = undefined;

        _.extend(this, Backbone.Events);
        _.extend(this, options);

        $App.layout = new LayoutView();
        if (this.region instanceof Region) {
            this.region.show($App.layout);
        } else {
            if (_.isString(this.region)) $(this.region).html($App.layout);
        }
        loading.hide();
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

    listRequests(tipo) {
        if (_.isNull($App.Collections.formParams)) this.trigger('params', { silent: true, callback: false });

        const view = new this.TableView({ model: { tipo } });

        this.listenTo(view, 'load:table', this.afiService.findDataTable);
        this.listenTo(view, 'remove:solicitud', this.afiService.cancelaSolicitud);
        this.listenTo(view, 'admin:cuenta', this.__adminCuenta);

        $App.layout.getRegion('body').show(view);

        this.afiService.initHeaderView({
            ...this.headerOptions,
            estado: tipo,
            isNew: false,
            breadcrumb_menu: 'Listar solicitudes',
        });
    }

    createRequest() {
        const model = new this.EntityModel();
        if (_.isNull($App.Collections.formParams)) {
            this.trigger('params', {
                callback: (response) => {
                    if (response) this.renderCreateRequest(model);
                },
            });
        } else {
            this.renderCreateRequest(model);
        }
    }

    renderCreateRequest(model = {}) {
        const view = new this.FormRequest({
            model: model,
            collection: this.serealizeParams(),
            isNew: model.get('id') == null,
            region: this.region,
        });

        this.listenTo(view, 'form:save', this.afiService.saveFormData);
        this.listenTo(view, 'form:send', this.afiService.sendRadicado);
        this.listenTo(view, 'form:find', this.afiService.validePk);
        this.listenTo(view, 'form:digit', this.afiService.digitVer);

        $App.layout.getRegion('body').show(view);

        this.afiService.initHeaderView({
            ...this.headerOptions,
            isNew: model.get('id') == null,
            breadcrumb_menu: 'Crear solicitud',
            create: 'disabled',
        });
    }

    procesoRute(id) {
        if (_.isNull($App.Collections.formParams)) {
            this.trigger('params', {
                silent: true,
                callback: (response) => {
                    if (response) {
                        this.afiService.serachRequestServer({
                            id: id,
                            callback: (response) => {
                                if (response) {
                                    if (response.success) {
                                        const model = new this.EntityModel(response.data);
                                        this.renderCreateRequest(model);
                                    } else {
                                        $App.trigger('alert:error', { message: response.msj });
                                    }
                                }
                            },
                        });
                    }
                },
            });
        } else {
            this.afiService.serachRequestServer({
                id: id,
                callback: (response) => {
                    if (response) {
                        if (response.success) {
                            const model = new this.EntityModel(response.data);
                            this.renderCreateRequest(model);
                        } else {
                            $App.trigger('alert:error', { message: response.msj });
                        }
                    }
                },
            });
        }
    }

    __adminCuenta({ id }) {
        window.location = $App.url('administrar_cuenta/' + id);
    }

    destroy() {
        this.stopListening();
        if (this.region && this.region instanceof Region) this.region.remove();
    }
}

export { ControllerRequest };
