import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination, validePk } from '../Glob/Glob';

window.App = $App;
let validator = undefined;

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
			codest: { required: true },
			detalle: { required: true }
        },
    });
};

$(() => {
	window.App.initialize();
	EventsPagination();

	const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));

	$(document).on('blur', '#codest', validePk);

	$('#captureModal').on('hide.bs.modal', function (e) {
		$('.select2-selection')
			.removeClass(validator.settings.errorClass)
			.removeClass(validator.settings.validClass);
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const codest = $(e.currentTarget).attr('data-cid');
		window.App.trigger('syncro', {
			url: window.App.url(window.ServerController + '/editar'),
			data: {
				codest: codest,
			},
			callback: (response) => {
				if(response.success){
					$.each(response, function (key, value) {
						$('#' + key.toString()).val(value);
					});
					$('#codest').attr('disabled', 'true');
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
		if (!validator.valid()) return;

		$('#form :input').each(function (elem) {
			$(this).removeAttr('disabled');
		});

		window.App.trigger('syncro', {
			url: window.App.url(window.ServerController + '/guardar'),
			data: $('#form').serialize(),
			callback: (response) => {
				if (response.success) {
					buscar();
					Messages.display(response.msj, 'success');
					modalCapture.hide();
				} else {
					Messages.display(response.msj, 'error');
				}
			}
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
				window.App.trigger('syncro', {
					url: window.App.url(window.ServerController + '/borrar'),
					data: {
						codest: codest,
					},
					callback: (response) => {
						if (response) {
							if(response.success){
								buscar();
								Messages.display(response.msj, 'success');
							}
						} else {
							Messages.display(response.msj, 'error');
						}
					},
				});
			}
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
			codest: '',
			detalle: '',
		}));
		modalCapture.show();
		validatorInit();
	});
});
