import { Utils, $Kumbia, Messages } from '@/Utils';

function proceso_reasignar_masivo() {
	if ($('#tipopc_proceso').val() == '') return;
	if ($('#usuori').val() == '') return;
	if ($('#usudes').val() == '') return;
	if ($('#fecini').val() == '') return;
	if ($('#fecfin').val() == '') return;
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/proceso_reasignar_masivo'),
		data: {
			tipopc_proceso: $('#tipopc_proceso').val(),
			usuori: $('#usuori').val(),
			usudes: $('#usudes').val(),
			fecini: $('#fecini').val(),
			fecfin: $('#fecfin').val(),
		},
	})
		.done(function (transport) {
			var response = transport;
			response.flag
				? Messages.display(response['msg'], 'success')
				: Messages.display(response['msg'], 'error');
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display(response['msg'], 'error');
		});
}

function traerDatos() {
	if ($('#tipopc').val() == '') return;
	if ($('#usuario').val() == '') return;
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/traerDatos'),
		data: {
			tipopc: $('#tipopc').val(),
			usuario: $('#usuario').val(),
		},
	})
		.done(function (transport) {
			var response = transport;
			$('#consulta').html(response);
		})
		.fail(function (jqXHR, textStatus) {
			alert('Request failed: ' + textStatus);
		});
}

function info(tipopc, id) {
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/info'),
		data: {
			tipopc: tipopc,
			id: id,
		},
	})
		.done(function (transport) {
			var response = transport;
			$('#result_info').html(response);
			$('#capture-modal-info').modal();
		})
		.fail(function (jqXHR, textStatus) {
			alert('Request failed: ' + textStatus);
		});
}

function cambiar_usuario(tipopc, id) {
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/cambiar_usuario'),
		data: {
			tipopc: tipopc,
			id: id,
			usuario: $('#usuario_rea').val(),
		},
	})
		.done(function (transport) {
			var response = transport;
			if (response.flag == true) {
				$('#capture-modal-info').modal('hide');
				Messages.display(response['msg'], 'success');
				traerDatos();
				$('#capture-modal-info').modal('hide');
			} else {
				Messages.display(response['msg'], 'error');
			}
		})
		.fail(function (jqXHR, textStatus) {
			alert('Request failed: ' + textStatus);
		});
}

function cambiarAccion() {
	const accion = $('#accion').val();
	if (accion == 'P') {
		$('#consultar_form').hide();
		$('#procesar_form').show();
	} else {
		$('#procesar_form').hide();
		$('#consultar_form').show();
	}
}

$(() => {
	$('#consultar_form').hide();
	$('#procesar_form').hide();

	$('#capture-modal').on('hide.bs.modal', function (e) {
		validator.resetForm();
		$('.select2-selection')
			.removeClass(validator.settings.errorClass)
			.removeClass(validator.settings.validClass);
	});
});
