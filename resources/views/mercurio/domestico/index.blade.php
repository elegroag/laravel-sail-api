@extends('layouts.bone')

@section('content')
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
                                        <select name="tipdoc" class="form-control" readonly>
                                            @foreach($_coddoc as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="id">
                                        <input type="hidden" name="calemp">
                                        <input type="hidden" name="codact">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cedtra" class="form-control-label">Cedula</label>
                                        <input type="number" name="cedtra" class="form-control" placeholder="Cedula" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="priape" class="form-control-label">Primer Apellido</label>
                                        <input type="text" name="priape" class="form-control" placeholder="Primer Apellido">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="segape" class="form-control-label">Segundo Apellido</label>
                                        <input type="text" name="segape" class="form-control" placeholder="Segundo Apellido">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="prinom" class="form-control-label">Primer Nombre</label>
                                        <input type="text" name="prinom" class="form-control" placeholder="Primer Nombre">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="segnom" class="form-control-label">Segundo Nombre</label>
                                        <input type="text" name="segnom" class="form-control" placeholder="Segundo Nombre">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecnac" class="form-control-label">Fecha Nacimiento</label>
                                        <input type="date" name="fecnac" class="form-control" placeholder="Fecha Nacimiento">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ciunac" class="form-control-label">Ciudad Nacimiento</label>
                                        <select name="ciunac" class="form-control">
                                            @foreach($_codciu as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sexo" class="form-control-label">Sexo</label>
                                        <select name="sexo" class="form-control">
                                            @foreach($_sexo as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estciv" class="form-control-label">Estado Civil</label>
                                        <select name="estciv" class="form-control">
                                            @foreach($_estciv as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cabhog" class="form-control-label">Cabeza Hogar</label>
                                        <select name="cabhog" class="form-control">
                                            @foreach($_cabhog as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="codciu" class="form-control-label">Ciudad</label>
                                        <select name="codciu" class="form-control">
                                            @foreach($_codciu as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="codzon" class="form-control-label">Zona</label>
                                        <select name="codzon" class="form-control">
                                            @foreach($_codzon as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="direccion" class="form-control-label">Direccion</label>
                                        <input type="text" name="direccion" class="form-control" placeholder="Direccion">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="barrio" class="form-control-label">Barrio</label>
                                        <input type="text" name="barrio" class="form-control" placeholder="Barrio">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="telefono" class="form-control-label">Telefono</label>
                                        <input type="number" name="telefono" class="form-control" placeholder="Telefono" maxlength="10" minlength="10">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="celular" class="form-control-label">Celular</label>
                                        <input type="number" name="celular" class="form-control" placeholder="Celular" maxlength="10" minlength="10">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fax" class="form-control-label">Fax</label>
                                        <input type="text" name="fax" class="form-control" placeholder="Fax">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email" class="form-control-label">Email</label>
                                        <input type="email" name="email" class="form-control" placeholder="Email">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecing" class="form-control-label">Fecha Ingreso</label>
                                        <input type="date" name="fecing" class="form-control" placeholder="Fecha Ingreso">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="salario" class="form-control-label">Salario</label>
                                        <input type="number" name="salario" class="form-control" placeholder="Salario">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="captra" class="form-control-label">Capacidad Trabajo</label>
                                        <select name="captra" class="form-control">
                                            @foreach($_captra as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipdis" class="form-control-label">Tipo Discapacidad</label>
                                        <select name="tipdis" class="form-control">
                                            @foreach($_tipdis as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nivedu" class="form-control-label">Nivel Educacion</label>
                                        <select name="nivedu" class="form-control">
                                            @foreach($_nivedu as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="rural" class="form-control-label">Rural</label>
                                        <select name="rural" class="form-control">
                                            @foreach($_rural as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vivienda" class="form-control-label">Vivienda</label>
                                        <select name="vivienda" class="form-control">
                                            @foreach($_vivienda as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipafi" class="form-control-label">Tipo Afiliados</label>
                                        <select name="tipafi" class="form-control">
                                            @foreach($_tipafi as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="autoriza" class="form-control-label">Autoriza</label>
                                        <select name="autoriza" class="form-control">
                                            <option value="S">SI</option>
                                            <option value="N">NO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
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
                                <h3 class="mb-0">{{ "Información" }}</h3>
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
@endsection
