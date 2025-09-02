import { ComponentModel } from '@/Componentes/Models/ComponentModel';
import { FormView } from '@/Mercurio/FormView';
import { ServicioDomesticoModel } from '../models/ServicioDomesticoModel';
import { $App } from '@/App';
import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { eventsFormControl } from 'src/Core';

class FormServicioDomesticoView extends FormView {
	constructor(options = {}) {
		super({ ...options, region: { form: '#formulario_empresa' } });
	}

	/**
	 * @override
	 */
	get events() {
		return {
			'click #guardar_ficha': 'saveFormData',
			'click #cancel': 'cancel',
			'focusout #telefono, #digver': 'isNumber',
			'focusout #cedtra': 'validePk',
			'change #tipdoc': 'changeTipoDocumento',
			'click [data-toggle="address"]': 'openAddress',
			'click #btEnviarRadicado': 'enviarRadicado',
		};
	}

	/**
	 * @override
	 */
	render() {
		const result = FormView.prototype.render.call(this);
		this.__afterRender();
		return result;
	}

	__afterRender() {
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
			...ServicioDomesticoModel.Rules,
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

		_.each(this.selectores, (element) => new Choices(element));

		if (this.model.get('id') !== null) {
			_.each(this.model.toJSON(), (valor, key) => {
				if (!(_.isEmpty(valor) == true || _.isUndefined(valor) == true))
					this.$el.find(`[name="${key}"]`).val(valor);
			});

			this.selectores.trigger('change');
			this.form.valid();
			$('#cedtra').attr('disabled', 'true');
		}

		eventsFormControl(this.$el);

		flatpickr(this.$el.find('#fecnac, #fecini'), {
			enableTime: false,
			dateFormat: 'Y-m-d',
			locale: Spanish,
		});
		return this;
	}

	changeTipoDocumento(e) {
		let tipdoc = $(e.currentTarget).val();
		let coddocrepleg = ServicioDomesticoModel.changeTipdoc(tipdoc);
		this.$el.find('#coddocrepleg').val(coddocrepleg);
	}

	serializeData() {
		var data;
		if (this.model.entity instanceof ServicioDomesticoModel) {
			data = this.model.entity.toJSON();
		}
		return data;
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

		this.$el.find('#cedtra').removeAttr('disabled');

		const entity = this.serializeModel(new ServicioDomesticoModel());

		if (entity.isValid() !== true) {
			target.removeAttr('disabled');
			$App.trigger('alert:warning', { message: entity.validationError.join('<br/>') });
			setTimeout(() => $('label.error').text(''), 6000);
			return false;
		}

		entity.set('repleg', this.nameRepleg());
		this.$el.find('#repleg').val(entity.get('repleg'));

		this.trigger('form:save', {
			entity: entity,
			isNew: this.isNew,
			callback: (response) => {
				target.removeAttr('disabled');
				this.$el.find('#cedtra').attr('disabled', true);

				if (response) {
					if (response.success) {
						$App.trigger('alert:success', { message: response.msj });
						this.model.set(response.data);
						if (this.isNew == true) {
							$App.router.navigate('proceso/' + this.model.get('id'));
						} else {
							Backbone.history.loadUrl();
						}
					} else {
						$App.trigger('alert:error', { message: response.msj });
					}
				}
			},
		});
	}

	nameRepleg() {
		return (
			this.getInput('#priape') +
			' ' +
			this.getInput('#segape') +
			' ' +
			this.getInput('#prinom') +
			' ' +
			this.getInput('#segnom')
		);
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
		let cedtra = $(e.currentTarget).val();
		if (cedtra === '') {
			return false;
		}
		$App.trigger('form:find', {
			cedtra: cedtra,
			callback: (entity) => {
				this.actualizaForm();
			},
		});
	}

	/**
	 * @override
	 */
	remove() {
		FormView.prototype.remove.call(this, {});
	}
}

export { FormServicioDomesticoView };
