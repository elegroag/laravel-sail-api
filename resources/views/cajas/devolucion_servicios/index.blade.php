@extends('layouts.cajas')

@push('styles')
    <style>
        .badge-dev-solicitada { background-color: #ffc107; color: #212529; }
        .badge-dev-tramite { background-color: #0d6efd; color: #fff; }
        .badge-dev-aprobado { background-color: #198754; color: #fff; }
        .badge-dev-rechazada { background-color: #dc3545; color: #fff; }
        .devolucion-resumen .card { min-width: 120px; }

        #chips_estado {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        #chips_estado .chip-estado {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border: 1px solid #cfd8dc;
            background: #fff;
            color: #37474f;
            border-radius: 999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.8125rem;
            font-weight: 500;
            line-height: 1.2;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
        }
        #chips_estado .chip-estado:hover {
            border-color: #26a69a;
            color: #00695c;
            background: #f1faf9;
        }
        #chips_estado .chip-estado.active {
            background: #00897b;
            border-color: #00897b;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 137, 123, 0.28);
        }
        #chips_estado .chip-estado .chip-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.5rem;
            height: 1.35rem;
            padding: 0 0.4rem;
            border-radius: 999px;
            background: #eceff1;
            color: #455a64;
            font-size: 0.75rem;
            font-weight: 700;
        }
        #chips_estado .chip-estado.active .chip-count {
            background: rgba(255, 255, 255, 0.92);
            color: #00695c;
        }
    </style>
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-green-blue p-1"></div>
                <div class="card-body p-0 m-3">
                    <div class="mb-3">
                        <div class="small text-muted mb-2">Filtrar por estado</div>
                        <div id="chips_estado" role="group" aria-label="Filtro por estado">
                            <button type="button" class="chip-estado active" data-toggle="chip-estado" data-estado="">
                                Todos
                                <span class="chip-count" data-count="TODOS">{{ $resumen['TODOS'] ?? 0 }}</span>
                            </button>
                            @foreach ($filtro_estados as $codigo => $descripcion)
                                <button type="button" class="chip-estado" data-toggle="chip-estado" data-estado="{{ $codigo }}">
                                    {{ $descripcion }}
                                    <span class="chip-count" data-count="{{ $codigo }}">{{ $resumen[$codigo] ?? 0 }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <form id="form_devoluciones" class="row g-2 align-items-end mb-3">
                        <input type="hidden" id="chip_estado" name="estado" value="">
                        <div class="col-auto">
                            <label for="filtro_documento" class="form-label mb-0 small">Documento</label>
                            <input type="text" id="filtro_documento" name="documento" class="form-control form-control-sm" placeholder="Cédula titular" autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <label for="filtro_q" class="form-label mb-0 small">Buscar</label>
                            <input type="text" id="filtro_q" name="q" class="form-control form-control-sm" placeholder="Ref, servicio, nota..." autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-primary" id="btn_buscar">
                                <i class="fas fa-search me-1"></i> Buscar
                            </button>
                        </div>
                    </form>

                    <div id="mensaje" class="mb-2"></div>
                    <div id="consulta" class="table-responsive"></div>
                    <div id="paginate" class="card-footer py-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_gestion_devolucion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_gestion_titulo">Gestionar devolución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="hid_devolucion_id" value="">
                <input type="hidden" id="hid_devolucion_accion" value="">
                <p class="small text-muted mb-2" id="modal_gestion_resumen"></p>
                <label for="txt_nota_gestion" class="form-label">Nota de gestión (opcional)</label>
                <textarea id="txt_nota_gestion" class="form-control" rows="3" maxlength="1000" placeholder="Observación del operador..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn_confirmar_gestion">Confirmar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        window.ServerController = 'devolucion-servicios';
        window.routesDevolucionServicios = {
            buscar: "{{ route('devolucion-servicios.buscar') }}",
            changeCantidad: "{{ route('devolucion-servicios.change-cantidad-pagina') }}",
            enTramiteBase: "{{ url('/cajas/devolucion-servicios') }}",
            verPrecompraBase: "{{ url('/cajas/admservicios/ver') }}",
        };
    </script>
    <script src="{{ versioned_asset('cajas/build/DevolucionServicios.js') }}"></script>
@endpush
