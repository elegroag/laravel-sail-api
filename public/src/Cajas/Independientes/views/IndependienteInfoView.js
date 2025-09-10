import { $App } from '@/App';
import { FormInfoView } from '@/Cajas/FormInfoView';
import IndependienteAprobarModel from '../models/IndependienteAprobarModel';

export default class IndependienteInfoView extends FormInfoView {
	constructor(options = {}) {
		super({
			...options,
			Rules: IndependienteAprobarModel.Rules,
			solicitudAprobar: options.collection.solicitud,
			camposDisponibles: options.collection.campos_disponibles,
		});
	}

	render() {
		const template = _.template(this.template);
		this.$el.html(
			template({
				$scope: this.collection,
				model: this.model,
			}),
		);
		this.afterRender();
		return this;
	}

	get events() {
		return {
			"click [data-toggle='event-cuenta']": 'cambioCuenta',
			"click [data-toggle='info']": 'infoDetalle',
			'click #aprobar_solicitud': 'aprobarSolicitud',
			'click #devolver_solicitud': 'devolverSolicitud',
			'click #rechazar_solicitud': 'rechazarSolicitud',
			"click [data-toggle='adjunto']": 'verArchivo',
			'change #tippag': 'valTippag',
		};
	}

	afterRender() {
		this.__afterRender();
		this.model.set({
			codind: null,
			todmes: 'S',
			forpre: 'S',
			tipemp: 'P',
			ofiafi: '01',
			colegio: 'N',
			contratista: 'N',
			tipdur: 'S',
			pymes: 'N',
			tipapo: 'I',
			tipsoc: '08',
		});
		this.actualizaForm();
		this.$el.find('.js-basic-multiple, #codind, #codban, #codgir').select2();

		flatpickr(this.$el.find('#fecafi, #fecapr'), {
			enableTime: false,
			dateFormat: 'Y-m-d',
			locale: Spanish,
		});

		if (this.model.get('tippag') == 'T') {
			this.$el.find('#codban').prop('disabled', true);
			this.$el.find('#numcue').prop('disabled', true);
			this.$el.find('#tipcue').prop('disabled', true);
			this.$el.find('#numcue').prop('disabled', true);
		}
	}

	aprobarSolicitud(e) {
		e.preventDefault();
		const _target = this.$el.find(e.currentTarget);

		if (!this.form.valid()) {
			$App.trigger('alert:warning', {
				message: 'Se requiere de resolver los campos requeridos para continuar.',
			});
			setTimeout(() => $('label.error').text(''), 6000);
			return false;
		}

		const entity = this.serializeModel(this.model);
		entity.set('feccap', entity.get('fecafi'));

		if (!entity.isValid()) {
			$App.trigger('alert:warning', { message: entity.validationError.join(' ') });
			setTimeout(() => this.$('label.error').text(''), 6000);
			return false;
		}

		_target.attr('disabled', true);

		this.trigger('load:aprobar', {
			data: entity.toJSON(),
			callback: (response) => {
				_target.removeAttr('disabled');
				if (response && response.success === true) {
					$App.trigger('confirma', {
						message: response.msj,
						callback: (status) => {
							if (status) {
								this.remove();
								$App.router.navigate('list', { trigger: true, replace: true });
							}
						},
					});
				} else {
					if (response.info && response.info.errors) {
						$.each(response.info.errors, (key, item) => {
							if (_.isArray(item) == true) {
								$.each(item, (key2, item2) => {
									$App.trigger('noty:error', item2);
								});
							} else {
								$App.trigger('noty:error', item);
							}
						});
					}

					$App.trigger('alert:error', {
						title: 'Notificación',
						message: response.msj,
						timer: 10000,
					});
				}
			},
		});
	}

	valTippag(e) {
		e.preventDefault();
		let tippag = this.$el.find(e.currentTarget).val();
		if (tippag == '') return;

		this.$el.find('#numcue').prop('disabled', false);
		this.$el.find('#tipcue').prop('disabled', false);
		this.$el.find('#numcue').attr('placeholder', '');

		switch (tippag) {
			case 'B':
				this.$el.find('#numcue').prop('disabled', true);
				this.$el.find('#tipcue').prop('disabled', true);

				this.$el.find('#numcue').val('');
				this.$el.find('#tipcue').val('');

				this.$el.find('#codban').rules('add', { required: false });
				this.$el.find('#codban').prop('disabled', true);
				break;
			case 'E':
				this.$el.find('#numcue').prop('disabled', true);
				this.$el.find('#tipcue').prop('disabled', true);

				this.$el.find('#numcue').val('');
				this.$el.find('#tipcue').val('');

				this.$el.find('#codban').rules('add', { required: false });
				$('#codban').prop('disabled', true);
				break;
			case 'T':
				this.$el.find('#numcue').prop('disabled', true);
				this.$el.find('#tipcue').prop('disabled', true);

				this.$el.find('#numcue').val('');
				this.$el.find('#tipcue').val('');

				this.$el.find('#codban').rules('add', { required: false });
				this.$el.find('#codban').prop('disabled', true);

				break;
			case 'A':
				this.$el.find('#codban').removeAttr('disabled');
				this.$el.find('#codban').rules('add', { required: true });
				break;
			case 'D':
				this.$el.find('#numcue').removeAttr('disabled');
				this.$el.find('#codban').val('51');
				this.$el.find('#tipcue').val('A');
				this.$el.find('#numcue').attr('placeholder', 'Número teléfono certificado');
				this.$el.find('#numcue').rules('add', { required: true });
				this.$el.find('#codban').rules('add', { required: true });
				break;
		}
	}
}
