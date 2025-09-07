import { $App } from '../../../App';
import { ComponentModel } from '../../../Componentes/Models/ComponentModel';
import { eventsFormControl } from '../../../Core';
import { FormView } from '../../FormView';
import { TrabajadorModel } from '../models/TrabajadorModel';

class FormTrabajadorView extends FormView {
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
            'focusout #telefono, #digver': 'isNumber',
            'focusout #cedtra': 'validePk',
            'change #tippag': 'changeTippag',
            'change #labora_otra_empresa': 'changeOtraEmpresa',
            'click #btEnviarRadicado': 'enviarRadicado',
            'click [data-toggle="address"]': 'openAddress',
            'click #cancel': 'cancel',
            'blur [data-toggle="is_numeric"]': 'isNumber',
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

            this.viewComponents.push(view);
            $el.find('#component_' + component.name).html(view.$el);
        });

        this.form.validate({
            ...TrabajadorModel.Rules,
            highlight: function (element) {
                $(element).removeClass('is-valid').addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
        });

        this.selectores = $el.find('#tipdoc, #tipsoc, #ciupri, #codzon, #codciu, #codact, #coddoc, #ciunac, #cargo, #pub_indigena_id, #resguardo_id');

        if (this.model.get('id') !== null) {
            $.each(this.model.toJSON(), (key, valor) => {
                const inputElement = this.$el.find(`[name="${key}"]`);
                if (inputElement.length && valor) {
                    inputElement.val(valor);
                }
            });

            $('#nit').removeAttr('disabled');
            $('#razsoc').removeAttr('disabled');

            if (this.model.get('tippag') == 'A' || this.model.get('tippag') == 'D') {
                $el.find('#show_numcue').removeClass('d-none');
                $el.find('#show_codban').removeClass('d-none');
                $el.find('#show_tipcue').removeClass('d-none');
            }

            if (this.model.get('otra_empresa') == 'N') {
                $el.find('#show_otra_empresa').removeClass('d-none');
            }

            if (this.model.isValid() === false) {
                $App.trigger('alert:warning', {
                    message: 'Algunos de los campos son requeridos: ' + this.model.validationError.join(' '),
                });
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

        flatpickr($el.find('#fecnac, #fecing'), {
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
            this.$el.find('#show_tipcue').removeClass('d-none');
        } else {
            this.$el.find('#show_numcue').addClass('d-none');
            this.$el.find('#show_codban').addClass('d-none');
            this.$el.find('#show_tipcue').addClass('d-none');
        }
    }

    serializeData() {
        let data;
        if (this.model.entity instanceof TrabajadorModel) {
            data = this.model.entity.toJSON();
        }
        return data;
    }

    saveFormData(event) {
        event.preventDefault();
        var target = this.$el.find(event.currentTarget);
        target.attr('disabled', 'true');
        this.$el.find('#nit').removeAttr('disabled');

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

        const entity = this.serializeModel(new TrabajadorModel());
        const autoriza = this.$el.find("[name='autoriza']")[0].checked ? 'S' : 'N';
        entity.set('autoriza', autoriza);

        if (entity.isValid() === false) {
            target.removeAttr('disabled');
            $App.trigger('alert:warning', {
                message: 'Algunos de los campos son requeridos: ' + entity.validationError.join(' '),
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

    validePk(e) {
        e.preventDefault();
        if (!this.isNew) return false;

        let cedtra = this.$el.find(e.currentTarget).val();
        if (cedtra === '') return false;

        this.trigger('form:find', {
            data: {
                cedtra: cedtra,
            },
            callback: (solicitud) => {
                if (solicitud) {
                    $App.trigger('confirma', {
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

export { FormTrabajadorView };
