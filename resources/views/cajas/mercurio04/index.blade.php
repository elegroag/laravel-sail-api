@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => true, 'listar' => false, 'salir' => false, 'add' => true])

<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-green-blue p-1"></div>
                <div class="card-body p-0 m-3">
                    <div id='consulta' class='table-responsive'></div>
                    <div id='paginate' class='card-footer py-4'></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])

    @include("partials.modal_generic", [
        "titulo" => 'Configuración básica',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal']
    )

    @include("partials.modal_generic", [
        "titulo" => 'Capturar opciones',
        "contenido" => '',
        "evento" => 'data-toggle="guardar-opciones"',
        "btnShowModal" => 'btCaptureOpciones',
        "idModal" => 'captureOpciones']
    )

    @include("partials.modal_generic", [
        "titulo" => 'Capturar ciudades',
        "contenido" => '',
        "evento" => 'data-toggle="guardar-ciudades"',
        "btnShowModal" => 'btCaptureCiudades',
        "idModal" => 'captureCiudades']
    )

    <script type="text/template" id="tmp_form">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="codofi" class="form-control-label">Oficina</label>
                <input type="text" id="codofi" class="form-control" placeholder="Oficina">
            </div>
            <div class="form-group">
                <label for="detalle" class="form-control-label">Detalle</label>
                <input type="text" id="detalle" class="form-control" placeholder="Detalle">
            </div>
            <div class="form-group">
                <label for="principal" class="form-control-label">Principal</label>
                <select id="principal" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($principal as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="estado" class="form-control-label">Estado</label>
                <select id="estado" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($estados as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </script>

    <script type="text/template" id="tmp_capture_opciones">
        <div class="container-fluid">
            <div class="row" id='div_opciones'></div>
        </div>
    </script>

    <script type="text/template" id="tmp_capture_ciudades">
        <div class="container-fluid">
            <div class="row" id='div_ciudades'></div>
        </div>
    </script>

    <script type="text/template" id="tmp_ciudades">
       <table class='table table-bordered'>
            <tbody>
                <% _.each(_collection, function(item){ %>
                <tr>
                    <td><%=item.codciu %></td>
                    <td class='table-actions'>
                        <a href='#!'
                            class='table-action btn btn-xs btn-primary'
                            data-toggle='ciudad-borrar'
                            data-codofi='<%=item.codofi%>'
                            data-codciu='<%=item.codciu%>'>
                                <i class='fas fa-trash text-white'></i>
                        </a>
                    </td>
                </tr>
                <% })%>
            </tbody>
       </table>
    </script>

    <script type="text/template" id="tmp_opciones">
        <form id="form_opcion" method="POST" action="#!" class="validation_form" autocomplete="off" novalidate>
            <div class="row justify-content-between mb-4">
                <div class="col-md-5">
                    <div class="form-group">
                        @component('components.select-field', [
                            'id' => 'tipopc_opt', 
                            'name' => 'tipopc_opt', 
                            'dummy' => 'seleccione aquí', 
                            'className' => 'form-control',
                            'options' => $tipopcs,
                            'label' => 'Tipo de opción'
                        ])
                        @endcomponent
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        @component('components.select-field', [
                            'id' => 'usuario_opt', 
                            'name' => 'usuario_opt', 
                            'dummy' => 'seleccione aquí', 
                            'className' => 'form-control',
                            'options' => $usuarios,
                            'label' => 'Usuario'
                        ])    
                        @endcomponent
                    </div>
                </div>
                <div class="col-auto pt-4">
                    <button id="btnAddOpcion" type="button" class="btn btn-success mt-3" data-codofi="<%=codofi %>" data-toggle="guardar-opcion" >
                        Adiciona
                    </button>
                </div>
            </div>
        </form>

        <h5>Lista de opciones por funcionario</h5>
        <table class='table table-bordered'>
            <thead>
                <tr>
                    <th>Tipo de opción</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <% _.each(_collection, function(item){ %>
                <tr>
                    <td><%=item.tipopc_detalle%></td>
                    <td><%=item.usuario_nombre%></td>
                    <td class='table-actions'>
                        <a href='#!'
                            class='table-action btn btn-xs btn-primary'
                            data-toggle='opcion-borrar'
                            data-codofi='<%=item.codofi%>'
                            data-tipopc='<%=item.tipopc%>'
                            data-usuario='<%=item.usuario%>'>
                                <i class='fas fa-trash text-white'></i>
                        </a>
                    </td>
                </tr>
                <% })%>
            </tbody>
        </table>
    </script>

    <script>
        window.ServerController = 'mercurio04';
    </script>

    <script src="{{ asset('cajas/build/Oficinas.js') }}"></script>
@endpush
