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

            <div id="sin_resultados_busqueda" class="compras-estado text-center py-4" style="display:none;">
                <i class="fas fa-search compras-estado__icon"></i>
                <p class="mt-3 compras-estado__texto">No hay compras que coincidan con su búsqueda</p>
            </div>

            <div id="contenido_compras" class="compras-contenido" style="display:none;">
                <div class="compras-toolbar">
                    <div class="compras-toolbar__controls">
                        <div class="input-group compras-toolbar__search">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input
                                type="search"
                                id="buscar_compra"
                                class="form-control"
                                placeholder="Buscar por servicio, beneficiario, referencia, estado..."
                                autocomplete="off"
                            >
                        </div>
                        <div class="compras-toolbar__por-pagina">
                            <label for="select_por_pagina" class="compras-toolbar__por-pagina-label">Por página</label>
                            <select id="select_por_pagina" class="form-select form-select-sm">
                                <option value="10" selected>10</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                    <div class="compras-toolbar__meta">
                        <span id="info_total" class="compras-toolbar__info badge"></span>
                        <span id="info_pagina" class="compras-toolbar__info text-muted"></span>
                    </div>
                </div>

                <div id="compras-grid-scroll-wrap" class="compras-grid-scroll">
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
    var busquedaCompras = '';
    var paginaActual = 0;
    var itemsPorPagina = 10;

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

    function compraCoincideBusqueda(compra, query) {
        if (!query) return true;
        var texto = (
            (compra.nombre_servicio || '') + ' ' +
            (compra.nombre_beneficiario || '') + ' ' +
            (compra.nombre_titular || '') + ' ' +
            (compra.documento || '') + ' ' +
            (compra.codben || '') + ' ' +
            (compra.cedtra_titular || '') + ' ' +
            (compra.refpago || '') + ' ' +
            (compra.forma_pago_detalle || '') + ' ' +
            (compra.estado_texto || '') + ' ' +
            (compra.estado || '') + ' ' +
            (compra.detcat || '') + ' ' +
            (compra.codcat || '') + ' ' +
            (compra.marca || '') + ' ' +
            (compra.nota || '') + ' ' +
            (compra.fecha || '') + ' ' +
            (compra.tipben_texto || '')
        ).toLowerCase();
        return texto.indexOf(query.toLowerCase()) !== -1;
    }

    function obtenerComprasFiltradas() {
        var query = busquedaCompras.trim();
        if (!query) return comprasData.slice();
        return comprasData.filter(function(compra) {
            return compraCoincideBusqueda(compra, query);
        });
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

        var comprasFiltradas = obtenerComprasFiltradas();
        var totalCompras = comprasFiltradas.length;
        var totalPaginas = Math.max(1, Math.ceil(totalCompras / itemsPorPagina) || 1);

        if (totalCompras === 0) {
            $('#compras-grid-scroll-wrap').hide();
            $('#sin_resultados_busqueda').toggle(comprasData.length > 0 && busquedaCompras.trim() !== '');
            $('.compras-paginador').hide();
            $('#info_total').text('0 compras');
            $('#info_pagina').text('');
            return;
        }

        $('#compras-grid-scroll-wrap').show();
        $('#sin_resultados_busqueda').hide();
        $('.compras-paginador').show();

        if (paginaActual < 0) paginaActual = 0;
        if (paginaActual >= totalPaginas) paginaActual = totalPaginas - 1;

        var inicio = paginaActual * itemsPorPagina;
        var fin = Math.min(inicio + itemsPorPagina, totalCompras);

        for (var i = inicio; i < fin; i++) {
            grid.append(buildCardCompra(comprasFiltradas[i]));
        }

        var textoTotal = totalCompras + ' compra' + (totalCompras === 1 ? '' : 's');
        if (busquedaCompras.trim() && comprasData.length !== totalCompras) {
            textoTotal += ' (de ' + comprasData.length + ')';
        }

        $('#paginador_texto').text((paginaActual + 1) + ' de ' + totalPaginas);
        $('#info_pagina').text('Página ' + (paginaActual + 1) + ' de ' + totalPaginas);
        $('#info_total').text(textoTotal);

        $('#btn_anterior').prop('disabled', paginaActual <= 0);
        $('#btn_siguiente').prop('disabled', paginaActual >= totalPaginas - 1);
    }

    function aplicarFiltrosCompras() {
        paginaActual = 0;
        renderizarPagina();
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
            data: { cedtra: cedtra, limit: 500 }
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

                paginaActual = 0;
                busquedaCompras = '';
                $('#buscar_compra').val('');
                itemsPorPagina = parseInt($('#select_por_pagina').val(), 10) || 10;
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
            var totalPaginas = Math.ceil(obtenerComprasFiltradas().length / itemsPorPagina);
            if (paginaActual < totalPaginas - 1) {
                paginaActual++;
                renderizarPagina();
            }
        });

        $(document).on('input', '#buscar_compra', function() {
            busquedaCompras = $(this).val().trim();
            aplicarFiltrosCompras();
        });

        $(document).on('change', '#select_por_pagina', function() {
            itemsPorPagina = parseInt($(this).val(), 10) || 10;
            paginaActual = 0;
            renderizarPagina();
        });
    });
</script>
@endpush
