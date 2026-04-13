import Logger from '@/Common/Logger';
import {
    AddressComponent,
    DateComponent,
    DialogComponent,
    InputComponent,
    RadioComponent,
    SelectComponent,
    TextComponent,
} from '@/Componentes/Views/ComponentsView';
import ConfirmPasswordModalView from '@/Componentes/Views/ConfirmPasswordModalView';
import OpenAddress from '@/Componentes/Views/OpenAddress';
import { is_numeric } from '@/Core';

export class UsuarioFormView extends Backbone.View {
    #onRender = null;

    constructor(options = {}) {
        super(options);
        this.children = new Array();
        this.region = options.region || null;
        this.isNew = options.isNew || false;
        this.template = options.template || null;
        this.form = options.form || null;
        this.selectores = {};
        this.address = null;
        this.subHeader = null;
        this.viewDocuments = null;
        _.extend(this, options);
        this.#onRender = options.onRender || null;
        this.App = options.App || window.App;
        this.logger = new Logger();
    }

    render() {
        const data = this.__serializeData();
        const template = this.__compileTemplate();
        this.$el.html(template(data));
        if (this.#onRender) this.#onRender(this.$el);
        return this;
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

    isNumber(e) {
        if ($(e.currentTarget).val() == '') return;
        if (!is_numeric($(e.currentTarget).val())) return $(e.currentTarget).val('');
    }

    __compileTemplate() {
        return _.template(this.template);
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
            switch (model.get('form_type')) {
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
                case 'textarea':
                    view = new TextComponent({ model });
                    break;
                case 'dialog':
                    view = new DialogComponent({ model, collection });
                    break;
                case 'address':
                    view = new AddressComponent({ model });
                    break;
                default:
                    model.set('form_type', 'input');
                    view = new InputComponent({ model });
                    break;
            }
            this.children[model.get('cid')] = view;
        }
        if (view) view.render();
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
                url: this.App.kumbiaURL('principal/lista_adress'),
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

    cancel(e) {
        e.preventDefault();
        this.App.router.navigate('list', { trigger: true });
    }

    actualizaForm(silent = void 0) {
        $.each(this.model.toJSON(), (key, valor) => {
            const inputElement = this.$el.find(`[name="${key}"]`);
            if (inputElement.length && valor) {
                let _type = inputElement.attr('type');
                if (_type === 'radio' || _type === 'checkbox') {
                    inputElement.prop('checked', valor == 'S');
                } else {
                    inputElement.val(valor);
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

    validateChoicesField(value, choicesInstance) {
        const containerInner = choicesInstance.containerInner.element;
        if (value !== '') {
            containerInner.classList.remove('is-invalid');
            containerInner.classList.add('is-valid');
        } else {
            containerInner.classList.remove('is-valid');
            containerInner.classList.add('is-invalid');
        }
    }

    confirmSend({ title = '', message = '', inputPlaceholder = '', confirmText = '', cancelText = '', inputAttributes = {}, callback = () => {} }) {
        const view = new ConfirmPasswordModalView({
            title,
            message,
            inputPlaceholder,
            confirmText,
            cancelText,
            inputAttributes,
        });

        this.App.trigger('show:modal', {
            title,
            view,
            options: {
                size: 'modal-md',
                scrollable: true,
                footer: [
                    {
                        text: 'Cancelar',
                        className: 'btn-secondary',
                        onClick: () => {
                            this.App.trigger('hide:modal', view);
                            callback({
                                isConfirmed: false,
                            });
                        },
                    },
                    {
                        text: 'Sí, Confirmo',
                        className: 'btn-primary',
                        onClick: () => {
                            const clave = $('#passwordInput').val();
                            if (!clave) {
                                this.App.trigger('alert:error', { message: 'La clave de firma digital es requerida.' });
                                return callback({
                                    isConfirmed: false,
                                });
                            }
                            this.App.trigger('hide:modal', view);
                            view.remove();
                            callback({
                                isConfirmed: true,
                                value: clave,
                            });
                        },
                    },
                ],
            },
        });
    }

    remove() {
        if (this.subHeader) this.subHeader.remove();
        if (this.viewDocuments) this.viewDocuments.remove();
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}
