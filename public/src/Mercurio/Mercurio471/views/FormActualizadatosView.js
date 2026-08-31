import { $App } from '@/App';
import { ComponentModel } from '@/Componentes/Models/ComponentModel';
import { eventsFormControl } from '@/Core';
import { FormView } from '@/Mercurio/FormView';
import { EmpresaModel } from '@/Mercurio/Mercurio30/models/EmpresaModel';
import { ActualizadatosModel } from '@/Mercurio/Mercurio471/models/ActualizadatosModel';
import Choices from 'choices.js';

class FormActualizadatosView extends FormView {
    #choiceComponents = null;

    constructor(options = {}) {
        super({
            ...options,
            onRender: (el = {}) => this.#afterRender(el),
        });
        this.viewComponents = [];
        this.modelEmpresa = null;
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
            'change #tipper': 'changeTipoPer',
            'focusout #nit': 'changeDigver',
            'focusout #digver': 'changeDigver',
            'focusout #sigla': 'changeSigla',
            'focusout #razsoc': 'traerRazsoc',
            'focusout #prinom, #segnom, #priape, #segape': 'changeRepleg',
            'click #btEnviarRadicado': 'enviarRadicado',
        };
    }

    async #afterRender($el = {}) {
        if (this.model.get('id') === null) {
            const response = await this.__findDataEmpresa();
            this.modelEmpresa = new EmpresaModel(response);
        }

        _.each(this.collection, (component) => {
            const view = this.addComponent(
                new ComponentModel({
                    ...component,
                    valor: this.model.get(component.name),
                }),
            );
            $el.find('#component_' + component.name).html(view.$el);
        });

        if (this.modelEmpresa instanceof EmpresaModel) {
            _.each(this.modelEmpresa.toJSON(), (valor, key) => {
                if (!(_.isEmpty(valor) == true || _.isUndefined(valor) == true)) {
                    this.$el.find(`[name="${key}"]`).val(valor);
                }
            });
        } else {
            this.modelEmpresa = new EmpresaModel(this.collection.props.empresa);
        }

        this.form.validate({
            ...ActualizadatosModel.Rules,
            highlight: (element) => {
                this.$el.find(element).removeClass('is-valid').addClass('is-invalid');
            },
            unhighlight: (element) => {
                this.$el.find(element).removeClass('is-invalid').addClass('is-valid');
            },
        });

        this.selectores = $el.find('#tipper, #tipdoc, #tipsoc, #ciupri, #codzon, #codciu, #codact, #coddocrepleg');

        if (this.model.get('id') !== null) {
            $.each(this.model.toJSON(), (key, valor) => {
                if (!(_.isEmpty(valor) == true || _.isUndefined(valor) == true)) {
                    $el.find(`[name="${key}"]`).val(valor);
                }
            });

            $el.find('#nit, #cedrep').attr('readonly', true);
            setTimeout(() => this.form.valid(), 200);

            $.each(this.selectores, (index, element) => {
                this.#choiceComponents[element.name] = new Choices(element);
                let name = this.model.get(element.name);
                if ((_.isUndefined(name) || _.isNull(name) || name === '') && element.name === 'tipdoc') {
                    name = this.model.get('coddoc');
                }
                if ((_.isUndefined(name) || _.isNull(name) || name === '') && element.name === 'tipper') {
                    const tipdocVal = this.model.get('tipdoc') || this.model.get('coddoc');
                    if (tipdocVal) {
                        name = tipdocVal == '1' || tipdocVal == 1 ? 'N' : 'J';
                    }
                }
                if (!(_.isUndefined(name) || _.isNull(name) || name === '')) {
                    this.$el.find(`[name="${element.name}"]`).val(name);
                    this.#choiceComponents[element.name].setChoiceByValue(String(name));
                }
            });
        } else {
            $.each(
                this.selectores,
                (index, element) => (this.#choiceComponents[element.name] = new Choices(element, { silent: true, itemSelectText: '' })),
            );

            if (this.modelEmpresa instanceof EmpresaModel) {
                $.each(this.selectores, (index, element) => {
                    const name = this.modelEmpresa.get(element.name);
                    if (name) this.#choiceComponents[element.name].setChoiceByValue(String(name));
                });
            }
        }

        this.selectores.on('change', (event) => {
            if (event.detail) {
                this.validateChoicesField(event.detail.value, this.#choiceComponents[event.currentTarget.name]);
            }
        });

        eventsFormControl(this.$el);
    }

    changeRepleg(e) {
        e.preventDefault();
        let repleg = this.nameRepleg();
        this.$el.find('#repleg').val(repleg);
        this.$el.find('#repleg').siblings('.control-label').addClass('top');
    }

    changeTipoPer(e) {
        e.preventDefault();
        const tipdocVal = this.$el.find('#tipper').val() == 'N' ? '1' : '3';
        this.$el.find('#tipdoc').val(tipdocVal);
        this.setChoice('tipdoc', tipdocVal);
        this.selectores.trigger('change');
    }

    changeTipoDocumento(e) {
        let tipdoc = $(e.currentTarget).val();
        let coddocrepleg = ActualizadatosModel.changeTipdoc(tipdoc);
        this.$el.find('#coddocrepleg').val(coddocrepleg);
        if (coddocrepleg) {
            this.setChoice('coddocrepleg', coddocrepleg);
        }
    }

    setChoice(fieldName, value) {
        if (this.#choiceComponents && this.#choiceComponents[fieldName]) {
            this.#choiceComponents[fieldName].setChoiceByValue(String(value));
        }
    }

    resetChoice(fieldName) {
        if (this.#choiceComponents && this.#choiceComponents[fieldName]) {
            this.#choiceComponents[fieldName].removeActiveItems();
            this.#choiceComponents[fieldName].setChoiceByValue('');
        }
    }

    saveFormData(event) {
        event.preventDefault();
        var target = this.$el.find(event.currentTarget);
        target.attr('disabled', true);

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

        this.$el.find('#cedrep').removeAttr('disabled');
        let entity;

        if (this.model.get('id') === null) {
            entity = this.serializeModel(new ActualizadatosModel(this.modelEmpresa.toJSON()));
        } else {
            entity = this.serializeModel(new ActualizadatosModel(this.model.toJSON()));
            entity.set('id', this.model.get('id'));
        }

        if (entity.isValid() === false) {
            target.removeAttr('disabled');
            $App.trigger('alert:warning', {
                message: 'Alerta, algunos de los campos son requeridos ' + entity.validationError.join(' '),
            });
            setTimeout(() => $('label.error').text(''), 6000);
            return false;
        }

        entity.set('repleg', this.nameRepleg());
        this.$el.find('#repleg').val(entity.get('repleg'));

        this.confirmSend({
            title: 'Confirmación requerida',
            message: 'Ingrese su clave numérica de 6 dígitos para confirmar el envío de la información.',
            inputPlaceholder: '000000',
            confirmText: 'Continuar',
            cancelText: 'Cancelar',
            inputAttributes: {
                autocapitalize: 'off',
            },
            callback: (result) => {
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

                $App.trigger('confirma', {
                    message: 'Confirma que desea guardar los datos del formulario.',
                    callback: (status) => {
                        if (status) {
                            this.trigger('form:save', {
                                entity: entity,
                                isNew: this.isNew,
                                callback: (response) => {
                                    target.removeAttr('disabled');
                                    this.$el.find('#nit').attr('disabled', true);

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
            },
        });
    }

    nameRepleg() {
        return this.getInput('#priape') + ' ' + this.getInput('#segape') + ' ' + this.getInput('#prinom') + ' ' + this.getInput('#segnom');
    }

    changeDigver(e) {
        e.preventDefault();
        let nit = this.$el.find('#nit').val();
        if (nit === '') {
            return false;
        }
        let $scope = this;
        this.trigger('form:digit', {
            nit: nit,
            callback: (entity) => {
                $scope.$el.find('#digver').val(entity.digver);
                $scope.$el.find('#digver').siblings('.control-label').addClass('top');
            },
        });
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

    validePk(e) {
        e.preventDefault();
        const cedrep = $(e.currentTarget).val();
        if (cedrep === '') return false;
        const $scope = this;
        this.trigger('form:find', {
            cedrep: cedrep,
            callback: (entity) => {
                console.log(entity);
                $scope.comfirmarSincronizar(entity);
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

    comfirmarSincronizar(entity, data) {
        this.model.entity = entity;

        $('#razsoc').val(this.model.entity.get('razsoc'));
        $('#codact').val(this.model.entity.get('codact'));
        $('#digver').val(this.model.entity.get('digver'));
        $('#calemp').val(this.model.entity.get('calemp'));
        $('#cedrep').val(this.model.entity.get('cedrep'));
        $('#repleg').val(this.model.entity.get('repleg'));
        $('#direccion').val(this.model.entity.get('direccion'));
        $('#codciu').val(this.model.entity.get('codciu'));
        $('#codzon').val(this.model.entity.get('codzon'));
        $('#telefono').val(this.model.entity.get('telefono'));
        $('#celular').val(this.model.entity.get('celular'));
        $('#email').val(this.model.entity.get('email'));
        $('#sigla').val(this.model.entity.get('sigla'));
        $('#tottra').val(this.model.entity.get('tottra'));
        $('#tipsoc').val(this.model.entity.get('tipsoc'));
        $('#dirpri').val(this.model.entity.get('dirpri'));
        $('#ciupri').val(this.model.entity.get('ciupri'));
        $('#tipper').val(this.model.entity.get('tipper'));
        $('#prinom').val(this.model.entity.get('prinom'));
        $('#segnom').val(this.model.entity.get('segnom'));
        $('#priape').val(this.model.entity.get('priape'));
        $('#segape').val(this.model.entity.get('segape'));
        $('#codcaj').val(this.model.entity.get('codcaj'));
        $('#tipdoc').val(this.model.entity.get('tipdoc'));
        $('#tipemp').val(this.model.entity.get('tipemp'));
        $('#matmer').val(this.model.entity.get('matmer'));
        $('#celular').val(this.model.entity.get('telr'));

        this.selectores.trigger('change');

        if (this.selectores && this.model.entity) {
            $.each(this.selectores, (index, element) => {
                const name = this.model.entity.get(element.name);
                if (name && this.#choiceComponents[element.name]) {
                    this.#choiceComponents[element.name].setChoiceByValue(String(name));
                }
            });
        }

        setTimeout(function () {
            Swal.fire({
                html: `<p style='font-size:14px'>El formulario se actualizo de forma correcta</p>`,
                showConfirmButton: false,
                timer: 2000,
            });
        }, 300);
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
            cedtra: empresa.nit,
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

    async __findDataEmpresa() {
        return new Promise((resolve, reject) => {
            $App.trigger('syncro', {
                url: $App.url('empresa_sisu', window.ServerController ?? 'empresa'),
                callback: (response) => {
                    if (response.success == true) resolve(response.data);
                    reject(false);
                },
            });
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

export { FormActualizadatosView };
