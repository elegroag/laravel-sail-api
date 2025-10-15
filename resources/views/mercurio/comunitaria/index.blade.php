@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.min.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div id='consulta' class='table-responsive'></div>
    <div id='paginate' class='card-footer py-4'></div>

    <!-- Modal Captura -->
    <div class="modal fade" id="capture-modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card mb-0">
                        <div class="card-header bg-secondary">
                            <div class="row align-items-center">
                                <div class="col-10">
                                    <h3 class="mb-0">{{ $title }}</h3>
                                </div>
                                <div class="col-2 text-right">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="form" class="validation_form" autocomplete="off" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipdoc" class="form-control-label">Tipo Documento</label>
                                        {{ Tag::selectStatic("tipdoc", $_coddoc, "use_dummy: true", "dummyValue: ", "class: form-control", "readonly: true") }}
                                        {{ Tag::hiddenField("id") }}
                                        {{ Tag::hiddenField("calemp") }}
                                        {{ Tag::hiddenField("codact") }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cedtra" class="form-control-label">Cedula</label>
                                        {{ Tag::numericField("cedtra", "class: form-control", "placeholder: Cedula", "readonly: true", "event: is_numeric") }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="priape" class="form-control-label">Primer Apellido</label>
                                        {{ Tag::textUpperField("priape", "class: form-control", "placeholder: Primer Apellido") }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="segape" class="form-control-label">Segundo Apellido</label>
                                        {{ Tag::textUpperField("segape", "class: form-control", "placeholder: Segundo Apellido") }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="prinom" class="form-control-label">Primer Nombre</label>
                                        {{ Tag::textUpperField("prinom", "class: form-control", "placeholder: Primer Nombre") }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="segnom" class="form-control-label">Segundo Nombre</label>
                                        {{ Tag::textUpperField("segnom", "class: form-control", "placeholder: Segundo Nombre") }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecnac" class="form-control-label">Fecha Nacimiento</label>
                                        {{ TagUser::calendar("fecnac", "class: form-control", "placeholder: Fecha Nacimiento") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ciunac" class="form-control-label">Ciudad Nacimiento</label>
                                        {{ Tag::selectStatic("ciunac", $_codciu, "use_dummy: true", "dummyValue: ", "class: form-control", "select2: true") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sexo" class="form-control-label">Sexo</label>
                                        {{ Tag::selectStatic("sexo", $_sexo, "use_dummy: true", "dummyValue: ", "class: form-control") }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estciv" class="form-control-label">Estado Civil</label>
                                        {{ Tag::selectStatic("estciv", $_estciv, "use_dummy: true", "dummyValue: ", "class: form-control") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cabhog" class="form-control-label">Cabeza Hogar</label>
                                        {{ Tag::selectStatic("cabhog", $_cabhog, "use_dummy: true", "dummyValue: ", "class: form-control") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="codciu" class="form-control-label">Ciudad</label>
                                        {{ Tag::selectStatic("codciu", $_codciu, "use_dummy: true", "dummyValue: ", "class: form-control", "select2: true") }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="codzon" class="form-control-label">Zona</label>
                                        {{ Tag::selectStatic("codzon", $_codzon, "use_dummy: true", "dummyValue: ", "class: form-control", "select2: true") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        @component('components/address', [
                                            'name' => 'direccion', 
                                            'value' => '',
                                            'placeholder' => 'Dirección',
                                            'event' => 'address',
                                            'label' => 'Dirección'
                                        ])@endcomponent
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="barrio" class="form-control-label">Barrio</label>
                                        {{ Tag::textUpperField("barrio", "class: form-control", "placeholder: Barrio") }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="telefono" class="form-control-label">Telefono</label>
                                        {{ Tag::numericField("telefono", "class: form-control", "placeholder: Telefono", "maxlength: 10", "minlength: 10", "event: is_numeric") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="celular" class="form-control-label">Celular</label>
                                        {{ Tag::numericField("celular", "class: form-control", "placeholder: Celular", "maxlength: 10", "minlength: 10", "event: is_numeric") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fax" class="form-control-label">Fax</label>
                                        {{ Tag::textUpperField("fax", "class: form-control", "placeholder: Fax") }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email" class="form-control-label">Email</label>
                                        {{ Tag::textUpperField("email", "class: form-control", "placeholder: Email") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecing" class="form-control-label">Fecha Ingreso</label>
                                        {{ TagUser::calendar("fecing", "class: form-control", "placeholder: Fecha Ingreso") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="salario" class="form-control-label">Salario</label>
                                        {{ Tag::numericField("salario", "class: form-control", "placeholder: Salario", "event: is_numeric") }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="captra" class="form-control-label">Capacidad Trabajo</label>
                                        {{ Tag::selectStatic("captra", $_captra, "use_dummy: true", "dummyValue: ", "class: form-control") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipdis" class="form-control-label">tipo discapacidad</label>
                                        {{ Tag::selectStatic("tipdis", $_tipdis, "use_dummy: true", "dummyValue: ", "class: form-control") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nivedu" class="form-control-label">Nivel Educacion</label>
                                        {{ Tag::selectStatic("nivedu", $_nivedu, "use_dummy: true", "dummyValue: ", "class: form-control") }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="rural" class="form-control-label">Rural</label>
                                        {{ Tag::selectStatic("rural", $_rural, "use_dummy: true", "dummyValue: ", "class: form-control") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vivienda" class="form-control-label">Vivienda</label>
                                        {{ Tag::selectStatic("vivienda", $_vivienda, "use_dummy: true", "dummyValue: ", "class: form-control") }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipafi" class="form-control-label">Tipo Afiliados</label>
                                        {{ Tag::selectStatic("tipafi", $_tipafi, "use_dummy: true", "dummyValue: ", "class: form-control") }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="autoriza" class="form-control-label">Autoriza</label>
                                        {{ Tag::selectStatic("autoriza", array("S" => "SI", "N" => "NO"), "use_dummy: true", "dummyValue: ", "class: form-control") }}
                                    </div>
                                </div>
                            </div>
                            {{ Tag::endform() }}
                        </div>
                        <div class="card-footer text-right">
                            <button type="button" class="btn btn-primary" onclick="guardar();">Guardar</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Captura -->
    <div class="modal fade" id="capture-modal-info" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card mb-0">
                        <div class="card-header bg-secondary">
                            <div class="row align-items-center">
                                <div class="col-10">
                                    <h3 class="mb-0">Información</h3>
                                </div>
                                <div class="col-2 text-right">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id='div_info'>

                        </div>
                        <div class="card-footer text-right">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('mercurio/Comunitaria.js') }}"></script>
@endpush
