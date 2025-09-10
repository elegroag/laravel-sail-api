import { RequestListView } from '@/Cajas/RequestListView';
import { $Kumbia, Utils } from '@/Utils';

export default class DatosTrabajadoresView extends RequestListView {
	constructor(options) {
		super({
			...options,
			titulo: 'Datos trabajadores',
			titulo_detalle: 'Aprobar datos trabajador - ',
		});
	}

	get events() {
		return {
			"click [data-toggle='event-cuenta']": 'cambioCuenta',
			"click [data-toggle='info']": 'infoDetalle',
			"click [toggle-event='buscar']": 'buscarPagina',
			"change [toggle-event='change']": 'changeCantidad',
			'click #btPendienteEmail': 'irPendienteEmail',
			'click #btenviar': 'sendMail',
		};
	}

	render() {
		const template = _.template(document.getElementById('tmp_table').innerHTML);
		this.$el.html(template());
		this.__beforeRender();
		this.__loadSubmenu();
		return this;
	}

	deshacerAfiliacion(event) {
		var target = $(event.currentTarget);
		target.attr('disabled', 'true');
		Swal.fire({
			title: 'Confirmar',
			html: "<p class='text-left' style='font-size:1rem'>Debes confirmar el proceso a ejecutar para poder continuar. Con el fin de evitar algun envío por error.</p>",
			icon: 'success',
			showCloseButton: true,
			showCancelButton: true,
			focusConfirm: false,
			confirmButtonText: 'Continuar',
			confirmButtonAriaLabel: 'Thumbs up, great!',
			cancelButtonText: 'Cancelar',
			cancelButtonAriaLabel: 'Thumbs down',
		}).then(function (e) {
			if (e.isConfirm === true) {
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
				let id = '<?=$id?>';
				let _data_array = _formulario.serializeArray();
				let _token = {};
				let $i = 0;
				while ($i < _.size(_data_array)) {
					_token[_data_array[$i].name] = _data_array[$i].value;
					$i++;
				}

				let _url = Utils.getKumbiaURL(
					$Kumbia.controller + '/deshacerAprobado/' + '<?= $idModel ?>',
				);

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
		});
	}

	sendMail(event) {
		event.preventDefault();
		let nerr = 0;
		let _cedtra = $('#cedtra').val();
		if (_cedtra == '') {
			nerr++;
			document.querySelector('.error_cedtra').innerHTML =
				'<span>El campo cedula es un valor requerido.</span>';
		} else {
			let express = /^([0-9]){8,13}$/;
			if (!express.test(_cedtra.toString())) {
				nerr++;
				document.querySelector('.error_cedtra').innerHTML =
					'<span>La cedula no es un valor valido para continuar.</span>';
			}
		}
		return nerr == 0 ? $('#form_pendiente').submit() : false;
	}
}
