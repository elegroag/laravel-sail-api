@extends('layouts.dash')

@section('content')
<div class="col mt-2">
    <div class="card">
        <div class="card-header py-2" style="background-color: #3f51b5; color: white;">
            <b>Mis Compras</b>
        </div>

        <div class="card-body">
            <div class="col-xs-12">

                <!-- ============================================ -->
                <!-- LOADER -->
                <!-- ============================================ -->
                <div id="loader_compras" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Consultando compras realizadas...</p>
                </div>

                <!-- ============================================ -->
                <!-- ERROR -->
                <!-- ============================================ -->
                <div id="error_compras" class="text-center py-5" style="display:none;">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 60px;"></i>
                    <p id="error_mensaje" class="mt-3" style="font-size: 16px; color: #e65100; font-weight: 500;"></p>
                    <a id="btn_volver_error" href="{{ route('servicios.ver-compras') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <!-- ============================================ -->
                <!-- SIN COMPRAS -->
                <!-- ============================================ -->
                <div id="sin_compras" class="text-center py-5" style="display:none;">
                    <i class="fas fa-shopping-cart text-muted" style="font-size: 60px;"></i>
                    <p class="mt-3 text-muted" style="font-size: 16px;">No se encontraron compras realizadas</p>
                    <a id="btn_volver_sin" href="{{ route('principal.index') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <!-- ============================================ -->
                <!-- CONTENIDO -->
                <!-- ============================================ -->
                <div id="contenido_compras" style="display:none;">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <span class="text-muted" id="info_total">Total: 0 compras</span>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="text-muted" id="info_pagina">Pagina 1 de 1</span>
                        </div>
                    </div>

                    <div id="grid_compras" class="row"></div>

                    <div class="row mt-3 mb-3">
                        <div class="col-12 text-center">
                            <div class="d-inline-flex align-items-center">
                                <button type="button" id="btn_anterior" class="btn btn-primary btn-sm" disabled>
                                    <i class="fas fa-arrow-left"></i> Anterior
                                </button>
                                <span id="paginador_texto" class="mx-3 font-weight-bold" style="font-size: 1em;">1 de 1</span>
                                <button type="button" id="btn_siguiente" class="btn btn-primary btn-sm" disabled>
                                    Siguiente <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<input type="hidden" id="hid_documento" value="{{ $documento }}">
@endsection

