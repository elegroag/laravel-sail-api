@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')
    <script type="text/template" id="templateNoGiro">
        <table class='table table-hover align-items-center table-bordered'>
        <thead>
                <tr>
                    <th scope='col'>Periodo Girado</th>
                    <th scope='col'>Periodo Pagado</th>
                    <th scope='col'>Razon Social</th>
                    <th scope='col'>Nombre Beneficiario</th>
                    <th scope='col'>Motivo</th>
                </tr>
            </thead>
            <tbody class='list'>
                <% if (motivos.length == 0) { %>
                    <tr align='center'>
                        <td colspan=10><label class='text-center'>No hay datos para mostrar</label></td>
                    </tr>
                <% } else { %>
                    <% _.each(motivos, function(item) { %>
                        <tr>
                            <td><%= item.pergir %></td>
                            <td><%= item.periodo %></td>
                            <td><%= item.razsoc %></td>
                            <td><%= item.nombre %></td>
                            <td><%= item.motivo %></td>
                        </tr>
                    <% }) %>
                <% } %>
            </tbody>
        </table>
    </script>

    <script>
        const _TITULO = "{{ $title }}";
        window.ServerController = 'subsidio';
    </script>

    <script src="{{ asset('mercurio/build/ConsultasTrabajador.js') }}"></script>
@endpush

@section('content')
<div class="col-12 mt-3">
    <div class="card mb-0">
        <div class="card-header p-3">
            <div class="btn-group w-100">
                <button type="button" class="btn btn-default w-10" id='bt_consulta_nogiro'><i class="fa fa-search"></i> Consultar</button>
            </div>
        </div>
        <div class="card-body">
            <form id="form" class="validation_form" autocomplete="off" novalidate>
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="perini" class="form-control-label">Periodo Inicial</label>
                            <input type="text" id="perini" name="perini" class="form-control" placeholder="Periodo Inicial" value="{{ date('Ym', strtotime('-3 month')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="perfin" class="form-control-label">Periodo Final</label>
                            <input type="text" id="perfin" name="perfin" class="form-control" placeholder="Periodo Final" value="{{ date('Ym') }}">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id='consulta' class='table-responsive'></div>
@endsection