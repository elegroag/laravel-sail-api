@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')
    <script type="text/template" id="templateNoGiro">
        <table id='dataTable' class='table table-hover align-items-center table-bordered'>
            <thead>
                <tr>
                    <th scope='col'>Periodo girado</th>
                    <th scope='col'>Periodo pagado</th>
                    <th scope='col'>Razon social</th>
                    <th scope='col'>Nombre beneficiario</th>
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
<div class="col-12 col-xl-8 mx-auto mt-3">
    <div class="card mb-0 shadow-sm border-0">
        <div class="card-header border-0 pb-2 pb-md-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <h2 class="h5 mb-1">{{ $title ?? 'Consulta de motivos de no giro' }}</h2>
                    <p class="mb-0 text-sm text-muted">
                        Consulta los periodos en los que no se realizó el giro y los motivos asociados al beneficiario.
                    </p>
                </div>
                <div class="text-md-end">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2"
                        id="bt_consulta_nogiro">
                        <i class="fas fa-search me-1"></i>
                        <span>Consultar</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body pt-3">
            <form id="form" class="validation_form mb-4" autocomplete="off" novalidate>
                <div class="row justify-content-center g-3">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="perini" class="form-control-label">
                                <i class="fas fa-calendar-alt text-muted me-1"></i>Periodo Inicial
                            </label>
                            <input
                                type="text"
                                id="perini"
                                name="perini"
                                date="month"
                                class="form-control"
                                placeholder="Periodo Inicial"
                                value="{{ date('Ym', strtotime('-3 month')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="perfin" class="form-control-label">
                                <i class="fas fa-calendar-alt text-muted me-1"></i>Periodo Final
                            </label>
                            <input
                                type="text"
                                id="perfin"
                                name="perfin"
                                date="month"
                                class="form-control"
                                placeholder="Periodo Final"
                                value="{{ date('Ym') }}">
                        </div>
                    </div>
                </div>
            </form>
            <div id="consulta" class="table-responsive mt-3"></div>
        </div>
    </div>
</div>
@endsection