@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>
@endpush

@section('content')
<div class="col-12">
    <div class="nav-wrapper">
        <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-bs-toggle="tab" href="#tabs-icons-text-1" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Afiliaciones Trabajador
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-bs-toggle="tab" href="#tabs-icons-text-2" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false">
                    <i class="fas fa-user-friends mr-2"></i>Novedades Retiro
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-3-tab" data-bs-toggle="tab" href="#tabs-icons-text-3" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false">
                    <i class="fas fa-child mr-2"></i>Datos Basicos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-4-tab" data-bs-toggle="tab" href="#tabs-icons-text-4" role="tab" aria-controls="tabs-icons-text-4" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Afiliaciones Conyuges
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-5-tab" data-bs-toggle="tab" href="#tabs-icons-text-5" role="tab" aria-controls="tabs-icons-text-5" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Afiliaciones Beneficiarios
                </a>
            </li>
        </ul>
    </div>
    <div class="card shadow">
        <div class="card-body pt-0">
            <div class="pt-0">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                        <div class="row p-2">
                            <table class='table table-hover align-items-center table-bordered'>
                                <thead>
                                <tr>
                                <th scope='col'>Cedula</th>
                                <th scope='col'>Nombre </th>
                                <th scope='col'>Fecha de Solicitud</th>
                                <th scope='col'>Estado</th>
                                <th scope='col'>Fecha de Estado</th>
                                <th scope='col'>Motivo</th>
                                </tr>
                                </thead>
                                <tbody class='list'>
                                @if ($mercurio31->count() == 0)
                                    <tr align='center'>
                                    <td colspan=6><label>No hay datos para mostrar</label></td>
                                    </tr>
                                @endif
                                @foreach ($mercurio31->get() as $mmercurio31)
                                    <tr>
                                        <td>{{$mmercurio31->getCedtra()}}</td>
                                        <td>{{$mmercurio31->getPriape()}} {{$mmercurio31->getPrinom()}}</td>
                                        <td>{{$mmercurio31->getFecsol()}}</td>
                                        <td>{{$mmercurio31->getEstadoDetalle()}}</td>
                                        <td>{{$mmercurio31->getFecest()}}</td>
                                        <td>{{$mmercurio31->getMotivo()}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                        <div class="row p-2">
                            <table class='table table-hover align-items-center table-bordered'>
                                <thead>
                                <tr>
                                <th scope='col'>Cedula</th>
                                <th scope='col'>Nombre </th>
                                <th scope='col'>Estado</th>
                                <th scope='col'>Fecha Estado</th>
                                <th scope='col'>Motivo</th>
                                </tr>
                                </thead>
                                <tbody class='list'>
                                @if ($mercurio35->count() == 0)
                                    <tr align='center'>
                                    <td colspan=5><label>No hay datos para mostrar</label></td>
                                    </tr>
                                @endif
                                @foreach ($mercurio35->get() as $mmercurio35)
                                    <tr>
                                        <td>{{$mmercurio35->getCedtra()}}</td>
                                        <td>{{$mmercurio35->getNomtra()}}</td>
                                        <td>{{$mmercurio35->getEstadoDetalle()}}</td>
                                        <td>{{$mmercurio35->getFecest()}}</td>
                                        <td>{{$mmercurio35->getMotivo()}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tabs-icons-text-3" role="tabpanel" aria-labelledby="tabs-icons-text-3-tab">
                        <div class="row p-2">
                            <table class='table table-hover align-items-center table-bordered'>
                                <thead>
                                <tr>
                                <th scope='col'>Campo</th>
                                <th scope='col'>Valor Anterior </th>
                                <th scope='col'>Valor Nuevo</th>
                                <th scope='col'>Estado</th>
                                <th scope='col'>Fecha Estado</th>
                                <th scope='col'>Motivo</th>
                                </tr>
                                </thead>
                                <tbody class='list'>
                                @if ($mercurio33->count() == 0)
                                    <tr align='center'>
                                    <td colspan=6><label>No hay datos para mostrar</label></td>
                                    </tr>
                                @endif
                                @foreach ($mercurio33->get() as $mmercurio33)
                                    <tr>
                                        <td>{{$mmercurio28->getDetalle()}}</td>
                                        <td>{{$mmercurio33->antval}}</td>
                                        <td>{{$mmercurio33->valor}}</td>
                                        <td>{{$mmercurio33->getEstadoDetalle()}}</td>
                                        <td>{{$mmercurio33->fecest}}</td>
                                        <td>{{$mmercurio33->motivo}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tabs-icons-text-4" role="tabpanel" aria-labelledby="tabs-icons-text-4-tab">
                        <div class="row p-2">
                            <table class='table table-hover align-items-center table-bordered'>
                                <thead>
                                <tr>
                                <th scope='col'>Cedula Trabajador</th>
                                <th scope='col'>Cedula</th>
                                <th scope='col'>Nombre </th>
                                <th scope='col'>Estado</th>
                                <th scope='col'>Fecha Estado</th>
                                <th scope='col'>Motivo</th>
                                </tr>
                                </thead>
                                <tbody class='list'>
                        
                                @if ($mercurio32->count() == 0)
                                    <tr align='center'>
                                    <td colspan=6><label>No hay datos para mostrar</label></td>
                                    </tr>
                                @else
                                    @if ($mercurio32)
                                        @foreach ($mercurio32->get() as $mmercurio32)
                                            <tr>
                                            <td>{{$mmercurio32->getCedtra()}}</td>
                                            <td>{{$mmercurio32->getCedcon()}}</td>
                                            <td>{{$mmercurio32->getPriape()}} {{$mmercurio32->getPrinom()}}</td>
                                            <td>{{$mmercurio32->getFecest()}}</td>
                                            <td>{{$mmercurio32->getMotivo()}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tabs-icons-text-5" role="tabpanel" aria-labelledby="tabs-icons-text-5-tab">
                        <div class="row p-2">
                            <table class='table table-hover align-items-center table-bordered'>
                                <thead>
                                <tr>
                                <th scope='col'>Cedula Trabajador</th>
                                <th scope='col'>Documento</th>
                                <th scope='col'>Nombre </th>
                                <th scope='col'>Estado</th>
                                <th scope='col'>Fecha Estado</th>
                                <th scope='col'>Motivo</th>
                                </tr>
                                </thead>
                                <tbody class='list'>
                                @if ($mercurio34->count() == 0)
                                    <tr align='center'>
                                    <td colspan=6><label>No hay datos para mostrar</label></td>
                                    </tr>
                                @endif
                                @foreach ($mercurio34->get() as $mmercurio34)
                                    <tr>
                                        <td>{{$mmercurio34->getCedtra()}}</td>
                                        <td>{{$mmercurio34->getNumdoc()}}</td>
                                        <td>{{$mmercurio34->getPriape()}} {{$mmercurio34->getPrinom()}}</td>
                                        <td>{{$mmercurio34->getEstadoDetalle()}}</td>
                                        <td>{{$mmercurio34->getFecest()}}</td>
                                        <td>{{$mmercurio34->getMotivo()}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection