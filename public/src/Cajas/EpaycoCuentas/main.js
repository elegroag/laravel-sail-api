import { $App } from '@/App';
import { Messages } from '@/Utils';

let validator;
let isEditing = false;
let editingId = null;
window.App = $App;

const setModalMode = (editing) => {
    isEditing = editing;
    if (!editing) {
        editingId = null;
    }
    $('#captureModal .card-header h5').text(editing ? 'Editar cuenta ePayco' : 'Nueva cuenta ePayco');
    $('#captureModal').find("[data-toggle='guardar']").text(editing ? 'Actualizar' : 'Guardar');
    $('#private_key_req, #p_key_req').toggle(!editing);
    $('#private_key_help, #p_key_help').text(
        editing
            ? 'Opcional. Deje vacío para conservar el valor actual.'
            : 'Requerido al crear.',
    );
    if (validator) {
        validator.destroy();
    }
    validatorInit();
};

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            account: { required: true, maxlength: 120 },
            env_mode: { required: true },
            p_id_customer: { required: true },
            public_key: { required: true },
            private_key: { required: !isEditing },
            p_key: { required: !isEditing },
        },
        messages: {
            account: 'El nombre de cuenta es requerido',
            env_mode: 'El ambiente es requerido',
            p_id_customer: 'El customer id es requerido',
            public_key: 'La public key es requerida',
            private_key: 'La private key es requerida',
            p_key: 'La P_KEY es requerida',
        },
    });
};

const resetForm = () => {
    const $form = $('#form');
    $form[0].reset();
    $('#id').val('');
    $('#env_mode').val('development');
    hideSecrets();
    if (validator) {
        validator.resetForm();
    }
};

const hideSecrets = () => {
    ['private_key', 'p_key'].forEach((fieldId) => {
        $(`#${fieldId}`).attr('type', 'password');
        $(`[data-toggle-secret="${fieldId}"] [data-eye-icon]`)
            .removeClass('fa-eye-slash')
            .addClass('fa-eye');
    });
};

const toggleSecretVisibility = (fieldId) => {
    const $input = $(`#${fieldId}`);
    const $icon = $(`[data-toggle-secret="${fieldId}"] [data-eye-icon]`);
    const show = $input.attr('type') === 'password';
    $input.attr('type', show ? 'text' : 'password');
    $icon.toggleClass('fa-eye', !show).toggleClass('fa-eye-slash', show);
};

const fillForm = ({
    id = '',
    account = '',
    env_mode = 'development',
    public_key = '',
    private_key = '',
    p_key = '',
    p_id_customer = '',
} = {}) => {
    resetForm();
    $('#id').val(id || '');
    $('#account').val(account || '');
    $('#env_mode').val(env_mode || 'development');
    $('#public_key').val(public_key || '');
    $('#private_key').val(private_key || '');
    $('#p_key').val(p_key || '');
    $('#p_id_customer').val(p_id_customer || '');
};

const submitForm = () => {
    if (!validator.valid()) return;

    const $guardarBtn = $('#captureModal').find("[data-toggle='guardar']");
    $guardarBtn.prop('disabled', true);

    const payload = {
        id: $('#id').val() || '',
        account: $('#account').val(),
        env_mode: $('#env_mode').val(),
        public_key: $('#public_key').val(),
        private_key: $('#private_key').val(),
        p_key: $('#p_key').val(),
        p_id_customer: $('#p_id_customer').val(),
    };

    window.App.trigger('syncro', {
        url: window.App.url(window.ServerController + '/guardar'),
        data: payload,
        callback: (response) => {
            $guardarBtn.prop('disabled', false);

            if (response?.flag === true) {
                Messages.display(response.msg, 'success');
                resetForm();
                editingId = null;
                isEditing = false;
                bootstrap.Modal.getInstance(document.getElementById('captureModal'))?.hide();
                buscarCuenta();
            } else {
                Messages.display(response?.msg || 'Error al guardar', 'error');
            }
        },
        error: (xhr) => {
            $guardarBtn.prop('disabled', false);
            Messages.display('Error al guardar: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        },
    });
};

const buscarCuenta = () => {
    window.App.trigger('syncro', {
        url: window.App.url(window.ServerController + '/buscar-cuenta'),
        callback: (response) => {
            if (!response || response.flag !== true) {
                return Messages.display(response?.msg || 'No se pudieron cargar los datos', 'error');
            }

            const items = response.data || [];
            if (!items.length) {
                $('#galeria').html(
                    '<div class="galeria-admin-empty">No hay cuentas ePayco. Use "Agregar cuenta" para comenzar.</div>',
                );
                return;
            }

            let html = '';
            const tmp = _.template(document.getElementById('tmp_galeria').innerHTML);
            $.each(items, function (key, value) {
                html += tmp({ value });
            });
            $('#galeria').html(html);
        },
        error: (xhr) => {
            Messages.display('Error al cargar cuentas: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        },
    });
};

$(() => {
    window.App.initialize();
    validatorInit();

    const modalCaptureEl = document.getElementById('captureModal');
    const modalCapture = new bootstrap.Modal(modalCaptureEl);

    modalCaptureEl.addEventListener('show.bs.modal', (event) => {
        if (!event.relatedTarget || !$(event.relatedTarget).is("[data-bs-target='#captureModal']")) {
            return;
        }
        setModalMode(false);
        fillForm();
    });

    buscarCuenta();

    $(document).on('click', "[data-toggle='editar']", (e) => {
        e.preventDefault();
        const id = $(e.currentTarget).attr('data-cid');

        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
            data: { id },
            callback: (response) => {
                if (!response || !response.id) {
                    return Messages.display(response?.msg || 'No fue posible cargar el registro.', 'error');
                }

                editingId = response.id;
                setModalMode(true);
                fillForm({
                    id: response.id,
                    account: response.account || '',
                    env_mode: response.env_mode,
                    public_key: response.public_key,
                    private_key: response.private_key || '',
                    p_key: response.p_key || '',
                    p_id_customer: response.p_id_customer,
                });
                modalCapture.show();
            },
        });
    });

    $(document).on('click', "[data-toggle='borrar']", (e) => {
        e.preventDefault();
        const id = $(e.currentTarget).attr('data-cid');
        Swal.fire({
            title: '¿Está seguro de borrar?',
            text: 'Se eliminará la cuenta ePayco.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-success btn-fill',
            cancelButtonClass: 'btn btn-danger btn-fill',
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (result.value) {
                window.App.trigger('syncro', {
                    url: window.App.url(window.ServerController + '/borrar'),
                    data: { id },
                    callback: (response) => {
                        if (response?.flag === true) {
                            buscarCuenta();
                            Messages.display(response.msg, 'success');
                        } else {
                            Messages.display(response?.msg || 'Error al borrar', 'error');
                        }
                    },
                    error: (xhr) => {
                        Messages.display('Error al borrar: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
                    },
                });
            }
        });
    });

    $(document).on('click', '[data-toggle-secret]', (e) => {
        e.preventDefault();
        const fieldId = $(e.currentTarget).attr('data-toggle-secret');
        if (fieldId) {
            toggleSecretVisibility(fieldId);
        }
    });

    $(document).on('click', "[data-toggle='guardar']", (e) => {
        e.preventDefault();
        submitForm();
    });
});
