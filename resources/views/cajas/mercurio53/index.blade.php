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
                            <h4 class="galeria-admin-toolbar-title mb-0">Imágenes destacadas</h4>
                            <p class="galeria-admin-toolbar-subtitle mb-0">{{ $help }}</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#captureModal">
                            <i class="fas fa-plus mr-1"></i> Agregar imagen
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
        'titulo' => 'Carga de archivo',
        'contenido' => view('cajas.mercurio53.partials.form')->render(),
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
        <article class="galeria-admin-card">
            <button
                type="button"
                class="galeria-admin-delete"
                data-toggle="borrar"
                data-cid="<%= value.numero %>"
                title="Eliminar"
                aria-label="Eliminar imagen <%= value.numero %>">
                <i class="fa fa-times"></i>
            </button>

            <div class="galeria-admin-media-wrap">
                <div
                    class="galeria-admin-image"
                    style="background-image: url('<%= value.archivo %>');"
                    data-toggle="preview"
                    data-cid="<%= value.numero %>"
                    data-file="<%= value.archivo %>"
                    data-archivo-nombre="<%= value.archivo_nombre %>"
                    data-url="<%= value.url %>"
                    role="button"
                    tabindex="0"
                    aria-label="Ampliar imagen <%= value.numero %>">
                </div>
            </div>

            <footer class="galeria-admin-footer">
                <div class="galeria-admin-meta">
                    <span class="galeria-admin-title">Imagen #<%= value.numero %> · Orden <%= value.orden %></span>
                </div>
                <div class="galeria-admin-actions">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        data-toggle="editar"
                        data-cid="<%= value.numero %>"
                        title="Editar"
                        aria-label="Editar imagen <%= value.numero %>">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-info"
                        data-toggle="arriba"
                        data-cid="<%= value.numero %>"
                        title="Mover arriba"
                        aria-label="Mover arriba">
                        <i class="fas fa-chevron-up"></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-info"
                        data-toggle="abajo"
                        data-cid="<%= value.numero %>"
                        title="Mover abajo"
                        aria-label="Mover abajo">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </footer>
        </article>
    </script>

    <script>
        window.ServerController = 'mercurio53';
    </script>

    <script src="{{ versioned_asset('cajas/build/Mercurio53.js') }}"></script>
@endpush
