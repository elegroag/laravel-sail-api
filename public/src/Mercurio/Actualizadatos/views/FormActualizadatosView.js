import { $App } from '../../../App';
import { ComponentModel } from '../../../Componentes/Models/ComponentModel';
import { eventsFormControl } from '../../../Core';
import { EmpresaModel } from '../../Empresas/models/EmpresaModel';
import { FormView } from '../../FormView';
import { ActualizadatosModel } from '../models/ActualizadatosModel';

class FormActualizadatosView extends FormView {
    #choiceComponents = null;

    constructor(options = {}) {
        super({
            ...options,
            onRender: (el = {}) => this.afterRender(el),
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

    async afterRender($el = {}) {
        if (this.model.get('id') === null) {
            const response = await this.__findDataEmpresa();
            this.modelEmpresa = new EmpresaModel(response);
        }

        _.each(this.collection, (component) => {
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
            this.viewComponents.push(view);
            this.$el.find('#component_' + component.name).html(view.$el);
        });

        if (this.modelEmpresa instanceof EmpresaModel) {
            _.each(this.modelEmpresa.toJSON(), (valor, key) => {
                if (!(_.isEmpty(valor) == true || _.isUndefined(valor) == true)) {
                    this.$el.find(`[name="${key}"]`).val(valor);
                }
            });
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

        this.selectores = this.$el.find('#tipdoc, #tipsoc, #ciupri, #codzon, #codciu, #codact, #coddocrepleg');

        if (this.model.get('id') !== null) {
            _.each(this.model.toJSON(), (valor, key) => {
                if (!(_.isEmpty(valor) == true || _.isUndefined(valor) == true)) {
                    this.$el.find(`[name="${key}"]`).val(valor);
                }
            });

            this.$el.find('#nit, #cedrep').attr('readonly', true);
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
        let coddocrepleg = ActualizadatosModel.changeTipdoc(tipdoc);
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
            $App.trigger('alert:warning', {
                message: 'Se requiere de resolver los campos requeridos para continuar.',
            });
            setTimeout(() => $('label.error').text(''), 6000);
            return false;
        }

        this.$el.find('#cedrep').removeAttr('disabled');

        const entity = this.serializeModel(new ActualizadatosModel(this.modelEmpresa.toJSON()));

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
