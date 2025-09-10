import { FormInfoView } from '@/Cajas/FormInfoView';
import { BeneficiarioAprobarModel } from '../models/BeneficiarioAprobarModel';
import { $App } from '@/App';
import { $Kumbia, Utils } from '@/Utils';
import { is_numeric } from '@/Core';

class BeneficiarioInfoView extends FormInfoView {
	constructor(options = {}) {
		super({
			...options,
			titulo: 'Aprobar beneficiario',
			titulo_detalle: 'Lista beneficiarios',
			Rules: BeneficiarioAprobarModel.Rules,
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
		this.loadSubmenu();
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
			'click #procesarDeshacer': 'deshacerAfiliacion',
			"click [data-toggle='adjunto']": 'verArchivo',
			'click #reaprobar_solicitud': 'reaprobarSolicitud',
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
		this.$el.find('.js-basic-multiple, #codgir').select2();

		flatpickr(this.$el.find('#fecafi, #fecapr, #fecpre'), {
			enableTime: false,
			dateFormat: 'Y-m-d',
			locale: Spanish,
		});
	}

	loadSubmenu() {
		this.__loadSubmenu();
		this.listenTo(this.headerView, 'reaprobar', this.reaprobarSolicitud);
		this.listenTo(this.headerView, 'load:deshacer', this.deshacerAfiliacion);
	}

	aprobarSolicitud(e) {
		e.preventDefault();
		const _target = this.$el.find(e.currentTarget);
		let _numcue = this.$el.find('#numcue').val();
		switch (this.$el.find('#tippag').val()) {
			case 'A':
			case 'D':
				this.$el.find('#tipcue').rules('add', {
					required: true,
				});
				if (!is_numeric(_numcue)) {
					this.$el.find('#numcue').val('');
				}
				break;
			case 'C':
				this.$el.find('#codcue').rules('add', {
					required: true,
				});
				break;
		}

		if (!this.form.valid()) {
			$App.trigger('alert:warning', {
				message: 'Se requiere de resolver los campos requeridos para continuar.',
			});
			setTimeout(() => this.$el.find('label.error').text(''), 6000);
			return false;
		}

		const entity = this.serializeModel(this.model);
		entity.set('feccap', entity.get('fecafi'));
		entity.set('recsub', 'N');

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

					$App.trigger('alert:warning', {
						message: response.msj,
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

	reaprobarSolicitud(e) {
		e.preventDefault();
		let _target = $(e.currentTarget);
		_target.attr('disabled', 'true');
		let $err = 0;
		let _giro = $('#giro').val();
		let _codgir = $('#codgir').val();
		let _nota_aprobar = $('#nota_aprobar').val();
		if (_nota_aprobar == '') {
			$err++;
			Swal.fire({
				title: 'Notificación Alerta',
				text: 'Digíte la nota',
				icon: 'error',
				showConfirmButton: false,
				showCloseButton: true,
				timer: 10000,
			});
			_target.removeAttr('disabled');
			return false;
		}

		if (_codgir == '') {
			$('#giro-error').text('Debe tener valor de gíro requerido.');
			$('#giro-error').attr('style', 'display:inline-block');
			$err++;
		}
		if (_giro == '') {
			$('#codgir-error').text('Debe tener un código de gíro requerido.');
			$('#codgir-error').attr('style', 'display:inline-block');
			$err++;
		}
		if ($err > 0) {
			Swal.fire({
				title: 'Notificación Alerta',
				text: 'Algunos campos son requeridos para continuar',
				icon: 'error',
				showConfirmButton: false,
				showCloseButton: true,
				timer: 10000,
			});
			setTimeout(function () {
				$('label.error').text('');
				_target.removeAttr('disabled');
			}, 5000);
			return false;
		}

		let _token = {
			id: this.solicitudAprobar.get('id'),
			nota: _nota_aprobar,
			giro: $('#giro').val(),
			codgir: $('#codgir').val(),
		};
		$.ajax({
			method: 'POST',
			dataType: 'JSON',
			cache: false,
			url: Utils.getKumbiaURL($Kumbia.controller + '/reaprobar'),
			data: _token,
		})
			.done(function (response) {
				if (response.success) {
					Swal.fire({
						title: 'Notificación',
						text: response.msj,
						icon: 'success',
						showConfirmButton: false,
						showCloseButton: true,
						timer: 10000,
					});
					setTimeout(function () {
						window.location.href = Utils.getKumbiaURL($Kumbia.controller + '/index');
					}, 5000);
				} else {
					Swal.fire({
						title: 'Notificación Alerta',
						text: response.msj,
						icon: 'error',
						showConfirmButton: false,
						showCloseButton: true,
						timer: 10000,
					});
				}
				_target.removeAttr('disabled');
			})
			.fail(function (err) {
				Swal.fire({
					title: 'Notificación Error',
					text: err.responseText,
					icon: 'error',
					showConfirmButton: false,
					showCloseButton: true,
					timer: 10000,
				});
				_target.removeAttr('disabled');
			});
	}

	deshacerAfiliacion(event) {
		var target = $(event.currentTarget);
		target.attr('disabled', true);

		$App.trigger('confirma', {
			message: "Debes confirmar el proceso a ejecutar para poder continuar. Con el fin de evitar algun envío por error",
			callback: (status) => {
				if (status === true) {
					let _codest = $('[name="codest"]').val();
					let _nota = $('[name="nota"]').val();
					let _send_email = $('[name="send_email"]').val();
					let _action = $('[name="action"]').val();

					let err = 0;
					if (_codest == '') err++;
					if (_nota == '') err++;
					if (_send_email == '') err++;
					if (_action == '') err++;

					if (err > 0) {
						Swal.fire({
							title: 'Error validación datos',
							text: 'Todos los datos son requeridos',
							icon: 'warning',
							showConfirmButton: false,
							showCloseButton: true,
							timer: 10000,
						});
						return false;
					}

					let _formulario = $('form');
					let id = this.solicitudAprobar.get('id');
					let _data_array = _formulario.serializeArray();
					let _token = {};
					let $i = 0;
					while ($i < _.size(_data_array)) {
						_token[_data_array[$i].name] = _data_array[$i].value;
						$i++;
					}

					let _url = Utils.getKumbiaURL($Kumbia.controller + '/deshacerAprobado/' + id);

					$.ajax({
						method: 'POST',
						dataType: 'JSON',
						cache: false,
						url: _url,
						data: _token,
						beforeSend: function (xhr) {
							Swal.fire({
								html: "<p class='text-center' style='font-size:1.2rem'><i class='fa fa-spinner fa-spin fa-2x fa-fw'></i> Procesando solicitud...</p>",
								icon: false,
								showCloseButton: false,
								showConfirmButton: false,
								allowOutsideClick: false,
								allowEscapeKey: false,
							});
						},
					})
						.done(function (response) {
							target.removeAttr('disabled');
							Swal.close();
							if (response.success) {
								Swal.fire({
									title: 'Notificación OK',
									html:
										"<p class='text-left' style='font-size:1rem'>" + response.msj + '</p>',
									icon: 'success',
									showCloseButton: false,
									showConfirmButton: true,
									allowOutsideClick: false,
									allowEscapeKey: false,
									confirmButtonText: 'Continuar',
								}).then(function (e) {
									if (e.value === true) {
										setTimeout(function () {
											window.location.href = Utils.getKumbiaURL(
												$Kumbia.controller + '/index/A',
											);
										}, 100);
									}
								});
							} else {
								Swal.fire({
									title: 'Notificación Error',
									text: response.msj,
									icon: 'warning',
									showConfirmButton: false,
									showCloseButton: true,
									timer: 10000,
								});
								return false;
							}
						})
						.fail(function (err) {
							target.removeAttr('disabled');
							Swal.fire({
								title: 'Notificación Error',
								text: err.responseText,
								icon: 'warning',
								showConfirmButton: false,
								showCloseButton: true,
							});
						});
				} else {
					target.removeAttr('disabled');
				}
			}
		});
	}
}

export { BeneficiarioInfoView };
