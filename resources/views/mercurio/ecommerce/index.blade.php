@extends('layouts.dash')

@section('content')
<!-- Epayco Standard Checkout JS -->
<script src="https://checkout.epayco.co/checkout.js"></script>

<div class="col mt-2">
    <div class="card">
        <div class="card-header py-2" style="background-color: #3f51b5; color: white;">
            <b>Compra de Servicio</b>
        </div>

        <div class="card-body">
            <div class="col-xs-12">

                <!-- ============================================ -->
                <!-- LOADER: Se muestra mientras carga el trabajador -->
                <!-- ============================================ -->
                <div id="loader_trabajador" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Verificando datos del trabajador...</p>
                </div>

                <!-- ============================================ -->
                <!-- ERROR: Se muestra si falla la identificacion -->
                <!-- ============================================ -->
                <div id="error_trabajador" class="text-center py-5" style="display:none;">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 60px;"></i>
                    <p id="error_mensaje" class="mt-3" style="font-size: 16px; color: #e65100; font-weight: 500;"></p>
                    <a id="btn_volver_error" href="{{ route('principal.index') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <!-- ============================================ -->
                <!-- FORMULARIO: Se muestra cuando el trabajador es valido -->
                <!-- ============================================ -->
                <div id="formulario_servicio" style="display:none;">

                    <!-- Datos del trabajador (solo lectura) -->
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white py-2">
                            <b>Datos del Trabajador</b>
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-control-label"><b>Documento</b></label>
                                        <input type="text" id="txt_cedtra" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group mb-2">
                                        <label class="form-control-label"><b>Nombre</b></label>
                                        <input type="text" id="txt_nombre" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-2">
                                        <label class="form-control-label"><b>Estado</b></label>
                                        <input type="text" id="txt_estado" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-2">
                                        <label class="form-control-label"><b>Categoria</b></label>
                                        <input type="text" id="txt_categoria" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-control-label"><b>Empresa</b></label>
                                        <input type="text" id="txt_empresa" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-2">
                                        <label class="form-control-label"><b>Edad</b></label>
                                        <input type="text" id="txt_edad" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-control-label"><b>Tipo Beneficiario</b></label>
                                        <input type="text" id="txt_tipbenef" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-control-label"><b>NIT</b></label>
                                        <input type="text" id="txt_nit" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seleccion de beneficiario (nucleo familiar) -->
                    <div class="card mb-3">
                        <div class="card-header text-white py-2" style="background-color: #3F51B5">
                            <b>Seleccionar Beneficiario</b>
                        </div>
                        <div class="card-body py-2">
                            <div id="loader_nucleo" class="text-center py-3" style="display:none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="ml-2 text-muted">Cargando nucleo familiar...</span>
                            </div>

                            <div id="contenedor_nucleo">
                                <div class="form-group mb-2">
                                    <label for="sel_beneficiario" class="form-control-label"><b>Beneficiario del servicio</b></label>
                                    <select id="sel_beneficiario" class="form-control">
                                        <option value="">-- Seleccione un beneficiario --</option>
                                    </select>
                                </div>
                                <div id="info_beneficiario" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <small class="text-muted">Documento:</small>
                                            <span id="txt_ben_documento" class="font-weight-bold"></span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Parentesco:</small>
                                            <span id="txt_ben_parentesco" class="font-weight-bold"></span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Edad:</small>
                                            <span id="txt_ben_edad" class="font-weight-bold"></span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Categoria:</small>
                                            <span id="txt_ben_categoria" class="font-weight-bold"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seleccion de servicio -->
                    <div class="card mb-3">
                        <div class="card-header text-white py-2" style="background-color: #afafaf">
                            <b>Seleccionar Servicio</b>
                        </div>
                        <div class="card-body">

                            <div id="loader_servicios" class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="ml-2 text-muted">Cargando servicios...</span>
                            </div>

                            <div id="contenedor_servicios" style="display:none;">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="sel_servicio" class="form-control-label"><b>Servicio</b></label>
                                            <select id="sel_servicio" class="form-control">
                                                <option value="">-- Seleccione un servicio --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"><b>Cupos disponibles</b></label>
                                            <input type="text" id="txt_cupos" class="form-control" readonly placeholder="-">
                                        </div>
                                    </div>
                                </div>

                                <div id="loader_tarifa" class="text-center py-3" style="display:none;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    <span class="ml-2 text-muted">Validando tarifa...</span>
                                </div>

                                <div id="error_tarifa" class="alert alert-warning text-center" style="display:none;">
                                    <i class="fas fa-exclamation-circle"></i> <span id="error_tarifa_msg"></span>
                                </div>

                                <div id="detalle_tarifa" style="display:none;">
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label"><b>Valor del servicio</b></label>
                                                <input type="text" id="txt_valor" class="form-control font-weight-bold text-success" readonly>
                                                <input type="hidden" id="hid_valor_raw">
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label for="txt_nota" class="form-control-label"><b>Nota (opcional)</b></label>
                                                <textarea id="txt_nota" class="form-control" rows="2" placeholder="Escriba una nota si lo desea..."></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="display:none;">
                                            <div class="form-group">
                                                <label class="form-control-label"><b>Categoria</b></label>
                                                <input type="text" id="txt_tarifa_categoria" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="display:none;">
                                            <div class="form-group">
                                                <label class="form-control-label"><b>Temporada</b></label>
                                                <input type="text" id="txt_temporada" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="display:none;">
                                            <div class="form-group">
                                                <label class="form-control-label"><b>Cupos</b></label>
                                                <input type="text" id="txt_tarifa_cupos" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mt-3">
                                        <button type="button" id="btn_procesar_pago" class="btn btn-primary btn-lg">
                                            <i class="fas fa-credit-card"></i> Procesar Pago
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /formulario_servicio -->

            </div>
        </div>
    </div>
