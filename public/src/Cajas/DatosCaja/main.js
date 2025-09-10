import { $Kumbia, Messages, Utils } from '@/Utils';
import { aplicarFiltro, buscar, validePk } from '../Glob/Glob';

var validator = undefined;

$(() => {
	aplicarFiltro();
	validator = $('#form').validate({
		rules: {
			codcaj: { required: false },
			nit: { required: false },
			razsoc: { required: false },
			sigla: { required: false },
			email: { required: false, email: true },
			direccion: { required: false },
			telefono: { required: false },
			codciu: { required: false },
			pagweb: { required: false },
			pagfac: { required: false },
			pagtwi: { required: false },
			pagyou: { required: false },
		},
	});

	$(document).on('blur', '#codcaj', (e) => {
		validePk('#codcaj');
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
		const codcaj = e.target.cid;
		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL($Kumbia.controller + '/editar'),
			data: {
				codcaj: codcaj,
			},
		})
			.done(function (transport) {
				var response = transport;
				$.each(response, function (key, value) {
					$('#' + key.toString()).val(value);
				});
				$('#codcaj').attr('disabled', 'true');
				document.getElementById('btCaptureModal').click();
				setTimeout(() => {
					$('#nit').trigger('focus');
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
				if (response.flag == true) {
					buscar();
					Messages.display(response.msg, 'success');
					$('#capture-modal').modal('hide');
				} else {
					Messages.display(response.msg, 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});
});
