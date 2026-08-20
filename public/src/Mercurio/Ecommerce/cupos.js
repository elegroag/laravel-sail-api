/**
 * Cupos del servicio en el mes: calculo, resumen y alerta de cupos agotados.
 *
 * Depende de globales del layout: $ (jQuery), Swal (SweetAlert2).
 */
export function obtenerCuposMes(data, srv) {
    var valor = null;

    if (data && data.cupos_mes !== undefined && data.cupos_mes !== null && data.cupos_mes !== '') {
        valor = data.cupos_mes;
    } else if (srv && srv.cupos_mes !== undefined && srv.cupos_mes !== null && srv.cupos_mes !== '') {
        valor = srv.cupos_mes;
    }

    if (valor === null) {
        return null;
    }

    return parseInt(valor, 10) || 0;
}

export function actualizarCuposMesResumen(cuposMes) {
    var texto = cuposMes === null ? '-' : String(cuposMes);
    $('#txt_cupos_mes').text(texto);
    $('#hid_cupos_mes').val(cuposMes === null ? '' : String(cuposMes));
    $('#txt_cupos_mes').toggleClass('panel-compra__cupos-mes--cero', cuposMes === 0);

    if (cuposMes === 0) {
        $('#btn_procesar_pago').prop('disabled', true).hide();
        return false;
    }

    $('#btn_procesar_pago').prop('disabled', false).show();
    return true;
}

export function mostrarAlertaCuposMesCero() {
    Swal.fire({
        title: 'Sin cupos disponibles',
        html: '<p>No hay cupos disponibles para este servicio en el mes actual.</p>' +
            '<p class="text-muted mb-0">No es posible continuar con la compra.</p>',
        icon: 'warning',
        confirmButtonText: 'Entendido'
    });
}
