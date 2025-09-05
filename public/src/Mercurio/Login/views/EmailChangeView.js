import { $App } from '@/App';
import { ModelView } from '@/Common/ModelView.js';
import { ComponentModel } from '@/Componentes/Models/ComponentModel.js';
import { InputComponent, SelectComponent } from '@/Componentes/Views/ComponentsView.js';
import Choices from 'choices.js';
import { UserRecoveryModel } from '../models/UserRecoveryModel.js';

export default class EmailChangeView extends ModelView {
    #App = null;
    #form = null;

    constructor(options = {}) {
        super({
            ...options,
            onRender: () => this.renderAfter(),
        });
        this.#App = options.App || $App;
        this.#form = null;
        this.children = [];
        this.template = _.template(document.getElementById('tmp_email_change').innerHTML);
    }

    get className() {
        return 'card mb-2';
    }

    get events() {
        return {
            'click #render_login_principal': 'renderLogin',
            'click #btn_cambio_correo': 'changeEmail',
        };
    }

    renderAfter() {
        let view = this.addComponent(
            new ComponentModel({
                name: 'coddoc',
                type: 'select',
                placeholder: 'coddoc',
                disabled: false,
                readonly: false,
                order: 0,
                target: 1,
                searchType: 'local',
                search: 'coddoc',
                className: 'js-choice',
            }),
        );
        this.$el.find('#component_coddoc').html(view.$el);

        view = this.addComponent(
            new ComponentModel({
                name: 'tipafi',
                type: 'select',
                placeholder: 'tipafi',
                disabled: false,
                readonly: false,
                order: 0,
                target: 1,
                searchType: 'local',
                search: 'tipafi',
            }),
        );

        this.$el.find('#component_tipafi').html(view.$el);

        const Elements = this.$el.find('.js-choice');
        $.each(Elements, (key, element) => {
            new Choices(element);
        });

        UserRecoveryModel.changeRulesProperty([
            { rule: 'telefono', prop: 'required', value: true },
            { rule: 'novedad', prop: 'required', value: true },
            { rule: 'tipo', prop: 'required', value: true },
            { rule: 'tipo', prop: 'maxlength', value: 1 },
        ]);

        this.#form = this.$el.find('#formCambioCorreo');
        this.#form.validate({
            ...UserRecoveryModel.Rules,
            highlight: function (element) {
                $(element).removeClass('is-valid').addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
        });
        return this;
    }

    renderLogin(e) {
        e.preventDefault();
        this.remove();
        this.#App.router.navigate('auth', { trigger: true });
    }

    addComponent(model = {}) {
        let view;
        if (_.size(this.children) > 0) {
            if (_.indexOf(this.children, model.get('cid')) != -1) {
                view = this.children[model.get('cid')];
            }
        }
        if (!view) {
            switch (model.get('type')) {
                case 'select':
                    view = new SelectComponent({
                        model: model,
                        collection: this.#App.Collections.formParams,
                    });
                    break;
                case 'input':
                    view = new InputComponent({
                        model: model,
                    });
                    break;
                default:
                    break;
            }
            this.children[model.get('cid')] = view;
        }
        view.render();
        return view;
    }

    closeChildren() {
        const children = this.children || {};
        _.each(children, (child) => this.closeChildView(child));
    }

    closeChildView(view) {
        if (!view) return;
        if (_.isFunction(view.remove)) {
            view.remove();
        }
        this.stopListening(view);
        if (view.model) {
            this.children[view.model.cid] = undefined;
        }
    }

    changeEmail(e) {
        e.preventDefault();
        var target = this.$el.find(e.currentTarget);
        target.attr('disabled', 'true');

        let _err = 0;
        if (this.#form.valid() == false) _err++;
        if (_err > 0) {
            target.removeAttr('disabled');
            this.#App.trigger('alert:warning', {
                message: 'Se requiere de resolver los campos requeridos para continuar.',
            });
            setTimeout(() => this.$el.find('label.error').fadeOut(), 6000);
            return false;
        }

        const entity = new UserRecoveryModel({
            documento: this.$el.find('#documento').val(),
            tipo: this.$el.find('#tipafi').val(),
            email: this.$el.find('#email').val(),
            coddoc: this.$el.find('#coddoc').val(),
            telefono: this.$el.find('#telefono').val(),
            novedad: this.$el.find('#novedad').val(),
        });

        if (entity.isValid() === false) {
            target.removeAttr('disabled');
            this.#App.trigger('alert:warning', {
                message: 'Todos los campos son requeridos para continuar el proceso: <br/> ' + entity.validationError.join('<br/>'),
            });
            setTimeout(() => this.#form.find('label.error').fadeOut(), 6000);
            return false;
        }

        this.#App.trigger('syncro', {
            url: this.#App.url('cambio_correo'),
            data: entity.toJSON(),
            callback: (response) => {
                target.removeAttr('disabled');
                this.$el.find('#documento').val('');
                this.$el.find('#email').val('');
                this.$el.find('#telefono').val('');
                this.$el.find('#novedad').val('');

                if (response && response.success) {
                    this.#App.router.navigate('auth', { trigger: true });
                    if (response.success) {
                        this.#App.trigger('alert:success', {
                            message: response.msj,
                        });
                    } else {
                        this.#App.trigger('alert:warning', {
                            message: response.msj,
                        });
                    }
                    return false;
                } else {
                    this.#App.router.navigate('auth', { trigger: true });
                    return false;
                }
            },
        });
    }

    remove() {
        this.closeChildren();
        ModelView.prototype.remove.call(this);
    }
}
