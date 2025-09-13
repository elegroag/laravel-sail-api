@php
use App\Services\Tag;
@endphp

@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
@endpush

@section('content')
<div class="col-md-8 mb-4 mt-2">

    <div class="card-header text-sm-left text-center pb-3 px-4 pt-2">
        <div id="botones" class='row justify-content-end mt-0'>
            <a href="{{ Utils.getKumbiaURL($instancePath) }}admproductos/lista" class='btn btn-light'>&nbsp;Salir</a>&nbsp;
        </div>
        <h3 class="mb-1">Registrar nuevo producto o servicio</h3>
    </div>
    <div class="card-body border-0">
        @php echo Tag::form("id: formulario", "autocomplete: off", "role: form"); @endphp
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Servicio:</label>
                    <input class="form-control" name="servicio" type="text" value="servicio" id="" placeholder="Servicio" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Código:</label>
                    <input class="form-control" name="codser" type="number" value="" id="codser" placeholder="Codigo" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="example-text-input" class="form-control-label">Cupos:</label>
                    <input class="form-control" name="cupos" type="number" value="" id="cupos" placeholder="Cupos" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="example-text-input" name="estado" id="estado" class="form-control-label">Estado:</label>
                    <select class="form-control form-control-sm">
                        <option value="A">Activo</option>
                        <option value="P">Pendiente</option>
                        <option value="F">Finalizado</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <button type="button" id="guardaRegistro" class='btn btn-primary'>&nbsp;Guardar</button>
            </div>
        </div>
        @php echo Tag::endform(); @endphp
    </div>

</div>
@endsection

@push('scripts')
<script type="text/javascript">
    const formData = function(formById) {
        let _arreglo = $('#' + formById).serializeArray();
        let _token = {};
        let _i = 0;
        while (_i < _.size(_arreglo)) {
            _token[_arreglo[_i].name] = _arreglo[_i].value;
            _i++;
        }
        return _token;
    };

    $(document).ready(function() {

        $(document).on('click', '#guardaRegistro', function(event) {
            event.preventDefault();
            var target = $(event.currentTarget);
            swal.fire({
                title: "¡Confirmar!",
                html: "<p style='font-size:0.97rem'>¿Está seguro que desea crear el servicio?</p>",
                showCancelButton: true,
                confirmButtonClass: "btn btn-sm btn-success",
                cancelButtonClass: "btn btn-sm btn-danger",
                confirmButtonText: "SI",
                cancelButtonText: "NO"
            }).then(function(result) {
                if (result.value) {
                    let _token = formData('formulario');
                    $.ajax({
                        method: "POST",
                        url: Utils.getKumbiaURL($Kumbia.controller + "/guardar"),
                        dataType: "JSON",
                        cache: false,
                        data: _token
                    }).done(function(response) {
                        if (response.success) {
                            swal.fire({
                                title: "Notificación OK",
                                html: "<p class='text-left' style='font-size:1rem'>" + response.msj + '</p>',
                                showCloseButton: false,
                                showConfirmButton: true,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                confirmButtonText: 'Continuar'
                            }).then(function(e) {
                                if (e.value === true) {
                                    setTimeout(function() {
                                        window.location.href = Utils.getKumbiaURL($Kumbia.controller + "/lista")
                                    }, 100);
                                }
                            });
                        } else {
                            swal.fire({
                                "title": "Notificación Alerta",
                                "text": response.msj,
                                "icon": "warning",
                                "showConfirmButton": false,
                                "showCloseButton": true,
                                "timer": 10000
                            });
                        }
                    }).fail(function(err) {
                        console.log(err.responseText);
                        return false;
                    });
                }
            });
        });
    });
</script>
@endpush
