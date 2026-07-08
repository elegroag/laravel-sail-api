@extends('layouts.bone')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="col mt-2 servicios-catalog compras-catalog">
    <div class="card shadow-sm">
        <div class="card-header servicios-catalog__header py-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <h1 class="servicios-catalog__title mb-1">Mis Compras</h1>
                    <p class="servicios-catalog__subtitle mb-0">
                        Consulte el historial de servicios adquiridos.
                    </p>
                </div>
                <a href="{{ route('servicios.index') }}" class="btn btn-sm servicios-catalog__btn-compras align-self-start align-self-md-center">
                    <i class="fas fa-store"></i> Volver al catálogo
                </a>
            </div>
        </div>

        <div class="card-body">
            <div id="loader_compras" class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Consultando compras realizadas...</p>
            </div>

            <div id="error_compras" class="compras-estado text-center py-5" style="display:none;">
                <i class="fas fa-exclamation-triangle compras-estado__icon compras-estado__icon--warning"></i>
                <p id="error_mensaje" class="mt-3 servicios-catalog__error-msg"></p>
                <button type="button" id="btn_reintentar" class="btn btn-primary btn-sm mt-2">
                    <i class="fas fa-redo"></i> Reintentar
                </button>
            </div>

            <div id="sin_compras" class="compras-estado text-center py-5" style="display:none;">
                <i class="fas fa-shopping-cart compras-estado__icon"></i>
                <p class="mt-3 compras-estado__texto">No se encontraron compras realizadas</p>
                <a href="{{ route('servicios.index') }}" class="btn btn-primary btn-sm mt-2">
                    <i class="fas fa-store"></i> Ir al catálogo
                </a>
            </div>

            <div id="contenido_compras" class="compras-contenido" style="display:none;">
                <div class="compras-toolbar">
                    <span id="info_total" class="compras-toolbar__info badge"></span>
                    <span id="info_pagina" class="compras-toolbar__info text-muted"></span>
                </div>

                <div class="compras-grid-scroll">
                    <div id="grid_compras" class="compras-grid" role="list" aria-label="Compras realizadas"></div>
                </div>

                <div class="compras-paginador">
                    <button type="button" id="btn_anterior" class="btn btn-primary btn-sm" disabled>
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                    <span id="paginador_texto" class="compras-paginador__texto">1 de 1</span>
                    <button type="button" id="btn_siguiente" class="btn btn-primary btn-sm" disabled>
                        Siguiente <i class="fas fa-arrow-right"></i>
                    </button>
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

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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

    function mostrarLoader(id) { $('#' + id).show(); }
    function ocultarLoader(id) { $('#' + id).hide(); }

    function calcularItemsPorPagina() {
        var ancho = $(window).width();
        if (ancho >= 1200) return 3;
        if (ancho >= 768) return 2;
        return 1;
    }

    function claseEstado(estado) {
        if (!estado) return 'compra-card__estado--x';
        var est = String(estado).toUpperCase();
        if (est === 'A') return 'compra-card__estado--a';
        if (est === 'C') return 'compra-card__estado--c';
        if (est === 'D') return 'compra-card__estado--d';
        return 'compra-card__estado--x';
    }

    function textoEstado(estado) {
        if (!estado) return 'Desconocido';
        var est = String(estado).toUpperCase();
        if (est === 'A') return 'Activo';
        if (est === 'C') return 'Cancelado';
        if (est === 'D') return 'Devuelto';
        return estado;
    }

    function buildFila(label, valor) {
        return '<div class="compra-card__row">' +
            '<span class="compra-card__label">' + escapeHtml(label) + '</span>' +
            '<span class="compra-card__value">' + valor + '</span>' +
            '</div>';
    }

    function buildCardCompra(compra) {
        var estado = compra.estado || '';
        var badgeTexto = compra.estado_texto || textoEstado(estado);
        var fecha = compra.fecha || '-';
        var hora = compra.hora || '';
        var servicio = escapeHtml(compra.nombre_servicio || '-');
        var beneficiario = escapeHtml(compra.nombre_beneficiario || '-');
        var tipbenTexto = escapeHtml(compra.tipben_texto || '');
        var edad = escapeHtml(compra.edad || '-');
        var categoria = escapeHtml(compra.detcat || compra.codcat || '-');
        var valor = formatearValor(compra.valpago || compra.valser || 0);
        var formaPago = escapeHtml(compra.forma_pago_detalle || '-');
        var refpago = escapeHtml(compra.refpago || '-');
        var nota = compra.nota || '';
        var marca = escapeHtml(compra.marca || '');
        var documento = escapeHtml(compra.documento || '');
        var titular = escapeHtml(compra.nombre_titular || '');

        var tituloDoc = marca ? marca + ' - ' + documento : documento;

        var html = '<article class="compra-card">';
        html += '<div class="compra-card__header">';
        html += '<span class="compra-card__doc">' + tituloDoc + '</span>';
        html += '<span class="compra-card__estado ' + claseEstado(estado) + '">' + escapeHtml(badgeTexto) + '</span>';
        html += '</div>';
        html += '<h3 class="compra-card__servicio">' + servicio + '</h3>';
        html += '<div class="compra-card__body">';
        html += buildFila('Fecha', escapeHtml(fecha + (hora ? ' ' + hora : '')));
        html += buildFila('Beneficiario', beneficiario + (tipbenTexto ? ' (' + tipbenTexto + ')' : ''));

        if (titular && String(compra.cedtra_titular) !== String(compra.codben)) {
            html += buildFila('Titular', titular + ' (' + escapeHtml(compra.cedtra_titular || '') + ')');
        }

        html += buildFila('Edad', edad);
        html += buildFila('Categoría', categoria);
        html += buildFila('Forma de pago', formaPago);
        html += buildFila('Ref. pago', '<span class="compra-card__ref">' + refpago + '</span>');

        if (nota && nota.trim() !== '') {
            html += buildFila('Nota', escapeHtml(nota));
        }

        html += '</div>';
        html += '<div class="compra-card__footer">';
        html += '<span class="compra-card__valor-label">Valor pagado</span>';
        html += '<span class="compra-card__valor">' + valor + '</span>';
        html += '</div>';
        html += '</article>';

        return html;
    }

    function renderizarPagina() {
        var grid = $('#grid_compras');
        grid.empty();

        var totalCompras = comprasData.length;
        var totalPaginas = Math.max(1, Math.ceil(totalCompras / itemsPorPagina));

        if (paginaActual < 0) paginaActual = 0;
        if (paginaActual >= totalPaginas) paginaActual = totalPaginas - 1;

        var inicio = paginaActual * itemsPorPagina;
        var fin = Math.min(inicio + itemsPorPagina, totalCompras);

        for (var i = inicio; i < fin; i++) {
            grid.append(buildCardCompra(comprasData[i]));
        }

        $('#paginador_texto').text((paginaActual + 1) + ' de ' + totalPaginas);
        $('#info_pagina').text('Página ' + (paginaActual + 1) + ' de ' + totalPaginas);
        $('#info_total').text(totalCompras + ' compra' + (totalCompras === 1 ? '' : 's'));

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
                    $('#sin_compras').show();
                    return;
                }

                itemsPorPagina = calcularItemsPorPagina();
                paginaActual = 0;
                $('#contenido_compras').css('display', 'flex');
                renderizarPagina();
            } else {
                $('#error_mensaje').text(response.message || 'Error al cargar las compras');
                $('#error_compras').show();
            }
        }).fail(function() {
            ocultarLoader('loader_compras');
            $('#error_mensaje').text('Error de conexión al consultar las compras');
            $('#error_compras').show();
        });
    }

    $(document).ready(function() {
        cargarCompras();

        $(document).on('click', '#btn_reintentar', function() {
            cargarCompras();
        });

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
                if (!$('#contenido_compras').is(':visible')) return;
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
@endpush
