import { $Kumbia, Messages, Utils } from '@/Utils';
import { aplicarFiltro, buscar, validePk } from '../Glob/Glob';

let validator = undefined;

$(() => {
	aplicarFiltro();

	validator = $('#form').validate({
		rules: {
			codapl: { required: false },
			email: { required: false, email: true },
			clave: { required: false },
			path: { required: false },
		},
	});

	$(document).on('blur', '#codapl', (e) => {
		validePk('#codapl');
	});

	$('#capture-modal').on('hide.bs.modal', (e) => {
		if (validator !== undefined) {
			validator.resetForm();
			$('.select2-selection')
				.removeClass(validator.settings.errorClass)
				.removeClass(validator.settings.validClass);
		}
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const codapl = e.target.cid;
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/editar'),
			data: {
				codapl: codapl,
			},
		})
			.done(function (response) {
				$.each(response, function (key, value) {
					$('#' + key.toString()).val(value);
				});
				$('#codapl').attr('disabled', 'true');
				document.getElementById('btCaptureModal').click();
				setTimeout(() => {
					$('#detalle').trigger('focus');
				}, 500);
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
});
