import { $Kumbia, Messages, Utils } from '@/Utils';
import { actualizar_select, aplicarFiltro, buscar, validePk } from '../Glob/Glob';

let validator = undefined;
let codofi_global = undefined;

$(function () {
	validator = $('#form').validate({
		rules: {
			codofi: { required: true },
			detalle: { required: true },
			principal: { required: true },
			estado: { required: true },
		},
	});

	$(document).on('blur', '#codofi', function () {
		validePk('#codofi');
	});

	$('#capture-modal').on('hide.bs.modal', function (e) {
		if (validator !== undefined) {
			validator.resetForm();
			$('.select2-selection')
				.removeClass(validator.settings.errorClass)
				.removeClass(validator.settings.validClass);
		}
	});

	aplicarFiltro();

	$(document).on('click', "[data-toggle='page-buscar']", (e) => {
		e.preventDefault();
		buscar($(e.currentTarget));
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const codofi = $(e.currentTarget).attr('data-cid');
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('mercurio04/editar'),
			data: {
				codofi: codofi,
			},
		})
			.done(function (transport) {
				var response = transport;
				$.each(response, function (key, value) {
					$('#' + key.toString()).val(value);
				});
				$('#codofi').attr('disabled', 'true');
				document.getElementById('btCaptureModal').click();
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});

	$(document).on('click', "[data-toggle='guardar']", (e) => {
		e.preventDefault();
		if (!$('#form').valid()) return;

		$('#form :input').each(function (elem) {
			$(this).removeAttr('disabled');
		});
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('mercurio04/guardar'),
			data: $('#form').serialize(),
		})
			.done(function (response) {
				if (response['flag'] == true) {
					document.getElementById('btnBuscar').click();
					Messages.display(response['msg'], 'success');
					$('#capture-modal').modal('hide');
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});

	$(document).on('click', "[data-toggle='borrar']", (e) => {
		e.preventDefault();
		const codofi = $(e.currentTarget).attr('data-cid');
		Swal.fire({
			title: 'Esta seguro de borrar?',
			text: '',
			type: 'warning',
			showCancelButton: true,
			confirmButtonClass: 'btn btn-success btn-fill',
			cancelButtonClass: 'btn btn-danger btn-fill',
			confirmButtonText: 'SI',
			cancelButtonText: 'NO',
		}).then((result) => {
			if (result.value) {
				$.ajax({
					type: 'POST',
					url: Utils.getKumbiaURL('mercurio04/borrar'),
					data: {
						codofi: codofi,
					},
				})
					.done(function (transport) {
						var response = transport;
						if (response['flag'] == true) {
							document.getElementById('btnBuscar').click();
							Messages.display(response['msg'], 'success');
						} else {
							Messages.display(response['msg'], 'error');
						}
					})
					.fail(function (jqXHR, textStatus) {
						Messages.display(jqXHR.statusText, 'error');
					});
			}
		});
	});

	$(document).on('click', "[data-toggle='nuevo']", (e) => {
		e.preventDefault();
		$('#form :input').each(function (elem) {
			$(this).val('');
			$(this).removeAttr('disabled');
		});
		actualizar_select();
		document.getElementById('btCaptureModal').click();
	});

	$(document).on('click', "[data-toggle='reporte']", (e) => {
		e.preventDefault();
		const tipo = $(e.currentTarget).attr('data-type');
		window.location.href = Utils.getKumbiaURL($Kumbia.controller + '/reporte/' + tipo);
	});

	$(document).on('click', "[data-toggle='filtrar']", (e) => {
		e.preventDefault();
		const Modal = new bootstrap.Modal(document.getElementById('filtrar-modal'), {});
		Modal.show();
	});

	$(document).on('click', "[data-toggle='opcion-view']", (e) => {
		e.preventDefault();
		const codofi = $(e.currentTarget).attr('data-cid');
		codofi_global = codofi;
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('mercurio04/opcion_view'),
			data: {
				codofi: codofi,
			},
		})
			.done(function (response) {
				$('#result_opcion').html(response);
				document.getElementById('btModalCapturarOpciones').click();

				$('#form_opcion :input').each(function (elem) {
					if (this.type != 'button') {
						$(this).val('');
						$(this).attr('disabled', false);
					}
				});
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='ciudad-view']", (e) => {
		e.preventDefault();
		const codofi = $(e.currentTarget).attr('data-cid');
		codofi_global = codofi;
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('mercurio04/ciudad_view'),
			data: {
				codofi: codofi,
			},
		})
			.done(function (response) {
				$('#result_ciudad').html(response);
				document.getElementById('btModalCapturarCiudades').click();
				$('#form_ciudad :input').each(function (elem) {
					if (this.type !== 'button') {
						$(this).val('');
						$(this).removeAttr('disabled');
					}
				});
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='opcion-guardar']", (e) => {
		e.preventDefault();
		if (!$('#form_opcion').valid()) return;

		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('mercurio04/guardarOpcion'),
			data: {
				codofi: codofi_global,
				tipopc: $('#tipopc_08').val(),
				usuario: $('#usuario_08').val(),
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					opcion_view(codofi_global);
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='opcion-borrar']", (e) => {
		e.preventDefault();
		const codofi = $(e.currentTarget).attr('data-codofi');
		const tipopc = $(e.currentTarget).attr('data-tipopc');
		const usuario = $(e.currentTarget).attr('data-usuario');

		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('mercurio04/borrarOpcion'),
			data: {
				codofi: codofi,
				tipopc: tipopc,
				usuario: usuario,
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					$('#ModalCapturarOpciones').modal('hide');
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='ciudad-borrar']", (e) => {
		e.preventDefault();
		const codofi = $(e.currentTarget).attr('data-codofi');
		const codciu = $(e.currentTarget).attr('data-codciu');
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('mercurio04/borrarCiudad'),
			data: {
				codofi: codofi,
				codciu: codciu,
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					$('#ModalCapturarCiudades').modal('hide');
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='ciudad-guardar']", (e) => {
		e.preventDefault();
		if (!$('#form_ciudad').valid()) return;
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('mercurio04/guardarCiudad'),
			data: {
				codofi: codofi_global,
				codciu: $('#codciu_05').val(),
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('blur', '#tipopc_08, #usuario_08', function () {
		if ($('#tipopc_08').val() == '') return;
		if ($('#usuario_08').val() == '') return;
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('mercurio04/validePkOpcion'),
			data: {
				codofi: codofi_global,
				tipopc: $('#tipopc_08').val(),
				usuario: $('#usuario_08').val(),
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == false) {
					Messages.display(response['msg'], 'warning');
					$('#usuario_08').val('');
					actualizar_select();
				}
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});

	$(document).on('blur', '#codciu_05', function () {
		if ($('#codciu_05').val() == '') return;
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('mercurio04/validePkCiudad'),
			data: {
				codofi: codofi_global,
				codciu: $('#codciu_05').val(),
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == false) {
					Messages.display(response['msg'], 'warning');
					$('#codciu_05').val('');
					$('#codciu_05').focus().select();
				}
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});
});
