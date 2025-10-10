import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination, validePk } from '../Glob/Glob';

let validator = undefined;

const validatorInit = () => {
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
};

window.App = $App;
$(() => {
    window.App.initialize();
    EventsPagination();
	const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));
   
    $(document).on('blur', '#codcaj', (e) => {
        validePk('#codcaj');
    });

    $(document).on('click', "[data-toggle='editar']", (e) => {
        e.preventDefault();
        const codcaj = e.target.cid;

        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
            data: {
                codcaj: codcaj,
            },
            callback: (response) => {
                if (response) {
                    $('#codcaj').attr('disabled', 'true');
                    modalCapture.show();
                    const tpl = _.template(document.getElementById('tmp_form').innerHTML);
                    $('#captureModalbody').html(tpl(response));
                    validatorInit();
                    setTimeout(() => {
                        $('#nit').trigger('focus');
                    }, 500);
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
                if (response) {
                    buscar();
                    Messages.display(response.msg, 'success');
                    modalCapture.hide();
                    // Resetear el formulario
                    const tpl = _.template(document.getElementById('tmp_form').innerHTML);
                    $('#captureModalbody').html(tpl({
                        codcaj: '',
                        nit: '',
                        razsoc: '',
                        sigla: '',
                        email: '',
                        direccion: '',
                        telefono: '',
                        codciu: '',
                        pagweb: '',
                        pagfac: '',
                        pagtwi: '',
                        pagyou: ''
                    }));
                    validatorInit();
                } else {
                    Messages.display(response.error, 'error');
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
            codcaj: '',
            nit: '',
            razsoc: '',
            sigla: '',
            email: '',
            direccion: '',
            telefono: '',
            codciu: '',
            pagweb: '',
            pagfac: '',
            pagtwi: '',
            pagyou: '',
		}));
		modalCapture.show();
		validatorInit();
	});
});
