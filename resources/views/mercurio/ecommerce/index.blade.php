@extends('layouts.bone')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://checkout.epayco.co/checkout.js"></script>

<div class="col mt-2 servicios-catalog">
    <div class="card shadow-sm">
        <div class="card-header servicios-catalog__header py-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <h1 class="servicios-catalog__title mb-1">Catálogo de Servicios</h1>
                    <p class="servicios-catalog__subtitle mb-0">
                        Elija el beneficiario, seleccione un servicio y complete el pago.
                    </p>
                </div>
                <a href="{{ route('servicios.ver-compras') }}" class="btn btn-sm servicios-catalog__btn-compras align-self-start align-self-md-center">
                    <i class="fas fa-receipt"></i> Mis compras
                </a>
            </div>
        </div>

        <div class="card-body">
            <div id="loader_trabajador" class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Cargando beneficiarios y servicios...</p>
            </div>

            <div id="error_trabajador" class="text-center py-5" style="display:none;">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 60px;"></i>
                <p id="error_mensaje" class="mt-3 servicios-catalog__error-msg"></p>
                <a id="btn_volver_error" href="{{ route('principal.index') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div id="formulario_servicio" style="display:none;">
                <section class="servicios-catalog__section mb-4">
                    <h2 class="servicios-catalog__section-title">
                        <i class="fas fa-users"></i> Beneficiarios disponibles
                    </h2>
                    <p class="servicios-catalog__section-desc">Seleccione para quién adquirirá el servicio.</p>
                    <div id="grid_beneficiarios" class="beneficiarios-grid" role="listbox" aria-label="Beneficiarios disponibles"></div>
                    <p id="sin_beneficiarios" class="text-muted text-center py-3" style="display:none;">
                        No hay beneficiarios disponibles en su núcleo familiar.
                    </p>
                </section>

                <div class="servicios-catalog__main">
                    <div class="servicios-catalog__content">
                        <section class="servicios-catalog__section">
                            <h2 class="servicios-catalog__section-title">
                                <i class="fas fa-store"></i> Servicios activos
                            </h2>

                            <div id="loader_servicios" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="ml-2 text-muted">Cargando servicios...</span>
                            </div>

                            <div id="contenedor_servicios" class="servicios-catalog__servicios-wrap" style="display:none;">
                                <div class="servicios-catalog__toolbar">
                                    <div class="servicios-catalog__filters">
                                        <div class="input-group servicios-catalog__search">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input
                                                type="search"
                                                id="buscar_servicio"
                                                class="form-control"
                                                placeholder="Buscar por nombre, categoría o código..."
                                                autocomplete="off"
                                            >
                                        </div>
                                        <div class="servicios-catalog__filter-wrap">
                                            <select
                                                id="filtro_codser"
                                                class="form-control servicios-catalog__filter"
                                                data-toggle="select"
                                                aria-label="Filtrar por servicio"
                                            >
                                                <option value="">Todos los servicios</option>
                                            </select>
                                        </div>
                                    </div>
                                    <span id="contador_servicios" class="servicios-catalog__counter badge"></span>
                                </div>

                                <div class="servicios-catalog__grid-scroll">
                                    <div id="grid_servicios" class="servicios-grid" role="listbox" aria-label="Servicios disponibles"></div>
                                    <p id="sin_servicios" class="text-muted text-center py-4" style="display:none;">
                                        No hay servicios que coincidan con su búsqueda.
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div id="panel_compra_slot" class="panel-compra-slot">
                        <aside id="panel_compra" class="panel-compra" style="display:none;">
                            <div class="panel-compra__inner">
                                <h3 class="panel-compra__title">Resumen de compra</h3>

                                <div id="panel_beneficiario" class="panel-compra__beneficiario" style="display:none;">
                                    <span class="panel-compra__beneficiario-label">Beneficiario</span>
                                    <span id="panel_beneficiario_nombre" class="panel-compra__beneficiario-nombre"></span>
                                    <span id="panel_beneficiario_doc" class="panel-compra__beneficiario-doc"></span>
                                </div>

                                <p id="panel_servicio_nombre" class="panel-compra__servicio"></p>

                                <div id="loader_tarifa" class="text-center py-3" style="display:none;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    <span class="ml-2 text-muted">Validando tarifa...</span>
                                </div>

                                <div id="error_tarifa" class="alert alert-warning py-2" style="display:none;">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span id="error_tarifa_msg"></span>
                                </div>

                                <div id="detalle_tarifa" style="display:none;">
                                    <div class="panel-compra__valor-wrap">
                                        <span class="panel-compra__valor-label">Valor del servicio</span>
                                        <span id="txt_valor" class="panel-compra__valor"></span>
                                        <input type="hidden" id="hid_valor_raw">
                                    </div>

                                    <div class="panel-compra__cupos-wrap mt-2">
                                        <span class="panel-compra__valor-label">Cupos disponibles este mes</span>
                                        <span id="txt_cupos_mes" class="panel-compra__cupos-mes">-</span>
                                    </div>

                                    <input type="hidden" id="txt_tarifa_categoria">
                                    <input type="hidden" id="txt_temporada">
                                    <input type="hidden" id="txt_tarifa_cupos">
                                    <input type="hidden" id="hid_cupos_mes" value="">

                                    <div class="form-group mt-3">
                                        <label for="txt_nota" class="form-label">Nota (opcional)</label>
                                        <textarea id="txt_nota" class="form-control" rows="2" placeholder="Escriba una nota si lo desea..."></textarea>
                                    </div>

                                    <button type="button" id="btn_procesar_pago" class="btn btn-primary btn-lg w-100 mt-3">
                                        <i class="fas fa-credit-card"></i> Procesar pago
                                    </button>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="button" id="btn_carrito_movil" class="servicios-cart-fab" aria-label="Ver resumen de compra" style="display:none;">
        <i class="fas fa-shopping-cart"></i>
    </button>

    <div class="modal fade" id="modal_resumen_compra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resumen de compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="modal_resumen_compra_body"></div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="hid_documento" value="{{ $documento }}">
