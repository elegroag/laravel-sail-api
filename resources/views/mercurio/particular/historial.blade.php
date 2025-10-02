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
                    <i class="fas fa-user-tie  mr-2"></i>Empresas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-2-tab" data-bs-toggle="tab" href="#tabs-icons-text-2" role="tab" aria-controls="tabs-icons-text-4" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Independientes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-3-tab" data-bs-toggle="tab" href="#tabs-icons-text-3" role="tab" aria-controls="tabs-icons-text-2" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Facultativo
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-4-tab" data-bs-toggle="tab" href="#tabs-icons-text-4" role="tab" aria-controls="tabs-icons-text-3" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Pensionado
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-5-tab" data-bs-toggle="tab" href="#tabs-icons-text-5" role="tab" aria-controls="tabs-icons-text-4" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Comunitaria
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-6-tab" data-bs-toggle="tab" href="#tabs-icons-text-6" role="tab" aria-controls="tabs-icons-text-4" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Domestico
                </a>
            </li>
        </ul>
    </div>
    <div class="card shadow">
        <div class="card-body pt-0">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="row p-2">
                        <table class='table table-hover align-items-center table-bordered'>
                            <thead >
                            <tr>
                            <th scope='col'>Nit</th>
                            <th scope='col'>Razon Social </th>
                            <th scope='col'>Estado</th>
                            <th scope='col'>Fecha Estado</th>
                            <th scope='col'>Motivo</th>
                            </tr>
                            </thead>
                            <tbody class='list'>
                            @if ($mercurio30->count() == 0)
                                <tr align='center'>
                                <td colspan=5><label>No hay datos para mostrar</label></td>
                                </tr>
                            @endif
                            @foreach ($mercurio30->get() as $mmercurio30)
                                <tr>
                                <td>{{$mmercurio30->nit}}</td>
                                <td>{{$mmercurio30->razsoc}}</td>
                                <td>{{$mmercurio30->estado}}</td>
                                <td>{{$mmercurio30->fecest}}</td>
                                <td>{{$mmercurio30->motivo}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade show" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                    <div class="row p-2">
                        <table class='table table-hover align-items-center table-bordered'>
                            <thead >
                            <tr>
                            <th scope='col'>Cedula</th>
                            <th scope='col'>Nombre </th>
                            <th scope='col'>Estado</th>
                            <th scope='col'>Fecha Estado</th>
                            <th scope='col'>Motivo</th>
                            </tr>
                            </thead>
                            <tbody class='list'>
                            @if ($mercurio41->count() == 0)
                                <tr align='center'>
                                <td colspan=5><label>No hay datos para mostrar</label></td>
                                </tr>
                            @endif
                            @foreach ($mercurio41->get() as $mmercurio41)
                                <tr>
                                    <td>{{$mmercurio41->cedtra}}</td>
                                    <td>{{$mmercurio41->priape}} {{$mmercurio41->prinom}}</td>
                                    <td>{{$mmercurio41->estado}}</td>
                                    <td>{{$mmercurio41->fecest}}</td>
                                    <td>{{$mmercurio41->motivo}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade show" id="tabs-icons-text-3" role="tabpanel" aria-labelledby="tabs-icons-text-3-tab">
                    <div class="row p-2">
                        <table class='table table-hover align-items-center table-bordered'>
                            <thead >
                            <tr>
                            <th scope='col'>Cedula</th>
                            <th scope='col'>Nombre </th>
                            <th scope='col'>Estado</th>
                            <th scope='col'>Fecha Estado</th>
                            <th scope='col'>Motivo</th>
                            </tr>
                            </thead>
                            <tbody class='list'>
                            @if ($mercurio36->count() == 0)
                                <tr align='center'>
                                <td colspan=5><label>No hay datos para mostrar</label></td>
                                </tr>
                            @endif
                            @foreach ($mercurio36->get() as $mmercurio36)
                                <tr>
                                <td>{{$mmercurio36->cedtra}}</td>
                                <td>{{$mmercurio36->priape}} {{$mmercurio36->prinom}}</td>
                                <td>{{$mmercurio36->estado}}</td>
                                <td>{{$mmercurio36->fecest}}</td>
                                <td>{{$mmercurio36->motivo}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade show" id="tabs-icons-text-4" role="tabpanel" aria-labelledby="tabs-icons-text-4-tab">
                    <div class="row p-2">
                        <table class='table table-hover align-items-center table-bordered'>
                            <thead >
                            <tr>
                            <th scope='col'>Cedula</th>
                            <th scope='col'>Nombre </th>
                            <th scope='col'>Estado</th>
                            <th scope='col'>Fecha Estado</th>
                            <th scope='col'>Motivo</th>
                            </tr>
                            </thead>
                            <tbody class='list'>
                            @if ($mercurio38->count() == 0)
                                <tr align='center'>
                                <td colspan=5><label>No hay datos para mostrar</label></td>
                                </tr>
                            @endif
                            @foreach ($mercurio38->get() as $mmercurio38)
                                <tr>
                                    <td>{{$mmercurio38->cedtra}}</td>
                                    <td>{{$mmercurio38->priape}} {{$mmercurio38->prinom}}</td>
                                    <td>{{$mmercurio38->estado}}</td>
                                    <td>{{$mmercurio38->fecest}}</td>
                                    <td>{{$mmercurio38->motivo}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade show" id="tabs-icons-text-5" role="tabpanel" aria-labelledby="tabs-icons-text-5-tab">
                    <div class="row p-2">
                        <table class='table table-hover align-items-center table-bordered'>
                            <thead >
                            <tr>
                            <th scope='col'>Cedula</th>
                            <th scope='col'>Nombre </th>
                            <th scope='col'>Estado</th>
                            <th scope='col'>Fecha Estado</th>
                            <th scope='col'>Motivo</th>
                            </tr>
                            </thead>
                            <tbody class='list'>
                            @if ($mercurio39->count() == 0)
                                <tr align='center'>
                                <td colspan=5><label>No hay datos para mostrar</label></td>
                                </tr>
                            @endif
                            @foreach ($mercurio39->get() as $mmercurio39)
                                <tr>
                                <td>{{$mmercurio39->cedtra}}</td>
                                <td>{{$mmercurio39->priape}} {{$mmercurio39->prinom}}</td>
                                <td>{{$mmercurio39->estado}}</td>
                                <td>{{$mmercurio39->fecest}}</td>
                                <td>{{$mmercurio39->motivo}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade show" id="tabs-icons-text-6" role="tabpanel" aria-labelledby="tabs-icons-text-6-tab">
                    <div class="row p-2">
                        
                        <table class='table table-hover align-items-center table-bordered'>
                            <thead >
                            <tr>
                            <th scope='col'>Cedula</th>
                            <th scope='col'>Nombre </th>
                            <th scope='col'>Estado</th>
                            <th scope='col'>Fecha Estado</th>
                            <th scope='col'>Motivo</th>
                            </tr>
                            </thead>
                            <tbody class='list'>
                            @if ($mercurio40->count() == 0)
                                <tr align='center'>
                                <td colspan=5><label>No hay datos para mostrar</label></td>
                                </tr>
                            @endif
                            @foreach ($mercurio40->get() as $mmercurio40)
                                <tr>
                                <td>{{$mmercurio40->cedtra}}</td>
                                <td>{{$mmercurio40->priape}} {{$mmercurio40->prinom}}</td>
                                <td>{{$mmercurio40->estado}}</td>
                                <td>{{$mmercurio40->fecest}}</td>
                                <td>{{$mmercurio40->motivo}}</td>
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
@endsection
