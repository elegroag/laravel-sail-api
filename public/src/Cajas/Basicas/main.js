import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination, validePk } from '../Glob/Glob';

window.App = $App;
let validator = undefined;

const validatorInit = () => {
	validator = $('#form').validate({
		rules: {
			codapl: { required: false },
			email: { required: false, email: true },
			clave: { required: false },
			path: { required: false },
		},
	});
}

$(() => {
	window.App.initialize();
	EventsPagination();
	const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));

	$(document).on('blur', '#codapl', (e) => {
		validePk('#codapl');
	});

	$('#captureModal').on('hide.bs.modal', (e) => {
		$('.select2-selection')
			.removeClass(validator.settings.errorClass)
			.removeClass(validator.settings.validClass);
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const codapl = $(e.currentTarget).attr('data-cid');
		window.App.trigger('syncro', {
			url: window.App.url(window.ServerController + '/editar'),
			data: {
				codapl: codapl,
			},
			callback: (response) => {
				if(response.success){
					$.each(response, function (key, value) {
						$('#' + key.toString()).val(value);
					});
					$('#codapl').attr('disabled', 'true');
					const tpl = _.template(document.getElementById('tmp_form').innerHTML);
					$('#captureModalbody').html(tpl(response.data));
					modalCapture.show();
					validatorInit();
				} else {
					Messages.display(response, 'error');
				}
			}
		});
	});

	$(document).on('click', "[data-toggle='guardar']", (e) => {
		e.preventDefault();
		if (!$('#form').valid()) return;

		$('#form :input').each(function (elem) {
			$(this).removeAttr('disabled');
		});

		window.App.trigger('syncro', {
			url: window.App.url(window.ServerController + '/guardar'),
			data: $('#form').serialize(),
			callback: (response) => {
				if(response){
					buscar();
					Messages.display(response.msj, 'success');
					modalCapture.show();
					const tpl = _.template(document.getElementById('tmp_form').innerHTML);
					$('#captureModalbody').html(tpl({
						codapl: '',
						email: '',
						clave: '',
						path: '',
						ftpserver: '',
						pathserver: '',
						userserver: '',
						passserver: '',
					}));
					validatorInit();	
				} else {
					Messages.display(response, 'error');
				}
			},
		});
	});

	$(document).on('click', "[data-toggle='header-nuevo']", (e) => {
		e.preventDefault();
		$('#form :input').each(function (elem) {
			$(this).val('');
			$(this).removeAttr('disabled');
		});

		const tpl = _.template(document.getElementById('tmp_form').innerHTML);
		$('#captureModalbody').html(tpl({
			codapl: '',
			detalle: '',
			email: '',
			clave: '',
			path: '',
			ftpserver: '',
			pathserver: '',
			userserver: '',
			passserver: '',
		}));
		modalCapture.show();
		validatorInit();
	});
});
