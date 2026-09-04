@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ versioned_asset('cajas/css/galeria-admin.css') }}" />
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
                            <h4 class="galeria-admin-toolbar-title mb-0">Banners login</h4>
                            <p class="galeria-admin-toolbar-subtitle mb-0">{{ $help }}</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#captureModal">
                            <i class="fas fa-plus mr-1"></i> Agregar banner
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
        'titulo' => 'Banner promocional',
        'contenido' => view('cajas.banners.partials.form')->render(),
        'evento' => 'data-toggle="guardar"',
        'btnShowModal' => 'btCaptureModal',
        'idModal' => 'captureModal',
    ])

    @include('partials.modal_generic', [
        'titulo' => 'Vista previa',
        'contenido' => '',
        'hideFooter' => true,
        'btnShowModal' => 'btZoomModal',
        'idModal' => 'zoomModal',
    ])

    <script type="text/template" id='tmp_galeria'>
        <article class="galeria-admin-card<%= value.estado !== 'A' ? ' galeria-admin-card--inactive' : '' %>">
            <button
                type="button"
                class="galeria-admin-delete"
                data-toggle="borrar"
                data-cid="<%= value.id %>"
                title="Eliminar"
                aria-label="Eliminar banner <%= value.id %>">
                <i class="fa fa-times"></i>
            </button>

            <div class="galeria-admin-media-wrap">
                <span class="galeria-admin-badge"><%= value.estado_label %></span>
                <div
                    class="galeria-admin-image"
                    style="background-image: url('<%= value.imagen %>');"
                    data-toggle="preview"
                    data-cid="<%= value.id %>"
                    data-file="<%= value.imagen %>"
                    data-archivo-nombre="<%= value.imagen_nombre || '' %>"
                    data-url="<%= value.url_imagen || value.imagen %>"
                    data-estado="<%= value.estado_label %>"
                    data-fechas="<%= value.fecha_inicia %> — <%= value.fecha_finaliza %>"
                    role="button"
                    tabindex="0"
                    aria-label="Ampliar banner <%= value.id %>">
                </div>
            </div>

            <footer class="galeria-admin-footer">
                <div class="galeria-admin-meta">
                    <span class="galeria-admin-title">Banner #<%= value.id %></span>
                    <span class="galeria-admin-toolbar-subtitle d-block"><%= value.fecha_inicia %> → <%= value.fecha_finaliza %></span>
                </div>
                <div class="galeria-admin-actions">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        data-toggle="editar"
                        data-cid="<%= value.id %>"
                        title="Editar"
                        aria-label="Editar banner <%= value.id %>">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </footer>
        </article>
    </script>

    <script>
        window.ServerController = 'banners';
    </script>

    <script src="{{ versioned_asset('cajas/build/Banners.js') }}"></script>
@endpush