</div>

<!-- Variables ocultas -->
<input type="hidden" id="hid_documento" value="{{ $documento }}">
<input type="hidden" id="hid_codser" value="">
<input type="hidden" id="hid_numero" value="">
<input type="hidden" id="hid_codben" value="">
@endsection

@push('scripts')
<script>
    // ================================================
    // Configuracion Epayco
    // ================================================
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

    // ================================================
    // Variables globales
    // ================================================
    var trabajadorData = null;
    var nucleoFamiliar = [];
    var beneficiarioSeleccionado = null;
    var serviciosData = [];
    var servicioSeleccionado = null;

    // ================================================
    // Rutas AJAX (Laravel named routes)
    // ================================================
    var routes = {
        identificarTrabajador: "{{ route('servicios.identificar-trabajador') }}",
        listarServicios: "{{ route('servicios.listar-servicios') }}",
        validarTarifa: "{{ route('servicios.validar-tarifa') }}",
        validarPagoEpayco: "{{ route('servicios.validar-pago-epayco') }}",
        guardarVenta: "{{ route('servicios.guardar-venta') }}",
    };

    // CSRF token para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ================================================
    // Funciones utilitarias
    // ================================================
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

    // ================================================
    // Capturar respuesta de Epayco al volver a la pagina
    // ================================================
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
            console.log('ref_payco detectado:', refPayco);
            if (window.history && window.history.replaceState) {
                var cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
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
        }).fail(function(err) {
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

    // ================================================
    // 1. Identificar trabajador
    // ================================================
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
                llenarDatosTrabajador(trabajadorData);
                llenarNucleoFamiliar(nucleoFamiliar);
                $('#formulario_servicio').fadeIn();
                cargarServicios();
            } else {
                $('#error_mensaje').text(response.message || 'Trabajador no encontrado');
                $('#error_trabajador').fadeIn();
            }
        }).fail(function(err) {
            ocultarLoader('loader_trabajador');
            $('#error_mensaje').text('Error de conexion al verificar el trabajador');
            $('#error_trabajador').fadeIn();
        });
    }

    function llenarDatosTrabajador(data) {
        $('#txt_cedtra').val(data.cedtra || '');
        $('#txt_nombre').val(data.nombre || '');
        $('#txt_estado').val(data.estado || '');
        $('#txt_categoria').val((data.codcat || '') + ' - ' + (data.detcat || ''));
        $('#txt_empresa').val(data.razsoc || '');
        $('#txt_edad').val(data.edad || '');
        $('#txt_tipbenef').val(data.tipbenef || '');
        $('#txt_nit').val(data.nit || '');
    }

    function llenarNucleoFamiliar(nucleo) {
        var select = $('#sel_beneficiario');
        select.empty();
        select.append('<option value="">-- Seleccione un beneficiario --</option>');

        var tiposBen = { 'T': 'Trabajador', 'C': 'Conyuge', 'B': 'Beneficiario' };

        $.each(nucleo, function(i, ben) {
            var codben = ben.codben || ben.cedtra || '';
            var nombre = ben.nombre || '';
            var tipo = ben.descripcion_tipo || tiposBen[ben.tipben] || ben.tipben || '';
            var edad = ben.edad || '';
            var texto = nombre + ' (' + tipo + ' - Edad: ' + edad + ')';
            select.append(
                $('<option></option>').val(codben).text(texto).data('ben', ben)
            );
        });

        if (nucleo.length === 1) {
            var primerCodben = nucleo[0].codben || nucleo[0].cedtra || '';
            select.val(primerCodben).trigger('change');
        }
    }

    // ================================================
    // 2. Cargar lista de servicios
    // ================================================
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
                serviciosData = response.data;
                var select = $('#sel_servicio');
                select.empty();
                select.append('<option value="">-- Seleccione un servicio --</option>');

                $.each(serviciosData, function(i, srv) {
                    var cupos = srv.cupos_disponibles || '0';
                    var texto = srv.nombre + ' (Cupos: ' + cupos + ')';
                    select.append(
                        $('<option></option>')
                            .val(srv.codser + '|' + srv.numero)
                            .text(texto)
                            .data('cupos', cupos)
                    );
                });

                $('#contenedor_servicios').fadeIn();
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message || 'No se pudieron cargar los servicios',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 5000
                });
            }
        }).fail(function(err) {
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

    // ================================================
    // 3. Validar tarifa
    // ================================================
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
                $('#txt_valor').val(formatearValor(data.valser));
                $('#hid_valor_raw').val(data.valser);
                $('#txt_tarifa_categoria').val(data.categoria || '');
                $('#txt_temporada').val(data.temporada || '');
                $('#txt_tarifa_cupos').val(data.cupos_disponibles || '0');
                $('#detalle_tarifa').fadeIn();
            } else {
                $('#error_tarifa_msg').text(response.message || 'No se pudo validar la tarifa');
                $('#error_tarifa').fadeIn();
            }
        }).fail(function(err) {
            ocultarLoader('loader_tarifa');
            $('#error_tarifa_msg').text('Error de conexion al validar tarifa');
            $('#error_tarifa').fadeIn();
        });
    }

    // ================================================
    // 4. Guardar venta
    // ================================================
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
                    $('#sel_servicio').val('').trigger('change');
                    $('#detalle_tarifa').hide();
                    $('#txt_nota').val('');
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
        }).fail(function(err) {
            Swal.fire({
                title: 'Error',
                text: 'Error de conexion al guardar la venta',
                icon: 'error',
                showConfirmButton: false,
                timer: 5000
            });
        });
    }

    // ================================================
    // Eventos
    // ================================================
    $(document).ready(function() {
        identificarTrabajador();
        verificarRespuestaEpayco();

        $(document).on('change', '#sel_beneficiario', function() {
            var codben = $(this).val();
            $('#hid_codben').val(codben);

            if (codben === '') {
                beneficiarioSeleccionado = null;
                $('#info_beneficiario').hide();
                $('#sel_servicio').val('').trigger('change');
                return;
            }

            var benData = $(this).find(':selected').data('ben');
            beneficiarioSeleccionado = benData;

            if (benData) {
                var tiposBen = { 'T': 'Trabajador', 'C': 'Conyuge', 'B': 'Beneficiario' };
                $('#txt_ben_documento').text(benData.codben || benData.cedtra || '');
                $('#txt_ben_parentesco').text(benData.descripcion_tipo || tiposBen[benData.tipben] || benData.tipben || '');
                $('#txt_ben_edad').text(benData.edad || '');
                $('#txt_ben_categoria').text(benData.codcat || '');
                $('#info_beneficiario').fadeIn();
            }

            $('#sel_servicio').val('');
            $('#detalle_tarifa').hide();
            $('#error_tarifa').hide();
        });

        $(document).on('change', '#sel_servicio', function() {
            var val = $(this).val();
            $('#detalle_tarifa').hide();
            $('#error_tarifa').hide();
            $('#txt_cupos').val('-');

            if (val === '') {
                servicioSeleccionado = null;
                return;
            }

            var partes = val.split('|');
            var codser = partes[0];
            var numero = partes[1];

            var cupos = $(this).find(':selected').data('cupos');
            $('#txt_cupos').val(cupos);

            validarTarifa(codser, numero);
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

            var nombre = sanitizarTexto($('#txt_nombre').val() || 'Cliente');
            var email = (trabajadorData && trabajadorData.email) ? trabajadorData.email.trim() : 'sin@email.com';
            var documento = $('#hid_documento').val();
            var invoice = 'ORD' + Date.now();

            var servicioNombre = 'Compra de servicio';
            var selText = $('#sel_servicio option:selected').text();
            if (selText) {
                servicioNombre = sanitizarTexto(selText.split('(')[0].trim());
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

<style>
    .form-control-label { font-size: 0.85em; color: #555; margin-bottom: 2px; }
    .form-control-sm { font-size: 0.9em; }
    #formulario_servicio .card-header { padding: 8px 15px; }
</style>
@endpush