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
                <a href="{{ route('servicios.ver-compras') }}" class="btn btn-outline-light btn-sm align-self-start align-self-md-center">
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

                    <aside id="panel_compra" class="panel-compra" style="display:none;">
                        <div class="panel-compra__inner">
                            <h3 class="panel-compra__title">Resumen de compra</h3>
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

                                <input type="hidden" id="txt_tarifa_categoria">
                                <input type="hidden" id="txt_temporada">
                                <input type="hidden" id="txt_tarifa_cupos">

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

    function limpiarSeleccionServicio() {
        servicioSeleccionado = null;
        $('#hid_codser').val('');
        $('#hid_numero').val('');
        $('.servicio-card--selected').removeClass('servicio-card--selected');
        $('#panel_compra').hide();
        $('#detalle_tarifa').hide();
        $('#error_tarifa').hide();
        $('#panel_servicio_nombre').text('');
    }

    function verificarRespuestaEpayco() {
        var urlParams = window.location.search;
        var refPayco = '';

        if (urlParams) {
            var params = new URLSearchParams(urlParams);
            refPayco = params.get('ref_payco') || params.get('refPayco') || params.get('x_ref_payco') || '';
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
                var estadoTexto = obtenerTextoEstadoEpayco(codEstado);
                var motivo = datos.motivo || datos.respuesta || 'Sin detalle';

                if (codEstado === 10 || codEstado === 11) {
                    Swal.fire({
                        title: 'Transaccion ' + (codEstado === 11 ? 'cancelada' : 'abandonada'),
                        html: '<p>Estado: <b>' + estadoTexto + '</b></p>' +
                            '<p>Referencia: ' + refPayco + '</p>' +
                            '<p class="text-muted mt-2">La venta NO fue registrada. Puede intentar nuevamente.</p>',
                        icon: 'error',
                        showConfirmButton: true,
                        confirmButtonText: 'Entendido'
                    });
                    sessionStorage.removeItem('epayco_cedtra');
                    sessionStorage.removeItem('epayco_codser');
                    sessionStorage.removeItem('epayco_numero');
                    sessionStorage.removeItem('epayco_nota');
                    sessionStorage.removeItem('epayco_codben');
                    return;
                }

                if (codEstado === 1) {
                    Swal.fire({
                        title: 'Pago aprobado',
                        html: '<p>Estado: <b>' + estadoTexto + '</b></p>' +
                            '<p>Referencia: <b>' + (datos.ref_payco || refPayco) + '</b></p>' +
                            '<p>Monto: <b>$' + datos.monto + '</b></p>' +
                            '<p>Guardando la venta...</p>',
                        icon: 'success',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    guardarVenta(datos.ref_payco || refPayco);
                } else {
                    Swal.fire({
                        title: 'Estado ePayco: ' + estadoTexto,
                        html: '<p>Motivo: ' + motivo + '</p>' +
                            '<p>Referencia: ' + refPayco + '</p>' +
                            '<hr><p class="text-info">Se registrara la venta.</p>',
                        icon: 'warning',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        timer: 4000
                    });
                    setTimeout(function() { guardarVenta(refPayco); }, 2000);
                }
            } else {
                Swal.fire({
                    title: 'No se pudo verificar el pago',
                    html: '<p>' + (response.message || 'Error al consultar ePayco') + '</p>' +
                        '<p>Referencia: ' + refPayco + '</p>' +
                        '<hr><p class="text-info">Se registrara la venta de todas formas.</p>',
                    icon: 'warning',
                    showConfirmButton: false,
                    timer: 4000
                });
                setTimeout(function() { guardarVenta(refPayco); }, 2000);
            }
        }).fail(function() {
            Swal.fire({
                title: 'Error de conexion',
                html: '<p>No se pudo verificar el pago con ePayco.</p>' +
                    '<p>Referencia: ' + refPayco + '</p>' +
                    '<hr><p class="text-info">Se registrara la venta de todas formas.</p>',
                icon: 'warning',
                showConfirmButton: false,
                timer: 4000
            });
            setTimeout(function() { guardarVenta(refPayco); }, 2000);
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
        limpiarSeleccionServicio();
    }

    function servicioCoincideBusqueda(srv, query) {
        if (!query) return true;
        var texto = (
            (srv.nombre || '') + ' ' +
            (srv.detalle || '') + ' ' +
            (srv.codser || '')
        ).toLowerCase();
        return texto.indexOf(query.toLowerCase()) !== -1;
    }

    function contarServiciosDisponibles(servicios, query) {
        var count = 0;
        $.each(servicios, function(i, srv) {
            var cupos = parseInt(srv.cupos_disponibles, 10) || 0;
            if (cupos > 0 && servicioCoincideBusqueda(srv, query)) {
                count++;
            }
        });
        return count;
    }

    function renderServiciosGrid(servicios, query) {
        var grid = $('#grid_servicios');
        grid.empty();
        query = query || '';
        busquedaServicio = query;

        var visibles = 0;
        var disponibles = contarServiciosDisponibles(servicios, query);

        $('#contador_servicios').text(
            disponibles + ' servicio' + (disponibles === 1 ? '' : 's') + ' disponible' + (disponibles === 1 ? '' : 's')
        );

        $.each(servicios, function(i, srv) {
            if (!servicioCoincideBusqueda(srv, query)) {
                return;
            }

            visibles++;
            var cupos = parseInt(srv.cupos_disponibles, 10) || 0;
            var sinCupos = cupos <= 0;
            var valmes = srv.valmes === 'S';
            var cardKey = srv.codser + '|' + srv.numero;

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

    function filtrarServicios(query) {
        renderServiciosGrid(serviciosData, query);
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
                renderServiciosGrid(serviciosData, busquedaServicio);
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
        servicioSeleccionado = srv;
        var cardKey = srv.codser + '|' + srv.numero;

        $('.servicio-card').removeClass('servicio-card--selected');
        $('.servicio-card[data-key="' + cardKey + '"]').addClass('servicio-card--selected');

        $('#panel_compra').show();
        $('#panel_servicio_nombre').text(srv.nombre || '');
        $('#detalle_tarifa').hide();
        $('#error_tarifa').hide();

        validarTarifa(srv.codser, srv.numero);
    }

    function validarTarifa(codser, numero) {
        var cedtra = $('#hid_documento').val();

        $('#detalle_tarifa').hide();
        $('#error_tarifa').hide();
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
                $('#txt_valor').text(formatearValor(data.valser));
                $('#hid_valor_raw').val(data.valser);
                $('#txt_tarifa_categoria').val(data.categoria || '');
                $('#txt_temporada').val(data.temporada || '');
                $('#txt_tarifa_cupos').val(data.cupos_disponibles || '0');
                $('#detalle_tarifa').fadeIn();
            } else {
                $('#error_tarifa_msg').text(response.message || 'No se pudo validar la tarifa');
                $('#error_tarifa').fadeIn();
            }
        }).fail(function() {
            ocultarLoader('loader_tarifa');
            $('#error_tarifa_msg').text('Error de conexion al validar tarifa');
            $('#error_tarifa').fadeIn();
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
                    renderServiciosGrid(serviciosData, busquedaServicio);
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
            filtrarServicios($(this).val().trim());
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
