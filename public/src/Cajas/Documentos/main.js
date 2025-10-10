import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination, validePk } from '../Glob/Glob';

let validator = undefined;
window.App = $App;

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
			coddoc: { required: true },
			detalle: { required: true },
        },
    });
};


$(() => {
	window.App.initialize();
	const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));
	EventsPagination();

	$(document).on('blur', '#coddoc', function () {
		validePk('#coddoc');
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const coddoc = $(e.currentTarget).attr('data-cid');
		window.App.trigger('syncro', {
			url: window.App.url(window.ServerController + '/editar'),
			data: {
				coddoc
			},
			callback: (response) => {
				$.each(response, function (key, value) {
					$('#' + key.toString()).val(value);
				});

				modalCapture.show();
				const tpl = _.template(document.getElementById('tmp_form').innerHTML);
				$('#captureModalbody').html(tpl(response));

				$('#coddoc').attr('disabled', 'true');
				validatorInit();
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
				if (response.flag == true) {
					buscar();
					Messages.display(response.msg, 'success');
					modalCapture.hide();
				} else {
					Messages.display(response.msg, 'error');
				}
			}
		});
	});

	$(document).on('click', "[data-toggle='borrar']", (e) => {
		e.preventDefault();
		const coddoc = $(e.currentTarget).attr('data-cid');
		Swal.fire({
			title: '¡Confirmar la acción!',
			text: 'Esta seguro de borrar?',
			type: 'warning',
			showCancelButton: true,
			confirmButtonClass: 'btn btn-success',
			cancelButtonClass: 'btn btn-danger',
			confirmButtonText: 'SI',
			cancelButtonText: 'NO',
		}).then((result) => {
			if (result.isConfirmed) {
				window.App.trigger('syncro', {
					url: window.App.url(window.ServerController + '/borrar'),
					data: {
						coddoc: coddoc,
					},
					callback: (response) => {
						if (response.flag == true) {
							buscar();
							Messages.display(response.msg, 'success');
						} else {
							Messages.display(response.msg, 'error');
						}
					}
				});
			}
		});
	});


	$(document).on('click', "[data-toggle='reporte']", (e) => {
		e.preventDefault();
		const tipo = $(e.currentTarget).attr('data-type');
		window.location.href = window.App.url(window.ServerController + '/reporte/' + tipo);
	});

	$(document).on('click', "[data-toggle='header-nuevo']", (e) => {
		e.preventDefault();
		$('#form :input').each(function (elem) {
			$(this).val('');
			$(this).removeAttr('disabled');
		});

		const tpl = _.template(document.getElementById('tmp_form').innerHTML);
		$('#captureModalbody').html(tpl({
			coddoc: '',
			detalle: '',
		}));
		modalCapture.show();
		validatorInit();
	});
});