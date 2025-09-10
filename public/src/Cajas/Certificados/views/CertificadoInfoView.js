import { $App } from '@/App';
import { FormInfoView } from '@/Cajas/FormInfoView';
import CertificadoAprobarModel from '../models/CertificadoAprobarModel';

export default class CertificadoInfoView extends FormInfoView {
	constructor(options={}) {
		super({
			...options,
			Rules: CertificadoAprobarModel.Rules,
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

		this.form = this.$el.find('#formAprobar');
		this.form.validate(CertificadoAprobarModel.Rules);
		this.$el.find('.js-basic-multiple').select2();

		flatpickr(this.$el.find('#fecapr'), {
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
			setTimeout(() => $('label.error').text(''), 6000);
			return false;
		}

		_target.attr('disabled', true);
		const entity = this.serializeModel(this.model);

		this.trigger('load:aprobar', {
			data: entity.toJSON(),
			callback: (response) => {
				_target.removeAttr('disabled');
				if (response.success) {
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
					Swal.fire({
						title: 'Notificación',
						text: response.msj,
						icon: 'error',
						showConfirmButton: false,
						showCloseButton: true,
						timer: 10000,
					});
				}
			},
		});
	}

	devolverSolicitud() {
		const _target = this.$el.find(e.currentTarget);
		const _nota_devolver = this.getInput('#nota_devolver');
		const _codest_devolver = this.getInput('#codest_devolver');

		if (_nota_devolver == '') {
			Swal.fire({
				title: 'Notificación',
				text: 'El valor de la nota es requerido para hacer la devolución.',
				icon: 'error',
				showConfirmButton: false,
				showCloseButton: true,
				timer: 10000,
			});
			return;
		}

		if (_codest_devolver == '') {
			Swal.fire({
				title: 'Notificación',
				text: 'El estado es requerido para hacer la devolución',
				icon: 'error',
				showConfirmButton: false,
				showCloseButton: true,
				timer: 10000,
			});
			return;
		}

		_target.attr('disabled', true);

		const _token = {
			id: this.collection.solicitud.get('id'),
			nota: _nota_devolver,
			codest: _codest_devolver,
			campos_corregir: this.$el.find('#campos_corregir').val(),
		};

		this.trigger('load:devolver', {
			data: _token,
			callback: (response) => {
				_target.removeAttr('disabled');
				if (response.success) {
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
					Swal.fire({
						title: 'Notificación',
						text: response.msj,
						icon: 'error',
						showConfirmButton: false,
						showCloseButton: true,
						timer: 10000,
					});
				}
			},
		});
	}

	rechazarSolicitud() {
		const _target = this.$el.find(e.currentTarget);
		let _nota_rechazar = this.getInput('#nota_rechazar');
		let _codest_rechazar = this.getInput('#codest_rechazar');

		if (_nota_rechazar == '') {
			Swal.fire({
				title: 'Notificación',
				text: 'El valor de la nota es requerido para hacer la rechazar.',
				icon: 'error',
				showConfirmButton: false,
				showCloseButton: true,
				timer: 10000,
			});
			return;
		}

		if (_codest_rechazar == '') {
			Swal.fire({
				title: 'Notificación',
				text: 'El estado es requerido para hacer rechazo',
				icon: 'error',
				showConfirmButton: false,
				showCloseButton: true,
				timer: 10000,
			});
			return;
		}

		_target.attr('disabled', true);
		const _token = {
			id: this.collection.solicitud.get('id'),
			nota: _nota_rechazar,
			codest: $('#codest').val(),
		};

		this.trigger('load:rechazar', {
			data: _token,
			callback: (response) => {
				_target.removeAttr('disabled');
				if (response.success) {
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
					Swal.fire({
						title: 'Notificación',
						text: response.msj,
						icon: 'error',
						showConfirmButton: false,
						showCloseButton: true,
						timer: 10000,
					});
				}
			},
		});
	}
}

