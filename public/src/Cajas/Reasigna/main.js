import { $App } from '@/App';
import { Messages } from '@/Utils';

window.App = $App;

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
		.done(function (response) {
			$('#consulta').html(response);
		})
		.fail(function (jqXHR, textStatus) {
			_alert('Request failed: ' + textStatus);
		});
}

function info(tipopc, id) {
	$.ajax({
		type: ' POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/info'),
		data: {
			tipopc: tipopc,
			id: id,
		},
	})
		.done(function (response) {
			$('#result_info').html(response);
			$('#capture-modal-info').modal();
		})
		.fail(function (jqXHR, textStatus) {
			_alert('Request failed: ' + textStatus);
		});
}

function cambiar_usuario(tipopc, id) {
	$.ajax({
		type: ' POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/cambiar_usuario'),
		data: {
			tipopc: tipopc,
			id: id,
			usuario: $('#usuario_rea').val(),
		},
	})
		.done(function (response) {
			if (response && response.flag == true) {
				$('#capture-modal-info').modal('hide');
				_alert('success', {
					message: response['msg'],
				});
				traerDatos();
				$('#capture-modal-info').modal('hide');
			} else {
				_alert('error', {
					message: response['msg'],
				});
			}
		})
		.fail(function (jqXHR, textStatus) {
			_alert('Request failed: ' + textStatus);
		});
}

function cambiarAccion() {
	const accion = $(' #accion').val();
	if (accion == 'P') {
		$('#consultar_form').hide();
		$('#procesar_form').show();
	} else {
		$('#procesar_form').hide();
		$('#consultar_form').show();
	}
}

$(() =>{
	window.App.initialize();

	$('#consultar_form').hide();
	$('#procesar_form').hide();
	
	$('#capture-modal').on('hide.bs.modal', function (e) {
		validator.resetForm();
		$('.select2-selection')
			.removeClass(validator.settings.errorClass)
			.removeClass(validator.settings.validClass);
	});

	$(document).on('click', '#btnProcesoReasignarMasivo', function (e) {
		e.preventDefault();
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
			.done(function (response) {
				if (response && response.success === true) {
					Messages.display(response.msj, 'success');
				} else {
					Messages.display(response.msj, 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display('Request failed: ' + textStatus, 'error');
			});
	});

	$(document).on('click', '#btnTraerDatos', function (e) {
		e.preventDefault();
		traerDatos();
	});
});
