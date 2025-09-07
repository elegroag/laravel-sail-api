import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { $App } from '../../../App';
import { ComponentModel } from '../../../Componentes/Models/ComponentModel';
import { eventsFormControl } from '../../../Core';
import { FormView } from '../../FormView';
import { IndependienteModel } from '../models/IndependienteModel';

export class FormIndependentView extends FormView {
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

            this.viewComponents.push(view);
            this.$el.find('#component_' + component.name).html(view.$el);
        });

        this.form.validate({
            ...IndependienteModel.Rules,
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

            this.$el.find('#cedtra').attr('readonly', true);

            if (this.model.get('tippag') == 'A' || this.model.get('tippag') == 'D') {
                this.form.find('#show_numcue').removeClass('d-none');
                this.form.find('#show_codban').removeClass('d-none');
                this.form.find('#show_tipcue').removeClass('d-none');
            } else {
                this.$el.find('#numcue').rules('remove', 'required');
                this.$el.find('#codban').rules('remove', 'required');
                this.$el.find('#tipcue').rules('remove', 'required');

                IndependienteModel.changeRulesProperty([
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
    }

    changeTipoDocumento(e) {
        let tipdoc = $(e.currentTarget).val();
        let coddocrepleg = IndependienteModel.changeTipdoc(tipdoc);
        this.$el.find('#coddocrepleg').val(coddocrepleg);
    }

    saveFormData(event) {
        event.preventDefault();
        const target = this.$el.find(event.currentTarget);
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

        const entity = this.serializeModel(new IndependienteModel());
        if (entity.get('numcue') == '') entity.set('numcue', '0');

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

        $App.trigger('form:find', {
            cedtra: cedtra,
            callback: () => {
                this.actualizaForm();
                this.$el.find('#cedtra').attr('disabled', true);

                $.each(this.selectores, (index, element) => {
                    const name = this.model.get(element.name);
                    if (name) this.#choiceComponents[element.name].setChoiceByValue(name);
                });
            },
        });
    }

    comfirmarSincronizar(model) {
        this.model.entity = model;

        $('#codact').val(this.model.entity.get('codact'));
        $('#calemp').val(this.model.entity.get('calemp'));
        $('#cedtra').val(this.model.entity.get('cedtra'));
        $('#direccion').val(this.model.entity.get('direccion'));
        $('#codciu').val(this.model.entity.get('codciu'));
        $('#codzon').val(this.model.entity.get('codzon'));
        $('#telefono').val(this.model.entity.get('telefono'));
        $('#celular').val(this.model.entity.get('celular'));
        $('#email').val(this.model.entity.get('email'));
        $('#prinom').val(this.model.entity.get('prinom'));
        $('#segnom').val(this.model.entity.get('segnom'));
        $('#priape').val(this.model.entity.get('priape'));
        $('#segape').val(this.model.entity.get('segape'));
        $('#codcaj').val(this.model.entity.get('codcaj'));
        $('#coddoc').val(this.model.entity.get('tipdoc'));

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
            cedtra: empresa.nit,
            tipdoc: empresa.coddoc,
            priape: empresa.priape,
            segape: empresa.segape,
            prinom: empresa.priape,
            segnom: empresa.segape,
            direccion: empresa.direccion,
            codciu: empresa.codciu,
            telefono: empresa.telefono,
            email: empresa.email,
            codzon: empresa.codzon,
            celular: empresa.telr,
            coddocrepleg: 'CC',
        });
    }

    setModelTrabajador(trabajador) {
        this.model.set({
            cedtra: trabajador.cedtra,
            tipdoc: trabajador.coddoc,
            priape: trabajador.priape,
            segape: trabajador.segape,
            prinom: trabajador.prinom,
            segnom: trabajador.segnom,
            direccion: trabajador.direccion,
            codciu: trabajador.codciu,
            telefono: trabajador.telefono,
            email: trabajador.email,
            codzon: trabajador.codzon,
            rural: trabajador.rural,
            cabhog: trabajador.cabhog,
            captra: trabajador.captra,
            tipdis: trabajador.tipdis,
            salario: trabajador.salario,
            sexo: trabajador.sexo,
            estciv: trabajador.estciv,
            vivienda: trabajador.vivienda,
            nivedu: trabajador.nivedu,
            vendedor: trabajador.vendedor,
            tippag: trabajador.tippag,
            codban: trabajador.codban,
            numcue: trabajador.numcue,
            tipcue: trabajador.tipcue,
            fecnac: trabajador.fecnac,
            ciunac: trabajador.ciunac,
            cargo: trabajador.cargo,
            orisex: trabajador.orisex,
            facvul: trabajador.facvul,
            peretn: trabajador.peretn,
            resguardo_id: trabajador.resguardo_id,
            pub_indigena_id: trabajador.pub_indigena_id,
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

            IndependienteModel.changeRulesProperty([
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

            IndependienteModel.changeRulesProperty([
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
