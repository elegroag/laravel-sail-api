import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { ComponentModel } from '@/Componentes/Models/ComponentModel';
import { eventsFormControl } from '@/Core';
import { FormView } from '@/Mercurio/FormView';
import { BeneficiarioModel } from '../models/BeneficiarioModel';

export class FormBeneficiarioView extends FormView {
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
            'focusout #numdoc': 'validaBeneficiario',
            'click [data-toggle="address"]': 'openAddress',
            'change.select2 #convive': 'changeConvive',
            'change #parent': 'changeParent',
            'change #peretn': 'changePeretn',
            "click [name='biodesco']": 'changeBiodesco',
            'change #tippag': 'changeTippag',
            'click #btEnviarRadicado': 'enviarRadicado',
            'focusout #cedtra': 'validaTrabajador',
            'focusout #cedcon': 'validaMother',
        };
    }

    #afterRender($el = {}) {
        _.each(this.collection, (component = { name: '', type: '' }) => {
            if (component.name == 'biourbana') component.type = 'radio';
            if (component.name == 'biodesco') component.type = 'radio';

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
            );

            this.viewComponents.push(view);
            this.$el.find('#component_' + component.name).html(view.$el);
        });

        this.form.validate({
            ...BeneficiarioModel.Rules,
            highlight: function (element) {
                $(element).removeClass('is-valid').addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
        });

        this.selectores = this.$el.find('#tipdoc, #ciunac, #codban, #biocodciu, #resguardo_id, #pub_indigena_id, #peretn');

        if (this.model.get('id') !== null) {
            let silent = true;
            this.actualizaForm(silent);

            this.$el.find('#cedtra').attr('disabled', 'true');
            this.$el.find('#numdoc').attr('disabled', 'true');
            this.$el.find('#parent').attr('disabled', 'true');

            if (this.model.get('parent') == '1') this.$el.find('#show_mother').removeClass('d-none');
            this.__hasBiologico(this.model.get('parent') == '1' || this.model.get('parent') == '4');

            this.$el.find("[name='biodesco'][value='N']").attr('checked', this.model.get('biodesco') != 'S');
            this.$el.find("[name='biodesco'][value='S']").attr('checked', this.model.get('biodesco') == 'S');

            this.$el.find("[name='biourbana'][value='N']").attr('checked', this.model.get('biourbana') == 'N');
            this.$el.find("[name='biourbana'][value='S']").attr('checked', this.model.get('biourbana') != 'N');

            if (this.model.get('parent') == '4') this.__changeTipHijo(true);
            if (this.model.get('parent') == '5') this.__changeCustodia(true);
            if (this.model.get('parent') == '2' || this.model.get('parent') == '3') this.__changePadres(true);

            if (this.model.get('tippag') == 'T') {
                this.$el.find('#show_numcue').addClass('d-none');
                this.$el.find('#show_codban').addClass('d-none');
                this.$el.find('#show_tipcue').addClass('d-none');
                this.$el.find('#numcue').val('');
                this.$el.find('#tipcue').val('');
                this.$el.find('#codban').val('');

                BeneficiarioModel.changeRulesProperty([
                    { rule: 'tippag', prop: 'required', value: false },
                    { rule: 'numcue', prop: 'required', value: false },
                    { rule: 'codban', prop: 'required', value: false },
                    { rule: 'tipcue', prop: 'required', value: false },
                ]);
            } else {
                this.$el.find('#show_numcue').removeClass('d-none');
                this.$el.find('#show_codban').removeClass('d-none');
                this.$el.find('#show_tipcue').removeClass('d-none');

                BeneficiarioModel.changeRulesProperty([
                    { rule: 'tippag', prop: 'required', value: true },
                    { rule: 'numcue', prop: 'required', value: true },
                    { rule: 'codban', prop: 'required', value: true },
                    { rule: 'tipcue', prop: 'required', value: true },
                ]);
            }

            $.each(this.selectores, (index, element) => {
                this.#choiceComponents[element.name] = new Choices(element);
                const name = this.model.get(element.name);
                if (name) this.#choiceComponents[element.name].setChoiceByValue(name);
            });

            setTimeout(() => this.form.valid(), 200);
        } else {
            this.$el.find("[name='biodesco'][value='N']").attr('checked', 'true');
            this.$el.find("[name='biourbana'][value='S']").attr('checked', 'true');
            $.each(this.selectores, (index, element) => (this.#choiceComponents[element.name] = new Choices(element)));
        }

        this.selectores.on('change', (event) => {
            this.validateChoicesField(event.detail.value, this.#choiceComponents[event.currentTarget.name]);
        });

        eventsFormControl(this.$el);

        flatpickr(this.$el.find('#fecnac, #fecing'), {
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: Spanish,
        });
    }

    serializeData() {
        var data;
        if (this.model instanceof BeneficiarioModel) {
            data = this.model.toJSON();
        }
        return data;
    }

    saveFormData(event) {
        event.preventDefault();
        var target = this.$el.find(event.currentTarget);
        target.attr('disabled', 'true');

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

        this.$el.find('#nit').removeAttr('disabled');
        this.$el.find('#cedtra').removeAttr('disabled');
        this.$el.find('#numdoc').removeAttr('disabled');
        this.$el.find('#parent').removeAttr('disabled');

        const entity = this.serializeModel(new BeneficiarioModel());

        entity.set('biodesco', this.$el.find("[name='biodesco']:checked").val());
        entity.set('biourbana', this.$el.find("[name='biourbana']:checked").val());

        if (entity.isValid() === false) {
            target.removeAttr('disabled');
            this.App.trigger('alert:warning', {
                message: 'Alerta algunos de los campos son requeridos: <br/> ' + entity.validationError.join('<br/>'),
            });
            setTimeout(() => $('label.error').text(''), 6000);
            return false;
        }

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
                message: 'Confirma que desea guardar los datos del formulario y continuar el proceso de solicitud de afiliación',
                callback: (status) => {
                    if (status) {
                        this.trigger('form:save', {
                            entity: entity,
                            isNew: this.isNew,
                            callback: (response) => {
                                target.removeAttr('disabled');
                                this.$el.find('#cedtra').attr('disabled', true);

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
                            },
                        });
                    } else {
                        target.removeAttr('disabled');
                    }
                },
            });

        });


    }

    validaBeneficiario(e) {
        e.preventDefault();
        if (!this.isNew) return false;

        let cedcon = this.$el.find(e.currentTarget).val();
        if (cedcon === '') return false;

        this.trigger('form:find', {
            cedcon: cedcon,
            callback: (solicitud) => {
                if (solicitud) {
                    this.App.trigger('confirma', {
                        message:
                            'El sistema identifica información del afiliado en el sistema central de la Caja. ¿Desea que se actualice el presente formulario con los datos existentes?.',
                        callback: (status) => {
                            if (status) {
                                solicitud.estado = 'T';
                                solicitud.fecsol = null;
                                this.model.set(solicitud);
                                this.actualizaForm();

                                $.each(this.selectores, (index, element) => {
                                    const name = this.model.get(element.name);
                                    if (name) this.#choiceComponents[element.name].setChoiceByValue(name);
                                });
                            }
                        },
                    });
                }
            },
        });
    }

    changeConvive(e) {
        const convive = parseInt(this.$el.find(e.currentTarget).val());
        const cedcon = this.$el.find('#cedcon').val();
        const cedtra = this.$el.find('#cedtra').val();

        let opts = ['', cedcon, cedtra, '@'];
        if (convive == 3) {
            $('#cedacu').attr('placeholder', 'No aplica');
            $('#cedacu').val('');
            return false;
        }

        if (convive == 4) {
            $('#cedacu').attr('placeholder', 'Cedula acudiente');
            $('#cedacu').removeAttr('readonly');
            $('#cedacu').val('');
            return false;
        } else {
            $('#cedacu').attr('readonly', true);
        }

        $('#cedacu').attr('placeholder', 'Pendiente definir acudiente convive');
        const valor = opts[convive];
        $('#cedacu').val(valor);
    }

    changeParent(e) {
        const parent = this.getInput('#parent');
        if (parent == '1' || parent == '4') {
            this.$el.find('#show_mother').removeClass('d-none');
            this.__hasBiologico(true);
            this.__changeTipHijo(parent == 4);
        } else {
            this.$el.find('#show_mother').addClass('d-none');
            this.__hasBiologico(false);
            if (parent == '5') this.__changeCustodia(true);
            if (parent == '2' || parent == '3') this.__changePadres(true);
        }
    }

    changeBiodesco(e) {
        const biodesco = this.$el.find("[name='biodesco']:checked").val();
        if (biodesco == 'S') {
            this.$el.find('.s-bio-desco').addClass('d-none');
            BeneficiarioModel.changeRulesProperty([
                { rule: 'biocodciu', prop: 'required', value: false },
                { rule: 'biodire', prop: 'required', value: false },
                { rule: 'biocedu', prop: 'required', value: false },
                { rule: 'biotipdoc', prop: 'required', value: false },
                { rule: 'bioprinom', prop: 'required', value: false },
                { rule: 'biopriape', prop: 'required', value: false },
                { rule: 'bioemail', prop: 'required', value: false },
                { rule: 'biophone', prop: 'required', value: false },
            ]);
        } else {
            BeneficiarioModel.changeRulesProperty([
                { rule: 'biocodciu', prop: 'required', value: true },
                { rule: 'biodire', prop: 'required', value: true },
                { rule: 'biocedu', prop: 'required', value: true },
                { rule: 'bioprinom', prop: 'required', value: true },
                { rule: 'biopriape', prop: 'required', value: true },
                { rule: 'biotipdoc', prop: 'required', value: true },
            ]);

            const cedcon = this.$el.find('#cedcon').val();
            this.$el.find('#biocedu').val(cedcon ? cedcon : '');
            this.$el.find('#biocedu').trigger('focus');
            this.$el.find('.s-bio-desco').removeClass('d-none');
        }
    }

    __hasBiologico(status) {
        if (status === true) {
            BeneficiarioModel.changeRulesProperty([
                { rule: 'biocedu', prop: 'required', value: true },
                { rule: 'biotipdoc', prop: 'required', value: true },
                { rule: 'bioprinom', prop: 'required', value: true },
                { rule: 'biopriape', prop: 'required', value: true },
                { rule: 'biocodciu', prop: 'required', value: true },
                { rule: 'biodire', prop: 'required', value: true },
                { rule: 'bioemail', prop: 'required', value: true },
                { rule: 'bioemail', prop: 'force', value: 'email' },
                { rule: 'biophone', prop: 'required', value: true },
            ]);
            this.$el.find('.show-biologico').removeClass('d-none');
        } else {
            BeneficiarioModel.changeRulesProperty([
                { rule: 'biocedu', prop: 'required', value: false },
                { rule: 'biodire', prop: 'required', value: false },
                { rule: 'biotipdoc', prop: 'required', value: false },
                { rule: 'bioprinom', prop: 'required', value: false },
                { rule: 'biopriape', prop: 'required', value: false },
                { rule: 'biocodciu', prop: 'required', value: false },
                { rule: 'bioemail', prop: 'required', value: false },
                { rule: 'biophone', prop: 'required', value: false },
            ]);
            this.$el.find('.show-biologico').addClass('d-none');
        }
    }

    __changeTipHijo(status) {
        if (status) {
            this.$el.find('#tiphij option[value="3"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="4"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="5"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="6"]').prop('disabled', false);
        } else {
            this.$el.find('#tiphij option[value="3"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="4"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="5"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="6"]').prop('disabled', true);
        }
    }

    __changeCustodia(status) {
        if (status) {
            this.$el.find('#tiphij option[value="1"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="2"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="3"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="4"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="5"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="6"]').prop('disabled', false);
        } else {
            this.$el.find('#tiphij option[value="1"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="2"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="3"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="4"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="5"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="6"]').prop('disabled', true);
        }
    }

    __changePadres(status) {
        if (status) {
            this.$el.find('#tiphij option[value="1"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="2"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="3"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="4"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="5"]').prop('disabled', true);
            this.$el.find('#tiphij option[value="6"]').prop('disabled', true);
        } else {
            this.$el.find('#tiphij option[value="1"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="2"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="3"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="4"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="5"]').prop('disabled', false);
            this.$el.find('#tiphij option[value="6"]').prop('disabled', false);
        }
    }

    changePeretn(e) {
        const _parent = this.$el.find(e.currentTarget).val();
        if (_parent == '3') {
            this.$el.find('.show-peretn').removeClass('d-none');
        } else {
            this.$el.find('.show-peretn').addClass('d-none');
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

            BeneficiarioModel.changeRulesProperty([
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

            BeneficiarioModel.changeRulesProperty([
                { rule: 'numcue', prop: 'required', value: false },
                { rule: 'codban', prop: 'required', value: false },
                { rule: 'tipcue', prop: 'required', value: false },
            ]);

            this.$el.find('#numcue').rules('remove', 'required');
            this.$el.find('#codban').rules('remove', 'required');
            this.$el.find('#tipcue').rules('remove', 'required');
        }
    }

    validaTrabajador(e) {
        e.preventDefault();
        if (!this.isNew) return false;
        let cedtra = this.getInput('[name="cedtra"]');
        if (cedtra == '') return false;

        let list = [];
        let has = [];

        const afili = this.App.Collections.formParams.list_afiliados;
        const mtrab = this.App.Collections.formParams.trabajadores;

        if (afili !== false && mtrab !== false) {
            list = _.union(afili, mtrab);
        } else {
            list = mtrab;
        }

        if (!_.isEmpty(list)) {
            has = _.where(list, { cedula: cedtra });
        }

        if (_.isEmpty(has)) {
            this.setInput('cedtra', '');
            this.App.trigger('alert:warning', {
                message:
                    'El número de identificación del trabajador no existe como afiliado o como una solicitud pendiente de validación. Se recomienda primero crear la solicitud del trabajador para poder continuar.',
            });
        }
    }

    validaMother(e) {
        e.preventDefault();
        if (!this.isNew) return false;
        let cedcon = this.getInput('[name="cedcon"]');
        if (cedcon == '') return false;

        let has = false;
        const afili = this.App.Collections.formParams.list_conyuges;
        const cedcons = this.App.Collections.formParams.conyuges;
        let list;

        if (afili !== false && cedcons !== false) {
            list = _.union(afili, cedcons);
        } else if (cedcons) {
            list = cedcons;
        } else {
            list = afili;
        }

        if (!_.isEmpty(list)) {
            for (const listKey in list) {
                has = list[listKey].cedula == cedcon ? true : false;
                if (has) break;
            }
        }
        if (has == false) {
            this.setInput('cedcon', '');
            this.App.trigger('alert:warning', {
                message:
                    'El número de identificación no existe como afiliado o como una solicitud pendiente de validación. Se recomienda primero crear la solicitud para poder continuar.',
            });
        }
    }

    remove() {
        if (_.size(this.viewComponents) > 0) {
            _.each(this.viewComponents, (view) => view.remove());
        }
        $.each(this.#choiceComponents, (choice) => choice.destroy());
        FormView.prototype.remove.call(this, {});
    }
}
