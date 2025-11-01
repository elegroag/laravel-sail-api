import { FormView } from '../../FormView';
import { ComponentModel } from '../../../Componentes/Models/ComponentModel';
import { $App } from '../../../App';
import { eventsFormControl } from '../../../Core';
import DatosTrabajadorModel from '../models/DatosTrabajadorModel';

export default class FormDatosTrabajador extends FormView {
	#choiceComponents = null;

	constructor(options = {}) {
		super({
			...options,
			onRender: (el={}) => this.#afterRender(el)
		});
		this.viewComponents = [];
		this.#choiceComponents = [];
	}

	get events() {
		return {
			'click #guardar_ficha': 'saveFormData',
			'click #cancel': 'cancel',
			'focusout #telefono': 'isNumber',
			'click [data-toggle="address"]': 'openAddress',
			'click #btEnviarRadicado': 'enviarRadicado',
			'change #tippag': 'changeTippag',
		};
	}

	#afterRender($el = {}) {
		_.each(this.collection.paramsForm, (component) => {
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

		_.each(this.collection.dataDefault, (valor, key) => {
			if (!(_.isEmpty(valor) == true || _.isUndefined(valor) == true)) {
				this.$el.find(`[name="${key}"]`).val(valor);
			}
		});

		this.form.validate({
			...DatosTrabajadorModel.Rules,
			highlight: function (element) {
				$(element).removeClass('is-valid').addClass('is-invalid');
			},
			unhighlight: function (element) {
				$(element).removeClass('is-invalid').addClass('is-valid');
			},
		});

		this.selectores = this.$el.find('#codzon, #codciu');

		if (this.model.get('id') !== null) {
			_.each(this.model.toJSON(), (valor, key) => {
				if (!(_.isEmpty(valor) == true || _.isUndefined(valor) == true)) {
					this.$el.find(`[name="${key}"]`).val(valor);
				}
			});
			if (this.model.get('tippag') == 'T') {
				this.$el.find('#codban').prop('disabled', true);
				this.$el.find('#numcue').prop('disabled', true);
				this.$el.find('#tipcue').prop('disabled', true);
				this.$el.find('#numcue').prop('disabled', true);
			}

			setTimeout(() => this.form.valid(), 200);
			$.each(this.selectores, (index, element) => {
				this.#choiceComponents[element.name] = new Choices(element);
				const name = this.model.get(element.name);
				if (name) this.#choiceComponents[element.name].setChoiceByValue(name);
			});
		} else {
			$.each(this.selectores, (index, element) => this.#choiceComponents[element.name] = new Choices(element));
		}

        this.selectores.on('change', (event) => {
            this.validateChoicesField(event.detail.value, this.#choiceComponents[event.currentTarget.name]);
        });

		flatpickr(this.$el.find('#expedicion, #respo_expedicion'), {
			enableTime: false,
			dateFormat: 'Y-m-d',
			locale: Spanish,
		});

		eventsFormControl(this.$el);
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
			setTimeout(() => this.$('label.error').text(''), 6000);
			return false;
		}

		const entity = this.serializeModel(new DatosTrabajadorModel());

		if (entity.isValid() !== true) {
			target.removeAttr('disabled');
			$App.trigger('alert:warning', { message: entity.validationError.join('<br/>') });
			setTimeout(() => this.$('label.error').text(''), 6000);
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

	changeTippag(event) {
		let target = $(event.currentTarget).val();
		if (target == 'A' || target == 'D') {
			if (target == 'D') {
				this.$el.find('#tipcue').val('A');
				this.$el.find('#codban').val(51);
			}
			this.$el.find('#codban').prop('disabled', false);
			this.$el.find('#numcue').prop('disabled', false);
			this.$el.find('#tipcue').prop('disabled', false);
		} else {
			this.$el.find('#tipcue').val('');
			this.$el.find('#codban').val('');
			this.$el.find('#numcue').val('');
			this.$el.find('#codban').prop('disabled', true);
			this.$el.find('#numcue').prop('disabled', true);
			this.$el.find('#tipcue').prop('disabled', true);
		}
	}

	remove() {
		if (_.size(this.viewComponents) > 0){
			_.each(this.viewComponents, (view) => view.remove());
		}
		$.each(this.#choiceComponents, (choice) => choice.destroy());
		FormView.prototype.remove.call(this, {});
	}
}
