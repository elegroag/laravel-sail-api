import { FormInfoView } from '@/Cajas/FormInfoView';
import { $App } from '@/App';
import { is_numeric } from '@/Core';
import TrabajadorAprobarModel from '../models/TrabajadorAprobarModel';

export default class TrabajadorInfoView extends FormInfoView {
	constructor(options = {}) {
		super({
			...options,
			Rules: TrabajadorAprobarModel.Rules,
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
			'change #tippag': 'valTippag',
			"click [data-toggle='adjunto']": 'verArchivo',
		};
	}

	afterRender() {
		this.__afterRender();
		this.model.set({
			vendedor: 'N',
			empleador: 'N',
			tippag: 'T',
			giro: 'N',
			codsuc: '001',
			codlis: '001',
		});

		this.actualizaForm();
		this.$el.find('.js-basic-multiple, #codind, #tipsoc, #tipapo, #codban, #codgir').select2();

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
		let $err = 0;
		const _numcue = this.$el.find('numcue').val();

		switch (this.$el.find('#tippag').val()) {
			case 'A':
			case 'D':
				this.$el.find('#tipcue').rules('add', { required: true });
				this.$el.find('#banco').rules('add', { required: true });
				if (is_numeric(_numcue)) {
					this.$el
						.find('#numcue-error')
						.text('Debe tener formato de número de cuenta correcto.');
					this.$el.find('#numcue-error').attr('style', 'display:inline-block');
					$err++;
				}
				break;
			case 'C':
				this.$el.find('#codban').rules('add', { required: false });
				break;
		}

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

		$App.trigger('syncro', {
			url: 'validarMultiafiliacion',
			data: {
				id: this.solicitudAprobar.get('id'),
			},
			callback: (response) => {
				if (response) {
					if (response.multi == true) {
						$App.trigger('confirma', {
							message:
								'El trabajador esta afiliado con otra empresa, desea registrar la actual ?',
							callback: (status) => {
								if (status) {
									this.__aprobar(_target, entity);
								}
							},
						});
					} else {
						this.__aprobar(_target, entity);
					}
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
				this.$el.find('#codban').prop('disabled', true);
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
