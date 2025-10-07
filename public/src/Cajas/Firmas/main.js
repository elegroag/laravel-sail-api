import { $App } from '@/App';
import { Messages } from '@/Utils';
import { actualizar_select, aplicarFiltro, buscar, validePk } from '../Glob/Glob';

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
    aplicarFiltro();
    validator = validatorInit();

    $(document).on('blur', '#codfir', (e) => {
        validePk('#codfir');
    });

    $('#capture-modal').on('hide.bs.modal', function (e) {
        if (validator) {
            validator.resetForm();
            $('.select2-selection')
                .removeClass(validator.settings.errorClass)
                .removeClass(validator.settings.validClass);
            
            // Limpiar el formulario al cerrar
            if ($('#form').length) {
                $('#form')[0].reset();
                $('#codfir').removeAttr('disabled');
            }
        }
    });

    $(document).on('click', "[data-toggle='editar']", (e) => {
        e.preventDefault();
        const codfir = $(e.currentTarget).data('cid');
        
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
            data: { codfir: codfir },
            callback: (response) => {
                if (response) {
                    // Llenar el formulario con los datos
                    Object.keys(response).forEach(key => {
                        if (key !== 'archivo') {
                            $(`#${key}`).val(response[key]);
                        }
                    });
                    
                    // Deshabilitar el campo código
                    $('#codfir').attr('disabled', 'true');
                    
                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('capture-modal'));
                    modal.show();
                    
                    // Enfocar el primer campo
                    setTimeout(() => $('#codfir').trigger('focus'), 500);
                } else {
                    Messages.display('Error al cargar los datos', 'error');
                }
            }
        });
    });

    $(document).on('click', "[data-toggle='guardar']", (e) => {
        e.preventDefault();
        if (!$('#form').valid()) return;

        // Crear FormData para manejar archivos
        const formData = new FormData($('#form')[0]);
        
        // Si hay un archivo, agregarlo al formData
        const fileInput = $('#archivo')[0];
        if (fileInput.files.length > 0) {
            formData.append('archivo', fileInput.files[0]);
        }

        // Habilitar campos deshabilitados antes de enviar
        $('#form :input').prop('disabled', false);

        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/guardar'),
            data: formData,
            processData: false,
            contentType: false,
            callback: (response) => {
                if (response && response.flag) {
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('capture-modal'));
                    if (modal) modal.hide();
                    
                    // Mostrar mensaje de éxito
                    Messages.display(response.msg || 'Operación realizada con éxito', 'success');
                    
                    // Actualizar la tabla
                    buscar();
                    
                    // Limpiar el formulario
                    $('#form')[0].reset();
                } else {
                    // Mostrar mensaje de error
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

    $(document).on('click', "[data-toggle='nuevo']", (e) => {
        e.preventDefault();
        $('#form')[0].reset();
        $('#codfir').removeAttr('disabled');
        const modal = new bootstrap.Modal(document.getElementById('capture-modal'));
        modal.show();
        actualizar_select();
    });

    $(document).on('click', "[data-toggle='reporte']", (e) => {
        e.preventDefault();
        const tipo = $(e.currentTarget).data('type');
        window.location.href = window.App.url(window.ServerController + '/reporte/' + tipo);
    });

    $(document).on('click', "[data-toggle='filtrar']", (e) => {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById('filtrar-modal'));
        modal.show();
    });

    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});