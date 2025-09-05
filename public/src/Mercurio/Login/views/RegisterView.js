import { $App } from '@/App';
import { ComponentModel } from '@/Componentes/Models/ComponentModel';
import { DateComponent, DialogComponent, InputComponent, RadioComponent, SelectComponent, TextComponent } from '@/Componentes/Views/ComponentsView';
import { eventsFormControl, Testeo } from '@/Core';
import Choices from 'choices.js';

import { UserRegisterModel } from '../models/UserRegisterModel';

export default class RegisterView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.children = [];
    }

    initialize() {
        this.selectores = undefined;
        this.form = undefined;
    }

    get className() {
        return 'card mb-2';
    }

    get events() {
        return {
            'click #btnRegresar': 'closeRegister',
            'focusout #email': 'validaEmail',
            'change #tipper': 'changeTipoPersona',
            'change #tipo': 'changeTipoAfiliado',
            'click #sesion_persona': 'sesionPersona',
            'click #btnRegistrar': 'registrarServer',
        };
    }

    render() {
        $('#render_sesion').fadeOut();
        $('#render_left_content').fadeOut();
        const renderedHtml = _.template(document.querySelector('#tmp_register').innerHTML);
        this.$el.html(renderedHtml());
        this.__afterRender();
        return this;
    }

    __afterRender() {
        _.each(this.collection, (component = {}) => {
            const view = this.addComponent(
                new ComponentModel({
                    disabled: false,
                    readonly: false,
                    order: 0,
                    target: 1,
                    searchType: 'local',
                    ...component,
                    valor: '',
                }),
            );
            this.$el.find('#component_' + component['name']).html(view.$el);
        });

        const Elements = this.$el.find('.js-choice');
        _.each(Elements, (element) => {
            new Choices(element);
        });

        this.form = this.$el.find('#formRegister');
        this.form.validate({
            ...UserRegisterModel.Rules,
            highlight: function (element) {
                $(element).removeClass('is-valid').addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
        });
        eventsFormControl(this.$el);
    }

    validaEmail() {
        let _email = $('#email').val();
        let _cedrep = $('#cedrep').val();
        let _nit = $('#calemp').val() == 'E' ? $('#nit').val() : $('#cedrep').val();

        if (_cedrep == '') {
            $App.trigger('alert:error', {
                message: 'Ingresa primero el número de documento del representante legal.',
            });
            $('#cedrep').focus();
            return false;
        }

        if (_nit == '') {
            return false;
        }

        if (Testeo.email({ attr: _email, target: false, label: 'email', out: false }) !== false) {
            $('#email-error').text('Debe tener formato de email correcto.');
            $('#email-error').attr('style', 'display:inline-block');
            return false;
        }

        $App.trigger('syncro', {
            url: $App.url('valida_email'),
            data: {
                email: _email,
                documento: _cedrep,
                nit: _nit,
            },
            callback: (response) => {
                if (!response.success) {
                    $App.trigger('alert:error', { message: response.msj });
                    $('#email').val('');
                    return;
                }
                return;
            },
        });
    }

    closeRegister(event) {
        event.preventDefault();
        Swal.fire({
            title: '¡Confirmar!',
            text: '¿Está seguro que desea abandonar el formulario de registro en plataforma.?',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-sm btn-success',
            cancelButtonClass: 'btn btn-sm btn-danger',
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (result.isConfirmed) {
                this.remove();
                $App.router.navigate('auth', { trigger: true });
            }
        });
    }

    changeTipoPersona(e) {
        e.preventDefault();
        const target = this.$el.find(e.currentTarget);
        const value = target.val();
        if (value == '') return false;
        if (value == 'J') this.$el.find('#coddoc').val('3');
        if (value == 'N') this.$el.find('#coddoc').val('1');
        this.$el.find('#coddocrepleg').val('CC');
        this.$el.find('#nit').trigger('focus');
    }

    changeTipoAfiliado(e) {
        e.preventDefault();
        const valor = e.target.value;
        this.$el.find('#tipsoc').val('08');
        switch (valor) {
            case 'I':
                this.$el.find("[toggle-event='call']").text('independiente');
                this.$el.find("[toggle-event='repre']").text('independiente');
                break;
            case 'F':
                this.$el.find("[toggle-event='call']").text('facultativo');
                this.$el.find("[toggle-event='repre']").text('facultativo');
                break;
            case 'O':
                this.$el.find("[toggle-event='call']").text('pensionado');
                this.$el.find("[toggle-event='repre']").text('pensionado');
                break;
            case 'P':
                this.$el.find("[toggle-event='call']").text('particular');
                this.$el.find("[toggle-event='repre']").text('particular');
                break;
            case 'S':
                this.$el.find("[toggle-event='call']").text('serivicio domestico');
                this.$el.find("[toggle-event='repre']").text('serivicio domestico');
                break;
            case 'N':
                this.$el.find("[toggle-event='call']").text('usuario foniñez');
                this.$el.find("[toggle-event='repre']").text('usuario foniñez');
                break;
            default:
                this.$el.find("[toggle-event='call']").text('empresa');
                this.$el.find("[toggle-event='repre']").text('representante Legal');
                this.$el.find('#tipsoc').val('');
                break;
        }

        this.__changeEmpresa(valor == 'E');
        this.__changeIndependiente(valor == 'I');
        this.__changePensionado(valor == 'O');
        this.__changeFacultativo(valor == 'F');
        this.__changeServicioDomestico(valor == 'S');

        switch (valor) {
            case 'I':
            case 'O':
            case 'F':
            case 'S':
                this.$el.find('#coddocrepleg').rules('remove', 'required');
                this.$el.find('#nit').rules('remove', 'required');
                this.$el.find('#tipper').rules('remove', 'required');
                this.$el.find('#razsoc').rules('remove', 'required');

                UserRegisterModel.changeRulesProperty([
                    { rule: 'coddocrepleg', prop: 'required', value: false },
                    { rule: 'nit', prop: 'required', value: false },
                    { rule: 'tipper', prop: 'required', value: false },
                    { rule: 'razsoc', prop: 'required', value: false },
                ]);

                this.$el.find('#coddoc').val('1');
                this.$el.find('#tipper').val('N');
                this.$el.find('.fg-tipper').fadeOut();
                this.$el.find('.fg-nit').fadeOut();
                this.$el.find('.fg-razsoc').fadeOut();
                this.$el.find('.fg-coddocrepleg').fadeOut();
                break;
            case 'P':
            case 'N':
                //particular
                this.$el.find('#coddocrepleg').rules('remove', 'required');
                this.$el.find('#nit').rules('remove', 'required');
                this.$el.find('#tipper').rules('remove', 'required');
                this.$el.find('#razsoc').rules('remove', 'required');
                this.$el.find('#calemp').rules('remove', 'required');
                this.$el.find('#tipsoc').rules('remove', 'required');

                UserRegisterModel.changeRulesProperty([
                    { rule: 'coddocrepleg', prop: 'required', value: false },
                    { rule: 'nit', prop: 'required', value: false },
                    { rule: 'tipper', prop: 'required', value: false },
                    { rule: 'razsoc', prop: 'required', value: false },
                    { rule: 'calemp', prop: 'required', value: false },
                    { rule: 'tipsoc', prop: 'required', value: false },
                ]);

                this.$el.find('#coddoc').val('1');
                this.$el.find('#tipper').val('N');
                this.$el.find('.fg-calemp').fadeOut();
                this.$el.find('.fg-tipsoc').fadeOut();
                this.$el.find('.fg-tipper').fadeOut();
                this.$el.find('.fg-nit').fadeOut();
                this.$el.find('.fg-razsoc').fadeOut();
                this.$el.find('.fg-coddocrepleg').fadeOut();
                break;
            default:
                this.$el.find('#coddocrepleg').rules('add', { required: true });
                this.$el.find('#nit').rules('add', { required: true });
                this.$el.find('#tipper').rules('add', { required: true });
                this.$el.find('#razsoc').rules('add', { required: true });
                this.$el.find('#calemp').rules('add', { required: true });
                this.$el.find('#tipsoc').rules('add', { required: true });

                UserRegisterModel.changeRulesProperty([
                    { rule: 'coddocrepleg', prop: 'required', value: true },
                    { rule: 'nit', prop: 'required', value: true },
                    { rule: 'tipper', prop: 'required', value: true },
                    { rule: 'razsoc', prop: 'required', value: true },
                    { rule: 'calemp', prop: 'required', value: true },
                    { rule: 'tipsoc', prop: 'required', value: true },
                ]);

                this.$el.find('.fg-calemp').fadeIn();
                this.$el.find('.fg-tipsoc').fadeIn();
                this.$el.find('.fg-tipper').fadeIn();
                this.$el.find('.fg-nit').fadeIn();
                this.$el.find('.fg-razsoc').fadeIn();
                this.$el.find('.fg-coddocrepleg').fadeIn();
                break;
        }
    }

    sesionPersona(event) {
        event.preventDefault();
        window.location.href = $App.kumbiaURL('login/ingreso_persona');
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
                        collection: $App.Collections.formParams,
                    });
                    break;
                case 'radio':
                    view = new RadioComponent({
                        model: model,
                    });
                    break;
                case 'date':
                    view = new DateComponent({
                        model: model,
                    });
                    break;
                case 'text':
                    view = new TextComponent({
                        model: model,
                    });
                    break;
                case 'dialog':
                    view = new DialogComponent({
                        model: model,
                        collection: $App.Collections.formParams,
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
        var children = this.children || {};
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

    registrarServer(e) {
        e.preventDefault();
        const target = this.$el.find(e.currentTarget);
        target.attr('disabled', 'true');

        let _err = 0;
        if (this.form.valid() == false) _err++;
        if (_err > 0) {
            target.removeAttr('disabled');
            $App.trigger('alert:warning', {
                message: 'Se requiere de resolver los campos requeridos para continuar.',
            });
            setTimeout(() => this.$el.find('label.error').fadeOut(), 6000);
            return false;
        }

        const entity = new UserRegisterModel(this.__serializeArray());
        const status = entity.isValid();
        if (status === false) {
            target.removeAttr('disabled');
            $App.trigger('alert:warning', {
                message: `Algunos de los campos son requeridos para poder continuar:
					${entity.validationError.join(' ')}`,
            });
            return false;
        }

        const _token = $("[name='csrf-token']").attr('content');
        this.trigger('send:register', {
            data: entity.toJSON(),
            token: _token,
            callback: (response) => {
                if (response) {
                    if (response.success == true) {
                        this.__confimStatic({
                            message: response.msj,
                            callback: (status) => {
                                if (status) {
                                    target.removeAttr('disabled');
                                    const miTokenAuth = btoa(
                                        JSON.stringify({
                                            documento: response.documento,
                                            coddoc: response.coddoc,
                                            tipo: 'P',
                                            tipafi: response.tipafi,
                                            id: response.id,
                                        }),
                                    );
                                    sessionStorage.setItem('miTokenAuth', miTokenAuth);
                                    $App.router.navigate('verification', {
                                        trigger: true,
                                        replace: true,
                                    });
                                } else {
                                    target.removeAttr('disabled');
                                }
                            },
                        });
                    } else {
                        target.removeAttr('disabled');
                    }
                } else {
                    target.removeAttr('disabled');
                }
            },
        });
    }

    __confimStatic(transfer = {}) {
        const { message = 'Nota no hay respuesta de la solicitud', callback } = transfer;
        Swal.fire({
            title: '¿Confirmar?',
            text: message,
            icon: 'success',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCancelButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#fc8c72',
            confirmButtonText: 'SI, Continuar!',
        }).then((result) => {
            callback(result.isConfirmed);
        });
    }

    __changeEmpresa(status) {
        if (status) {
            let target = this.$el.find('#calemp option[value="E"]');
            target.prop('disabled', false);
            target.prop('selected', true);
            this.$el.find('#calemp option[value!="E"]').prop('disabled', true);
        }
    }

    __changeIndependiente(status) {
        if (status) {
            let target = this.$el.find('#calemp option[value="I"]');
            target.prop('disabled', false);
            target.prop('selected', true);
            this.$el.find('#calemp option[value!="I"]').prop('disabled', true);
        }
    }

    __changePensionado(status) {
        if (status) {
            let target = this.$el.find('#calemp option[value="P"]');
            target.prop('disabled', false);
            target.prop('selected', true);
            this.$el.find('#calemp option[value!="P"]').prop('disabled', true);
        }
    }

    __changeFacultativo(status) {
        if (status) {
            let target = this.$el.find('#calemp option[value="F"]');
            target.prop('disabled', false);
            target.prop('selected', true);
            this.$el.find('#calemp option[value!="F"]').prop('disabled', true);
        }
    }

    __changeServicioDomestico(status) {
        if (status) {
            let target = this.$el.find('#calemp option[value="D"]');
            target.prop('disabled', false);
            target.prop('selected', true);
            this.$el.find('#calemp option[value!="D"]').prop('disabled', true);
        }
    }

    __serializeArray() {
        const _arreglo = this.form.serializeArray();
        let _datos = {};
        let _i = 0;
        while (_i < _arreglo.length) {
            _datos[_arreglo[_i].name] = _arreglo[_i].value;
            _i++;
        }
        return _datos;
    }

    remove() {
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
        this.closeChildren();
    }
}
