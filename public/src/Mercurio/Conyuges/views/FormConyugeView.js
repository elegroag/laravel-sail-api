import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es.js';
import { $App } from '../../../App';
import Logger from '../../../Common/Logger';
import { ComponentModel } from '../../../Componentes/Models/ComponentModel';
import { eventsFormControl } from '../../../Core';
import { FormView } from '../../FormView';
import { ConyugeModel } from '../models/ConyugeModel';

export class FormConyugeView extends FormView {
    #choiceComponents = null;
    #logger;

    constructor(options = {}) {
        super({
            ...options,
            onRender: (el = {}) => this.#afterRender(el),
        });
        this.viewComponents = [];
        this.#choiceComponents = [];
        this.#logger = new Logger();
    }

    get events() {
        return {
            'focusout #cedtra': 'validaTrabajador',
            'click #guardar_ficha': 'saveFormData',
            'click #cancel': 'cancel',
            'focusout #telefono, #digver': 'isNumber',
            'focusout #cedcon': 'validaConyuge',
            'change #tippag': 'changeTippag',
            'change #labora_otra_empresa': 'changeOtraEmpresa',
            'click [data-toggle="address"]': 'openAddress',
            'click #btEnviarRadicado': 'enviarRadicado',
            'change #codocu': 'changeCodocu',
        };
    }

    #afterRender($el = {}) {
        _.each(this.collection, (component = { name: '', type: undefined }) => {
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
            );
            $el.find('#component_' + component.name).html(view.$el);
        });

        this.form.validate({
            ...ConyugeModel.Rules,
            highlight: function (element = '') {
                $(element).removeClass('is-valid').addClass('is-invalid');
            },
            unhighlight: function (element = '') {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
        });

        this.selectores = $el.find('#tipdoc, #codzon, #ciures, #ciunac, #pub_indigena_id, #resguardo_id');

        if (this.model.get('id') !== null) {
            $.each(this.model.toJSON(), (key, valor) => {
                const inputElement = this.$el.find(`[name="${key}"]`);
                if (inputElement.length) {
                    inputElement.val(valor);
                }
            });

            this.form.find('#nit').attr('disabled', 'true');

            if (this.model.get('tippag') == 'A' || this.model.get('tippag') == 'D') {
                this.form.find('#show_numcue').removeClass('d-none');
                this.form.find('#show_codban').removeClass('d-none');
            }

            if (this.model.get('otra_empresa') == 'N') {
                this.form.find('#show_otra_empresa').removeClass('d-none');
            }

            setTimeout(() => this.form.valid(), 300);
            setTimeout(() => $('label.error').text(''), 3000);

            $.each(this.selectores, (index, element) => {
                this.#choiceComponents[element.name] = new Choices(element);
                const name = this.model.get(element.name);
                if (name) this.#choiceComponents[element.name].setChoiceByValue(name);
            });
        } else {
            $.each(this.selectores, (index, element) => (this.#choiceComponents[element.name] = new Choices(element)));
        }

        eventsFormControl($el);

        flatpickr($el.find('#fecnac'), {
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: Spanish,
        });
    }

    changeOtraEmpresa(e) {
        let target = this.$el.find(e.currentTarget).val();
        if (target == 'S') {
            this.form.find('#show_otra_empresa').removeClass('d-none');
        } else {
            this.$el.find('#show_otra_empresa').addClass('d-none');
        }
    }

    changeTippag(e) {
        let target = this.$el.find(e.currentTarget).val();
        if (target == 'A' || target == 'D') {
            this.$el.find('#show_numcue').removeClass('d-none');
            this.$el.find('#show_codban').removeClass('d-none');
        } else {
            this.$el.find('#show_numcue').addClass('d-none');
            this.$el.find('#show_codban').addClass('d-none');
        }
    }

    serializeData() {
        let data;
        if (this.model instanceof ConyugeModel) {
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
            $App.trigger('alert:warning', {
                message: 'Se requiere de resolver los campos requeridos para continuar.',
            });
            setTimeout(() => $('label.error').text(''), 6000);
            return false;
        }

        this.$el.find('#nit').removeAttr('disabled');
        const entity = this.serializeModel(new ConyugeModel());

        if (entity.isValid() === false) {
            target.removeAttr('disabled');
            $App.trigger('alert:warning', {
                message: 'Algunos campos son requeridos para continuar: ' + entity.validationError.join(' '),
            });
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
                            this.$el.find('#nit').attr('disabled', 'true');

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
                                        this.__renderDocumentos();
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

    validaConyuge(e) {
        if (!this.isNew) return false;
        let cedcon = this.$el.find(e.currentTarget).val();
        if (cedcon === '') return false;

        $App.trigger('syncro', {
            url: $App.url('valida'),
            data: {
                cedcon: cedcon,
            },
            callback: (solicitud) => {
                if (solicitud) {
                    const conyuge = solicitud.conyuge || {};
                    $App.trigger('confirma', {
                        message:
                            'El sistema identifica información del afiliado en el sistema central de la Caja. ¿Desea que se actualice el presente formulario con los datos existentes?.',
                        callback: (status) => {
                            if (status) {
                                conyuge.estado = 'T';
                                conyuge.fecsol = null;

                                this.model.set(conyuge);
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

    actualizaForm() {
        _.each(this.model.toJSON(), (valor, key) => {
            if (_.isEmpty(valor) == true || _.isUndefined(valor) == true) {
            } else {
                this.$el.find(`[name="${key}"]`).val(valor);
                this.$el.find(`[for="${key}"]`).addClass('top');
            }
        });
        this.selectores.trigger('change');
        this.form.valid();
        $('#nit').attr('disabled', true);

        setTimeout(() => {
            $App.trigger('alert:success', {
                message: 'El formulario se actualizo de forma correcta',
            });
        }, 700);
    }

    validaTrabajador(e) {
        if (!this.isNew) return false;
        let cedtra = this.getInput('#cedtra');
        if (cedtra == '') return false;

        let has = [];
        let list = [];

        const afili = $App.Collections.formParams.list_afiliados || [];
        const mtrab = $App.Collections.formParams.trabajadores || [];
        list = _.union(afili, mtrab);

        if (!_.isEmpty(list)) has = _.where(list, { cedula: cedtra });

        if (_.isEmpty(has)) {
            this.setInput('cedtra', '');
            $App.trigger('alert:warning', {
                message:
                    'El número de identificación del trabajador no existe como afiliado o como una solicitud pendiente de validación. ' +
                    'Se recomienda primero crear la solicitud del trabajador para poder continuar.',
            });
        }
    }

    changeCodocu(e) {
        let valor = this.$el.find(e.currentTarget).val();
        if (valor == '01') {
            this.$el.find('#show_empresalab').removeClass('d-none');
            this.$el.find('#show_fecing').removeClass('d-none');
            this.$el.find('#show_salario').removeClass('d-none');
            this.setInput('salario', '');
            this.setInput('empresalab', '');
            this.setInput('fecing', '');
        } else {
            this.$el.find('#show_empresalab').addClass('d-none');
            this.$el.find('#show_fecing').addClass('d-none');
            this.$el.find('#show_salario').addClass('d-none');
            this.setInput('salario', '0');
            this.setInput('empresalab', 'NULL');
            this.setInput('fecing', 'NULL');
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
