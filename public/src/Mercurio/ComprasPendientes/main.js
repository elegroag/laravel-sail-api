/**
 * Modulo ComprasPendientes - Precompras abandonadas (Mercurio)
 *
 * Lista las precompras en estado pendiente de pago (PE) y permite:
 *   - Retomar el pago: valida la tarifa vigente y reabre el checkout de ePayco
 *     reutilizando la misma precompra (via sessionStorage epayco_precompra_id).
 *     La respuesta de ePayco redirige al catalogo, donde el flujo existente
 *     (verificarRespuestaEpayco -> guardarVenta) completa la venta.
 *   - Desestimar: registra un motivo del catalogo (texto libre si es OTRO).
 *
 * Depende de globales cargados por el layout / blade:
 *   $ (jQuery), Swal (SweetAlert2), bootstrap, sessionStorage
 *   window.routes        -> rutas generadas con route() en el blade
 *   window.epaycoHandler -> handler configurado con ePayco.checkout.configure en el blade
 */

const ComprasPendientesModule = (function () {
    // === Estado privado ===
    let trabajadorData = null;
    let precomprasData = [];
    let serviciosData = [];
    let precompraSeleccionada = null;
    let modalDesestimar = null;
    let routes = {};

    const MOTIVO_OTRO = 'OTRO';

    // === Helpers ===

    function escapeHtml(texto) {
        if (!texto) return '';
        return String(texto)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function formatearValor(valor) {
        var num = parseFloat(valor) || 0;
        return '$' + num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function sanitizarTexto(texto) {
        if (!texto) return '';
        return texto
            .replace(/[áàäâ]/g, 'a').replace(/[éèëê]/g, 'e')
            .replace(/[íìïî]/g, 'i').replace(/[óòöô]/g, 'o')
            .replace(/[úùüû]/g, 'u').replace(/[ñ]/g, 'n')
            .replace(/[ÁÀÄÂ]/g, 'A').replace(/[ÉÈËÊ]/g, 'E')
            .replace(/[ÍÌÏÎ]/g, 'I').replace(/[ÓÒÖÔ]/g, 'O')
            .replace(/[ÚÙÜÛ]/g, 'U').replace(/[Ñ]/g, 'N')
            .replace(/[^a-zA-Z0-9\s.\-]/g, '');
    }

    function mostrarLoader(id) {
        $('#' + id).show();
    }

    function ocultarLoader(id) {
        $('#' + id).hide();
    }

    function obtenerEpaycoHandler() {
        return window.epaycoHandler || null;
    }

    // === Datos ===

    function buscarServicio(codser, numero) {
        for (var i = 0; i < serviciosData.length; i++) {
            var srv = serviciosData[i];
            if (String(srv.codser) === String(codser) && String(srv.numero) === String(numero)) {
                return srv;
            }
        }
        return null;
    }

    function nombreServicio(precompra) {
        var srv = buscarServicio(precompra.codser, precompra.numero);
        if (srv && srv.nombre) {
            return srv.nombre;
        }
        return 'Servicio ' + precompra.codser + ' - ' + precompra.numero;
    }

    function buscarPrecompra(id) {
        for (var i = 0; i < precomprasData.length; i++) {
            if (String(precomprasData[i].id) === String(id)) {
                return precomprasData[i];
            }
        }
        return null;
    }

    // === Render ===

    function buildFila(label, valor) {
        return '<div class="compra-card__row">' +
            '<span class="compra-card__label">' + escapeHtml(label) + '</span>' +
            '<span class="compra-card__value">' + valor + '</span>' +
            '</div>';
    }

    function buildCardPendiente(precompra) {
        var servicio = escapeHtml(nombreServicio(precompra));
        var beneficiario = escapeHtml(precompra.codben || '-');
        var fecha = escapeHtml(precompra.fecha_precompra || '-');
        var valor = precompra.valor !== null && precompra.valor !== '' ? formatearValor(precompra.valor) : '-';
        var nota = precompra.nota || '';

        var html = '<article class="compra-card" data-precompra-id="' + escapeHtml(String(precompra.id)) + '">';
        html += '<div class="compra-card__header">';
        html += '<span class="compra-card__doc">#' + escapeHtml(String(precompra.id)) + '</span>';
        html += '<span class="compra-card__estado compra-card__estado--x">' + escapeHtml(precompra.estado_descripcion || 'Pendiente de pago') + '</span>';
        html += '</div>';
        html += '<h3 class="compra-card__servicio">' + servicio + '</h3>';
        html += '<div class="compra-card__body">';
        html += buildFila('Fecha', fecha);
        html += buildFila('Beneficiario', beneficiario);

        if (nota && nota.trim() !== '') {
            html += buildFila('Nota', escapeHtml(nota));
        }

        html += '</div>';
        html += '<div class="compra-card__footer">';
        html += '<span class="compra-card__valor-label">Valor</span>';
        html += '<span class="compra-card__valor">' + valor + '</span>';
        html += '</div>';
        html += '<div class="d-flex gap-2 mt-3">';
        html += '<button type="button" class="btn btn-primary btn-sm flex-fill btn-retomar-pago" data-id="' + escapeHtml(String(precompra.id)) + '">' +
            '<i class="fas fa-credit-card me-1"></i> Retomar pago</button>';
        html += '<button type="button" class="btn btn-outline-danger btn-sm flex-fill btn-desestimar" data-id="' + escapeHtml(String(precompra.id)) + '">' +
            '<i class="fas fa-ban me-1"></i> Desestimar</button>';
        html += '</div>';
        html += '</article>';

        return html;
    }

    function renderPendientes() {
        var grid = $('#grid_pendientes');
        grid.empty();

        if (!precomprasData.length) {
            $('#contenido_pendientes').hide();
            $('#sin_pendientes').show();
            return;
        }

        $('#sin_pendientes').hide();

        $.each(precomprasData, function (i, precompra) {
            grid.append(buildCardPendiente(precompra));
        });

        $('#info_total').text(precomprasData.length + ' compra' + (precomprasData.length === 1 ? '' : 's') + ' pendiente' + (precomprasData.length === 1 ? '' : 's'));
        $('#contenido_pendientes').css('display', 'flex');
    }

    function removerPrecompra(id) {
        precomprasData = precomprasData.filter(function (precompra) {
            return String(precompra.id) !== String(id);
        });
        renderPendientes();
    }

    // === Carga inicial ===

    function cargarDatos() {
        var cedtra = $('#hid_documento').val();

        mostrarLoader('loader_pendientes');
        $('#error_pendientes').hide();
        $('#sin_pendientes').hide();
        $('#contenido_pendientes').hide();

        var reqPrecompras = $.ajax({
            url: routes.listarPrecompras,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {}
        });

        var reqServicios = $.ajax({
            url: routes.listarServicios,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {}
        });

        var reqTrabajador = $.ajax({
            url: routes.identificarTrabajador,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: { cedtra: cedtra }
        });

        $.when(reqPrecompras, reqServicios, reqTrabajador).done(function (resPrecompras, resServicios, resTrabajador) {
            ocultarLoader('loader_pendientes');

            var precompras = resPrecompras[0];
            if (!precompras.success) {
                $('#error_mensaje').text(precompras.message || 'Error al cargar las compras pendientes');
                $('#error_pendientes').show();
                return;
            }

            precomprasData = precompras.data || [];

            var servicios = resServicios[0];
            serviciosData = (servicios.success && servicios.data) ? servicios.data : [];

            var trabajador = resTrabajador[0];
            if (trabajador.success && trabajador.data) {
                var rawTrabajador = trabajador.data.trabajador || trabajador.data;
                if (rawTrabajador && rawTrabajador.trabajador && !rawTrabajador.cedtra) {
                    trabajadorData = rawTrabajador.trabajador;
                } else {
                    trabajadorData = rawTrabajador;
                }
            }

            renderPendientes();
        }).fail(function () {
            ocultarLoader('loader_pendientes');
            $('#error_mensaje').text('Error de conexión al consultar las compras pendientes');
            $('#error_pendientes').show();
        });
    }

    // === Retomar pago ===

    function retomarPago(precompra) {
        var cedtra = $('#hid_documento').val();
        var epaycoHandler = obtenerEpaycoHandler();

        if (!epaycoHandler) {
            Swal.fire({
                title: 'Error',
                text: 'No se pudo inicializar la pasarela de pago. Recargue la página.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        Swal.fire({
            title: 'Validando tarifa...',
            text: 'Consultando la disponibilidad y el valor vigente del servicio.',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $.ajax({
            url: routes.validarTarifa,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {
                cedtra: cedtra,
                codser: precompra.codser,
                numero: precompra.numero,
                codben: precompra.codben || cedtra
            }
        }).done(function (response) {
            if (!response.success) {
                Swal.fire({
                    title: 'Servicio no disponible',
                    html: '<p>' + escapeHtml(response.message || 'No se pudo validar la tarifa del servicio.') + '</p>' +
                        '<p class="text-muted mb-0">Puede desestimar esta compra si ya no desea continuar.</p>',
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            var data = response.data || {};
            var valor = data.valser;

            if (!valor || parseFloat(valor) <= 0) {
                Swal.fire({
                    title: 'Atención',
                    text: 'No se pudo obtener el valor vigente del servicio.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            var cuposMes = null;
            if (data.cupos_mes !== undefined && data.cupos_mes !== null && data.cupos_mes !== '') {
                cuposMes = parseInt(data.cupos_mes, 10) || 0;
            }

            if (cuposMes === 0) {
                Swal.fire({
                    title: 'Sin cupos disponibles',
                    html: '<p>No hay cupos disponibles para este servicio en el mes actual.</p>' +
                        '<p class="text-muted mb-0">Puede desestimar esta compra si ya no desea continuar.</p>',
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            Swal.close();
            abrirCheckout(precompra, valor);
        }).fail(function () {
            Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudo validar la tarifa del servicio. Intente nuevamente.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        });
    }

    function abrirCheckout(precompra, valor) {
        var cedtra = $('#hid_documento').val();
        var epaycoHandler = obtenerEpaycoHandler();
        var servicioNombre = sanitizarTexto(nombreServicio(precompra)) || 'Compra de servicio';
        var nombre = sanitizarTexto((trabajadorData && trabajadorData.nombre) ? trabajadorData.nombre : 'Cliente');
        var email = (trabajadorData && trabajadorData.email) ? trabajadorData.email.trim() : 'sin@email.com';
        var invoice = 'ORD' + Date.now();

        var data = {
            name: servicioNombre,
            description: servicioNombre,
            invoice: invoice,
            currency: 'cop',
            amount: String(valor),
            tax_base: '0',
            tax: '0',
            country: 'co',
            lang: 'es',
            external: 'false',
            extra1: cedtra,
            extra2: String(precompra.codser),
            extra3: String(precompra.numero),
            response: routes.catalogo,
            name_billing: nombre,
            type_doc_billing: 'cc',
            number_doc_billing: cedtra,
            email_billing: email
        };

        // Reutilizar la misma precompra: el flujo del catalogo la retomara al validar el pago
        sessionStorage.setItem('epayco_cedtra', cedtra);
        sessionStorage.setItem('epayco_codser', String(precompra.codser));
        sessionStorage.setItem('epayco_numero', String(precompra.numero));
        sessionStorage.setItem('epayco_nota', precompra.nota || '');
        sessionStorage.setItem('epayco_codben', precompra.codben || cedtra);
        sessionStorage.setItem('epayco_precompra_id', String(precompra.id));

        epaycoHandler.onCloseModal = function () {
            setTimeout(function () {
                if (window.__epaycoPagoEnValidacion) {
                    return;
                }

                $.ajax({
                    url: routes.abandonarPrecompra,
                    method: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    data: { precompra_id: precompra.id },
                }).done(function (response) {
                    if (!response.success || !response.data || !response.data.abandonada) {
                        return;
                    }

                    sessionStorage.removeItem('epayco_cedtra');
                    sessionStorage.removeItem('epayco_codser');
                    sessionStorage.removeItem('epayco_numero');
                    sessionStorage.removeItem('epayco_nota');
                    sessionStorage.removeItem('epayco_codben');
                    sessionStorage.removeItem('epayco_precompra_id');

                    Swal.fire({
                        title: 'Pago no completado',
                        html:
                            '<p>Cerraste la pasarela sin finalizar el pago.</p>' +
                            '<p class="mb-0">La compra quedó marcada como <b>abandonada</b> y no se puede retomar.</p>',
                        icon: 'info',
                        confirmButtonText: 'Entendido',
                    }).then(function () {
                        cargarDatos();
                    });
                });
            }, 2500);
        };

        epaycoHandler.open(data);
    }

    // === Desestimar ===

    function abrirModalDesestimar(precompra) {
        precompraSeleccionada = precompra;

        $('#desestimar_servicio').text(nombreServicio(precompra));
        $('input[name="motivo_desestimar"]').prop('checked', false);
        $('#txt_detalle_otro').val('');
        $('#grupo_detalle_otro').hide();
        $('#error_desestimar').hide();
        $('#btn_confirmar_desestimar').prop('disabled', false);

        if (modalDesestimar) {
            modalDesestimar.show();
        }
    }

    function confirmarDesestimar() {
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

        if (!precompraSeleccionada) {
            return;
        }

        $('#btn_confirmar_desestimar').prop('disabled', true);

        $.ajax({
            url: routes.desestimarPrecompra,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {
                precompra_id: precompraSeleccionada.id,
                motivo: motivo,
                detalle: detalle
            }
        }).done(function (response) {
            $('#btn_confirmar_desestimar').prop('disabled', false);

            if (response.success) {
                var id = precompraSeleccionada.id;
                precompraSeleccionada = null;

                if (modalDesestimar) {
                    modalDesestimar.hide();
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

    // === Bind de eventos e inicializacion ===

    function bindHandlers() {
        var modalEl = document.getElementById('modal_desestimar');
        if (modalEl && typeof bootstrap !== 'undefined') {
            modalDesestimar = new bootstrap.Modal(modalEl);
        }

        $(document).on('click', '#btn_reintentar', function () {
            cargarDatos();
        });

        $(document).on('click', '.btn-retomar-pago', function () {
            var precompra = buscarPrecompra($(this).data('id'));
            if (precompra) {
                retomarPago(precompra);
            }
        });

        $(document).on('click', '.btn-desestimar', function () {
            var precompra = buscarPrecompra($(this).data('id'));
            if (precompra) {
                abrirModalDesestimar(precompra);
            }
        });

        $(document).on('change', 'input[name="motivo_desestimar"]', function () {
            var esOtro = $(this).val() === MOTIVO_OTRO;
            $('#grupo_detalle_otro').toggle(esOtro);
            $('#error_desestimar').hide();
            if (esOtro) {
                $('#txt_detalle_otro').trigger('focus');
            }
        });

        $(document).on('click', '#btn_confirmar_desestimar', confirmarDesestimar);
    }

    function init() {
        routes = window.routes || {};

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        bindHandlers();
        cargarDatos();
    }

    return { init };
})();

$(function () {
    ComprasPendientesModule.init();
});
