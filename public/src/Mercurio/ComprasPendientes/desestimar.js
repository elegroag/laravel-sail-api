/**
 * Desestimacion de una precompra: modal de motivos y confirmacion.
 *
 * Depende de globales del layout: $ (jQuery), Swal (SweetAlert2).
 */
import store from './store.js';
import { MOTIVO_OTRO } from './constants.js';
import { nombreServicio } from './datos.js';
import { removerPrecompra } from './render.js';

export function abrirModalDesestimar(precompra) {
    store.precompraSeleccionada = precompra;

    $('#desestimar_servicio').text(nombreServicio(precompra));
    $('input[name="motivo_desestimar"]').prop('checked', false);
    $('#txt_detalle_otro').val('');
    $('#grupo_detalle_otro').hide();
    $('#error_desestimar').hide();
    $('#btn_confirmar_desestimar').prop('disabled', false);

    if (store.modalDesestimar) {
        store.modalDesestimar.show();
    }
}

export function confirmarDesestimar() {
    var motivo = $('input[name="motivo_desestimar"]:checked').val();
    var detalle = $('#txt_detalle_otro').val().trim();

    $('#error_desestimar').hide();

    if (!motivo) {
        $('#error_desestimar_msg').text('Debe seleccionar un motivo');
        $('#error_desestimar').show();
        return;
    }

    if (motivo === MOTIVO_OTRO && !detalle) {
        $('#error_desestimar_msg').text('Debe indicar el motivo en el campo de texto');
        $('#error_desestimar').show();
        return;
    }

    if (!store.precompraSeleccionada) {
        return;
    }

    $('#btn_confirmar_desestimar').prop('disabled', true);

    $.ajax({
        url: store.routes.desestimarPrecompra,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {
            precompra_id: store.precompraSeleccionada.id,
            motivo: motivo,
            detalle: detalle
        }
    }).done(function (response) {
        $('#btn_confirmar_desestimar').prop('disabled', false);

        if (response.success) {
            var id = store.precompraSeleccionada.id;
            store.precompraSeleccionada = null;

            if (store.modalDesestimar) {
                store.modalDesestimar.hide();
            }

            removerPrecompra(id);

            Swal.fire({
                title: 'Compra desestimada',
                text: response.message || 'La compra fue desestimada correctamente',
                icon: 'success',
                showConfirmButton: false,
                timer: 4000
            });
        } else {
            $('#error_desestimar_msg').text(response.message || 'No se pudo desestimar la compra');
            $('#error_desestimar').show();
        }
    }).fail(function () {
        $('#btn_confirmar_desestimar').prop('disabled', false);
        $('#error_desestimar_msg').text('Error de conexión al desestimar la compra');
        $('#error_desestimar').show();
    });
}