<input type="hidden" id="hid_codser" value="">
<input type="hidden" id="hid_numero" value="">
<input type="hidden" id="hid_codben" value="">
@endsection

@push('scripts')
<script>
    var EPAYCO_PUBLIC_KEY = '{{ $EPAYCO_PUBLIC_KEY }}';
    var EPAYCO_TEST = {{ $EPAYCO_TEST ? 'true' : 'false' }};

    var epaycoHandler = null;

    try {
        epaycoHandler = ePayco.checkout.configure({
            key: EPAYCO_PUBLIC_KEY,
            test: EPAYCO_TEST
        });
    } catch (e) {
        console.log('Error inicializando ePayco:', e);
    }

    var trabajadorData = null;
    var nucleoFamiliar = [];
    var beneficiarioSeleccionado = null;
    var serviciosData = [];
    var servicioSeleccionado = null;
    var busquedaServicio = '';
    var filtroCodserServicio = '';
    var modalResumenCompra = null;

    var TIPOS_BEN = { T: 'Trabajador', C: 'Conyuge', B: 'Beneficiario' };

    var routes = {
        identificarTrabajador: "{{ route('servicios.identificar-trabajador') }}",
        listarServicios: "{{ route('servicios.listar-servicios') }}",
        validarTarifa: "{{ route('servicios.validar-tarifa') }}",
        validarPagoEpayco: "{{ route('servicios.validar-pago-epayco') }}",
        guardarVenta: "{{ route('servicios.guardar-venta') }}",
    };

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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
    }

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

    function limpiarSessionEpayco() {
        sessionStorage.removeItem('epayco_cedtra');
        sessionStorage.removeItem('epayco_codser');
        sessionStorage.removeItem('epayco_numero');
        sessionStorage.removeItem('epayco_nota');
        sessionStorage.removeItem('epayco_codben');
    }

    function pagoEpaycoAprobado(datos) {
        return datos && parseInt(datos.cod_estado) === 1 && datos.aprobado === true;
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
            var match = path.match(/checkout\/([^\/]+)\/response/);
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
            data: { ref_payco: refPayco }
        }).done(function(response) {
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
        }).fail(function() {
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

    function obtenerTextoEstadoEpayco(codigo) {
        var estados = {
            1: 'Aceptada', 2: 'Rechazada', 3: 'Pendiente', 4: 'Fallida',
            6: 'Reversada', 7: 'Retenida', 8: 'Iniciada', 9: 'Expirada',
            10: 'Abandonada', 11: 'Cancelada', 12: 'Antifraude'
        };
        return estados[codigo] || 'Desconocido (' + codigo + ')';
    }

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
        }).done(function(response) {
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
        }).fail(function() {
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

        $.each(nucleo, function(i, ben) {
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
                noResults: function() {
                    return 'Sin resultados';
                },
                searching: function() {
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

        $.each(servicios || [], function(i, srv) {
            if (!srv.codser || opciones[srv.codser]) {
                return;
            }
            opciones[srv.codser] = srv.detalle || srv.nombre || String(srv.codser);
        });

        var items = Object.keys(opciones).map(function(codser) {
            return { codser: codser, detalle: opciones[codser] };
        });

        items.sort(function(a, b) {
            return String(a.detalle).localeCompare(String(b.detalle), 'es', { sensitivity: 'base' });
        });

        select.find('option:not(:first)').remove();

        $.each(items, function(i, item) {
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

    function contarServiciosDisponibles(servicios, query, codser) {
        var count = 0;
        $.each(servicios, function(i, srv) {
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

        $.each(servicios, function(i, srv) {
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
        }).done(function(response) {
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
        }).fail(function() {
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
        $('#panel_servicio_nombre').text(srv.nombre || '');
        $('#detalle_tarifa').hide();
        $('#error_tarifa').hide();

        validarTarifa(srv.codser, srv.numero);
    }

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
        }).done(function(response) {
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
        }).fail(function() {
            ocultarLoader('loader_tarifa');
            $('#error_tarifa_msg').text('Error de conexion al validar tarifa');
            $('#error_tarifa').fadeIn();
            actualizarFabCarrito();
        });
    }

    function guardarVenta(refpago) {
        var cedtra = $('#hid_documento').val() || sessionStorage.getItem('epayco_cedtra') || '';
        var codser = $('#hid_codser').val() || sessionStorage.getItem('epayco_codser') || '';
        var numero = $('#hid_numero').val() || sessionStorage.getItem('epayco_numero') || '';
        var nota = $('#txt_nota').val() || sessionStorage.getItem('epayco_nota') || '';
        var codben = $('#hid_codben').val() || sessionStorage.getItem('epayco_codben') || cedtra;

        sessionStorage.removeItem('epayco_cedtra');
        sessionStorage.removeItem('epayco_codser');
        sessionStorage.removeItem('epayco_numero');
        sessionStorage.removeItem('epayco_nota');
        sessionStorage.removeItem('epayco_codben');

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
            data: { cedtra, codser, numero, refpago, nota, codben }
        }).done(function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Compra exitosa',
                    html: '<p style="font-size:1em">' + (response.message || 'Venta guardada exitosamente') + '</p>',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 5000
                });
                setTimeout(function() {
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
        }).fail(function() {
            Swal.fire({
                title: 'Error',
                text: 'Error de conexion al guardar la venta',
                icon: 'error',
                showConfirmButton: false,
                timer: 5000
            });
        });
    }

    $(document).ready(function() {
        var modalEl = document.getElementById('modal_resumen_compra');
        if (modalEl && typeof bootstrap !== 'undefined') {
            modalResumenCompra = new bootstrap.Modal(modalEl);
        }

        $('#btn_carrito_movil').on('click', abrirResumenCompraMovil);

        $('#modal_resumen_compra').on('hidden.bs.modal', function() {
            restaurarPanelAlSlot();
            if (esVistaMovil()) {
                $('#panel_compra').hide();
                actualizarFabCarrito();
            }
        });

        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(sincronizarVistaCompra, 150);
        });

        identificarTrabajador();
        verificarRespuestaEpayco();

        $(document).on('click', '.beneficiario-card', function() {
            var ben = $(this).data('ben');
            var codben = obtenerCodben(ben);
            if ($(this).hasClass('beneficiario-card--selected')) {
                return;
            }
            seleccionarBeneficiario(codben, ben);
        });

        $(document).on('input', '#buscar_servicio', function() {
            filtrarServicios();
        });

        $(document).on('change', '#filtro_codser', function() {
            filtrarServicios();
        });

        $(document).on('click', '.servicio-card:not(.servicio-card--disabled)', function() {
            var srv = $(this).data('srv');
            if (!srv) return;
            seleccionarServicio(srv);
        });

        $(document).on('click', '#btn_procesar_pago', function(event) {
            event.preventDefault();
            var target = $(this);
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

            epaycoHandler.open(data);
            target.removeAttr('disabled');
        });
    });
</script>
@endpush