@push('scripts')
<script>
    var comprasData = [];
    var paginaActual = 0;
    var itemsPorPagina = 1;

    var routes = {
        misCompras: "{{ route('servicios.mis-compras') }}",
    };

    // CSRF token para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function formatearValor(valor) {
        var num = parseFloat(valor) || 0;
        return '$' + num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function mostrarLoader(id) { $('#' + id).show(); }
    function ocultarLoader(id) { $('#' + id).hide(); }

    function calcularItemsPorPagina() {
        var ancho = $(window).width();
        if (ancho >= 992) return 3;
        else if (ancho >= 768) return 2;
        else return 1;
    }

    function colorEstado(estado) {
        if (!estado) return '#6c757d';
        var est = estado.toUpperCase();
        if (est === 'A') return '#28a745';
        if (est === 'C') return '#dc3545';
        if (est === 'D') return '#fd7e14';
        return '#6c757d';
    }

    function textoEstado(estado) {
        if (!estado) return 'Desconocido';
        var est = estado.toUpperCase();
        if (est === 'A') return 'Activo';
        if (est === 'C') return 'Cancelado';
        if (est === 'D') return 'Devuelto';
        return estado;
    }

    function buildDetalle(label, valor) {
        return '<div class="row mb-1">' +
            '<div class="col-4"><span class="campo-label">' + label + '</span></div>' +
            '<div class="col-8"><span class="campo-valor">' + valor + '</span></div>' +
            '</div>';
    }

    function buildCardCompra(compra) {
        var estado = compra.estado || '';
        var badgeColor = colorEstado(estado);
        var badgeTexto = compra.estado_texto || textoEstado(estado);

        var fecha = compra.fecha || '-';
        var hora = compra.hora || '';
        var servicio = compra.nombre_servicio || '-';
        var beneficiario = compra.nombre_beneficiario || '-';
        var tipbenTexto = compra.tipben_texto || '';
        var edad = compra.edad || '-';
        var categoria = compra.codcat || '-';
        var valor = formatearValor(compra.valpago || compra.valser || 0);
        var formaPago = compra.forma_pago_detalle || '-';
        var refpago = compra.refpago || '-';
        var nota = compra.nota || '';
        var marca = compra.marca || '';
        var documento = compra.documento || '';
        var titular = compra.nombre_titular || '';

        var html = '<div class="card card-compra mb-3">';

        html += '<div class="card-header py-2" style="background-color: #3f51b5; color: white;">';
        html += '<div class="d-flex justify-content-between align-items-center">';
        html += '<span class="font-weight-bold">';
        if (marca) html += marca + ' - ';
        html += documento;
        html += '</span>';
        html += '<span class="badge" style="background-color:' + badgeColor + '; color: white; font-size: 0.85em; padding: 4px 10px; border-radius: 10px;">' + badgeTexto + '</span>';
        html += '</div>';
        html += '</div>';

        html += '<div class="card-body py-2">';

        html += buildDetalle('Fecha', fecha + ' ' + hora);
        html += buildDetalle('Servicio', servicio);
        html += buildDetalle('Beneficiario', beneficiario + (tipbenTexto ? ' (' + tipbenTexto + ')' : ''));

        if (titular && String(compra.cedtra_titular) !== String(compra.codben)) {
            html += buildDetalle('Titular', titular + ' (' + (compra.cedtra_titular || '') + ')');
        }

        html += buildDetalle('Edad', edad);
        html += buildDetalle('Categoria', categoria);
        html += buildDetalle('Valor', '<span class="font-weight-bold text-success">' + valor + '</span>');
        html += buildDetalle('Forma de pago', formaPago);
        html += buildDetalle('Ref. Pago', '<span style="word-break:break-all; font-size:0.85em;">' + refpago + '</span>');

        if (nota && nota.trim() !== '') {
            html += buildDetalle('Nota', nota);
        }

        html += '</div></div>';

        return html;
    }

    function renderizarPagina() {
        var grid = $('#grid_compras');
        grid.empty();

        var totalCompras = comprasData.length;
        var totalPaginas = Math.ceil(totalCompras / itemsPorPagina);

        if (paginaActual < 0) paginaActual = 0;
        if (paginaActual >= totalPaginas) paginaActual = totalPaginas - 1;
        if (paginaActual < 0) paginaActual = 0;

        var inicio = paginaActual * itemsPorPagina;
        var fin = Math.min(inicio + itemsPorPagina, totalCompras);

        var colClass = 'col-12';
        if (itemsPorPagina === 2) colClass = 'col-md-6';
        if (itemsPorPagina === 3) colClass = 'col-md-4';

        for (var i = inicio; i < fin; i++) {
            var cardHtml = '<div class="' + colClass + '">' + buildCardCompra(comprasData[i]) + '</div>';
            grid.append(cardHtml);
        }

        $('#paginador_texto').text((paginaActual + 1) + ' de ' + totalPaginas);
        $('#info_pagina').text('Pagina ' + (paginaActual + 1) + ' de ' + totalPaginas);
        $('#info_total').text('Total: ' + totalCompras + ' compras');

        $('#btn_anterior').prop('disabled', paginaActual <= 0);
        $('#btn_siguiente').prop('disabled', paginaActual >= totalPaginas - 1);
    }

    function cargarCompras() {
        var cedtra = $('#hid_documento').val();

        mostrarLoader('loader_compras');
        $('#error_compras').hide();
        $('#sin_compras').hide();
        $('#contenido_compras').hide();

        $.ajax({
            url: routes.misCompras,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: { cedtra: cedtra }
        }).done(function(response) {
            ocultarLoader('loader_compras');

            if (response.success) {
                var data = response.data;

                if (Array.isArray(data)) {
                    comprasData = data;
                } else if (data && Array.isArray(data.compras)) {
                    comprasData = data.compras;
                } else if (data && typeof data === 'object') {
                    comprasData = [data];
                } else {
                    comprasData = [];
                }

                if (comprasData.length === 0) {
                    $('#sin_compras').fadeIn();
                    return;
                }

                itemsPorPagina = calcularItemsPorPagina();
                paginaActual = 0;
                $('#contenido_compras').fadeIn();
                renderizarPagina();
            } else {
                $('#error_mensaje').text(response.message || 'Error al cargar las compras');
                $('#error_compras').fadeIn();
            }
        }).fail(function(err) {
            ocultarLoader('loader_compras');
            $('#error_mensaje').text('Error de conexion al consultar las compras');
            $('#error_compras').fadeIn();
        });
    }

    $(document).ready(function() {
        cargarCompras();

        $(document).on('click', '#btn_anterior', function() {
            if (paginaActual > 0) {
                paginaActual--;
                renderizarPagina();
            }
        });

        $(document).on('click', '#btn_siguiente', function() {
            var totalPaginas = Math.ceil(comprasData.length / itemsPorPagina);
            if (paginaActual < totalPaginas - 1) {
                paginaActual++;
                renderizarPagina();
            }
        });

        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                var nuevoItems = calcularItemsPorPagina();
                if (nuevoItems !== itemsPorPagina) {
                    itemsPorPagina = nuevoItems;
                    var primerItem = paginaActual * itemsPorPagina;
                    paginaActual = Math.floor(primerItem / itemsPorPagina);
                    renderizarPagina();
                }
            }, 250);
        });
    });
</script>

<style>
    .card-compra {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    .card-compra:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .card-compra .card-header {
        border-radius: 10px 10px 0 0;
        padding: 8px 15px;
    }
    .card-compra .card-body {
        padding: 12px 15px;
    }
    .campo-label {
        font-size: 0.82em;
        color: #777;
        font-weight: 600;
    }
    .campo-valor {
        font-size: 0.9em;
        color: #333;
    }
    #btn_anterior, #btn_siguiente {
        min-width: 100px;
    }
    #paginador_texto {
        color: #3f51b5;
    }
</style>
@endpush