import { $Kumbia, Messages, Utils } from '@/Utils';
import { actualizar_select, aplicarFiltro, buscar, validePk } from '../Glob/Glob';

$(() => {
	aplicarFiltro();
	const validator = $('#form').validate({
		rules: {
			codfir: { required: true },
			nombre: { required: true },
			cargo: { required: true },
			archivo: { required: true },
			email: { required: true },
		},
	});

	$(document).on('blur', '#codfir', (e) => {
		validePk('#codfir');
	});

	$('#capture-modal').on('hide.bs.modal', function (e) {
		if (validator !== undefined) {
			validator.resetForm();
			$('.select2-selection')
				.removeClass(validator.settings.errorClass)
				.removeClass(validator.settings.validClass);
		}
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const codfir = $(e.currentTarget).attr('data-cid');
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/editar'),
			data: {
				codfir: codfir,
			},
		})
			.done(function (transport) {
				var response = transport;
				$.each(response, function (key, value) {
					if (key != 'archivo') $('#' + key.toString()).val(value);
				});
				$('#codfir').attr('disabled', 'true');
				document.getElementById('btCaptureModal').click();
				setTimeout(() => {
					$('#codfir').trigger('focus');
				}, 500);
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});

	$(document).on('click', "[data-toggle='guardar']", (e) => {
		e.preventDefault();
		if (!$('#form').valid()) return;
		var archivo = $('#archivo').val();
		if (archivo == '') {
			Messages.display('Adjunte el Archivo', 'error');
			return;
		}
		$('#form :input').each(function (elem) {
			$(this).removeAttr('disabled');
		});

		$('#archivo').upload(
			Utils.getKumbiaURL($Kumbia.controller + '/guardar'),
			{
				codfir: $('#codfir').val(),
				nombre: $('#nombre').val(),
				cargo: $('#cargo').val(),
				email: $('#email').val(),
			},
			function (response) {
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					buscar();
					$('#capture-modal').modal('hide');
				} else {
					Messages.display(response['msg'], 'error');
				}
			},
		);
	});

	$(document).on('click', "[data-toggle='borrar']", (e) => {
		e.preventDefault();
		const codfir = $(e.currentTarget).attr('data-cid');
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
			if (result.isConfirmed) {
				$.ajax({
					type: 'POST',
					url: Utils.getKumbiaURL($Kumbia.controller + '/borrar'),
					data: {
						codfir: codfir,
					},
				})
					.done(function (response) {
						if (response.flag == true) {
							buscar();
							Messages.display(response.msg, 'success');
						} else {
							Messages.display(response.msg, 'error');
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
});
