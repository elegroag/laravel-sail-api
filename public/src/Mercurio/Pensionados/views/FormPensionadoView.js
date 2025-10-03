import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { ComponentModel } from '@/Componentes/Models/ComponentModel';
import { eventsFormControl } from '@/Core';
import { FormView } from '@/Mercurio/FormView';
import { PensionadoModel } from '../models/PensionadoModel';

class FormPensionadoView extends FormView {
    #choiceComponents = null;

    constructor(options = {}) {
        super({
            ...options,
            onRender: (el = {}) => this.#afterRender(el),
        });
        this.viewComponents = [];
        this.#choiceComponents = [];
    }

    get events() {
        return {
            'click #guardar_ficha': 'saveFormData',
            'click #cancel': 'cancel',
            'focusout #telefono, #digver': 'isNumber',
            'focusout #cedtra': 'validePk',
            'change #tipdoc': 'changeTipoDocumento',
            'click [data-toggle="address"]': 'openAddress',
            'click #btEnviarRadicado': 'enviarRadicado',
            'change #peretn': 'changePeretn',
            'change #tippag': 'changeTippag',
        };
    }

    #afterRender($el = {}) {
        _.each(this.collection, (component) => {
            if (component.name == 'ruralt') component.type = 'radio';
            if (component.name == 'rural') component.type = 'radio';
            if (component.name == 'autoriza') component.type = 'radio';

            const view = this.addComponent(
                new ComponentModel({
                    disabled: false,
                    readonly: false,
                    order: 0,
                    target: 1,
                    searchType: 'local',
                    ...component,
                    valor: this.model.get(component.name),
                }),
                component.type,
            );
            this.$el.find('#component_' + component.name).html(view.$el);
        });

