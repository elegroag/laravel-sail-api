import { $App } from '@/App';
import { Messages } from '@/Utils';
import { aplicarFiltro, buscar, validePk } from '../Glob/Glob';

let validator = undefined;
let validator_campo = undefined;
let tipo_global = undefined;

/**
 * Inicializa los validadores de formularios
 */
const initValidators = () => {
    // Validador del formulario principal
    validator = $('#form').validate({
        rules: {
            tipo: { required: true },
            detalle: { required: true },
        },
        messages: {
            tipo: 'El campo tipo es obligatorio',
            detalle: 'El campo detalle es obligatorio'
        }
    });

    // Validador del formulario de campos
    validator_campo = $('#form_campo').validate({
        rules: {
            campo_28: { required: true },
            detalle_28: { required: true },
            orden_28: { 
                required: true, 
                number: true,
                min: 1
            },
        },
        messages: {
            campo_28: 'El campo es obligatorio',
            detalle_28: 'El detalle es obligatorio',
            orden_28: {
                required: 'El orden es obligatorio',
                number: 'Debe ser un número válido',
                min: 'El valor mínimo es 1'
            }
        }
    });
};

/**
 * Valida si una clave primaria ya existe
 * @param {string} campoId - ID del campo a validar
 * @param {string} tipo - Tipo de validación
 */
const validarClaveUnica = (campoId, tipo) => {
    const $campo = $(`#${campoId}`);
    if ($campo.val().trim() === '') return;

    window.App.trigger('syncro', {
        url: window.App.url(`${window.ServerController}/validePkCampo`),
        data: {
            tipo: tipo || tipo_global,
            campo_28: $campo.val()
        },
        callback: (response) => {
            if (response?.flag === false) {
                Messages.display(response.msg || 'El campo ya existe', 'warning');
                $campo.val('').trigger('focus');
            }
        }
    });
};

/**
 * Carga los campos asociados a un tipo
 * @param {string} tipo - Tipo de acceso
 */
const cargarCampos = (tipo) => {
    if (!tipo) return;
    
    window.App.trigger('syncro', {
        url: window.App.url(`${window.ServerController}/campo_view`),
        data: { tipo },
        callback: (html) => {
            if (html) {
                $('#result_campos').html(html);
            }
        }
    });
};

// Inicialización de la aplicación
window.App = $App;

$(() => {
    // Inicialización
    window.App.initialize();
    aplicarFiltro();
    initValidators();

    // Eventos de validación
    $(document).on('blur', '#tipo', function() {
        if ($(this).val().trim() === '') return;
        validePk('#tipo');
    });

    $(document).on('blur', '#campo_28', function() {
        validarClaveUnica('campo_28');
    });

    // Limpiar formulario al cerrar el modal
    $('#captureModalCampo').on('hide.bs.modal', function () {
        const $form = $('#form_campo');
        $form[0].reset();
        
        if (validator_campo) {
            validator_campo.resetForm();
            $('.select2-selection', $form)
                .removeClass(validator_campo.settings.errorClass)
                .removeClass(validator_campo.settings.validClass);
        }
    });

    // Editar
    $(document).on('click', "[data-toggle='editar']", function () {
        const tipo = $(this).data('tipo');
        const campo = $(this).data('campo');

        if (!tipo) return;

        window.App.trigger('syncro', {
            url: window.App.url(`${window.ServerController}/editar`),
            data: { tipo, campo },
            callback: (response) => {
                if (response) {
                    $('#tipo_edit').val(response.tipo || '');
                    $('#campo_28_edit').val(response.campo || '');
                    $('#detalle_28_edit').val(response.detalle || '');
                    $('#orden_28_edit').val(response.orden || '');

                    // Mostrar el modal de edición
                    const modal = new bootstrap.Modal(document.getElementById('editModal'));
                    modal.show();
                } else {
                    Messages.display('No se pudieron cargar los datos', 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al cargar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    // Buscar
    $(document).on('click', "[data-toggle='page-buscar']", function (e) {
        e.preventDefault();
        buscar($(e.currentTarget));
    });
});
