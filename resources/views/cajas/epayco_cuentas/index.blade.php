@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('cajas/css/galeria-admin.css') }}" />
    <style>
        #galeria.galeria-admin-grid {
            grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
            gap: 1.25rem;
        }

        #galeria .galeria-admin-media-wrap {
            aspect-ratio: auto;
            min-height: 0;
            padding: 0.75rem !important;
            font-size: 0.9rem;
            line-height: 1.35;
        }

        #galeria .galeria-admin-media-wrap .epayco-cuenta-field {
            margin: 0.25rem 0 0;
            word-break: break-all;
        }

        #galeria .galeria-admin-media-wrap .epayco-cuenta-field:first-of-type {
            margin-top: 1.75rem;
        }

        #galeria .galeria-admin-title {
            font-size: 0.9rem;
        }

        #galeria .galeria-admin-footer {
            padding: 0.5rem 0.75rem;
        }

        @media (max-width: 575.98px) {
            #galeria.galeria-admin-grid {
                grid-template-columns: 1fr;
            }
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
                <div class="card-body p-3">
                    <div class="galeria-admin-toolbar">
                        <div>
                            <h4 class="galeria-admin-toolbar-title mb-0">Cuentas ePayco</h4>
                            <p class="galeria-admin-toolbar-subtitle mb-0">{{ $help }}</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#captureModal">
                            <i class="fas fa-plus mr-1"></i> Agregar cuenta
                        </button>
                    </div>

                    <div class="galeria-admin-grid" id="galeria"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('partials.modal_generic', [
        'titulo' => 'Cuenta ePayco',
        'contenido' => view('cajas.epayco_cuentas.partials.form')->render(),
        'evento' => 'data-toggle="guardar"',
        'btnShowModal' => 'btCaptureModal',
        'idModal' => 'captureModal',
    ])

    <script type="text/template" id="tmp_galeria">
        <article class="galeria-admin-card">
            <button
                type="button"
                class="galeria-admin-delete"
                data-toggle="borrar"
                data-cid="<%= value.id %>"
                title="Eliminar"
                aria-label="Eliminar cuenta <%= value.id %>">
                <i class="fa fa-times"></i>
            </button>

            <div class="galeria-admin-media-wrap p-3">
                <span class="galeria-admin-badge"><%= value.env_mode %></span>
                <p class="epayco-cuenta-field"><strong>Account:</strong> <%= value.account %></p>
                <p class="epayco-cuenta-field"><strong>Customer:</strong> <%= value.p_id_customer %></p>
                <p class="epayco-cuenta-field"><strong>Public:</strong> <%= value.public_key %></p>
                <p class="epayco-cuenta-field"><strong>Private:</strong> <%= value.private_key_mask %></p>
                <p class="epayco-cuenta-field"><strong>P_KEY:</strong> <%= value.p_key_mask %></p>
            </div>

            <footer class="galeria-admin-footer">
                <div class="galeria-admin-meta">
                    <span class="galeria-admin-title"><%= value.account || ('Cuenta #' + value.id) %></span>
                    <span class="galeria-admin-toolbar-subtitle d-block">Act. <%= value.updated_at || value.created_at || '—' %></span>
                </div>
                <div class="galeria-admin-actions">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        data-toggle="editar"
                        data-cid="<%= value.id %>"
                        title="Editar"
                        aria-label="Editar cuenta <%= value.id %>">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </footer>
        </article>
    </script>

    <script>
        window.ServerController = 'epayco-cuentas';
    </script>

    <script src="{{ versioned_asset('cajas/build/EpaycoCuentas.js') }}"></script>
@endpush
