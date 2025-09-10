import { $Kumbia, Messages, Utils } from '@/Utils';
import { actualizar_select, aplicarFiltro, buscar } from '../Glob/Glob';

let validator = undefined;
let validator_campo = undefined;
let tipo_global = undefined;

$(function () {
	aplicarFiltro();
	validator = $('#form').validate({
		rules: {
			tipo: { required: true },
			detalle: { required: true },
		},
	});

	validator_campo = $('#form_campo').validate({
		rules: {
			campo_28: { required: true },
			detalle_28: { required: true },
			orden_28: { required: true },
		},
	});

	$(document).on('blur', '#tipo', function () {
		if ($('#tipo').val() == '') return;
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/validePk'),
			data: {
				tipo: $('#tipo').val(),
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == false) {
					Messages.display(response['msg'], 'warning');
					$('#tipo').val('');
					$('#tipo').trigger('focus');
				}
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});

	$(document).on('blur', '#campo_28', function () {
		if ($('#campo_28').val() == '') return;
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/validePkCampo'),
			data: {
				tipo: tipo_global,
				campo_28: $('#campo_28').val(),
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == false) {
					Messages.display(response['msg'], 'warning');
					$('#campo_28').val('');
					$('#campo_28').focus().select();
				}
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});

	$('#capture-modal').on('hide.bs.modal', function (e) {
		validator.resetForm();
		$('.select2-selection')
			.removeClass(validator.settings.errorClass)
			.removeClass(validator.settings.validClass);
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const tipo = $(e.currentTarget).attr('data-cid');
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/editar'),
			data: {
				tipo: tipo,
			},
		})
			.done(function (response) {
				$.each(response, function (key, value) {
					$('#' + key.toString()).val(value);
				});
				$('#tipo').attr('disabled', 'true');
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
			url: Utils.getKumbiaURL($Kumbia.controller + '/guardar'),
			data: $('#form').serialize(),
		})
			.done(function (response) {
				if (response['flag'] == true) {
					buscar();
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
		const tipo = $(e.currentTarget).attr('data-cid');
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
					url: Utils.getKumbiaURL($Kumbia.controller + '/borrar'),
					data: {
						tipo: tipo,
					},
				})
					.done(function (transport) {
						var response = transport;
						if (response['flag'] == true) {
							buscar();
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

	$(document).on('click', "[data-toggle='campo-view']", (e) => {
		e.preventDefault();
		const tipo = $(e.currentTarget).attr('data-cid');
		tipo_global = tipo;
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/campo_view'),
			data: {
				tipo: tipo,
			},
		})
			.done(function (response) {
				$('#result_campos').html(response);
				document.getElementById('btModalCapturarCampo').click();

				$('#form_campo :input').each(function (elem) {
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

	$(document).on('click', "[data-toggle='campo-guardar']", (e) => {
		e.preventDefault();
		if (!validator_campo.valid()) return;

		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/guardarCampo'),
			data: {
				tipo: tipo_global,
				campo: $('#campo_28').val(),
				detalle: $('#detalle_28').val(),
				orden: $('#orden_28').val(),
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					$.ajax({
						type: 'POST',
						url: Utils.getKumbiaURL($Kumbia.controller + '/campo_view'),
						data: {
							tipo: tipo_global,
						},
					})
						.done(function (response) {
							$('#result_campos').html(response);
							document.getElementById('btModalCapturarCampo').click();

							$('#form_campo :input').each(function (elem) {
								if (this.type !== 'button') {
									$(this).val('');
									$(this).removeAttr('disabled');
								}
							});
						})
						.fail(function (jqXHR, textStatus) {
							alert('Request failed: ' + textStatus);
						});
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='campo-borrar']", (e) => {
		e.preventDefault();
		const tipo = $(e.currentTarget).attr('data-tipo');
		const campo = $(e.currentTarget).attr('data-campo');
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/borrarCampo'),
			data: {
				tipo: tipo,
				campo: campo,
			},
		})
			.done(function (response) {
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					$.ajax({
						type: 'POST',
						url: Utils.getKumbiaURL($Kumbia.controller + '/campo_view'),
						data: {
							tipo: tipo_global,
						},
					})
						.done(function (response) {
							$('#result_campos').html(response);
							document.getElementById('btModalCapturarCampo').click();

							$('#form_campo :input').each(function (elem) {
								if (this.type !== 'button') {
									$(this).val('');
									$(this).removeAttr('disabled');
								}
							});
						})
						.fail(function (jqXHR, textStatus) {
							alert('Request failed: ' + textStatus);
						});
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='campo-editar']", (e) => {
		const tipo = $(e.currentTarget).attr('data-tipo');
		const campo = $(e.currentTarget).attr('data-campo');
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/editarCampo'),
			data: {
				tipo: tipo,
				campo: campo,
			},
		})
			.done(function (response) {
				$('#campo_28').val(response.campo);
				$('#detalle_28').val(response.detalle);
				$('#orden_28').val(response.orden);
				$('#campo_28').attr('disabled', 'true');
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});

	$(document).on('click', "[data-toggle='page-buscar']", (e) => {
		e.preventDefault();
		buscar($(e.currentTarget));
	});
});
