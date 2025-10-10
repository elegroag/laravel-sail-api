@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])
    @include("partials.modal_generic", [
        "titulo" => $title,
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btGenericoModal',
        "idModal" => 'genericoModal']
    )

    @include("partials.modal_generic", [
        "titulo" => 'Configuración básica',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal']
    )

    <script>
        window.ServerController = 'mercurio06';
    </script>

    <script type="text/template" id='tmp_form'>
        <form id="form" class="validation_form" autocomplete="off" novalidate>
        <div class="form-group">
            <label for="tipo" class="form-control-label">Tipo</label>
            <input type="text" name="tipo" class="form-control" placeholder="Tipo" value="<%=tipo%>" />
        </div>
        <div class="form-group">
            <label for="detalle" class="form-control-label">Detalle</label>
            <input type="text" name="detalle" class="form-control" placeholder="Detalle" value="<%=detalle%>" />
        </div>
        </form>
    </script>

    <script type="text/template" id='tmp_table_campo'>
        <div class='col-auto p-3'>
            <button 
            type="button" 
            class="btn btn-md btn-default" 
            data-tipo="<%=tipo%>"
            data-toggle="campo-agregar">
            <i class="fas fa-plus"></i>
            </button>
        </div>
        <table class='table table-bordered' id='dataTableCampo'>
            <thead>
                <tr>
                    <th scope='col' style="width: 20%;">Options</th>
                    <th scope='col' style="width: 10%;">Tipo</th>
                    <th scope='col' style="width: 60%;">Detalle</th>
                    <th scope='col' style="width: 10%;">Orden</th>
                </tr>
            </thead>
            <tbody>
                <% 
                if(_collection.length == 0){ %>
                    <tr>
                        <td colspan="4" class="text-center">No hay datos</td>
                    </tr>
                <% } else { 
                    _.each(_collection, function(mercurio28){ %>
                        <tr>
                            <td class='table-actions'>
                                <button type="button" 
                                    class='table-action btn btn-xs btn-primary' 
                                    data-toggle='campo-editar' 
                                    data-tipo='<%=mercurio28.tipo%>' 
                                    data-campo='<%=mercurio28.campo%>'>
                                    <i class='fas fa-user-edit text-white'></i>
                                </button>
                                <button type="button" 
                                    class='table-action btn btn-xs btn-danger' 
                                    data-toggle='campo-borrar' 
                                    data-tipo='<%=mercurio28.tipo%>' 
                                    data-campo='<%=mercurio28.campo%>'>
                                    <i class='fas fa-trash text-white'></i>
                                </button>
                            </td>
                            <td><%=mercurio28.campo%></td>
                            <td><%=mercurio28.detalle%></td>
                            <td><%=mercurio28.orden%></td>
                        </tr>
                    <% }); %>
                <% } %>
            </tbody>
       </table>
    </script>

    <script type="text/template" id='tmp_form_campo'>
        <form id="form_campo" class="validation_form" autocomplete="off" novalidate>
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="campo_28">Campo</label>
                        <input name="campo_28" id="campo_28" type="text" class="form-control" value="<%=campo%>" placeholder="Campo"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="detalle_28">Detalle</label>
                        <input name="detalle_28" id="detalle_28" type="text" class="form-control" value="<%=detalle%>" placeholder="Detalle" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="orden_28">Orden</label>
                        <input name="orden_28" id="orden_28" type="number" class="form-control" value="<%=orden%>" placeholder="Orden" />
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-auto">
                    <button 
                        type="button" 
                        id="btnAdicionar" 
                        name="btnAdicionar" 
                        data-tipo="<%=tipo%>"
                        data-campo="<%=campo%>"
                        class="btn btn-success" 
                        data-toggle='campo-guardar'>Adicionar</button>
                    
                    <button 
                        type="button" 
                        class="btn btn-secondary"
                        data-tipo="<%=tipo%>"
                        data-toggle="campo-cancelar">Cancelar</button>
                </div>
            </div>
        </form>
    </script>

    <script src="{{ asset('cajas/build/TipoAcceso.js') }}"></script>
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