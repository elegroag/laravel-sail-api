import { $App } from '../App';
import { GestionAdjuntoService } from '../Componentes/Services/GestionAdjuntoService';
import {
    DateComponent,
    DialogComponent,
    InputComponent,
    OpenAddress,
    RadioComponent,
    SelectComponent,
    TextComponent,
} from '../Componentes/Views/ComponentsView';
import { LoadDocumentsView } from '../Componentes/Views/LoadDocumentsView';
import { SeguimientosView } from '../Componentes/Views/SeguimientosView';
import { SubHeaderView } from '../Componentes/Views/SubHeaderView';
import { is_numeric } from '../Core';

export class FormView extends Backbone.View {
    #onRender = null;
    App = null;

    constructor(options = {}) {
        super(options);
        this.children = new Array();
        this.gestionAdjuntoService = new GestionAdjuntoService();
        this.region = options.region || null;
        this.isNew = options.isNew || false;
        this.template = '#tmp_create';
        this.form = null;
        this.selectores = {};
        this.address = null;
        this.subHeader = null;
        this.viewDocuments = null;
        _.extend(this, options);
        this.#onRender = options.onRender || null;
        this.App = options.App || $App;
    }

    render() {
        const data = this.__serializeData();
        const template = this.__compileTemplate();
        this.$el.html(template(data));
        this.form = this.$el.find(this.region.form);
        this.__initSubHeader();
        if (this.#onRender) this.#onRender(this.$el);
        return this;
    }

    enviarRadicado(event) {
        event.preventDefault();
        this.__enviarCaja();
    }

    get className() {
        return 'page-container';
    }

    __serializeData() {
        if (_.isNull(this.model) || _.isUndefined(this.model)) {
            return this.model;
        } else {
            return this.model.toJSON();
        }
    }

    __initSubHeader() {
        if (this.subHeader) this.subHeader.remove();
        this.subHeader = new SubHeaderView({
            model: this.model,
            collection: [
                {
                    id: 'closeForm',
                    hidden: false,
                    label: 'Salir',
                    icon: 'fa fa-times text-warning',
                    active: false,
                    tab: '',
                },
                {
                    id: 'seguimiento-tab',
                    hidden: this.model.get('id') && this.model.get('estado') !== 'T' ? false : true,
                    label: 'Seguimiento',
                    icon: 'fa fas fa-eye text-info',
                    active: false,
                    tab: 'seguimiento',
                },
                {
                    id: 'datos_solicitud_tab',
                    hidden: false,
                    label: 'Ficha Principal Registro',
                    icon: 'fa fas fa-edit text-yellow',
                    active: true,
                    tab: 'datos_solicitud',
                },
                {
                    id: 'documentos_adjuntos_tab',
                    hidden: this.model.get('id') ? false : true,
                    label: 'Documentos Adjuntar',
                    icon: 'fa fas fa-file text-purple',
                    active: false,
                    tab: 'documentos_adjuntos',
                },
                {
                    id: 'enviarCaja',
                    hidden: this.model.get('id') && (this.model.get('estado') === 'D' || this.model.get('estado') === 'T') ? false : true,
                    label: 'Enviar Radicado',
                    icon: 'fa fas fa-upload text-success',
                    active: false,
                    tab: 'enviar_radicado',
                },
            ],
        });

        this.listenTo(this.subHeader, 'show:documentos', this.__renderDocumentos);
        this.listenTo(this.subHeader, 'show:seguimiento', this.__renderSeguimiento);
        this.listenTo(this.subHeader, 'show:enviar', this.__enviarCaja);
        this.App.layout.getRegion('subheader').show(this.subHeader);
    }

    isNumber(e) {
        if ($(e.currentTarget).val() == '') return;
        if (!is_numeric($(e.currentTarget).val())) return $(e.currentTarget).val('');
    }

    __compileTemplate() {
        return _.template($(this.template).html());
    }

    getInput(selector) {
        return this.$el.find(selector).val();
    }

    setInput(name, value) {
        return this.$el.find(`[name='${name}']`).val(value);
    }

    serializeModel(entity) {
        const dataArray = this.form.serializeArray();
        _.each(dataArray, (item) => entity.set(item.name, item.value));
        return entity;
    }

    addComponent(model = {}) {
        const collection = this.App.Collections.formParams;
        let view;
        if (_.size(this.children) > 0) {
            if (_.indexOf(this.children, model.get('cid')) != -1) {
                view = this.children[model.get('cid')];
            }
        }
        if (!view) {
            switch (model.get('type')) {
                case 'select':
                    view = new SelectComponent({ model, collection });
                    break;
                case 'input':
                    view = new InputComponent({ model });
                    break;
                case 'radio':
                    view = new RadioComponent({ model });
                    break;
                case 'date':
                    view = new DateComponent({ model });
                    break;
                case 'text':
                    view = new TextComponent({ model });
                    break;
                case 'dialog':
                    view = new DialogComponent({ model, collection });
                    break;
                default:
                    break;
            }
            this.children[model.get('cid')] = view;
        }
        view.render();
        return view;
    }

    openAddress(e) {
        const target = this.$el.find(e.currentTarget);
        const name = target.attr('data-name');

        if (this.address) {
            const view = new OpenAddress({
                collection: this.address,
                model: { name: name },
            });
            $('#show_modal_generic').html(view.render().el);
        } else {
            this.App.trigger('syncro', {
                url: this.App.kumbiaURL('principal/listaAdress'),
                data: {},
                silent: true,
                callback: (response) => {
                    if (response) {
                        if (response.success) {
                            this.address = response.data;
                            const view = new OpenAddress({
                                collection: response.data,
                                model: { name: name },
                            });
                            $('#show_modal_generic').html(view.render().el);
                        }
                    }
                },
            });
        }
    }

    __renderDocumentos() {
        this.App.trigger('syncro', {
            url: this.App.url('consultaDocumentos/' + this.model.get('id')),
            data: {},
            callback: (response) => {
                if (response.success) {
                    if (this.viewDocuments != null) this.viewDocuments.remove();

                    this.viewDocuments = new LoadDocumentsView({
                        model: this.model,
                        collection: [response.data],
                    });

                    this.listenTo(this.viewDocuments, 'file:trash', this.gestionAdjuntoService.borrarArchivo);
                    this.listenTo(this.viewDocuments, 'file:save', this.gestionAdjuntoService.guardarArchivo);
                    this.listenTo(this.viewDocuments, 'file:prodoc', this.gestionAdjuntoService.processDocument);
                    //this.listenTo(this.viewDocuments, 'file:firma', this.__findFirmas);
                    this.listenTo(this.viewDocuments, 'file:reload', this.__renderDocumentos);

                    this.$el.find('#documentos_adjuntos').html(this.viewDocuments.render().el);
                } else {
                    this.App.trigger('alert:error', { message: response.msj });
                }
            },
        });
    }

    __renderSeguimiento() {
        this.App.trigger('syncro', {
            url: this.App.url('seguimiento/' + this.model.get('id')),
            data: {},
            callback: (response) => {
                if (response.success) {
                    const view = new SeguimientosView({
                        model: this.model,
                        collection: [
                            {
                                campos_disponibles: response.data.campos_disponibles,
                                estados_detalles: response.data.estados_detalles,
                                seguimientos: response.data.seguimientos,
                            },
                        ],
                    });
                    this.$el.find('#seguimiento').html(view.render().el);
                } else {
                    this.App.trigger('alert:error', { message: response.msj });
                }
            },
        });
    }

    __enviarCaja() {
        this.App.trigger('confirma', {
            message: '¿Está seguro de envíar para verificación.?',
            callback: (status) => {
                if (status) {
                    this.trigger('form:send', {
                        model: this.model,
                        callback: (response) => {
                            if (response.success) {
                                this.remove();
                                this.App.router.navigate('list', { trigger: true });
                            }
                        },
                    });
                }
            },
        });
    }

    cancel(e) {
        e.preventDefault();
        this.App.router.navigate('list', { trigger: true });
    }

    actualizaForm(silent = void 0) {
        _.each(this.model.toJSON(), (valor, key) => {
            if (_.isEmpty(valor) == true || _.isUndefined(valor) == true) {
            } else {
                let _type = this.$el.find(`[name='${key}']`).attr('type');
                if (_type === 'radio' || _type === 'checkbox') {
                } else {
                    this.setInput(key, valor);
                    this.$el.find(`[for="${key}"]`).addClass('top');
                }
            }
        });

        this.selectores.trigger('change');
        this.form.valid();
        if (silent == void 0) {
            setTimeout(() => {
                this.App.trigger('noty:info', 'El formulario se actualizo de forma correcta');
            }, 700);
        }
    }

    remove() {
        if (this.subHeader) this.subHeader.remove();
        if (this.viewDocuments) this.viewDocuments.remove();
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}
