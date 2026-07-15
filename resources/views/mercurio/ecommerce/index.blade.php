@extends('layouts.bone')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://checkout.epayco.co/checkout.js"></script>

<div class="col-12 col-xl-11 mx-auto mt-3 servicios-catalog">
    <div class="card shadow-sm border-0 servicios-page-card">
        <div class="card-header border-0 servicios-catalog__header py-3 px-3 px-md-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h1 class="servicios-catalog__title mb-1">Catálogo de servicios</h1>
                    <p class="servicios-catalog__subtitle mb-0">
                        Elija el beneficiario, seleccione un servicio y complete el pago de forma segura.
                    </p>
                </div>
                <div class="d-flex gap-2 align-self-start align-self-md-center">
                    <a href="{{ route('servicios.compras-pendientes') }}" class="btn btn-sm servicios-catalog__btn-compras position-relative">
                        <i class="fas fa-clock me-1"></i> Pendientes de pago
                        @if (($pendientesCount ?? 0) > 0)
                        <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">{{ $pendientesCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('servicios.ver-compras') }}" class="btn btn-sm servicios-catalog__btn-compras">
                        <i class="fas fa-receipt me-1"></i> Mis compras
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body px-3 px-md-4 pb-4">
            <div id="loader_trabajador" class="servicios-empty-state py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3 mb-0">Cargando beneficiarios y servicios...</p>
            </div>

            <div id="error_trabajador" class="servicios-empty-state py-5" style="display:none;">
                <i class="fas fa-exclamation-triangle text-warning" aria-hidden="true"></i>
                <p id="error_mensaje" class="mt-3 mb-0 servicios-catalog__error-msg"></p>
                <a id="btn_volver_error" href="{{ route('principal.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>

            <div id="formulario_servicio" style="display:none;">
                <section class="servicios-catalog__section mb-4">
                    <h2 class="servicios-catalog__section-title">
                        <i class="fas fa-users"></i> Beneficiarios disponibles
                    </h2>
                    <p class="servicios-catalog__section-desc">Seleccione para quién adquirirá el servicio.</p>
                    <div id="grid_beneficiarios" class="beneficiarios-grid" role="listbox" aria-label="Beneficiarios disponibles"></div>
                    <div id="sin_beneficiarios" class="servicios-empty-state py-4" style="display:none;">
                        <i class="fas fa-users" aria-hidden="true"></i>
                        <span>No hay beneficiarios disponibles en su núcleo familiar.</span>
                    </div>
                </section>

                <div class="servicios-catalog__main">
                    <div class="servicios-catalog__content">
                        <section class="servicios-catalog__section">
                            <h2 class="servicios-catalog__section-title">
                                <i class="fas fa-store"></i> Servicios activos
                            </h2>

                            <div id="loader_servicios" class="servicios-inline-loader py-4">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="ms-2">Cargando servicios...</span>
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
                                    <div id="sin_servicios" class="servicios-empty-state py-4" style="display:none;">
                                        <i class="fas fa-search" aria-hidden="true"></i>
                                        <span>No hay servicios que coincidan con su búsqueda.</span>
                                    </div>
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
                                <p id="panel_servicio_descripcion" class="panel-compra__servicio-descripcion" style="display:none;"></p>

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
    // Valores que provienen de Blade (config y rutas). La logica del modulo
    // vive en public/src/Mercurio/Ecommerce/main.js (compilado a mercurio/build/Ecommerce.js)
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

    var routes = {
        identificarTrabajador: "{{ route('servicios.identificar-trabajador') }}",
        listarServicios: "{{ route('servicios.listar-servicios') }}",
        validarTarifa: "{{ route('servicios.validar-tarifa') }}",
        crearPrecompra: "{{ route('servicios.crear-precompra') }}",
        validarPagoEpayco: "{{ route('servicios.validar-pago-epayco') }}",
        guardarVenta: "{{ route('servicios.guardar-venta') }}",
    };
</script>
<script src="{{ asset('mercurio/build/Ecommerce.js') }}"></script>
@endpush
