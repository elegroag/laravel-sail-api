import { $App } from '@/App';
import { $Kumbia, Messages, Utils } from '@/Utils';
import { aplicarFiltro, buscar, validePk } from '../Glob/Glob';

window.App = $App;
let validator = undefined;

$(() => {
	window.App.initialize();
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

		window.App.trigger('syncro', {
			url: window.App.url('/editar'),
			data: {
				codapl: codapl,
			},
			callback: (response) => {
				if(response){
				$.each(response, function (key, value) {
					$('#' + key.toString()).val(value);
				});
				$('#codapl').attr('disabled', 'true');
				document.getElementById('btCaptureModal').click();
				setTimeout(() => {
					$('#detalle').trigger('focus');
				}, 500);
			}else{
				Messages.display(response.error, 'error');
			}
		}});
	});

	$(document).on('click', "[data-toggle='guardar']", (e) => {
		e.preventDefault();
		if (!$('#form').valid()) return;

		$('#form :input').each(function (elem) {
			$(this).removeAttr('disabled');
		});

		window.App.trigger('syncro', {
			url: window.App.url('/guardar'),
			data: $('#form').serialize(),
			callback: (response) => {
				if(response){
					buscar();
					Messages.display(response['msg'], 'success');
					$('#capture-modal').modal('hide');
				}else{
					Messages.display(response.error, 'error');
				}
			},
		});
	});
});
