import { Utils, $Kumbia, Messages } from '@/Utils';

import { actualizar_select, aplicarFiltro, buscar } from '../Glob/Glob';

$(document).ready(function () {
	validator = $('#form').validate({
		rules: {
			codest: { required: true },
			detalle: { required: true },
		},
	});

	$('#codest').blur(function () {
		validePk();
	});

	$('#capture-modal').on('hide.bs.modal', function (e) {
		validator.resetForm();
		$('.select2-selection')
			.removeClass(validator.settings.errorClass)
			.removeClass(validator.settings.validClass);
	});
	aplicarFiltro();
});

function reporte(codest) {
	window.location.href = Utils.getKumbiaURL($Kumbia.controller + '/reporte/' + codest);
}

function validePk() {
	if ($('#codest').val() == '') return;
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/validePk'),
		data: {
			codest: $('#codest').val(),
		},
	})
		.done(function (transport) {
			var response = transport;
			if (response['flag'] == false) {
				Messages.display(response['msg'], 'warning');
				$('#codest').val('');
				$('#codest').focus().select();
			}
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display(jqXHR.statusText, 'error');
		});
}
function nuevo() {
	$('#form :input').each(function (elem) {
		$(this).val('');
		$(this).attr('disabled', false);
	});
	actualizar_select();
	$('#capture-modal').modal();
	setTimeout('focus_nuevo()', 500);
}

function focus_nuevo() {
	$('#codest').focus().select();
}

function focus_editar() {
	$('#nombre').focus().select();
}

function borrar(codest) {
	swal
		.fire({
			title: 'Esta seguro de borrar?',
			text: '',
			type: 'warning',
			showCancelButton: true,
			confirmButtonClass: 'btn btn-success btn-fill',
			cancelButtonClass: 'btn btn-danger btn-fill',
			confirmButtonText: 'SI',
			cancelButtonText: 'NO',
		})
		.then((result) => {
			if (result.value) {
				$.ajax({
					type: 'POST',
					url: Utils.getKumbiaURL($Kumbia.controller + '/borrar'),
					data: {
						codest: codest,
					},
				});
				request.done(function (transport) {
					var response = transport;
					if (response['flag'] == true) {
						buscar();
						Messages.display(response['msg'], 'success');
					} else {
						Messages.display(response['msg'], 'error');
					}
				});
				request.fail(function (jqXHR, textStatus) {
					Messages.display(jqXHR.statusText, 'error');
				});
			}
		});
}

function editar(codest) {
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/editar'),
		data: {
			codest: codest,
		},
	})
		.done(function (transport) {
			var response = transport;
			$.each(response, function (key, value) {
				$('#' + key).val(value);
			});
			$('#codest').attr('disabled', true);
			$('#capture-modal').modal();
			setTimeout('focus_editar()', 500);
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display(jqXHR.statusText, 'error');
		});
}

function guardar() {
	if (!$('#form').valid()) {
		return;
	}
	$('#form :input').each(function (elem) {
		$(this).attr('disabled', false);
	});
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/guardar'),
		data: $('#form').serialize(),
	})
		.done(function (transport) {
			var response = transport;
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
}
