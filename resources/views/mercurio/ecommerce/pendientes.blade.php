@extends('layouts.bone')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
@if ($EPAYCO_CHECKOUT_VERSION === '2')
<script src="https://checkout.epayco.co/checkout-v2.js"></script>
@else
<script src="https://checkout.epayco.co/checkout.js"></script>
@endif

<div class="col-12 col-xl-11 mx-auto mt-3 servicios-catalog compras-catalog">
    <div class="card shadow-sm border-0 servicios-page-card">
        <div class="card-header border-0 servicios-catalog__header py-3 px-3 px-md-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h1 class="servicios-catalog__title mb-1">Compras pendientes de pago</h1>
                    <p class="servicios-catalog__subtitle mb-0">
                        Retome el pago de sus compras abandonadas o desestímelas si ya no desea continuar.
                    </p>
                </div>
                <div class="d-flex gap-2 align-self-start align-self-md-center">
                    <a href="{{ route('servicios.index') }}" class="btn btn-sm servicios-catalog__btn-compras">
                        <i class="fas fa-store me-1"></i> Volver al catálogo
                    </a>
                    <a href="{{ route('servicios.ver-compras') }}" class="btn btn-sm servicios-catalog__btn-compras">
                        <i class="fas fa-receipt me-1"></i> Mis compras
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body px-3 px-md-4 pb-4">
            <div id="loader_pendientes" class="servicios-empty-state py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3 mb-0">Consultando compras pendientes de pago...</p>
            </div>

            <div id="error_pendientes" class="servicios-empty-state py-5" style="display:none;">
                <i class="fas fa-exclamation-triangle text-warning" aria-hidden="true"></i>
                <p id="error_mensaje" class="mt-3 mb-0 servicios-catalog__error-msg"></p>
                <button type="button" id="btn_reintentar" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-redo me-1"></i> Reintentar
                </button>
            </div>

            <div id="sin_pendientes" class="servicios-empty-state py-5" style="display:none;">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <span class="compras-estado__texto">No tiene compras pendientes de pago</span>
                <a href="{{ route('servicios.index') }}" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-store me-1"></i> Ir al catálogo
                </a>
            </div>

            <div id="contenido_pendientes" class="compras-contenido" style="display:none;">
                <div class="compras-toolbar">
                    <div class="compras-toolbar__meta">
                        <span id="info_total" class="compras-toolbar__info badge"></span>
                    </div>
                </div>

                <div id="pendientes-grid-scroll-wrap" class="compras-grid-scroll">
                    <div id="grid_pendientes" class="compras-grid" role="list" aria-label="Compras pendientes de pago"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_desestimar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Desestimar compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Servicio: <span id="desestimar_servicio" class="fw-bold"></span></p>
                <p class="text-muted mb-3">Indique el motivo por el cual no desea continuar con la compra.</p>

                <div id="lista_motivos" class="d-flex flex-column gap-2 mb-3">
                    @foreach ($motivosDesestimacion as $codigo => $descripcion)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="motivo_desestimar" id="motivo_{{ $codigo }}" value="{{ $codigo }}">
                        <label class="form-check-label" for="motivo_{{ $codigo }}">{{ $descripcion }}</label>
                    </div>
                    @endforeach
                </div>

                <div id="grupo_detalle_otro" class="form-group" style="display:none;">
                    <label for="txt_detalle_otro" class="form-label">Escriba el motivo</label>
                    <textarea id="txt_detalle_otro" class="form-control" rows="2" maxlength="255" placeholder="Cuéntenos por qué no desea continuar..."></textarea>
                </div>

                <div id="error_desestimar" class="alert alert-warning py-2 mt-2" style="display:none;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="error_desestimar_msg"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn_confirmar_desestimar" class="btn btn-danger btn-sm">
                    <i class="fas fa-ban me-1"></i> Desestimar compra
                </button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="hid_documento" value="{{ $documento }}">
@endsection

@push('scripts')
<script>
    // Infra de checkout (version). Llaves/test por cuenta vienen al crear sesión.
    var EPAYCO_CHECKOUT_VERSION = '{{ $EPAYCO_CHECKOUT_VERSION }}';
    var EPAYCO_TEST = false;
    var epaycoHandler = null;

    var routes = {
        catalogo: "{{ route('servicios.index') }}",
        identificarTrabajador: "{{ route('servicios.identificar-trabajador') }}",
        listarServicios: "{{ route('servicios.listar-servicios') }}",
        validarTarifa: "{{ route('servicios.validar-tarifa') }}",
        listarPrecompras: "{{ route('servicios.listar-precompras') }}",
        desestimarPrecompra: "{{ route('servicios.desestimar-precompra') }}",
        abandonarPrecompra: "{{ route('servicios.abandonar-precompra') }}",
        crearSesionEpayco: "{{ route('servicios.crear-sesion-epayco') }}",
        epaycoConfirmation: "{{ route('api.epayco.confirmation') }}",
    };
</script>
<script src="{{ versioned_asset('mercurio/build/ComprasPendientes.js') }}"></script>
@endpush
