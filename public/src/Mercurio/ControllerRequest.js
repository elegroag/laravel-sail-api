import { Region } from '@/Common/Region';
import { LayoutView } from '@/Componentes/Views/LayoutView';
import loading from '@/Componentes/Views/Loading';

class ControllerRequest {
    constructor(options = {}) {
        this.region = {};
        this.afiService = {};
        this.App = {};
        this.headerOptions = {};

        this.TableView = undefined;
        this.EntityModel = undefined;
        this.FormRequest = undefined;
        this.trigger = undefined;
        this.layout = undefined;

        _.extend(this, Backbone.Events);
        _.extend(this, options);

        this.App.layout = new LayoutView();
        if (this.region instanceof Region) {
            this.region.show(this.App.layout);
        } else {
            if (_.isString(this.region)) $(this.region).html(this.App.layout);
        }
        loading.hide();
    }

    serealizeParams() {
        return this.App.Collections.formParams;
    }

    listRequests(tipo) {
        if (_.isNull(this.App.Collections.formParams)) this.trigger('params', { silent: true, callback: false });

        const view = new this.TableView({ model: { tipo } });

        this.listenTo(view, 'load:table', this.afiService.findDataTable);
        this.listenTo(view, 'remove:solicitud', this.afiService.cancelaSolicitud);
        this.listenTo(view, 'admin:cuenta', this.__adminCuenta);

        this.App.layout.getRegion('body').show(view);

        this.afiService.initHeaderView({
            ...this.headerOptions,
            estado: tipo,
            isNew: false,
            breadcrumb_menu: 'Listar solicitudes',
        });
    }

    createRequest() {
        const model = new this.EntityModel();
        if (_.isNull(this.App.Collections.formParams)) {
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

        this.App.layout.getRegion('body').show(view);

        this.afiService.initHeaderView({
            ...this.headerOptions,
            isNew: model.get('id') == null,
            breadcrumb_menu: 'Crear solicitud',
            create: 'disabled',
        });
    }

    procesoRute(id) {
        if (_.isNull(this.App.Collections.formParams)) {
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
                                        this.App.trigger('alert:error', { message: response.msj });
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
                            this.App.trigger('alert:error', { message: response.msj });
                        }
                    }
                },
            });
        }
    }

    __adminCuenta({ id }) {
        window.location = this.App.url('administrar_cuenta/' + id);
    }

    destroy() {
        this.stopListening();
        if (this.region && this.region instanceof Region) this.region.remove();
    }
}

export { ControllerRequest };
