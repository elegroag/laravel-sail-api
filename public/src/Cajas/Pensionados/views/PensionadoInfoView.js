import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { $App } from '@/App';
import { FormInfoView } from '@/Cajas/FormInfoView';
import { PensionadoAprobarModel } from '../models/PensionadoAprobarModel';
import { ValidaTipoPago } from '@/Cajas/ValidaTipoPago';

class PensionadoInfoView extends FormInfoView {
	constructor(options = {}) {
		super({
			...options,
			titulo: 'Aprobar pensionado',
			titulo_detalle: 'Lista pensionados',
			Rules: PensionadoAprobarModel.Rules,
			solicitudAprobar: options.collection.solicitud,
			camposDisponibles: options.collection.campos_disponibles,
		});
		this.selectores = undefined;
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
			'change #tippag': 'validaTipoPago',
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
			tipapo: 'P',
		});
		this.actualizaForm();
		this.selectores = this.$el.find('.js-basic-multiple, #codind, #tipapo, #codban, #codgir');
		this.selectores.select2({
			placeholder: 'Seleccione',
			allowClear: true,
			zIndex: 9999,
		});

		if (this.model.get('tippag') == 'T') {
			this.$el.find('#codban').prop('disabled', true);
			this.$el.find('#numcue').prop('disabled', true);
			this.$el.find('#tipcue').prop('disabled', true);
			this.$el.find('#numcue').prop('disabled', true);
		}

		flatpickr(this.$el.find('#fecafi, #fecapr'), {
			enableTime: false,
			dateFormat: 'Y-m-d',
			locale: Spanish,
		});
	}

	aprobarSolicitud(e) {
		e.preventDefault();
		const _target = this.$el.find(e.currentTarget);

		if (!this.form.valid()) {
			$App.trigger('alert:warning', {
				message: 'Se requiere de resolver los campos requeridos para continuar.',
			});
			setTimeout(() => this.$('label.error').text(''), 6000);
			return false;
		}

		const entity = this.serializeModel(this.model);
		entity.set('feccap', entity.get('fecafi'));
		entity.set('tipsoc', '06');

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
						message: response.msj,
						timer: 10000,
						title: 'Notificación error',
					});
				}
			},
		});
	}

	validaTipoPago(e) {
		e.stopPropagation();
		let tippag = this.$el.find(e.currentTarget).val();
		let el = this.$el;
		ValidaTipoPago({ tippag, el});
		this.selectores.trigger('change');
	}
}

export { PensionadoInfoView };