        this.form.validate({
            ...PensionadoModel.Rules,
            highlight: function (element) {
                $(element).removeClass('is-valid').addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
        });

        this.selectores = this.$el.find(
            '#tipdoc, #tipsoc, #ciupri, #codzon, #codciu, #codact, #coddocrepleg, #ciunac, #cargo, #pub_indigena_id, #resguardo_id',
        );

        if (this.model.get('id') !== null) {
            $.each(this.model.toJSON(), (key, valor) => {
                const inputElement = this.$el.find(`[name="${key}"]`);
                if (inputElement.length && valor) {
                    inputElement.val(valor);
                }
            });

            if (this.model.get('tippag') == 'A' || this.model.get('tippag') == 'D') {
                this.form.find('#show_numcue').removeClass('d-none');
                this.form.find('#show_codban').removeClass('d-none');
                this.form.find('#show_tipcue').removeClass('d-none');
            } else {
                this.$el.find('#numcue').rules('remove', 'required');
                this.$el.find('#codban').rules('remove', 'required');
                this.$el.find('#tipcue').rules('remove', 'required');

                PensionadoModel.changeRulesProperty([
                    { rule: 'numcue', prop: 'required', value: false },
                    { rule: 'codban', prop: 'required', value: false },
                    { rule: 'tipcue', prop: 'required', value: false },
                ]);
            }

            if (this.model.get('peretn') == '3') {
                this.$el.find('.show-peretn').removeClass('d-none');
            } else {
                this.$el.find('.show-peretn').addClass('d-none');
                this.$el.find('#resguardo_id').val('2');
                this.$el.find('#pub_indigena_id').val('2');
            }

            this.selectores.trigger('change');
            this.$el.find('#cedtra').attr('disabled', true);
            setTimeout(() => this.form.valid(), 200);

            $.each(this.selectores, (index, element) => {
                this.#choiceComponents[element.name] = new Choices(element);
                const name = this.model.get(element.name);
                if (name) this.#choiceComponents[element.name].setChoiceByValue(name);
            });
        } else {
            $.each(this.selectores, (index, element) => (this.#choiceComponents[element.name] = new Choices(element)));
        }

        eventsFormControl(this.$el);

        flatpickr(this.$el.find('#fecnac, #fecini'), {
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: Spanish,
        });

        eventsFormControl($el);
    }

    changeTipoDocumento(e) {
        let tipdoc = $(e.currentTarget).val();
        let coddocrepleg = PensionadoModel.changeTipdoc(tipdoc);
        this.$el.find('#coddocrepleg').val(coddocrepleg);
    }

    serializeData() {
        var data;
        if (this.model.entity instanceof PensionadoModel) {
            data = this.model.entity.toJSON();
        }
        return data;
    }

    saveFormData(event) {
        event.preventDefault();
        var target = this.$el.find(event.currentTarget);
        target.attr('disabled', true);

        const _parent = this.$el.find('#peretn').val();
        if (_parent != '3') {
            this.$el.find('#resguardo_id').val('2');
            this.$el.find('#pub_indigena_id').val('2');
        }

        if (this.$el.find('#tippag').val() == 'T') {
            this.$el.find('#numcue').val('0');
        }

        let _err = 0;
        if (this.form.valid() == false) _err++;

        if (_err > 0) {
            target.removeAttr('disabled');
            this.App.trigger('alert:warning', {
                message: 'Se requiere de resolver los campos requeridos para continuar.',
            });
            setTimeout(() => $('label.error').text(''), 6000);
            return false;
        }

        this.$el.find('#cedtra').removeAttr('disabled');

        console.log(this.getInput('[name="fecsol"]'));

        const entity = this.serializeModel(new PensionadoModel());

        if (entity.isValid() !== true) {
            console.log(entity.validationError);
            target.removeAttr('disabled');
            this.App.trigger('alert:warning', { message: entity.validationError.join(' ') });
            setTimeout(() => $('label.error').text(''), 6000);
            return false;
        }

        entity.set('repleg', this.nameRepleg());
        this.$el.find('#repleg').val(entity.get('repleg'));

        Swal.fire({
            title: 'Confirmación requerida',
            html: `<p style='font-size:14px;margin-bottom:8px'>Ingrese su clave para confirmar el envío de la información.</p>`,
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                autocomplete: 'current-password',
            },
            showCancelButton: true,
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cancelar',
            preConfirm: (clave) => {
                if (!clave) {
                    Swal.showValidationMessage('La clave es requerida');
                    return false;
                }
                return clave;
            },
        }).then((result) => {
            if (!result.isConfirmed) {
                target.removeAttr('disabled');
                return;
            }
            const clave = result.value;
            try {
                entity.set('clave', clave);
            } catch (e) {
                if (typeof entity === 'object' && typeof entity.set !== 'function') {
                    entity.clave = clave;
                }
            }
            this.App.trigger('confirma', {
                message: 'Confirma que desea guardar los datos del formulario.',
                callback: (status) => {
                    if (status) {
                        this.trigger('form:save', {
                            entity: entity,
                            isNew: this.isNew,
                            callback: (response) => {
                                target.removeAttr('disabled');
                                this.$el.find('#cedtra').attr('disabled', true);
    
                                if (response) {
                                    if (response.success) {
                                        this.App.trigger('alert:success', { message: response.msj });
                                        this.model.set({ id: parseInt(response.data.id) });
                                        if (this.isNew === true) {
                                            this.App.router.navigate('proceso/' + this.model.get('id'), {
                                                trigger: true,
                                                replace: true,
                                            });
                                        } else {
                                            const _tab = new bootstrap.Tab('a[href="#documentos_adjuntos"]');
                                            _tab.show();
                                        }
                                    } else {
                                        this.App.trigger('alert:error', { message: response.msj });
                                    }
                                }
                            },
                        });
                    } else {
                        target.removeAttr('disabled');
                    }
                },
            });
            
        });

       
    }

    nameRepleg() {
        return this.getInput('#priape') + ' ' + this.getInput('#segape') + ' ' + this.getInput('#prinom') + ' ' + this.getInput('#segnom');
    }

    digver(e) {
        e.preventDefault();
        let cedtra = $(e.currentTarget).val();
        if (cedtra === '') {
            return false;
        }
        this.appController.trigger('form:digit', {
            cedtra: cedtra,
            callback: (entity) => {
                console.log(entity);
                $('#digver').val(entity.digver);
            },
        });
    }

    validePk(e) {
        e.preventDefault();
        let cedtra = this.$el.find(e.currentTarget).val();
        if (cedtra === '') return false;
        this.App.trigger('form:find', {
            cedtra: cedtra,
            callback: (entity) => {
                this.actualizaForm();
                $.each(this.selectores, (index, element) => {
                    const name = this.model.get(element.name);
                    if (name) this.#choiceComponents[element.name].setChoiceByValue(name);
                });
            },
        });
    }

    changePeretn(e) {
        let _parent = this.$el.find(e.currentTarget).val();
        if (_parent == '3') {
            this.$el.find('.show-peretn').removeClass('d-none');
        } else {
            this.$el.find('.show-peretn').addClass('d-none');
            this.$el.find('#resguardo_id').val('2');
            this.$el.find('#pub_indigena_id').val('2');
        }
    }

    changeTippag(e) {
        const target = this.getInput(e.currentTarget);
        if (target == 'A' || target == 'D') {
            this.$el.find('#show_numcue').removeClass('d-none');
            this.$el.find('#show_codban').removeClass('d-none');
            this.$el.find('#show_tipcue').removeClass('d-none');
            if (target == 'D') {
                this.setInput('codban', '51');
                this.setInput('tipcue', 'A');
                this.selectores.trigger('change');
            }

            PensionadoModel.changeRulesProperty([
                { rule: 'numcue', prop: 'required', value: true },
                { rule: 'codban', prop: 'required', value: true },
                { rule: 'tipcue', prop: 'required', value: true },
            ]);

            this.$el.find('#numcue').rules('add', { required: true });
            this.$el.find('#codban').rules('add', { required: true });
            this.$el.find('#tipcue').rules('add', { required: true });
        } else {
            this.$el.find('#show_numcue').addClass('d-none');
            this.$el.find('#show_codban').addClass('d-none');
            this.$el.find('#show_tipcue').addClass('d-none');

            PensionadoModel.changeRulesProperty([
                { rule: 'numcue', prop: 'required', value: false },
                { rule: 'codban', prop: 'required', value: false },
                { rule: 'tipcue', prop: 'required', value: false },
            ]);

            this.$el.find('#numcue').rules('remove', 'required');
            this.$el.find('#codban').rules('remove', 'required');
            this.$el.find('#tipcue').rules('remove', 'required');
        }
    }

    /**
     * @override
     */
    remove() {
        if (_.size(this.viewComponents) > 0) {
            _.each(this.viewComponents, (view) => view.remove());
        }
        FormView.prototype.remove.call(this, {});
        $.each(this.#choiceComponents, (choice) => choice.destroy());
    }
}

export { FormPensionadoView };
