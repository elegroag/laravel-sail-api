@extends('layouts.bone')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="col-12 col-xl-11 mx-auto mt-3 servicios-catalog compras-catalog">
    <div class="card shadow-sm border-0 servicios-page-card">
        <div class="card-header border-0 servicios-catalog__header py-3 px-3 px-md-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h1 class="servicios-catalog__title mb-1">Mis devoluciones</h1>
                    <p class="servicios-catalog__subtitle mb-0">
                        Consulte el estado de las solicitudes de devolución presentadas por compras vencidas.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-self-start align-self-md-center">
                    <a href="{{ route('servicios.ver-compras') }}" class="btn btn-sm servicios-catalog__btn-compras">
                        <i class="fas fa-receipt me-1"></i> Mis compras
                    </a>
                    <a href="{{ route('servicios.index') }}" class="btn btn-sm servicios-catalog__btn-compras">
                        <i class="fas fa-store me-1"></i> Catálogo
                    </a>
                    <a href="{{ route('servicios.compras-pendientes') }}" class="btn btn-sm servicios-catalog__btn-compras">
                        <i class="fas fa-clock me-1"></i> Pendientes
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body px-3 px-md-4 pb-4">
            <div class="d-flex flex-wrap gap-2 mb-3" role="group" aria-label="Filtro por estado">
                <button type="button" class="btn btn-sm btn-outline-primary active" data-toggle="filtro-dev" data-estado="">
                    Todas
                </button>
                @foreach ($filtro_estados as $codigo => $label)
                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="filtro-dev" data-estado="{{ $codigo }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div id="loader_devoluciones" class="servicios-empty-state py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3 mb-0">Consultando sus solicitudes de devolución...</p>
            </div>

            <div id="error_devoluciones" class="servicios-empty-state py-5" style="display:none;">
                <i class="fas fa-exclamation-triangle text-warning" aria-hidden="true"></i>
                <p id="error_devoluciones_msg" class="mt-3 mb-0 servicios-catalog__error-msg"></p>
                <button type="button" id="btn_reintentar_devoluciones" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-redo me-1"></i> Reintentar
                </button>
            </div>

            <div id="sin_devoluciones" class="servicios-empty-state py-5" style="display:none;">
                <i class="fas fa-undo" aria-hidden="true"></i>
                <span class="compras-estado__texto">No tiene solicitudes de devolución</span>
                <a href="{{ route('servicios.ver-compras') }}" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-receipt me-1"></i> Ir a mis compras
                </a>
            </div>

            <div id="contenido_devoluciones" style="display:none;">
                <div class="mb-2">
                    <span id="info_total_devoluciones" class="badge"></span>
                </div>
                <div id="grid_devoluciones" class="compras-grid" role="list" aria-label="Solicitudes de devolución"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_detalle_devolucion" tabindex="-1" aria-labelledby="modal_detalle_devolucion_titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_detalle_devolucion_titulo">Detalle de devolución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modal_detalle_devolucion_body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var devolucionesData = [];
    var filtroEstado = '';
    var modalDetalleDevolucion = null;

    var routes = {
        listarDevoluciones: "{{ route('servicios.listar-devoluciones') }}"
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
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatearValor(valor) {
        var n = parseFloat(valor);
        if (isNaN(n)) return '$0';
        return '$' + n.toLocaleString('es-CO', { maximumFractionDigits: 0 });
    }

    function claseEstadoDev(estado) {
        var est = String(estado || '').toUpperCase();
        if (est === 'SOLICITADA') return 'compra-card__estado--pe';
        if (est === 'EN_TRAMITE') return 'compra-card__estado--pa';
        if (est === 'APROBADO') return 'compra-card__estado--de';
        if (est === 'RECHAZADA') return 'compra-card__estado--re';
        return 'compra-card__estado--x';
    }

    function buildFila(label, valor) {
        return '<div class="compra-card__row">' +
            '<span class="compra-card__label">' + escapeHtml(label) + '</span>' +
            '<span class="compra-card__value">' + valor + '</span>' +
            '</div>';
    }

    function buildCardDevolucion(item) {
        var pre = item.precompra || {};
        var html = '<article class="compra-card">';
        html += '<div class="compra-card__header">';
        html += '<span class="compra-card__doc">Solicitud #' + escapeHtml(String(item.id || '')) + '</span>';
        html += '<span class="compra-card__estado ' + claseEstadoDev(item.estado) + '">' +
            escapeHtml(item.estado_descripcion || item.estado || '') + '</span>';
        html += '</div>';
        html += '<h3 class="compra-card__servicio">' +
            escapeHtml((pre.codser ? ('Servicio ' + pre.codser) : 'Servicio') +
                (pre.numero != null ? (' / ' + pre.numero) : '')) +
            '</h3>';
        html += '<div class="compra-card__body">';
        html += buildFila('Precompra', '#' + escapeHtml(String(pre.id || '—')));
        html += buildFila('Fecha solicitud', escapeHtml(item.fecha_solicitud || '—'));
        html += buildFila('Ref. ePayco', '<span class="compra-card__ref">' + escapeHtml(pre.ref_payco || '—') + '</span>');
        html += buildFila('Estado compra', escapeHtml(pre.estado_descripcion || pre.estado || '—'));
        html += '</div>';
        html += '<div class="compra-card__footer">';
        html += '<span class="compra-card__valor-label">Valor</span>';
        html += '<span class="compra-card__valor">' + formatearValor(pre.valor || 0) + '</span>';
        html += '</div>';
        html += '<div class="mt-3">';
        html += '<button type="button" class="btn btn-outline-secondary btn-sm w-100 btn-detalle-devolucion" data-id="' +
            escapeHtml(String(item.id || '')) + '">';
        html += '<i class="fas fa-list me-1"></i> Ver detalle</button>';
        html += '</div></article>';
        return html;
    }

    function obtenerFiltradas() {
        if (!filtroEstado) return devolucionesData.slice();
        return devolucionesData.filter(function (item) {
            return String(item.estado || '').toUpperCase() === String(filtroEstado).toUpperCase();
        });
    }

    function renderizar() {
        var lista = obtenerFiltradas();
        var grid = $('#grid_devoluciones');
        grid.empty();

        if (lista.length === 0) {
            $('#contenido_devoluciones').hide();
            if (devolucionesData.length === 0) {
                $('#sin_devoluciones').show();
            } else {
                $('#sin_devoluciones').show();
                $('#sin_devoluciones .compras-estado__texto').text('No hay solicitudes con el filtro seleccionado');
            }
            return;
        }

        $('#sin_devoluciones').hide();
        $('#contenido_devoluciones').show();
        $('#info_total_devoluciones').text(lista.length + ' solicitud' + (lista.length === 1 ? '' : 'es'));

        lista.forEach(function (item) {
            grid.append(buildCardDevolucion(item));
        });
    }

    function abrirDetalle(item) {
        var pre = item.precompra || {};
        var html = '<div class="compra-detalle">';
        html += '<h6 class="compra-detalle__section">Solicitud</h6>';
        html += buildFilaDetalle('Número', '#' + escapeHtml(String(item.id || '')));
        html += buildFilaDetalle('Estado', escapeHtml(item.estado_descripcion || item.estado || '—'));
        html += buildFilaDetalle('Fecha solicitud', escapeHtml(item.fecha_solicitud || '—'));
        html += buildFilaDetalle('En trámite', escapeHtml(item.fecha_en_tramite || '—'));
        html += buildFilaDetalle('Resolución', escapeHtml(item.fecha_resolucion || '—'));
        html += buildFilaDetalle('Motivo', escapeHtml(item.motivo_solicitud || '—'));
        if (item.nota_afiliado) {
            html += buildFilaDetalle('Su nota', escapeHtml(item.nota_afiliado));
        }
        if (item.nota_gestion) {
            html += buildFilaDetalle('Respuesta Cajas', escapeHtml(item.nota_gestion));
        }

        html += '<h6 class="compra-detalle__section mt-3">Precompra asociada</h6>';
        html += buildFilaDetalle('Precompra', '#' + escapeHtml(String(pre.id || '—')));
        html += buildFilaDetalle('Servicio', escapeHtml((pre.codser || '—') + ' / ' + (pre.numero != null ? pre.numero : '—')));
        html += buildFilaDetalle('Estado compra', escapeHtml(pre.estado_descripcion || pre.estado || '—'));
        html += buildFilaDetalle('Ref. ePayco', escapeHtml(pre.ref_payco || '—'));
        html += buildFilaDetalle('Fecha pago', escapeHtml(pre.fecha_pago || '—'));
        html += buildFilaDetalle('Valor', formatearValor(pre.valor || 0));
        if (pre.motivo_vencida_descripcion) {
            html += buildFilaDetalle('Motivo vencimiento', escapeHtml(pre.motivo_vencida_descripcion));
        }
        html += '</div>';

        $('#modal_detalle_devolucion_titulo').text('Devolución #' + (item.id || ''));
        $('#modal_detalle_devolucion_body').html(html);

        if (!modalDetalleDevolucion) {
            var el = document.getElementById('modal_detalle_devolucion');
            if (el && typeof bootstrap !== 'undefined') {
                modalDetalleDevolucion = new bootstrap.Modal(el);
            }
        }
        if (modalDetalleDevolucion) {
            modalDetalleDevolucion.show();
        }
    }

    function buildFilaDetalle(label, valorHtml) {
        return '<div class="compra-detalle__row">' +
            '<span class="compra-detalle__label">' + escapeHtml(label) + '</span>' +
            '<span class="compra-detalle__value">' + valorHtml + '</span>' +
            '</div>';
    }

    function cargarDevoluciones() {
        $('#loader_devoluciones').show();
        $('#error_devoluciones').hide();
        $('#sin_devoluciones').hide();
        $('#contenido_devoluciones').hide();

        $.ajax({
            url: routes.listarDevoluciones,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: { estado: '' }
        }).done(function (response) {
            $('#loader_devoluciones').hide();
            if (response && response.success) {
                devolucionesData = Array.isArray(response.data) ? response.data : [];
                if (devolucionesData.length === 0) {
                    $('#sin_devoluciones .compras-estado__texto').text('No tiene solicitudes de devolución');
                    $('#sin_devoluciones').show();
                    return;
                }
                renderizar();
            } else {
                $('#error_devoluciones_msg').text((response && response.message) || 'Error al cargar devoluciones');
                $('#error_devoluciones').show();
            }
        }).fail(function () {
            $('#loader_devoluciones').hide();
            $('#error_devoluciones_msg').text('Error de conexión al consultar devoluciones');
            $('#error_devoluciones').show();
        });
    }

    $(document).ready(function () {
        cargarDevoluciones();

        $(document).on('click', "[data-toggle='filtro-dev']", function () {
            $("[data-toggle='filtro-dev']").removeClass('active');
            $(this).addClass('active');
            filtroEstado = $(this).attr('data-estado') || '';
            if (devolucionesData.length) {
                renderizar();
            }
        });

        $(document).on('click', '#btn_reintentar_devoluciones', function () {
            cargarDevoluciones();
        });

        $(document).on('click', '.btn-detalle-devolucion', function () {
            var id = parseInt($(this).data('id'), 10);
            var item = null;
            for (var i = 0; i < devolucionesData.length; i++) {
                if (parseInt(devolucionesData[i].id, 10) === id) {
                    item = devolucionesData[i];
                    break;
                }
            }
            if (item) abrirDetalle(item);
        });
    });
</script>
@endpush
