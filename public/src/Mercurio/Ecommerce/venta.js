/**
 * Registro de la venta tras un pago aprobado.
 *
 * Depende de globales del layout: $ (jQuery), Swal (SweetAlert2), sessionStorage.
 */
import store from './store.js';
import { limpiarSeleccionServicio } from './panelCompra.js';
import { renderServiciosGrid } from './serviciosRender.js';

export function guardarVenta(refpago) {
    var cedtra = $('#hid_documento').val() || sessionStorage.getItem('epayco_cedtra') || '';
    var codser = $('#hid_codser').val() || sessionStorage.getItem('epayco_codser') || '';
    var numero = $('#hid_numero').val() || sessionStorage.getItem('epayco_numero') || '';
    var nota = $('#txt_nota').val() || sessionStorage.getItem('epayco_nota') || '';
    var codben = $('#hid_codben').val() || sessionStorage.getItem('epayco_codben') || cedtra;
    var precompraId = sessionStorage.getItem('epayco_precompra_id') || 0;

    sessionStorage.removeItem('epayco_cedtra');
    sessionStorage.removeItem('epayco_codser');
    sessionStorage.removeItem('epayco_numero');
    sessionStorage.removeItem('epayco_nota');
    sessionStorage.removeItem('epayco_codben');
    sessionStorage.removeItem('epayco_precompra_id');

    if (!cedtra || !codser || !numero) {
        Swal.fire({
            title: 'Error',
            text: 'No se encontraron los datos del servicio. Por favor seleccione el servicio nuevamente.',
            icon: 'error',
            showConfirmButton: true
        });
        return;
    }

    $.ajax({
        url: store.routes.guardarVenta,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: { cedtra, codser, numero, refpago, nota, codben, precompra_id: precompraId }
    }).done(function (response) {
        if (response.success) {
            Swal.fire({
                title: 'Compra exitosa',
                html: '<p style="font-size:1em">' + (response.message || 'Venta guardada exitosamente') + '</p>',
                icon: 'success',
                showConfirmButton: false,
                timer: 5000
            });
            setTimeout(function () {
                limpiarSeleccionServicio();
                $('#txt_nota').val('');
                renderServiciosGrid(store.serviciosData, store.busquedaServicio, store.filtroCodserServicio);
            }, 3000);
        } else {
            Swal.fire({
                title: 'Error',
                text: response.message || 'Error al guardar la venta',
                icon: 'error',
                showConfirmButton: false,
                timer: 5000
            });
        }
    }).fail(function () {
        Swal.fire({
            title: 'Error',
            text: 'Error de conexion al guardar la venta',
            icon: 'error',
            showConfirmButton: false,
            timer: 5000
        });
    });
}
