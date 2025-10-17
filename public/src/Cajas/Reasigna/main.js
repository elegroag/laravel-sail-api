import { $App } from '@/App';
import { Messages } from '@/Utils';
import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';

window.App = $App;

$(() =>{
	window.App.initialize();


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
		const tipopc = $("[name='tipopc']").val();
		const usuario = $("[name='usuario']").val();

		if (tipopc == '') return;
		if (usuario == '') return;
		
		window.App.trigger('ajax', {
			url: window.ServerController + '/traer_datos',
			data: {
				tipopc,
				usuario,
			},
			callback: function (response) {
				$('#consulta').html(response);
			},
			error: function (jqXHR, textStatus) {
				Messages.display('Request failed: ' + textStatus, 'error');
			}
		});
	});

	$(document).on('change', '[data-toggle="cambiar-main-accion"]', function(e){
		const target = $('#renderProceso');
		let html = '';
		if ($('#accion').val() == 'P') {
			const tpl = _.template($('#tmp_proceso').html()); 
			html = tpl({});
			target.html(html);
			$('#usuori').select2();
			$('#usudes').select2();	
			$('#tipopc_proceso').select2();
			flatpickr($('#fecini, #fecfin'), {
				enableTime: false,
				dateFormat: 'Y-m-d',
				locale: Spanish,
			});
		} else {
			const tpl = _.template($('#tmp_consulta').html()); 
			html = tpl({});
			target.html(html);
			$('#tipopc').select2();
			$('#usuario').select2();

		}
	});

});
