import { ComponentModel } from '@/Componentes/Models/ComponentModel';
import { TrabajadorNominaModel } from '@/Componentes/Models/TrabajadorNominaModel';
import { eventsFormControl } from '@/Core';
import { FormView } from '@/Mercurio/FormView';
import { TraNomCollection } from '@/Mercurio/Trabajadores/collections/TrabajadoresCollection';
import { setTimeout } from 'timers/promises';
import { EmpresaModel } from '../models/EmpresaModel';
import { TrabajadoresNominaView } from './TrabajadoresNominaView';

export class FormEmpresaView extends FormView {
    #choiceComponents = null;

    constructor(options = {}) {
        super({
            ...options,
            onRender: (el = {}) => this.#afterRender(el),
        });
        this.trabajadoresNomina = new TraNomCollection();
        this.viewTranom = null;
        this.viewComponents = [];
        this.#choiceComponents = [];
    }

    get events() {
        return {
            'click #guardar_ficha': 'saveFormData',
            'click #cancel': 'cancel',
            'focusout #telefono, #digver': 'isNumber',
            'focusout #nit': 'valideNitkey',
            'change #tipdoc': 'changeTipoDocumento',
            'click [data-toggle="address"]': 'openAddress',
            'change #tipper': 'changeTipoPer',
            'focusout #sigla': 'changeSigla',
            'focusout #razsoc': 'traerRazsoc',
            'focusout #prinom, #segnom, #priape, #segape': 'changeRepleg',
            'click #add_trabajador': 'addTrabajador',
            'click #clean_formtra': 'cleanFormTra',
            'click #btEnviarRadicado': 'enviarRadicado',
        };
    }

    #afterRender($el = {}) {
        _.each(this.collection, (component) => {
            const view = this.addComponent(
                new ComponentModel({
                    ...component,
                    valor: this.model.get(component.name),
                }),
            );
            $el.find('#component_' + component.name).html(view.$el);
        });

