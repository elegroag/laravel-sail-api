import { Utils, $Kumbia, Messages } from '@/Utils';
import {
	actualizar_select,
	aplicarFiltro,
	buscar,
	changeCantidadPagina,
	validePk,
} from '../Glob/Glob';

var validator;

function reporte(coddoc) {
	window.location.href = Utils.getKumbiaURL($Kumbia.controller + '/reporte/' + coddoc);
}

$(function () {
	aplicarFiltro();

	validator = $('#form').validate({
		rules: {
			coddoc: { required: true },
			detalle: { required: true },
		},
	});

	$(document).on('blur', '#coddoc', function () {
		validePk('#coddoc');
	});

	$('#capture-modal').on('hide.bs.modal', function (e) {
		validator.resetForm();
		$('.select2-selection')
			.removeClass(validator.settings.errorClass)
			.removeClass(validator.settings.validClass);
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const coddoc = $(e.currentTarget).attr('data-cid');
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/editar'),
			data: {
				coddoc: coddoc,
			},
		})
			.done(function (transport) {
				var response = transport;
				$.each(response, function (key, value) {
					$('#' + key.toString()).val(value);
				});

				$('#coddoc').attr('disabled', 'true');
				document.getElementById('btCaptureModal').click();
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});

	$(document).on('click', "[data-toggle='guardar']", (e) => {
		e.preventDefault();
		if (!validator.valid()) return;

		$('#form :input').each(function (elem) {
			$(this).removeAttr('disabled');
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
	});

	$(document).on('click', "[data-toggle='borrar']", (e) => {
		e.preventDefault();
		const coddoc = $(e.currentTarget).attr('data-cid');
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
						coddoc: coddoc,
					},
				})
					.done(function (response) {
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

	$(document).on('click', "[data-toggle='page-buscar']", (e) => {
		e.preventDefault();
		buscar($(e.currentTarget));
	});

	$(document).on('click', "[toggle-event='aplicar_filtro']", (e) => aplicarFiltro(e));

	$(document).on('click', "[toggle-event='add_filtro']", (e) => addFiltro(e));

	$(document).on('click', "[toggle-event='remove']", (e) =>
		delFiltro($(e.currentTarget)),
	);

	$(document).on('change', '#cantidad_paginate', (e) => changeCantidadPagina(e));
});
