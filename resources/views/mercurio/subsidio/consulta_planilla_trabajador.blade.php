@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
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
@endpush

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <script type="text/template" id="templatePlanillas">
        <table id='dataTable' class='table table-hover align-items-center table-bordered'>
            <thead>
                <tr>
                    <th scope='col'>Razon Social</th>
                    <th scope='col'>Periodo Aporte</th>
                    <th scope='col'>Indice Aporte</th>
                    <th scope='col'>Salario Base</th>
                    <th scope='col'>Dias Trabajados</th>
                    <th scope='col'>Fecha Pago</th>
                    <th scope='col' data-toggle='tooltip' data-placement='top' title='INGRESO'>Ing</th>
                    <th scope='col' data-toggle='tooltip' data-placement='top' title='RETIRO'>Ret</th>
                    <th scope='col' data-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL TEMPORAL'>VST</th>
                    <th scope='col' data-toggle='tooltip' data-placement='top' title='SUSPENCION TEMPORAL CONTRATO'>STC</th>
                    <th scope='col' data-toggle='tooltip' data-placement='top' title='INCAPACIDAD TEMPORAL ENFERMEDAD'>ITE</th>
                    <th scope='col' data-toggle='tooltip' data-placement='top' title='LICENCIA MATERNIDAD'>LM</th>
                    <th scope='col' data-toggle='tooltip' data-placement='top' title='VACACIONES'>VAC</th>
                    <th scope='col' data-toggle='tooltip' data-placement='top' title='INCAPACIDAD TEMPORAL ACCIDENTE DE TRABAJO'>ITAT</th>
                    <th scope='col' data-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL PERMANENTE'>VSP</th>
                </tr>
            </thead>
            <tbody class='list'>
                <% if (planilla.length == 0) { %>
                    <tr align='center'>
                        <td colspan=15><label class='text-center'>No hay datos para mostrar</label></td>
                    </tr>
                <% } else { 
                    _.each(planilla, function (item) {
                    %>
                    <tr>
                        <td><small><%=item.razsoc%></small></td>
                        <td><%=item.perapo%></td>
                        <td><%=item.tarapo%></td>
                        <td><%=item.salbas%></td>
                        <td><%=item.diatra%></td>
                        <td><%=item.fecrec%></td>
                        <td data-toggle='tooltip' data-placement='top' title='INGRESO'><%=item.ingtra%></td>
                        <td data-toggle='tooltip' data-placement='top' title='RETIRO'><%=item.novret%></td>
                        <td data-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL TEMPORAL'><%=item.novvps%></td>
                        <td data-toggle='tooltip' data-placement='top' title='SUSPENCION TEMPORAL CONTRATO'><%=item.novstc%></td>
                        <td data-toggle='tooltip' data-placement='top' title='INCAPACIDAD TEMPORAL ENFERMEDAD'><%=item.novitg%></td>
                        <td data-toggle='tooltip' title='LICENCIA MATERNIDAD'><%=item.licnom%></td>
                        <td data-toggle='tooltip' title='VACACIONES'><%=item.vacnom%></td>
                        <td data-toggle='tooltip' title='INCAPACIDAD TEMPORAL ACCIDENTE DE TRABAJO'><%=item.incnom%></td>
                        <td data-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL PERMANENTE'><%=item.novvts%></td>
                    </tr>
                    <% }); %>
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
<div class="col-12 mx-auto mt-3">
    <div class="card mb-0 shadow-sm border-0">
        <div class="card-header border-0 pb-2 pb-md-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <h2 class="h5 mb-1">{{ $title ?? 'Consulta planilla del trabajador' }}</h2>
                    <p class="mb-0 text-sm text-muted">
                        Consulta las planillas de aportes del trabajador por periodo de pago.
                    </p>
                </div>
                <div class="text-md-end">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2"
                        id="bt_consulta_planilla_trabajador">
                        <i class="fas fa-search me-1"></i>
                        <span>Consultar</span>
                    </button>
                </div>
            </div>
            <form id="form" class="validation_form mb-4" autocomplete="off" novalidate>
                <div class="row justify-content-center g-3">
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
        </div>
        <div class="card-body">
            <div id="consulta" class="col table-responsive mt-3"></div>
        </div>
    </div>
</div>
@endsection











