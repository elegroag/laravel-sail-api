@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.min.css') }}">
    <style>
        .list-group-item {
            cursor: pointer;
        }
    
        .list-group-item.active {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    
        .btn-sucursal {
            min-width: 120px;
        }
    
        .table th,
        .table td {
            vertical-align: middle;
        }
    
        #periodo-list {
            max-height: 600px;
            overflow-y: auto;
        }
    
        .badge-afiliado {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
    
        .badge-pago {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
    
        .list-group-item.active {
            background-color: rgb(171, 231, 184);
            border-color: #c2ffcd;
        }
    </style>
@endpush

@push('scripts')
<script type="text/template" id="tmp_mora_presunta">
    <div class="mb-0">
        <div class="col-auto">
            <table id='dataTable' class='table table-hover align-items-center table-bordered'>
                <thead>
                    <tr>
                        <th scope='col'>Cédula</th>
                        <th scope='col'>Afiliado</th>
                        <th scope='col'>Valor</th>
                        <th scope='col'>Pago</th>
                        <th scope='col'>Nombre Completo</th>
                    </tr>
                </thead>
                <tbody class='list'>
                    <% if (!cartera || _.size(cartera) == 0) { %>
                        <tr align='center'>
                            <td colspan=15><label class='text-center'>No hay datos para mostrar</label></td>
                        </tr>
                    <% } else { 
                        _.each(cartera, function (item) {
                        %>
                        <tr>
                            <td><%=item.cedtra%></td>
                            <td><%=(item.afiliado=='S') ? 'Sí' : 'No'%></td>
                            <td><%=item.valcar%></td>
                            <td><%=(item.pago == 'S') ? 'Pagado' : 'Pendiente'%></td>
                            <td><%=item.fullname%></td>
                        </tr>
                        <% }); %>
                    <% } %>
                </tbody>
            </table> 
        </div>
    </div>
</script>

<script type="text/template" id="tmp_layout">
    <div class="row">
        <!-- Tabla de datos principal -->
        <div class="col-md-10">
            <div class="mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <label>Datos de mora presunta <span id="view_mora_title"></span></label>
                    <div class="btn-group" id="sucursal-tabs">
                        <!-- Aquí se generarán las pestañas de sucursales dinámicamente -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="data-table">
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de períodos -->
        <div class="col-md-2">
            <div class="card">
                <div class="card-header">
                    <h5>Períodos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="periodo-list">
                        <!-- Aquí se generarán los períodos dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
@endpush

@section('content')
<div class="col-12 mt-3">
    <div id='boneLayout'></div>
</div>
<script src="{{ asset('mercurio/build/MoraPresunta.js') }}"></script>
@endsection