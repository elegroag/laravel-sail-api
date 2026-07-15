/**
 * Modulo Ecommerce - Catalogo de servicios (Mercurio)
 *
 * Logica del catalogo de compra de servicios: identificacion del trabajador,
 * beneficiarios, servicios, tarifa, precompra y pago con ePayco.
 *
 * Depende de globales cargados por el layout / blade:
 *   $ (jQuery), Swal (SweetAlert2), bootstrap, sessionStorage
 *   window.routes        -> rutas generadas con route() en el blade
 *   window.epaycoHandler -> handler configurado con ePayco.checkout.configure en el blade
 */

const EcommerceModule = (function () {
    // === Estado privado ===
    let trabajadorData = null;
    let nucleoFamiliar = [];
    let beneficiarioSeleccionado = null;
    let serviciosData = [];
    let servicioSeleccionado = null;
    let busquedaServicio = '';
    let filtroCodserServicio = '';
    let modalResumenCompra = null;
    let routes = {};

    const TIPOS_BEN = { T: 'Trabajador', C: 'Conyuge', B: 'Beneficiario' };

    // === Helpers ===

    function formatearValor(valor) {
        var num = parseFloat(valor) || 0;
        return '$' + num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function mostrarLoader(id) {
        $('#' + id).show();
    }

    function ocultarLoader(id) {
        $('#' + id).hide();
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

    function escapeHtml(texto) {
        if (!texto) return '';
        return String(texto)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function obtenerTipoBeneficiario(ben) {
        return ben.descripcion_tipo || TIPOS_BEN[ben.tipben] || ben.tipben || '';
    }

    function obtenerCodben(ben) {
        return ben.codben || ben.cedtra || '';
    }

    // === Panel de compra ===

    function actualizarPanelBeneficiario() {
        var ben = beneficiarioSeleccionado;
        if (!ben) {
            $('#panel_beneficiario').hide();
            $('#panel_beneficiario_nombre').text('');
            $('#panel_beneficiario_doc').text('');
            return;
        }

        $('#panel_beneficiario_nombre').text(ben.nombre || '-');
        $('#panel_beneficiario_doc').text(obtenerCodben(ben));
        $('#panel_beneficiario').show();
    }

    function limpiarSeleccionServicio() {
        servicioSeleccionado = null;
        $('#hid_codser').val('');
        $('#hid_numero').val('');
        $('#hid_cupos_mes').val('');
        $('#txt_cupos_mes').text('-').removeClass('panel-compra__cupos-mes--cero');
        $('#btn_procesar_pago').prop('disabled', false).show();
        $('.servicio-card--selected').removeClass('servicio-card--selected');
        cerrarResumenCompraMovil();
        restaurarPanelAlSlot();
        $('#panel_compra').hide();
        $('#btn_carrito_movil').hide();
        $('#detalle_tarifa').hide();
        $('#error_tarifa').hide();
        $('#panel_servicio_nombre').text('');
        $('#panel_servicio_descripcion').text('').hide();
    }

    function actualizarPanelServicio(srv) {
        if (!srv) {
            $('#panel_servicio_nombre').text('');
            $('#panel_servicio_descripcion').text('').hide();
            return;
        }

        $('#panel_servicio_nombre').text(srv.nombre || '');

        var descripcion = srv.descripcion;
        if (descripcion !== null && descripcion !== undefined && String(descripcion).trim() !== '') {
            $('#panel_servicio_descripcion').text(descripcion).show();
        } else {
            $('#panel_servicio_descripcion').text('').hide();
        }
    }

    // === Vista movil (panel / fab / modal) ===

    function esVistaMovil() {
        return window.matchMedia('(max-width: 991.98px)').matches;
    }

    function moverPanelAlModal() {
        $('#modal_resumen_compra_body').append($('#panel_compra'));
    }

    function restaurarPanelAlSlot() {
        $('#panel_compra_slot').append($('#panel_compra'));
    }

    function actualizarFabCarrito() {
        if (esVistaMovil() && servicioSeleccionado) {
            $('#btn_carrito_movil').css('display', 'flex');
        } else {
            $('#btn_carrito_movil').hide();
        }
    }

    function mostrarPanelCompra() {
        if (esVistaMovil()) {
            if ($('#modal_resumen_compra').hasClass('show')) {
                cerrarResumenCompraMovil();
            } else {
                restaurarPanelAlSlot();
                $('#panel_compra').hide();
            }
            actualizarFabCarrito();
            return;
        }

        restaurarPanelAlSlot();
        $('#panel_compra').show();
        $('#btn_carrito_movil').hide();
    }

    function abrirResumenCompraMovil() {
        if (!servicioSeleccionado) {
            Swal.fire({
                title: 'Atención',
                text: 'Seleccione un beneficiario y un servicio para ver el resumen.',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        moverPanelAlModal();
        $('#panel_compra').show();

        if (modalResumenCompra) {
            modalResumenCompra.show();
        }
    }

    function cerrarResumenCompraMovil() {
        if (modalResumenCompra && $('#modal_resumen_compra').hasClass('show')) {
            modalResumenCompra.hide();
        }
    }

    function sincronizarVistaCompra() {
        if (!esVistaMovil()) {
            cerrarResumenCompraMovil();
            restaurarPanelAlSlot();
            if (servicioSeleccionado) {
                $('#panel_compra').show();
            } else {
                $('#panel_compra').hide();
            }
            $('#btn_carrito_movil').hide();
            return;
        }

        if ($('#modal_resumen_compra').hasClass('show')) {
            return;
        }

        restaurarPanelAlSlot();
        if (servicioSeleccionado) {
            $('#panel_compra').hide();
            actualizarFabCarrito();
        } else {
            $('#panel_compra').hide();
            $('#btn_carrito_movil').hide();
        }
    }

    // === Cupos ===

    function obtenerCuposMes(data, srv) {
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

    function actualizarCuposMesResumen(cuposMes) {
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

    function mostrarAlertaCuposMesCero() {
        Swal.fire({
            title: 'Sin cupos disponibles',
            html: '<p>No hay cupos disponibles para este servicio en el mes actual.</p>' +
                '<p class="text-muted mb-0">No es posible continuar con la compra.</p>',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    // === ePayco ===

    function obtenerEpaycoHandler() {
        return window.epaycoHandler || null;
    }

    function limpiarSessionEpayco() {
        sessionStorage.removeItem('epayco_cedtra');
        sessionStorage.removeItem('epayco_codser');
        sessionStorage.removeItem('epayco_numero');
        sessionStorage.removeItem('epayco_nota');
        sessionStorage.removeItem('epayco_codben');
        sessionStorage.removeItem('epayco_precompra_id');
    }

    function pagoEpaycoAprobado(datos) {
        return datos && parseInt(datos.cod_estado) === 1 && datos.aprobado === true;
    }

    function obtenerTextoEstadoEpayco(codigo) {
        var estados = {
            1: 'Aceptada', 2: 'Rechazada', 3: 'Pendiente', 4: 'Fallida',
            6: 'Reversada', 7: 'Retenida', 8: 'Iniciada', 9: 'Expirada',
            10: 'Abandonada', 11: 'Cancelada', 12: 'Antifraude'
        };
        return estados[codigo] || 'Desconocido (' + codigo + ')';
    }

    function mostrarPagoNoRegistrado(codEstado, refPayco, motivo) {
        var estadoTexto = obtenerTextoEstadoEpayco(codEstado);
        limpiarSessionEpayco();

        Swal.fire({
            title: 'Pago no completado',
            html: '<p>Estado: <b>' + estadoTexto + '</b></p>' +
                '<p>Referencia: ' + (refPayco || '-') + '</p>' +
                (motivo ? '<p>Motivo: ' + motivo + '</p>' : '') +
                '<p class="text-muted mt-2">La venta no fue registrada.</p>',
            icon: 'warning',
            showConfirmButton: true,
            confirmButtonText: 'Entendido'
        });
    }

    function verificarRespuestaEpayco() {
        var urlParams = window.location.search;
        var refPayco = '';
        var codEstadoUrl = 0;
        var motivoUrl = '';

        if (urlParams) {
            var params = new URLSearchParams(urlParams);
            refPayco = params.get('ref_payco') || params.get('refPayco') || params.get('x_ref_payco') || '';
            codEstadoUrl = parseInt(params.get('x_cod_transaction_state') || params.get('cod_transaction_state') || '0') || 0;
            motivoUrl = params.get('x_response_reason_text') || params.get('x_response') || '';
        }

        if (!refPayco) {
            var path = window.location.pathname;
            var match = path.match(/checkout\/([^/]+)\/response/);
            if (match) {
                refPayco = match[1];
            }
        }

        if (refPayco) {
            if (window.history && window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            if (codEstadoUrl && codEstadoUrl !== 1) {
                mostrarPagoNoRegistrado(codEstadoUrl, refPayco, motivoUrl);
                return;
            }

            Swal.fire({
                title: 'Verificando pago...',
                html: '<p>Referencia: <b>' + refPayco + '</b></p><p>Consultando estado de la transaccion en ePayco...</p>',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false
            });
            validarPagoEpayco(refPayco);
        }
    }

    function validarPagoEpayco(refPayco) {
        $.ajax({
            url: routes.validarPagoEpayco,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {
                ref_payco: refPayco,
                precompra_id: sessionStorage.getItem('epayco_precompra_id') || 0
            }
        }).done(function (response) {
            if (response.success && response.data) {
                var datos = response.data;
                var codEstado = parseInt(datos.cod_estado) || 0;

                if (pagoEpaycoAprobado(datos)) {
                    Swal.fire({
                        title: 'Pago aprobado',
                        html: '<p>Estado: <b>' + obtenerTextoEstadoEpayco(codEstado) + '</b></p>' +
                            '<p>Referencia: <b>' + (datos.ref_payco || refPayco) + '</b></p>' +
                            '<p>Monto: <b>$' + datos.monto + '</b></p>' +
                            '<p>Guardando la venta...</p>',
                        icon: 'success',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    guardarVenta(datos.ref_payco || refPayco);
                    return;
                }

                mostrarPagoNoRegistrado(codEstado, refPayco, datos.motivo || datos.respuesta || 'Sin detalle');
            } else {
                limpiarSessionEpayco();
                Swal.fire({
                    title: 'No se pudo verificar el pago',
                    html: '<p>' + (response.message || 'Error al consultar ePayco') + '</p>' +
                        '<p>La venta no fue registrada.</p>',
                    icon: 'error',
                    showConfirmButton: true,
                    confirmButtonText: 'Entendido'
                });
            }
        }).fail(function () {
            limpiarSessionEpayco();
            Swal.fire({
                title: 'Error de conexion',
                html: '<p>No se pudo verificar el pago con ePayco.</p>' +
                    '<p>La venta no fue registrada.</p>',
                icon: 'error',
                showConfirmButton: true,
                confirmButtonText: 'Entendido'
            });
        });
    }

    // === Trabajador y beneficiarios ===

    function identificarTrabajador() {
        var cedtra = $('#hid_documento').val();

        mostrarLoader('loader_trabajador');
        $('#error_trabajador').hide();
        $('#formulario_servicio').hide();

        $.ajax({
            url: routes.identificarTrabajador,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: { cedtra: cedtra }
        }).done(function (response) {
            ocultarLoader('loader_trabajador');

            if (response.success) {
                var rawTrabajador = response.data.trabajador || response.data;
                if (rawTrabajador && rawTrabajador.trabajador && !rawTrabajador.cedtra) {
                    nucleoFamiliar = rawTrabajador.nucleo_familiar || [];
                    trabajadorData = rawTrabajador.trabajador;
                } else {
                    trabajadorData = rawTrabajador;
                    nucleoFamiliar = response.data.nucleo_familiar || [];
                }
                renderBeneficiariosGrid(nucleoFamiliar, trabajadorData.detcat);
                $('#formulario_servicio').css('display', 'flex');
                cargarServicios();
            } else {
                $('#error_mensaje').text(response.message || 'Trabajador no encontrado');
                $('#error_trabajador').fadeIn();
            }
        }).fail(function () {
            ocultarLoader('loader_trabajador');
            $('#error_mensaje').text('Error de conexion al cargar beneficiarios y servicios');
            $('#error_trabajador').fadeIn();
        });
    }

    function renderBeneficiariosGrid(nucleo, detcat) {
        var grid = $('#grid_beneficiarios');
        grid.empty();

        if (!nucleo || nucleo.length === 0) {
            $('#sin_beneficiarios').show();
            return;
        }

        $('#sin_beneficiarios').hide();

        $.each(nucleo, function (i, ben) {
            var codben = obtenerCodben(ben);
            var tipo = obtenerTipoBeneficiario(ben);
            var categoria = detcat || '';

            var card = $(
                '<button type="button" class="beneficiario-card" role="option" data-codben="' + escapeHtml(codben) + '">' +
                    '<span class="beneficiario-card__tipo">' + escapeHtml(tipo) + '</span>' +
                    '<span class="beneficiario-card__nombre">' + escapeHtml(ben.nombre || '') + '</span>' +
                    '<span class="beneficiario-card__meta">' +
                        '<span><i class="far fa-id-card"></i> ' + escapeHtml(codben) + '</span>' +
                        (ben.edad ? '<span><i class="fas fa-birthday-cake"></i> ' + escapeHtml(String(ben.edad)) + ' años</span>' : '') +
                        (categoria ? '<span>' + escapeHtml(categoria) + '</span>' : '') +
                    '</span>' +
                '</button>'
            );
            card.data('ben', ben);
            grid.append(card);
        });

        if (nucleo.length === 1) {
            seleccionarBeneficiario(obtenerCodben(nucleo[0]), nucleo[0]);
        }
    }

    function seleccionarBeneficiario(codben, benData) {
        if (!codben) {
            beneficiarioSeleccionado = null;
            $('#hid_codben').val('');
            $('.beneficiario-card--selected').removeClass('beneficiario-card--selected');
            limpiarSeleccionServicio();
            return;
        }

        beneficiarioSeleccionado = benData;
        $('#hid_codben').val(codben);
        $('.beneficiario-card').removeClass('beneficiario-card--selected');
        $('.beneficiario-card[data-codben="' + codben + '"]').addClass('beneficiario-card--selected');
        actualizarPanelBeneficiario();
        limpiarSeleccionServicio();
    }

    // === Filtros de servicios ===

    function servicioCoincideBusqueda(srv, query) {
        if (!query) return true;
        var texto = (
            (srv.nombre || '') + ' ' +
            (srv.detalle || '') + ' ' +
            (srv.codser || '') + ' ' +
            (srv.uis_ciudad || '')
        ).toLowerCase();
        return texto.indexOf(query.toLowerCase()) !== -1;
    }

    function servicioCoincideCodser(srv, codser) {
        if (!codser) return true;
        return String(srv.codser) === String(codser);
    }

    function servicioPasaFiltros(srv, query, codser) {
        return servicioCoincideBusqueda(srv, query) && servicioCoincideCodser(srv, codser);
    }

    function initFiltroCodserSelect2() {
        var $select = $('#filtro_codser');

        if (!$select.length || typeof $.fn.select2 === 'undefined') {
            return;
        }

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            allowClear: true,
            placeholder: 'Todos los servicios',
            width: '100%',
            minimumResultsForSearch: 6,
            language: {
                noResults: function () {
                    return 'Sin resultados';
                },
                searching: function () {
                    return 'Buscando...';
                }
            }
        });
    }

    function poblarFiltroCodser(servicios) {
        var select = $('#filtro_codser');
        var valorActual = filtroCodserServicio;
        var opciones = {};

        if (select.hasClass('select2-hidden-accessible')) {
            select.select2('destroy');
        }

        $.each(servicios || [], function (i, srv) {
            if (!srv.codser || opciones[srv.codser]) {
                return;
            }
            opciones[srv.codser] = srv.detalle || srv.nombre || String(srv.codser);
        });

        var items = Object.keys(opciones).map(function (codser) {
            return { codser: codser, detalle: opciones[codser] };
        });

        items.sort(function (a, b) {
            return String(a.detalle).localeCompare(String(b.detalle), 'es', { sensitivity: 'base' });
        });

        select.find('option:not(:first)').remove();

        $.each(items, function (i, item) {
            select.append(
                $('<option></option>')
                    .val(item.codser)
                    .text(item.detalle)
            );
        });

        if (valorActual && opciones[valorActual]) {
            select.val(valorActual);
        } else {
            filtroCodserServicio = '';
            select.val('');
        }

        initFiltroCodserSelect2();
    }

    // === Render de servicios ===

    function contarServiciosDisponibles(servicios, query, codser) {
        var count = 0;
        $.each(servicios, function (i, srv) {
            var cupos = parseInt(srv.cupos_disponibles, 10) || 0;
            if (cupos > 0 && servicioPasaFiltros(srv, query, codser)) {
                count++;
            }
        });
        return count;
    }

    function renderServiciosGrid(servicios, query, codser) {
        var grid = $('#grid_servicios');
        grid.empty();
        query = query || '';
        codser = codser || '';
        busquedaServicio = query;
        filtroCodserServicio = codser;

        var visibles = 0;
        var disponibles = contarServiciosDisponibles(servicios, query, codser);

        $('#contador_servicios').text(
            disponibles + ' servicio' + (disponibles === 1 ? '' : 's') + ' disponible' + (disponibles === 1 ? '' : 's')
        );

        $.each(servicios, function (i, srv) {
            if (!servicioPasaFiltros(srv, query, codser)) {
                return;
            }

            visibles++;
            var cupos = parseInt(srv.cupos_disponibles, 10) || 0;
            var sinCupos = cupos <= 0;
            var valmes = srv.valmes === 'S';
            var cardKey = srv.codser + '|' + srv.numero;
            var ciudadHtml = srv.uis_ciudad
                ? '<span><i class="fas fa-map-marker-alt"></i> ' + escapeHtml(srv.uis_ciudad) + '</span>'
                : '';

            var card = $(
                '<button type="button" class="servicio-card' + (sinCupos ? ' servicio-card--disabled' : '') + '"' +
                    ' role="option"' +
                    ' data-key="' + escapeHtml(cardKey) + '"' +
                    (sinCupos ? ' disabled' : '') + '>' +
                    '<div class="servicio-card__header">' +
                        '<span class="servicio-card__badge' + (sinCupos ? ' servicio-card__badge--muted' : '') + '">' +
                            (sinCupos ? 'Sin cupos' : cupos + ' cupos') +
                        '</span>' +
                        (valmes ? '<span class="servicio-card__badge servicio-card__badge--info">Mensual</span>' : '') +
                    '</div>' +
                    '<h3 class="servicio-card__titulo">' + escapeHtml(srv.nombre || '') + '</h3>' +
                    '<p class="servicio-card__detalle">' + escapeHtml(srv.detalle || '') + '</p>' +
                    '<div class="servicio-card__meta">' +
                        ciudadHtml +
                        '<span><i class="fas fa-child"></i> ' + escapeHtml(String(srv.edadini)) + '–' + escapeHtml(String(srv.edadfin)) + ' años</span>' +
                        '<span><i class="far fa-calendar-alt"></i> ' + escapeHtml(srv.fecini || '') + ' – ' + escapeHtml(srv.fecfin || '') + '</span>' +
                    '</div>' +
                '</button>'
            );

            card.data('srv', srv);
            grid.append(card);

            if (servicioSeleccionado &&
                String(servicioSeleccionado.codser) === String(srv.codser) &&
                String(servicioSeleccionado.numero) === String(srv.numero) &&
                !sinCupos) {
                card.addClass('servicio-card--selected');
            }
        });

        $('#sin_servicios').toggle(visibles === 0);
    }

    function filtrarServicios() {
        var query = $('#buscar_servicio').val().trim();
        var codser = $('#filtro_codser').val();

        if (servicioSeleccionado && !servicioPasaFiltros(servicioSeleccionado, query, codser)) {
            limpiarSeleccionServicio();
        }

        renderServiciosGrid(serviciosData, query, codser);
    }

    function cargarServicios() {
        mostrarLoader('loader_servicios');
        $('#contenedor_servicios').hide();

        $.ajax({
            url: routes.listarServicios,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {}
        }).done(function (response) {
            ocultarLoader('loader_servicios');

            if (response.success) {
                serviciosData = response.data || [];
                poblarFiltroCodser(serviciosData);
                renderServiciosGrid(serviciosData, busquedaServicio, filtroCodserServicio);
                $('#contenedor_servicios').css('display', 'flex');
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message || 'No se pudieron cargar los servicios',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 5000
                });
            }
        }).fail(function () {
            ocultarLoader('loader_servicios');
            Swal.fire({
                title: 'Error',
                text: 'Error de conexion al cargar servicios',
                icon: 'error',
                showConfirmButton: false,
                timer: 5000
            });
        });
    }

    function seleccionarServicio(srv) {
        var codben = $('#hid_codben').val();

        if (!codben || !beneficiarioSeleccionado) {
            Swal.fire({
                title: 'Atención',
                text: 'Debe seleccionar un beneficiario antes de elegir un servicio.',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        servicioSeleccionado = srv;
        var cardKey = srv.codser + '|' + srv.numero;

        $('.servicio-card').removeClass('servicio-card--selected');
        $('.servicio-card[data-key="' + cardKey + '"]').addClass('servicio-card--selected');

        mostrarPanelCompra();
        actualizarPanelBeneficiario();
        actualizarPanelServicio(srv);
        $('#detalle_tarifa').hide();
        $('#error_tarifa').hide();

        validarTarifa(srv.codser, srv.numero);
    }

    // === Tarifa ===

    function validarTarifa(codser, numero) {
        var cedtra = $('#hid_documento').val();

        $('#detalle_tarifa').hide();
        $('#error_tarifa').hide();
        $('#txt_cupos_mes').text('-').removeClass('panel-compra__cupos-mes--cero');
        $('#btn_procesar_pago').prop('disabled', false).show();
        mostrarLoader('loader_tarifa');

        $('#hid_codser').val(codser);
        $('#hid_numero').val(numero);

        $.ajax({
            url: routes.validarTarifa,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {
                cedtra: cedtra,
                codser: codser,
                numero: numero,
                codben: $('#hid_codben').val() || cedtra
            }
        }).done(function (response) {
            ocultarLoader('loader_tarifa');

            if (response.success) {
                var data = response.data;
                var cuposMes = obtenerCuposMes(data, servicioSeleccionado);

                $('#txt_valor').text(formatearValor(data.valser));
                $('#hid_valor_raw').val(data.valser);
                $('#txt_tarifa_categoria').val(data.categoria || '');
                $('#txt_temporada').val(data.temporada || '');
                $('#txt_tarifa_cupos').val(data.cupos_disponibles || '0');

                var puedeComprar = actualizarCuposMesResumen(cuposMes);
                $('#detalle_tarifa').fadeIn();

                if (!puedeComprar) {
                    mostrarAlertaCuposMesCero();
                }

                actualizarFabCarrito();
            } else {
                $('#error_tarifa_msg').text(response.message || 'No se pudo validar la tarifa');
                $('#error_tarifa').fadeIn();
                actualizarFabCarrito();
            }
        }).fail(function () {
            ocultarLoader('loader_tarifa');
            $('#error_tarifa_msg').text('Error de conexion al validar tarifa');
            $('#error_tarifa').fadeIn();
            actualizarFabCarrito();
        });
    }

    // === Venta ===

    function guardarVenta(refpago) {
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
            url: routes.guardarVenta,
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
                    renderServiciosGrid(serviciosData, busquedaServicio, filtroCodserServicio);
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

    // === Procesar pago (precompra + checkout ePayco) ===

    function procesarPago(event) {
        event.preventDefault();
        var target = $(event.currentTarget);
        target.attr('disabled', true);

        var valor = $('#hid_valor_raw').val();
        if (!valor || parseFloat(valor) <= 0) {
            Swal.fire({
                title: 'Atencion',
                text: 'No se ha obtenido el valor del servicio',
                icon: 'warning',
                showConfirmButton: false,
                timer: 3000
            });
            target.removeAttr('disabled');
            return;
        }

        var cuposMes = obtenerCuposMes(null, servicioSeleccionado);
        if (cuposMes === null && $('#hid_cupos_mes').val() !== '') {
            cuposMes = parseInt($('#hid_cupos_mes').val(), 10) || 0;
        }

        if (cuposMes === 0) {
            mostrarAlertaCuposMesCero();
            target.removeAttr('disabled');
            return;
        }

        var epaycoHandler = obtenerEpaycoHandler();
        if (!epaycoHandler) {
            Swal.fire({
                title: 'Error',
                text: 'No se pudo inicializar la pasarela de pago. Recargue la pagina.',
                icon: 'error',
                showConfirmButton: false,
                timer: 5000
            });
            target.removeAttr('disabled');
            return;
        }

        var nombre = sanitizarTexto((trabajadorData && trabajadorData.nombre) ? trabajadorData.nombre : 'Cliente');
        var email = (trabajadorData && trabajadorData.email) ? trabajadorData.email.trim() : 'sin@email.com';
        var documento = $('#hid_documento').val();
        var invoice = 'ORD' + Date.now();
        var servicioNombre = 'Compra de servicio';

        if (servicioSeleccionado && servicioSeleccionado.nombre) {
            servicioNombre = sanitizarTexto(servicioSeleccionado.nombre);
        }

        var data = {
            name: servicioNombre,
            description: servicioNombre,
            invoice: invoice,
            currency: 'cop',
            amount: valor,
            tax_base: '0',
            tax: '0',
            country: 'co',
            lang: 'es',
            external: 'false',
            extra1: documento,
            extra2: $('#hid_codser').val(),
            extra3: $('#hid_numero').val(),
            response: window.location.href,
            name_billing: nombre,
            type_doc_billing: 'cc',
            number_doc_billing: documento,
            email_billing: email
        };

        sessionStorage.setItem('epayco_cedtra', documento);
        sessionStorage.setItem('epayco_codser', $('#hid_codser').val());
        sessionStorage.setItem('epayco_numero', $('#hid_numero').val());
        sessionStorage.setItem('epayco_nota', $('#txt_nota').val() || '');
        sessionStorage.setItem('epayco_codben', $('#hid_codben').val() || documento);

        // Respaldar la precompra en base de datos antes de abrir la pasarela
        $.ajax({
            url: routes.crearPrecompra,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {
                cedtra: documento,
                codser: $('#hid_codser').val(),
                numero: $('#hid_numero').val(),
                codben: $('#hid_codben').val() || documento,
                nota: $('#txt_nota').val() || '',
                valor: valor
            }
        }).done(function (response) {
            if (response.success && response.data && response.data.id) {
                sessionStorage.setItem('epayco_precompra_id', response.data.id);
                epaycoHandler.open(data);
            } else {
                Swal.fire({
                    title: 'No se pudo iniciar el pago',
                    text: response.message || 'Error al registrar la precompra. Intente nuevamente.',
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
            }
            target.removeAttr('disabled');
        }).fail(function () {
            Swal.fire({
                title: 'Error de conexion',
                text: 'No se pudo registrar la precompra. Intente nuevamente.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
            target.removeAttr('disabled');
        });
    }

    // === Bind de eventos e inicializacion ===

    function bindHandlers() {
        var modalEl = document.getElementById('modal_resumen_compra');
        if (modalEl && typeof bootstrap !== 'undefined') {
            modalResumenCompra = new bootstrap.Modal(modalEl);
        }

        $('#btn_carrito_movil').on('click', abrirResumenCompraMovil);

        $('#modal_resumen_compra').on('hidden.bs.modal', function () {
            restaurarPanelAlSlot();
            if (esVistaMovil()) {
                $('#panel_compra').hide();
                actualizarFabCarrito();
            }
        });

        var resizeTimer;
        $(window).on('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(sincronizarVistaCompra, 150);
        });

        $(document).on('click', '.beneficiario-card', function () {
            var ben = $(this).data('ben');
            var codben = obtenerCodben(ben);
            if ($(this).hasClass('beneficiario-card--selected')) {
                return;
            }
            seleccionarBeneficiario(codben, ben);
        });

        $(document).on('input', '#buscar_servicio', function () {
            filtrarServicios();
        });

        $(document).on('change', '#filtro_codser', function () {
            filtrarServicios();
        });

        $(document).on('click', '.servicio-card:not(.servicio-card--disabled)', function () {
            var srv = $(this).data('srv');
            if (!srv) return;
            seleccionarServicio(srv);
        });

        $(document).on('click', '#btn_procesar_pago', procesarPago);
    }

    function init() {
        routes = window.routes || {};

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        bindHandlers();
        identificarTrabajador();
        verificarRespuestaEpayco();
    }

    return { init };
})();

$(function () {
    EcommerceModule.init();
});
