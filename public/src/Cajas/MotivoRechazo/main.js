import { $App } from '@/App';
import { Messages } from '@/Utils';
import { actualizar_select, buscar, EventsPagination, validePk } from '../Glob/Glob';

let validator;

$(() => {
	$App.initialize();
	EventsPagination();
	const modalCapture = new bootstrap.Modal(document.getElementById('capture-modal'));

	validator = $('#form').validate({
		rules: {
			codest: { required: true },
			detalle: { required: true },
		},
	});

	$(document).on('blur', '#codest', function () {
		validePk('#codest');
	});

	$('#capture-modal').on('hide.bs.modal', function (e) {
		validator.resetForm();
		$('.select2-selection')
			.removeClass(validator.settings.errorClass)
			.removeClass(validator.settings.validClass);
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const codest = $(e.currentTarget).attr('data-cid');
		$.ajax({
			type: 'POST',
			url: $App.url('editar'),
			data: {
				codest: codest,
			},
		})
			.done(function (response) {
				$.each(response, function (key, value) {
					$('#' + key.toString()).val(value);
				});
				$('#codest').attr('disabled', 'true');
				modalCapture.show();
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
			url: $App.url('guardar'),
			data: $('#form').serialize(),
		})
			.done(function (response) {
				if (response['flag'] == true) {
					buscar();
					Messages.display(response['msg'], 'success');
					modalCapture.hide();
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
		const codest = $(e.currentTarget).attr('data-cid');
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
					url: $App.url('borrar'),
					data: {
						codest: codest,
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
		modalCapture.show();
	});
});
