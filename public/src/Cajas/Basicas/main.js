import { $App } from '@/App';
import { Messages } from '@/Utils';
import { aplicarFiltro, buscar, validePk } from '../Glob/Glob';

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

window.App = $App;
$(() => {
	window.App.initialize();
	aplicarFiltro();


	$(document).on('blur', '#codapl', (e) => {
		validePk('#codapl');
	});

	$('#captureModal').on('hide.bs.modal', (e) => {
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
			url: window.App.url(window.ServerController + '/editar'),
			data: {
				codapl: codapl,
			},
			callback: (response) => {
				if(response){
					$('#codapl').attr('disabled', 'true');
					const instance = new bootstrap.Modal(document.getElementById('captureModal'));
					instance.show();
					const tpl = _.template(document.getElementById('tmp_form').innerHTML);
					$('#captureModalbody').html(tpl(response));
					validatorInit();
				} else {
					Messages.display(response.error, 'error');
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
					Messages.display(response['msg'], 'success');
					const instance = new bootstrap.Modal(document.getElementById('captureModal'));
					instance.show();
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
					
				}else{
					Messages.display(response.error, 'error');
				}
			},
		});
	});
});
