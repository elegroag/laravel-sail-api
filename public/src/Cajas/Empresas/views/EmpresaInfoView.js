import { FormInfoView } from '@/Cajas/FormInfoView';
import EmpresaAprobarModel from '../models/EmpresaAprobarModel';
import { $App } from '@/App';

export default class EmpresaInfoView extends FormInfoView {
	constructor(options = {}) {
		super({
			...options,
			Rules: EmpresaAprobarModel.Rules,
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
		};
	}

	afterRender() {
		this.__afterRender();
		this.model.set({
			codind: '03',
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
		this.$el.find('#codind, #tipsoc, #tipapo, .js-basic-multiple').select2();
		
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
				if (response.success === true) {
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
						timer: 10000,
						message: response.msj,
					});
				}
			},
		});
	}
}
