@extends('layouts.bone')

@push('styles')
<style>
    #dataTable {
        font-size: 0.7rem;
    }

    #dataTable thead {
        background-color: #f0f0f0;
    }

    #dataTable th {
        padding: 0.3rem;
        text-align: left;
        vertical-align: middle;
        font-size: 0.85rem;
    }

    #dataTable td {
        padding: 0.3rem;
        text-align: center;
        vertical-align: middle;
    }
</style>
<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('mercurio/build/ConsultaGiro.js') }}"></script>
@endpush

@section('content')
<script type="text/template" id="templateConsulta">
    <div class='p-2 m-0'>
        <p class='m-0'><span><b>Número de Cuotas:</b> <%= cuotas.reduce((total, item) => total + item.numcuo, 0) %></span><br/>
            <span><b>Valor Neto:</b> <%= cuotas.reduce((total, item) => total + item.valor, 0) %></span><br/>
            <span><b>Valor Credito:</b> <%= cuotas.reduce((total, item) => total + item.valcre, 0) %></span><br/>
            <span><b>Valor Ajuste:</b> <%= cuotas.reduce((total, item) => total + item.valaju, 0) %></span>
        </p>
    </div>

    <table id='dataTable' class='table table-hover align-items-center table-bordered'>
        <thead>
            <tr>
                <th scope='col'>Periodo Girado</th>
                <th scope='col'>Tipo</th>
                <th scope='col'>Nombre Responsable</th>
                <th scope='col'>Nombre Beneficiario</th>
                <th scope='col'>Forma Pago</th>
                <th scope='col'>Num. Cuo.</th>
                <th scope='col'>Valor Neto</th>
                <th scope='col'>Valor Credito</th>
                <th scope='col'>Valor Ajuste</th>
            </tr>
        </thead>
        <tbody class='list'>
            <% if (cuotas.length == 0) { %>
                <tr align='center'>
                    <td colspan=10><label class='text-center'>No hay datos para mostrar</label></td>
                </tr>
            <% } else { %>
                <% _.each(cuotas, function(item) { %>
                    <tr>
                        <td><%= item.pergir %></td>
                        <td><%= item.tipo_pago %></td>
                        <td><%= item.nomres %></td>
                        <td><%= item.nombre %></td>
                        <td><%= item.tippag %></td>
                        <td><%= item.numcuo %></td>
                        <td><%= item.valor %></td>
                        <td><%= item.valcre %></td>
                        <td><%= item.valaju %></td>
                    </tr>
                <% }) %>
            <% } %>
            </tbody>
        </table>
</script>

<div class="card mb-0">
    <div class="card-header bg-green-blue p-1">
        <div class="btn-group w-100">
            <button type="button" class="btn btn-default w-10" id='bt_consulta_giro'><i class="fa fa-search"></i> Consultar</button>
        </div>
    </div>
    <div class="card-body">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="perini" class="form-control-label">Periodo Inicial</label>
                        <input type="date" name="perini" placeholder="Periodo Inicial" class="form-control" value="@php echo date('Y-m-d', strtotime('-3 month')); @endphp">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="perfin" class="form-control-label">Periodo Final</label>
                        <input type="date" name="perfin" placeholder="Periodo Final" class="form-control" value="@php echo date('Y-m-d'); @endphp">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id='consulta' class='table-responsive'></div>

@endsection
