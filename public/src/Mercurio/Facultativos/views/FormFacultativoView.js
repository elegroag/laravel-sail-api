import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { $App } from '../../../App';
import { ComponentModel } from '../../../Componentes/Models/ComponentModel';
import { eventsFormControl } from '../../../Core';
import { FormView } from '../../FormView';
import { FacultativoModel } from '../models/FacultativoModel';

export class FormFacultativoView extends FormView {
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
            'focusout #telefono': 'isNumber',
            'focusout #cedtra': 'valideNitkey',
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
            ...FacultativoModel.Rules,
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

                FacultativoModel.changeRulesProperty([
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

        flatpickr(this.$el.find('#fecnac, #fecini'), {
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: Spanish,
        });

        eventsFormControl(this.$el);
    }

    changeTipoDocumento(e) {
        let tipdoc = $(e.currentTarget).val();
        let coddocrepleg = FacultativoModel.changeTipdoc(tipdoc);
        this.$el.find('#coddocrepleg').val(coddocrepleg);
    }

    serializeData() {
        var data;
        if (this.model.entity instanceof FacultativoModel) {
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

        let _err = 0;
        if (this.form.valid() == false) _err++;

        if (_err > 0) {
            target.removeAttr('disabled');
            $App.trigger('alert:warning', {
                message: 'Se requiere de resolver los campos requeridos para continuar.',
            });
            setTimeout(() => $('label.error').text(''), 6000);
            return false;
        }

        this.$el.find('#cedtra').removeAttr('disabled');

        const entity = this.serializeModel(new FacultativoModel());

        if (entity.isValid() !== true) {
            target.removeAttr('disabled');
            $App.trigger('alert:warning', { message: entity.validationError.join(' ') });
            setTimeout(() => $('label.error').text(''), 6000);
            return false;
        }

        $App.trigger('confirma', {
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
                                    $App.trigger('alert:success', { message: response.msj });
                                    this.model.set({ id: parseInt(response.data.id) });
                                    if (this.isNew === true) {
                                        $App.router.navigate('proceso/' + this.model.get('id'), {
                                            trigger: true,
                                            replace: true,
                                        });
                                    } else {
                                        const _tab = new bootstrap.Tab('a[href="#documentos_adjuntos"]');
                                        _tab.show();
                                    }
                                } else {
                                    $App.trigger('alert:error', { message: response.msj });
                                }
                            }
                        },
                    });
                } else {
                    target.removeAttr('disabled');
                }
            },
        });
    }

    nameRepleg() {
        return this.getInput('#priape') + ' ' + this.getInput('#segape') + ' ' + this.getInput('#prinom') + ' ' + this.getInput('#segnom');
    }

    valideNitkey(e) {
        e.preventDefault();
        if (!this.isNew) return false;

        let nit = this.$el.find(e.currentTarget).val();
        if (nit === '' || _.isUndefined(nit)) return false;
        this.$el.find('#digver').val('0');

        this.trigger('form:find', {
            data: {
                nit: nit,
            },
            callback: (solicitud) => {
                if (solicitud) {
                    $App.trigger('confirma', {
                        message:
                            'El sistema identifica datos de la empresa en su sistema principal. ¿Desea que se actualice el presente formulario con los datos existentes?.',
                        callback: (status) => {
                            if (status) {
                                solicitud.estado = 'T';
                                solicitud.fecsol = null;
                                this.model.set(solicitud);
                                this.actualizaForm();
                                $('#nit').attr('disabled', true);
                            }
                        },
                    });
                }
            },
        });
    }

    changePeretn(e) {
        const _parent = this.$el.find(e.currentTarget).val();
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

            FacultativoModel.changeRulesProperty([
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

            FacultativoModel.changeRulesProperty([
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
        $.each(this.#choiceComponents, (choice) => choice.destroy());
        FormView.prototype.remove.call(this, {});
    }
}
