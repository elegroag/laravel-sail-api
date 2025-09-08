@extends('layouts.dash')

@push('scripts')
<script src="{{ asset('Mercurio/core/upload.js') }}"></script>
@endpush

@section('content')

<div class="card mb-0">
    <div class="card-body">
        <?php echo Tag::form("subsidioemp/activacion_masiva_trabajador", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="row">
            <div class="col-md-5 ml-auto">
                <div class="form-group">
                    <label for="archivo" class="form-control-label">Archivoa</label>
                    <?php echo Tag::fileField("archivo", "class: form-control", "accept: .csv,text/plain"); ?>
                </div>
            </div>
            <div class="col-md-auto d-flex mr-auto">
                <button type="button" class="btn btn-icon btn-primary align-self-center" id="bt_activacion_masiva_trabajador">
                    <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                    <span class="btn-inner--text">Cargue Masivo</span>
                </button>
                <div class="col-md-auto d-flex mr-auto">
                    <button type="button" class="btn btn-danger align-self-center" id="bt_ejemplo_planilla_activacion_masiva">Ejemplo</button>
                </div>
            </div>
            <div style="width: 100%" , align="center">
                <p> <b>Tener en cuenta que el formato de la fecha debe ser:</b></p>
                <p><b> Año-Mes-Dia</b></p>
                <p><b> Ejemplo: 2020-01-01</b></p>
            </div>

        </div>
    </div>
    <?php echo Tag::endform(); ?>
</div>
</div>


<div id='consulta' class='table-responsive'>
</div>

<script src="{{ asset('Mercurio/consultasempresa/consultasempresa.build.js') }}"></script>
@endsection