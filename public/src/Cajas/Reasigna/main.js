import { $App } from '@/App';
import { Messages } from '@/Utils';
import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';

window.App = $App;

$(() =>{
	window.App.initialize();
	const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));


	$(document).on('click', '#btnProcesoReasignarMasivo', function (e) {
		e.preventDefault();
		const tipopc = $('#tipopc_proceso').val();
		const usuori = $('#usuori_proceso').val();
		const usudes = $('#usudes_proceso').val();
		const fecini = $('#fecini').val();
		const fecfin = $('#fecfin').val();

		if (tipopc == '' || usuori == '' || usudes == '' || fecini == '' || fecfin == '') return;

		window.App.trigger('syncro', {
			url: window.App.url(window.ServerController + '/proceso_reasignar_masivo'),
			data: {
				tipopc,
				usuori,
				usudes,
				fecini,
				fecfin
			},
			callback: (response) => {
				if (response && response.success === true) {
					Messages.display(response.msj, 'success');
					window.location.reload();
				} else {
					Messages.display(response.msj, 'error');
				}
			},
			error: (jqXHR, textStatus) => {
				Messages.display('Request failed: ' + textStatus, 'error');
			}
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

	$(document).on('click', '[data-toggle="info"]', function (e) {
		e.preventDefault();
		const id = $(this).data('id');
		const tipopc = $(this).data('tipopc');
		window.App.trigger('ajax', {
			url: window.ServerController + '/infor',
			data: {
				id,
				tipopc,
			},
			callback: function (response) {
				if(response && response.success === true) {
					modalCapture.show();
					$('#captureModal').find('.modal-body').html(response.html);
				}
			},
			error: function (jqXHR, textStatus) {
				Messages.display('Request failed: ' + textStatus, 'error');
			}
		});
	});

	$(document).on('click', '[data-toggle="cambiar-usuario"]', function (e) {
		e.preventDefault();
		const id = $(this).data('id');
		const tipopc = $(this).data('tipopc');
		const usuario = $('#usuario_rea').val();

		window.App.trigger('ajax', {
			url: window.ServerController + '/cambiar_usuario',
			data: {
				id,
				tipopc,
				usuario
			},
			callback: function (response) {
				if(response && response.success === true) {
					Messages.display(response.msj, 'success');
					modalCapture.hide();
					$('#btnTraerDatos').trigger('click');
				}
			},
			error: function (jqXHR, textStatus) {
				Messages.display('Request failed: ' + textStatus, 'error');
			}
		});
	});

});
