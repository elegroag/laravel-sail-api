import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination, validePk } from '../Glob/Glob';

window.App = $App;

let validator;
let validator_campo;

const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));
const genericoModal = new bootstrap.Modal(document.getElementById('genericoModal'));

const validatorInit = () => {
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
};

const validatorCampos = () => {
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

const editarCampos = ({tipo, campo}) => {
    window.App.trigger('syncro', {
        url: window.App.url(`${window.ServerController}/editar_campo`),
        data: { 
            tipo, 
            campo
        },
        callback: (response) => {
            if (response.success === true) {
                const tpl = _.template(document.getElementById('tmp_form_campo').innerHTML);
                $('#genericoModalbody').html(tpl(response.data));
                validatorCampos();
            } else {
                Messages.display('No se pudieron cargar los datos', 'error');
            }
        },
        error: (xhr) => {
            Messages.display('Error al cargar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
    });
}

const initDataTableCampo = () => {
    const $tbl = $('#dataTableCampo');
    if ($tbl.length === 0) return;

    // Destruir instancia previa si existe para evitar errores de re-inicialización
    if ($.fn.DataTable && $.fn.DataTable.isDataTable($tbl)) {
        $tbl.DataTable().destroy();
    }

    $tbl.DataTable({
        responsive: true,
        autoWidth: false,
        searching: true,
        paging: true,
        lengthChange: true,
        pageLength: 12,
        ordering: true,
        order: [],
        columnDefs: [
            { targets: 0, orderable: false, searchable: false, width: '20%' }
        ],
        language: {
            url: typeof window.DATATABLES_LANG_URL !== 'undefined' ? window.DATATABLES_LANG_URL : undefined,
            decimal: ",",
            thousands: ".",
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros en total)",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles",
            paginate: {
                first: "<<",
                previous: "<",
                next: ">",
                last: ">>"
            }
        }
    });
};

const listarCampos= (tipo) => {
    window.App.trigger('syncro', {
        url: window.App.url(`${window.ServerController}/campo_view`),
        data: { 
            tipo 
        },
        callback: (response) => {
            if (response) {
                genericoModal.show();
                const tpl = _.template(document.getElementById('tmp_table_campo').innerHTML);
                $('#genericoModalbody').html(tpl({
                    tipo: tipo,
                    _collection: response.collection,
                    detalle: response.detalle,
                }));
                // Inicializar DataTable sobre la tabla renderizada
                if(response.collection.length > 0){
                    initDataTableCampo();
                }
            } else {
                Messages.display('No se pudieron cargar los datos', 'error');
            }
        }
    });
}


$(() => {
    window.App.initialize();
    EventsPagination();

    $(document).on('blur', '#tipo', function() {
        if ($(this).val().trim() === '') return;
        validePk('#tipo');
    });

    $(document).on('blur', '#campo_28', function() {
        validarClaveUnica('campo_28');
    });

    $(document).on('click', "[data-toggle='editar']", function (e) {
        e.preventDefault();
        const tipo = $(this).data('cid');
        if (!tipo) return;
        window.App.trigger('syncro', {
            url: window.App.url(`${window.ServerController}/editar`),
            data: { 
                tipo
            },
            callback: (response) => {
                if (response) {
                    modalCapture.show();
                    const tpl = _.template(document.getElementById('tmp_form').innerHTML);
                    $('#captureModalbody').html(tpl(response));
                    validatorInit();

                    $('#tipo_edit').val(response.tipo || '');
                    $('#campo_28_edit').val(response.campo || '');
                    $('#detalle_28_edit').val(response.detalle || '');
                    $('#orden_28_edit').val(response.orden || '');                    
                } else {
                    Messages.display('No se pudieron cargar los datos', 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al cargar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
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
                            tipo: '',
                            detalle: '',
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
                tipo: '',
                detalle: '',
            }));
            modalCapture.show();
            validatorInit();
    });

    $(document).on('click', "[data-toggle='campo_view']", (e) => {
        e.preventDefault();
        const tipo = $(e.currentTarget).data('cid');
        listarCampos(tipo);
    });

    $(document).on('click', "[data-toggle='campo-editar']", (e) => {
        e.preventDefault();
        const tipo = $(e.currentTarget).data('tipo');
        const campo = $(e.currentTarget).data('campo');
        editarCampos({tipo, campo});
    });

    $(document).on('click', "[data-toggle='campo-borrar']", (e) => {
        e.preventDefault();
        const tipo = $(e.currentTarget).data('tipo');
        const campo = $(e.currentTarget).data('campo');
        window.App.trigger('syncro', {
            url: window.App.url(`${window.ServerController}/borrar_campo`),
            data: { tipo, campo },
            callback: (response) => {
                if (response) {
                    Messages.display(response.msg, 'success');
                    buscar();
                } else {
                    Messages.display(response.error, 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al borrar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('click', "[data-toggle='campo-guardar']", (e) => {
        e.preventDefault();
        const tipo = $(e.currentTarget).data('tipo');
        const campo_28 = $('#campo_28').val();
        const detalle_28 = $('#detalle_28').val();
        const orden_28 = $('#orden_28').val();
        
        window.App.trigger('syncro', {
            url: window.App.url(`${window.ServerController}/guardar_campo`),
            data: { 
                tipo, 
                campo: campo_28,
                detalle: detalle_28,
                orden: orden_28 
            },
            callback: (response) => {
                if (response) {
                    Messages.display(response.msg, 'success');
                    listarCampos(tipo);
                } else {
                    Messages.display(response.error, 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al guardar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('click', "[data-toggle='campo-cancelar']", (e) => {
        e.preventDefault();
        const tipo = $(e.currentTarget).data('tipo');
        listarCampos(tipo);
    });

    $(document).on('click', "[data-toggle='campo-agregar']", (e) => {
        e.preventDefault();
        const tipo = $(e.currentTarget).data('tipo');
        const tpl = _.template(document.getElementById('tmp_form_campo').innerHTML);
        $('#genericoModalbody').html(tpl({
            tipo,
            campo: '',
            detalle: '',
            orden: '',
        }));
        validatorCampos();
    });
});