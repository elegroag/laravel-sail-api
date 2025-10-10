import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination, validePk } from '../Glob/Glob';

let validator = undefined;

const validatorInit = () => {
    return $('#form').validate({
        rules: {
            codfir: { required: true },
            nombre: { required: true },
            cargo: { required: true },
            archivo: { required: true },
            email: { required: true, email: true },
        },
        messages: {
            codfir: "El código es requerido",
            nombre: "El nombre es requerido",
            cargo: "El cargo es requerido",
            archivo: "El archivo es requerido",
            email: {
                required: "El email es requerido",
                email: "Ingrese un email válido"
            }
        }
    });
};


window.App = $App;
$(() => {
    window.App.initialize();
    const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));
    EventsPagination();

    $(document).on('blur', '#codfir', (e) => {
        validePk('#codfir');
    });

    $(document).on('click', "[data-toggle='editar']", (e) => {
        e.preventDefault();
        const codfir = $(e.currentTarget).data('cid');
        
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
            data: { codfir: codfir },
            callback: (response) => {
                if (response) {
                    Object.keys(response).forEach(key => {
                        if (key !== 'archivo') {
                            $(`#${key}`).val(response[key]);
                        }
                    });
                            
                    modalCapture.show();
                    const tpl = _.template(document.getElementById('tmp_form').innerHTML);
                    $('#captureModalbody').html(tpl(response));
                    validatorInit();
                    
                    $('[data-bs-toggle="tooltip"]').tooltip();                    
                    setTimeout(() => $('#codfir').trigger('focus'), 500);
                    $('#codfir').attr('disabled', 'true');
                } else {
                    Messages.display('Error al cargar los datos', 'error');
                }
            }
        });
    });

    $(document).on('click', "[data-toggle='guardar']", (e) => {
        e.preventDefault();
        if (!$('#form').valid()) return;

        const formData = new FormData($('#form')[0]);
        const fileInput = $('#archivo')[0];
        if (fileInput.files.length > 0) {
            formData.append('archivo', fileInput.files[0]);
        }
        $('#form :input').prop('disabled', false);

        window.App.trigger('upload', {
            url: window.App.url(window.ServerController + '/guardar'),
            data: formData,
            processData: false,
            contentType: false,
            callback: (response) => {
                if (response && response.flag) {
                    modalCapture.hide();
                    Messages.display(response.msg || 'Operación realizada con éxito', 'success');
                    buscar();
                    $('#form')[0].reset();
                } else {
                    Messages.display(response?.msg || 'Error al guardar los datos', 'error');
                }
            }
        });
    });

    $(document).on('click', "[data-toggle='borrar']", (e) => {
        e.preventDefault();
        const codfir = $(e.currentTarget).data('cid');
        
        Swal.fire({
            title: '¿Está seguro de eliminar este registro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.App.trigger('syncro', {
                    url: window.App.url(window.ServerController + '/borrar'),
                    data: { codfir: codfir },
                    callback: (response) => {
                        if (response && response.flag) {
                            Messages.display(response.msg || 'Registro eliminado correctamente', 'success');
                            buscar();
                        } else {
                            Messages.display(response?.msg || 'Error al eliminar el registro', 'error');
                        }
                    }
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
            codfir: '',
            nombre: '',
            cargo: '',
            archivo: '',
            email: ''            
		}));
		modalCapture.show();
		validatorInit();
        $('[data-bs-toggle="tooltip"]').tooltip();
	});

    $(document).on('click', "[data-toggle='reporte']", (e) => {
        e.preventDefault();
        const tipo = $(e.currentTarget).data('type');
        window.location.href = window.App.url(window.ServerController + '/reporte/' + tipo);
    });

    
});