        this.form.validate({
            ...EmpresaModel.Rules,
            highlight: function (element) {
                $(element).removeClass('is-valid').addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
        });

        this.selectores = $el.find('#tipdoc, #tipsoc, #ciupri, #codzon, #codciu, #codact, #coddocrepleg, #cartra');

        if (this.model.get('id') !== null) {
            $.each(this.model.toJSON(), (key, valor) => {
                const inputElement = this.$el.find(`[name="${key}"]`);
                if (inputElement.length && valor) {
                    inputElement.val(valor);
                }
            });

            this.selectores.trigger('change');
            $el.find('#nit, #cedrep').attr('readonly', true);
            setTimeout(() => this.form.valid(), 200);

            $.each(this.selectores, (index, element) => {
                this.#choiceComponents[element.name] = new Choices(element);
                const name = this.model.get(element.name);
                if (name) this.#choiceComponents[element.name].setChoiceByValue(name);
            });
        } else {
            $.each(
                this.selectores,
                (index, element) => (this.#choiceComponents[element.name] = new Choices(element, { silent: true, itemSelectText: '' })),
            );
        }

        this.selectores.on('change', (event) => {
            const value = $(event.currentTarget).val();
            this.validateChoicesField(value, this.#choiceComponents[event.currentTarget.name]);
        });

        flatpickr(this.$el.find('#fecini, #fectra'), {
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: Spanish,
        });

        eventsFormControl($el);

        this.beforeRender();
    }

    beforeRender() {
        const tranoms = this.model.get('tranoms');
        this.trabajadoresNomina.add(tranoms);
        this.viewTranom = new TrabajadoresNominaView({ collection: this.trabajadoresNomina });
        this.$el.find('#tableTranomRow').append(this.viewTranom.render().el);
    }

    changeRepleg(e) {
        e.preventDefault();
        let repleg = this.nameRepleg();
        this.$el.find('#repleg').val(repleg);
        this.$el.find('#repleg').siblings('.control-label').addClass('top');
    }

    changeTipoPer(e) {
        e.preventDefault();
        if (this.$el.find('#tipper').val() == 'N') {
            this.$el.find('#tipdoc').val(1);
        } else {
            this.$el.find('#tipdoc').val(3);
        }
        this.selectores.trigger('change');
    }

    changeTipoDocumento(e) {
        let tipdoc = $(e.currentTarget).val();
        let coddocrepleg = EmpresaModel.changeTipdoc(tipdoc);
        this.$el.find('#coddocrepleg').val(coddocrepleg);
    }

    saveFormData(event) {
        event.preventDefault();
        var target = this.$el.find(event.currentTarget);
        target.attr('disabled', true);

        let _err = 0;
        if (this.form.valid() == false) _err++;
        if (_err > 0) {
            target.removeAttr('disabled');
            this.App.trigger('alert:warning', {
                message: 'Se requiere de resolver los campos requeridos para continuar.',
            });
            return false;
        }

        this.$el.find('#nit').removeAttr('disabled');

        const entity = this.serializeModel(new EmpresaModel());

        if (entity.isValid() !== true) {
            target.removeAttr('disabled');
            this.App.trigger('alert:warning', { message: entity.validationError.join(' ') });
            setTimeout(() => $('label.error').text(''), 6000);
            return false;
        }

        if (_.size(this.trabajadoresNomina) == 0) {
            target.removeAttr('disabled');
            this.App.trigger('alert:warning', {
                message:
                    'Error, se requiere de tener minimo un trabajador en nomina. Adicionalo en la sección inferior de "Relaciona trabajadores en nomina"',
            });
            return false;
        }

        entity.set('repleg', this.nameRepleg());
        this.$el.find('#repleg').val(entity.get('repleg'));

        entity.set({ tranoms: this.trabajadoresNomina.toJSON() });

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
            // Adjuntamos la clave al entity para que viaje al backend
            try {
                entity.set('clave', clave);
            } catch (e) {
                // fallback por si entity no es un Backbone.Model estándar
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
                            callback: (response) => {
                                target.removeAttr('disabled');
                                this.$el.find('#nit').attr('disabled', true);

                                if (response) {
                                    if (response.success) {
                                        this.App.trigger('alert:success', { message: response.msj });
                                        this.model.set({ id: parseInt(response.data.id) });
                                        if (this.isNew === true) {
                                            this.App.router.navigate('proceso/' + this.model.get('id'), {
                                                trigger: true,
                                                replace: true,
                                            });
                                        }
                                        setTimeout(() => {
                                            const _tab = new bootstrap.Tab('a[href="#documentos_adjuntos"]');
                                            _tab.show();
                                        }, 300);
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

    changeSigla(e) {
        e.preventDefault();
        let razsoc = $('#razsoc').val();
        if (_.isUndefined(razsoc) || _.isEmpty(razsoc)) {
            return false;
        } else {
            let sigla = _.map(razsoc.split(/\s/g), function (cadena) {
                return _.first(cadena);
            }).join('');
            this.$el.find('#sigla').val(sigla.toUpperCase());
        }
        return false;
    }

    valideNitkey(e) {
        e.preventDefault();
        if (!this.isNew) return false;

        let nit = this.$el.find(e.currentTarget).val();
        if (nit === '' || _.isUndefined(nit)) return false;

        this.trigger('form:digit', {
            nit: nit,
            callback: (entity) => {
                this.$el.find('#digver').val(entity.digver);
                this.$el.find('#digver').siblings('.control-label').addClass('top');
            },
        });

        this.trigger('form:find', {
            data: {
                nit: nit,
            },
            callback: (solicitud) => {
                if (solicitud) {
                    this.App.trigger('confirma', {
                        message:
                            'El sistema identifica datos de la empresa en su sistema principal. ¿Desea que se actualice el presente formulario con los datos existentes?.',
                        callback: (status) => {
                            if (status) {
                                solicitud.estado = 'T';
                                solicitud.fecsol = null;
                                this.model.set(solicitud);
                                this.actualizaForm();
                                this.$el.find('#nit').attr('disabled', true);

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

    traerRazsoc(e) {
        e.preventDefault();
        let valor = this.$el.find('#razsoc').val();
        if (valor.length > 0) {
            let sigla = _.map(valor.split(/\s/g), function (cadena) {
                return _.first(cadena);
            }).join('');
            this.$el.find('#sigla').val(sigla.toUpperCase());
            this.$el.find('#sigla').siblings('.control-label').addClass('top');
        }
        return false;
    }

    setModelUseEmpresa(empresa) {
        let nombre;
        if (empresa.priaperepleg == null) {
            nombre = empresa.priaperepleg + ' ' + empresa.segaperepleg + ' ' + empresa.prinomrepleg + ' ' + empresa.segnomrepleg;
        } else {
            nombre = empresa.priape + ' ' + empresa.segape + ' ' + empresa.prinom + ' ' + empresa.segnom;
        }
        this.model.set({
            nit: empresa.nit,
            cedrep: empresa.nit,
            tipdoc: empresa.coddoc,
            digver: empresa.digver,
            razsoc: nombre,
            priape: empresa.priape,
            segape: empresa.segape,
            prinom: empresa.priape,
            segnom: empresa.segape,
            nomemp: nombre,
            repleg: nombre,
            direccion: empresa.direccion,
            codciu: empresa.codciu,
            telefono: empresa.telefono,
            email: empresa.email,
            codzon: empresa.codzon,
            codase: empresa.codase,
            calemp: empresa.calemp,
            tipemp: empresa.tipemp,
            tipsoc: empresa.tipsoc,
            tipapo: empresa.tipapo,
            estado: 'T',
            celuar: empresa.telr,
            coddocrepleg: 'CC',
            codcat: 'B',
        });
    }

    addTrabajador(e) {
        e.preventDefault();
        const trabajador = new TrabajadorNominaModel({
            cedtra: this.getInput('#cedtra'),
            nomtra: this.getInput('#nomtra'),
            apetra: this.getInput('#apetra'),
            saltra: this.getInput('#saltra'),
            fectra: this.getInput('#fectra'),
            cartra: this.getInput('#cartra'),
            request: this.model.get('id'),
        });

        if (!trabajador.isValid()) {
            $('label.error').fadeIn();
            this.App.trigger('alert:warning', { message: trabajador.validationError.join(', ') });
            setTimeout(() => $('label.error').fadeOut(), 6000);
            return false;
        }
        this.trabajadoresNomina.add(trabajador, { merge: true });

        this.setInput('cedtra', '');
        this.setInput('nomtra', '');
        this.setInput('apetra', '');
        this.setInput('saltra', '');
        this.setInput('fectra', '');
        this.setInput('cartra', '');
    }

    cleanFormTra(e) {
        e.preventDefault();
        this.setInput('cedtra', '');
        this.setInput('nomtra', '');
        this.setInput('apetra', '');
        this.setInput('saltra', '');
        this.setInput('fectra', '');
        this.setInput('cartra', '');
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
